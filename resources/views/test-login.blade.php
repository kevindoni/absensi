<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Login - AbsensiPro</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .test-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .test-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fafafa;
        }
        .test-section h3 {
            color: #2c3e50;
            margin-top: 0;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #495057;
        }
        input[type="text"], input[type="password"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin: 5px;
            width: 100%;
        }
        button:hover {
            background-color: #0056b3;
        }
        .status {
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .status.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .status.info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }
        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin-right: 10px;
        }
        pre {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            border: 1px solid #dee2e6;
            white-space: pre-wrap;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 15px;
        }
        .info-item {
            background: white;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <h1>🔐 Test Login - AbsensiPro</h1>
        
        <div class="test-section">
            <h3>📊 Session Status</h3>
            <div class="info-grid">
                <div class="info-item">
                    <strong>Session ID:</strong><br>
                    <small>{{ session()->getId() }}</small>
                </div>
                <div class="info-item">
                    <strong>Session Lifetime:</strong><br>
                    {{ config('session.lifetime') }} menit
                </div>
                <div class="info-item">
                    <strong>Authentication Status:</strong><br>
                    @php
                        $guards = ['admin', 'guru', 'siswa', 'orangtua'];
                        $authenticated = [];
                        foreach($guards as $guard) {
                            if(Auth::guard($guard)->check()) {
                                $user = Auth::guard($guard)->user();
                                $authenticated[] = ucfirst($guard) . ': ' . ($user->nama ?? $user->name ?? $user->username ?? 'Unknown');
                            }
                        }
                    @endphp
                    @if(count($authenticated) > 0)
                        <span style="color: green;">✅ Logged in</span><br>
                        <small>{{ implode('<br>', $authenticated) }}</small>
                    @else
                        <span style="color: red;">❌ Not logged in</span>
                    @endif
                </div>
                <div class="info-item">
                    <strong>CSRF Token:</strong><br>
                    <small>{{ substr(csrf_token(), 0, 20) }}...</small>
                </div>
            </div>
        </div>

        <div class="test-section">
            <h3>🚀 Test Login Cepat</h3>
            <form id="quickLoginForm">
                @csrf
                <div class="form-group">
                    <label for="userType">Tipe User:</label>
                    <select id="userType" name="userType" onchange="updateLoginFields()">
                        <option value="admin">Admin</option>
                        <option value="guru">Guru</option>
                        <option value="siswa">Siswa</option>
                        <option value="orangtua">Orang Tua</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="username" id="usernameLabel">Username:</label>
                    <input type="text" id="username" name="username" placeholder="Masukkan username/nisn">
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan password">
                </div>
                
                <div class="checkbox-group">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember Me</label>
                </div>
                
                <button type="submit">Test Login</button>
            </form>
            
            <div id="loginResult" class="status info" style="display: none;">
                Ready to test login...
            </div>
        </div>

        <div class="test-section">
            <h3>🔧 Quick Actions</h3>
            <button onclick="testSessionPersistence()">Test Session Persistence</button>
            <button onclick="refreshCSRFToken()">Refresh CSRF Token</button>
            <button onclick="checkSessionActivity()">Check Session Activity</button>
            <button onclick="clearAllSessions()">Clear All Sessions</button>
            
            <div id="actionResult" class="status info" style="display: none;">
                Ready to perform actions...
            </div>
        </div>

        <div class="test-section">
            <h3>🗄️ Session Database Info</h3>
            @php
                try {
                    $sessionCount = DB::table('sessions')->count();
                    $activeSessions = DB::table('sessions')
                        ->where('last_activity', '>', time() - (config('session.lifetime') * 60))
                        ->count();
                    $currentUserSessions = DB::table('sessions')
                        ->where('id', session()->getId())
                        ->first();
                } catch (\Exception $e) {
                    $sessionCount = 'Error: ' . $e->getMessage();
                    $activeSessions = 0;
                    $currentUserSessions = null;
                }
            @endphp
            
            <div class="info-grid">
                <div class="info-item">
                    <strong>Total Sessions:</strong><br>
                    {{ $sessionCount }}
                </div>
                <div class="info-item">
                    <strong>Active Sessions:</strong><br>
                    {{ $activeSessions }}
                </div>
                <div class="info-item">
                    <strong>Current Session:</strong><br>
                    @if($currentUserSessions)
                        Found in database ✅
                    @else
                        Not found in database ❌
                    @endif
                </div>
                <div class="info-item">
                    <strong>Session Activity:</strong><br>
                    @if($currentUserSessions)
                        {{ date('Y-m-d H:i:s', $currentUserSessions->last_activity) }}
                    @else
                        Unknown
                    @endif
                </div>
            </div>
        </div>

        <div class="test-section">
            <h3>📝 Troubleshooting Tips</h3>
            <div class="status info">
                <strong>Jika session masih bermasalah:</strong>
                <ol>
                    <li>Pastikan browser menerima cookies</li>
                    <li>Clear browser cache dan cookies</li>
                    <li>Cek apakah ada konflik dengan extension browser</li>
                    <li>Pastikan URL konsisten (http/https)</li>
                    <li>Periksa konfigurasi server time</li>
                </ol>
            </div>
        </div>
    </div>

    <script>
        function updateLoginFields() {
            const userType = document.getElementById('userType').value;
            const usernameLabel = document.getElementById('usernameLabel');
            const usernameInput = document.getElementById('username');
            
            if (userType === 'siswa' || userType === 'orangtua') {
                usernameLabel.textContent = 'NISN:';
                usernameInput.placeholder = 'Masukkan NISN';
            } else {
                usernameLabel.textContent = 'Username:';
                usernameInput.placeholder = 'Masukkan username';
            }
        }

        document.getElementById('quickLoginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const userType = formData.get('userType');
            const username = formData.get('username');
            const password = formData.get('password');
            const remember = formData.get('remember') ? true : false;
            
            const resultDiv = document.getElementById('loginResult');
            resultDiv.style.display = 'block';
            resultDiv.className = 'status info';
            resultDiv.innerHTML = 'Testing login...';
            
            // Determine the login URL based on user type
            let loginUrl = '/auth/' + userType + '/login';
            let credentials = {};
            
            if (userType === 'siswa' || userType === 'orangtua') {
                credentials.nisn = username;
            } else {
                credentials.username = username;
            }
            credentials.password = password;
            if (remember) credentials.remember = 1;
            credentials._token = document.querySelector('meta[name="csrf-token"]').content;
            
            fetch(loginUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': credentials._token
                },
                body: new URLSearchParams(credentials)
            })
            .then(response => {
                if (response.redirected) {
                    resultDiv.className = 'status success';
                    resultDiv.innerHTML = `
                        ✅ Login berhasil!<br>
                        <strong>Redirect to:</strong> ${response.url}<br>
                        <a href="${response.url}" target="_blank">Open Dashboard</a>
                    `;
                    // Refresh page after 2 seconds to show new auth status
                    setTimeout(() => location.reload(), 2000);
                } else {
                    return response.json();
                }
            })
            .then(data => {
                if (data && data.errors) {
                    resultDiv.className = 'status error';
                    resultDiv.innerHTML = '❌ Login gagal: ' + Object.values(data.errors).flat().join(', ');
                }
            })
            .catch(error => {
                resultDiv.className = 'status error';
                resultDiv.innerHTML = '❌ Error: ' + error.message;
            });
        });

        function testSessionPersistence() {
            const resultDiv = document.getElementById('actionResult');
            resultDiv.style.display = 'block';
            resultDiv.className = 'status info';
            resultDiv.innerHTML = 'Testing session persistence...';
            
            // Set a test value in session
            fetch('/csrf-token', {
                method: 'GET',
                headers: {
                    'X-Test-Session': 'persistence-test'
                }
            })
            .then(response => response.json())
            .then(data => {
                resultDiv.className = 'status success';
                resultDiv.innerHTML = `
                    ✅ Session persistence test completed<br>
                    <strong>CSRF Token received:</strong> ${data.csrf_token.substring(0, 20)}...<br>
                    <strong>Time:</strong> ${new Date().toLocaleString()}
                `;
            })
            .catch(error => {
                resultDiv.className = 'status error';
                resultDiv.innerHTML = '❌ Session test failed: ' + error.message;
            });
        }

        function refreshCSRFToken() {
            const resultDiv = document.getElementById('actionResult');
            resultDiv.style.display = 'block';
            resultDiv.className = 'status info';
            resultDiv.innerHTML = 'Refreshing CSRF token...';
            
            fetch('/csrf-token')
            .then(response => response.json())
            .then(data => {
                // Update the meta tag
                document.querySelector('meta[name="csrf-token"]').content = data.csrf_token;
                
                resultDiv.className = 'status success';
                resultDiv.innerHTML = `
                    ✅ CSRF token refreshed successfully<br>
                    <strong>New token:</strong> ${data.csrf_token.substring(0, 20)}...
                `;
            })
            .catch(error => {
                resultDiv.className = 'status error';
                resultDiv.innerHTML = '❌ Failed to refresh token: ' + error.message;
            });
        }

        function checkSessionActivity() {
            const resultDiv = document.getElementById('actionResult');
            resultDiv.style.display = 'block';
            resultDiv.className = 'status info';
            resultDiv.innerHTML = 'Checking session activity...';
            
            // This is a simple check - in a real app you might have a dedicated endpoint
            fetch('/debug-session')
            .then(response => {
                if (response.ok) {
                    resultDiv.className = 'status success';
                    resultDiv.innerHTML = `
                        ✅ Session is active<br>
                        <strong>Time:</strong> ${new Date().toLocaleString()}<br>
                        <a href="/debug-session" target="_blank">View Full Debug Info</a>
                    `;
                } else {
                    throw new Error('Session check failed');
                }
            })
            .catch(error => {
                resultDiv.className = 'status error';
                resultDiv.innerHTML = '❌ Session check failed: ' + error.message;
            });
        }

        function clearAllSessions() {
            if (confirm('This will log out all users. Continue?')) {
                const resultDiv = document.getElementById('actionResult');
                resultDiv.style.display = 'block';
                resultDiv.className = 'status info';
                resultDiv.innerHTML = 'Clearing all sessions...';
                
                fetch('/logout', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(() => {
                    resultDiv.className = 'status success';
                    resultDiv.innerHTML = '✅ All sessions cleared. Page will reload...';
                    setTimeout(() => location.reload(), 2000);
                })
                .catch(error => {
                    resultDiv.className = 'status error';
                    resultDiv.innerHTML = '❌ Failed to clear sessions: ' + error.message;
                });
            }
        }

        // Initialize form fields
        updateLoginFields();
    </script>
</body>
</html>
