<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Page Expired - AbsensiPro">
    <meta name="author" content="AbsensiPro">
    <title>Sesi Berakhir - AbsensiPro</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        
        .error-container {
            background: white;
            border-radius: 1rem;
            padding: 3rem 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        
        .error-icon {
            font-size: 4rem;
            color: #f59e0b;
            margin-bottom: 1.5rem;
        }
        
        .error-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
        }
        
        .error-message {
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .button-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: #3b82f6;
            color: white;
        }
        
        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }
        
        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }
        
        .btn-secondary:hover {
            background: #e5e7eb;
            transform: translateY(-2px);
        }
        
        @media (max-width: 480px) {
            .error-container {
                padding: 2rem 1.5rem;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-clock"></i>
        </div>
        
        <h1 class="error-title">Sesi Berakhir</h1>
        
        <p class="error-message">
            Sesi Anda telah berakhir untuk alasan keamanan. Silakan masuk kembali untuk melanjutkan menggunakan sistem.
        </p>
        
        <div class="button-group">
            <a href="{{ route('welcome') }}" class="btn btn-primary">
                <i class="fas fa-home"></i>
                Kembali ke Beranda
            </a>
            <button onclick="history.back()" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </button>
        </div>
    </div>

    <script>
        // Auto refresh halaman setelah 5 detik jika user tidak melakukan aksi
        setTimeout(() => {
            if (confirm('Ingin kembali ke halaman login?')) {
                window.location.href = '{{ route("welcome") }}';
            }
        }, 10000);
    </script>
</body>
</html>
