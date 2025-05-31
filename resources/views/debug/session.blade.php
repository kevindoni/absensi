<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Debug Session - AbsensiPro</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .debug-container {
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
        .debug-section {
            margin-bottom: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fafafa;
        }
        .debug-section h3 {
            color: #2c3e50;
            margin-top: 0;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 15px;
        }
        .info-item {
            background: white;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #e9ecef;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
        }
        .info-value {
            color: #6c757d;
            font-family: 'Courier New', monospace;
            word-break: break-all;
        }
        .status {
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
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
        .status.warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        pre {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            border: 1px solid #dee2e6;
            white-space: pre-wrap;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .test-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-top: 20px;
        }
        .test-links a {
            display: block;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            text-decoration: none;
            color: #495057;
            text-align: center;
            transition: background-color 0.3s;
        }
        .test-links a:hover {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="debug-container">
        <h1>🔍 Debug Session - AbsensiPro</h1>
        
        <div class="debug-section">
            <h3>📋 Informasi Session</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Session ID:</div>
                    <div class="info-value">{{ session()->getId() }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Session Driver:</div>
                    <div class="info-value">{{ config('session.driver') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Session Lifetime:</div>
                    <div class="info-value">{{ config('session.lifetime') }} menit</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Session Domain:</div>
                    <div class="info-value">{{ config('session.domain') ?: 'Default' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Session Path:</div>
                    <div class="info-value">{{ config('session.path') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Secure Cookie:</div>
                    <div class="info-value">{{ config('session.secure') ? 'Ya' : 'Tidak' }}</div>
                </div>
            </div>
        </div>

        <div class="debug-section">
            <h3>🔐 CSRF Token</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">CSRF Token:</div>
                    <div class="info-value">{{ csrf_token() }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Token Length:</div>
                    <div class="info-value">{{ strlen(csrf_token()) }} karakter</div>
                </div>
            </div>
        </div>

        <div class="debug-section">
            <h3>⚙️ Konfigurasi Environment</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">APP_ENV:</div>
                    <div class="info-value">{{ config('app.env') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">APP_DEBUG:</div>
                    <div class="info-value">{{ config('app.debug') ? 'Aktif' : 'Nonaktif' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">APP_URL:</div>
                    <div class="info-value">{{ config('app.url') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Cache Driver:</div>
                    <div class="info-value">{{ config('cache.default') }}</div>
                </div>
            </div>
        </div>

        <div class="debug-section">
            <h3>🗄️ Database Session Table</h3>
            @php
                try {
                    $sessionCount = DB::table('sessions')->count();
                    $recentSessions = DB::table('sessions')
                        ->orderBy('last_activity', 'desc')
                        ->take(5)
                        ->get();
                    $sessionTableExists = true;
                } catch (\Exception $e) {
                    $sessionCount = 0;
                    $recentSessions = collect();
                    $sessionTableExists = false;
                    $sessionError = $e->getMessage();
                }
            @endphp
            
            @if($sessionTableExists)
                <div class="status success">
                    ✅ Tabel sessions tersedia dengan {{ $sessionCount }} records
                </div>
                
                @if($recentSessions->count() > 0)
                    <h4>5 Session Terakhir:</h4>
                    <pre>
@foreach($recentSessions as $session)
ID: {{ $session->id }}
User ID: {{ $session->user_id ?: 'Guest' }}
IP: {{ $session->ip_address }}
Last Activity: {{ date('Y-m-d H:i:s', $session->last_activity) }}
---
@endforeach
                    </pre>
                @endif
            @else
                <div class="status error">
                    ❌ Error accessing sessions table: {{ $sessionError ?? 'Unknown error' }}
                </div>
            @endif
        </div>

        <div class="debug-section">
            <h3>🔍 Current Session Data</h3>
            <pre>{{ json_encode(session()->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
        </div>

        <div class="debug-section">
            <h3>🔐 Auth Guards Status</h3>
            <div class="info-grid">
                @foreach(['admin', 'guru', 'siswa', 'orangtua'] as $guard)
                <div class="info-item">
                    <div class="info-label">{{ ucfirst($guard) }}:</div>
                    <div class="info-value">
                        @if(Auth::guard($guard)->check())
                            ✅ Logged in as: {{ Auth::guard($guard)->user()->nama ?? Auth::guard($guard)->user()->name ?? 'Unknown' }}
                        @else
                            ❌ Not logged in
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="debug-section">
            <h3>🧪 Test Session</h3>
            <div id="session-test-results">
                <div class="status warning">
                    Klik tombol di bawah untuk test session functionality
                </div>
            </div>
            <button onclick="testSession()">Test Session</button>
            <button onclick="clearSession()">Clear Session</button>
            <button onclick="refreshPage()">Refresh Page</button>
        </div>

        <div class="debug-section">
            <h3>🔗 Test Login Pages</h3>
            <div class="test-links">
                <a href="/auth/admin/login" target="_blank">Admin Login</a>
                <a href="/auth/guru/login" target="_blank">Guru Login</a>
                <a href="/auth/siswa/login" target="_blank">Siswa Login</a>
                <a href="/auth/orangtua/login" target="_blank">Orangtua Login</a>
                <a href="/" target="_blank">Welcome Page</a>
            </div>
        </div>

        <div class="debug-section">
            <h3>📝 Rekomendasi Troubleshooting</h3>
            <div class="status warning">
                <strong>Jika session terus habis, coba langkah berikut:</strong>
                <ol>
                    <li>Pastikan tabel sessions tidak penuh atau corrupt</li>
                    <li>Cek konfigurasi domain dan path di config/session.php</li>
                    <li>Pastikan cookie berfungsi di browser</li>
                    <li>Periksa apakah ada konflik dengan middleware</li>
                    <li>Clear cache: <code>php artisan config:clear && php artisan cache:clear</code></li>
                    <li>Regenerate app key jika perlu: <code>php artisan key:generate</code></li>
                </ol>
            </div>
        </div>
    </div>

    <script>
        function testSession() {
            const resultDiv = document.getElementById('session-test-results');
            resultDiv.innerHTML = '<div class="status warning">Testing session...</div>';
            
            // Test CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            
            fetch('/csrf-token', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.csrf_token) {
                    resultDiv.innerHTML = `
                        <div class="status success">
                            ✅ Session test berhasil!<br>
                            <strong>CSRF Token:</strong> ${data.csrf_token.substring(0, 20)}...<br>
                            <strong>Time:</strong> ${new Date().toLocaleString()}
                        </div>
                    `;
                } else {
                    throw new Error('No CSRF token in response');
                }
            })
            .catch(error => {
                resultDiv.innerHTML = `
                    <div class="status error">
                        ❌ Session test gagal: ${error.message}
                    </div>
                `;
            });
        }
        
        function clearSession() {
            if (confirm('Clear session data? Ini akan logout semua user.')) {
                fetch('/logout', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(() => {
                    alert('Session cleared. Halaman akan di-refresh.');
                    location.reload();
                })
                .catch(error => {
                    alert('Error clearing session: ' + error.message);
                });
            }
        }
        
        function refreshPage() {
            location.reload();
        }

        // Auto test session on page load
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(testSession, 1000);
        });
    </script>
</body>
</html>
