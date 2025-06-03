#!/bin/bash

# Git Upload Script for WhatsApp Service Auto-Startup
# This script prepares and uploads the project to GitHub

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_header() {
    echo -e "${BLUE}=== $1 ===${NC}"
}

# Get current directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

print_header "WhatsApp Service GitHub Upload"
echo ""

# Check if git is available
if ! command -v git &> /dev/null; then
    print_error "Git is not installed. Please install Git first."
    exit 1
fi

# Check if this is already a git repository
if [ ! -d ".git" ]; then
    print_status "Initializing Git repository..."
    git init
    
    # Create initial gitignore if it doesn't exist
    if [ ! -f ".gitignore" ]; then
        print_warning ".gitignore not found. Please create one before continuing."
        exit 1
    fi
else
    print_status "Git repository already exists"
fi

# Check git configuration
if ! git config user.name > /dev/null 2>&1; then
    print_warning "Git user.name not configured."
    read -p "Enter your name: " git_name
    git config user.name "$git_name"
fi

if ! git config user.email > /dev/null 2>&1; then
    print_warning "Git user.email not configured."
    read -p "Enter your email: " git_email
    git config user.email "$git_email"
fi

print_status "Git configured as: $(git config user.name) <$(git config user.email)>"

# Check for remote repository
if ! git remote get-url origin > /dev/null 2>&1; then
    print_warning "No remote repository configured."
    echo ""
    echo "Please create a new repository on GitHub first, then provide the URL:"
    echo "Example: https://github.com/username/repository-name.git"
    echo "         git@github.com:username/repository-name.git"
    echo ""
    read -p "Enter GitHub repository URL: " repo_url
    
    if [ -n "$repo_url" ]; then
        git remote add origin "$repo_url"
        print_status "Remote repository added: $repo_url"
    else
        print_error "Repository URL is required"
        exit 1
    fi
else
    print_status "Remote repository: $(git remote get-url origin)"
fi

# Make sure all Linux scripts are executable
print_status "Setting executable permissions for Linux scripts..."
chmod +x *.sh 2>/dev/null || true

# Show current status
print_status "Current git status:"
git status --short

echo ""
print_header "File Summary"
echo ""

# Count files by category
total_files=$(find . -type f ! -path "./.git/*" ! -path "./node_modules/*" ! -path "./logs/*" ! -path "./sessions/*" | wc -l)
script_files=$(find . -name "*.sh" -o -name "*.ps1" -o -name "*.bat" | wc -l)
doc_files=$(find . -name "*.md" | wc -l)
config_files=$(find . -name "*.js" -o -name "*.json" -o -name "*.service" | wc -l)

print_status "📄 Total files: $total_files"
print_status "🔧 Script files: $script_files"
print_status "📚 Documentation: $doc_files"
print_status "⚙️ Configuration: $config_files"

echo ""
print_header "Commit Message"

# Suggest commit message
current_date=$(date '+%Y-%m-%d')
default_message="WhatsApp Service Auto-Startup - Complete Implementation ($current_date)

✅ Windows auto-startup: Fully implemented and tested
✅ Linux auto-startup: Scripts ready for deployment
✅ Cross-platform support: Auto-detection and setup
✅ Health monitoring: HTTP endpoint and automated checks
✅ Documentation: Complete setup guides and troubleshooting
✅ PM2 integration: Process management and auto-restart
✅ Multiple deployment methods: PM2, systemd, Windows Service

Features:
- Auto-startup on Windows boot (Startup folder + Task Scheduler)
- Auto-startup on Linux boot (PM2 + systemd service)
- Health check endpoint (/health)
- Comprehensive logging and monitoring
- Easy verification and troubleshooting tools
- Production-ready configuration

Ready for deployment on Linux servers."

echo ""
echo "Suggested commit message:"
echo "----------------------------------------"
echo "$default_message"
echo "----------------------------------------"
echo ""

read -p "Use this commit message? (y/n) [y]: " use_default
use_default=${use_default:-y}

if [[ $use_default =~ ^[Yy]$ ]]; then
    commit_message="$default_message"
else
    echo "Enter your custom commit message (end with empty line):"
    commit_message=""
    while IFS= read -r line; do
        if [ -z "$line" ]; then
            break
        fi
        if [ -z "$commit_message" ]; then
            commit_message="$line"
        else
            commit_message="$commit_message"$'\n'"$line"
        fi
    done
fi

# Add all files to staging
print_status "Adding files to staging area..."
git add .

# Show what will be committed
echo ""
print_status "Files to be committed:"
git diff --cached --name-status

# Commit
echo ""
print_status "Creating commit..."
git commit -m "$commit_message"

# Check if we need to set upstream
current_branch=$(git branch --show-current)
if ! git rev-parse --verify origin/$current_branch > /dev/null 2>&1; then
    print_status "Setting upstream branch: origin/$current_branch"
    upstream_flag="-u origin $current_branch"
else
    upstream_flag=""
fi

# Push to GitHub
echo ""
print_status "Pushing to GitHub..."
if git push $upstream_flag; then
    print_status "✅ Successfully pushed to GitHub!"
    
    # Show repository info
    repo_url=$(git remote get-url origin)
    if [[ $repo_url == git@* ]]; then
        # SSH URL format: git@github.com:username/repo.git
        web_url="https://github.com/$(echo $repo_url | sed 's/git@github.com://' | sed 's/.git$//')"
    else
        # HTTPS URL format: https://github.com/username/repo.git
        web_url="${repo_url%.git}"
    fi
    
    echo ""
    print_header "Repository Information"
    print_status "🌐 Repository URL: $web_url"
    print_status "🌿 Branch: $current_branch"
    print_status "📋 Latest commit: $(git log -1 --oneline)"
    
    echo ""
    print_status "🎉 Your WhatsApp Service Auto-Startup project is now on GitHub!"
    echo ""
    echo "Next steps:"
    echo "1. Visit your repository: $web_url"
    echo "2. Add a description and topics to your repository"
    echo "3. Consider adding a LICENSE file"
    echo "4. Deploy to your Linux servers using the setup scripts"
    echo ""
    
else
    print_error "Failed to push to GitHub. Please check your credentials and try again."
    print_status "You can try pushing manually with: git push $upstream_flag"
    exit 1
fi

echo ""
print_header "Upload Complete"
print_status "✅ Project successfully uploaded to GitHub!"
print_status "🔗 Access your repository at: $web_url"
