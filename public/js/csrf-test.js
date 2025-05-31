// CSRF Token Test and Refresh Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Get CSRF token from meta tag
    const token = document.querySelector('meta[name="csrf-token"]');
    
    if (token) {
        // Set up axios defaults if axios is available
        if (typeof axios !== 'undefined') {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
        }
        
        // Set up jQuery AJAX defaults if jQuery is available
        if (typeof $ !== 'undefined') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': token.content
                }
            });
        }
        
        console.log('CSRF token initialized:', token.content.substring(0, 10) + '...');
    } else {
        console.error('CSRF token meta tag not found!');
    }
});

// Function to refresh CSRF token (useful for long-running sessions)
function refreshCSRFToken() {
    fetch('/csrf-token', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.csrf_token) {
            // Update meta tag
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            if (metaTag) {
                metaTag.content = data.csrf_token;
            }
            
            // Update all CSRF input fields
            const csrfInputs = document.querySelectorAll('input[name="_token"]');
            csrfInputs.forEach(input => {
                input.value = data.csrf_token;
            });
            
            // Update axios defaults if available
            if (typeof axios !== 'undefined') {
                axios.defaults.headers.common['X-CSRF-TOKEN'] = data.csrf_token;
            }
            
            console.log('CSRF token refreshed successfully');
        }
    })
    .catch(error => {
        console.error('Failed to refresh CSRF token:', error);
    });
}

// Auto-refresh CSRF token every 2 hours
setInterval(refreshCSRFToken, 2 * 60 * 60 * 1000);
