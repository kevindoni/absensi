@extends('layouts.admin')

@section('title', 'Validasi QR Code - Enhanced')

@section('styles')
<style>
    .enhanced-scanner-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .scanner-wrapper {
        position: relative;
        border: 3px solid #4e73df;
        border-radius: 15px;
        overflow: hidden;
        background: #f8f9fc;
    }
    
    #qr-reader {
        width: 100%;
        min-height: 400px;
        background: #000;
    }
    
    .scanner-overlay {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 10;
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        display: none;
    }
    
    .scanner-controls {
        padding: 20px;
        background: white;
        border-top: 1px solid #e3e6f0;
    }
    
    .control-group {
        margin-bottom: 15px;
    }
    
    .control-group:last-child {
        margin-bottom: 0;
    }
    
    .control-label {
        font-weight: 600;
        color: #5a5c69;
        margin-bottom: 5px;
        display: block;
    }
    
    .status-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 15px;
        background: #f1f3f4;
        border-bottom: 1px solid #e3e6f0;
        font-size: 0.875rem;
    }
    
    .status-indicator {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .status-badge {
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .status-badge.scanning {
        background: #d4edda;
        color: #155724;
    }
    
    .status-badge.error {
        background: #f8d7da;
        color: #721c24;
    }
    
    .status-badge.success {
        background: #cce5ff;
        color: #004085;
    }
    
    .results-container {
        max-height: 400px;
        overflow-y: auto;
        margin-top: 20px;
    }
    
    .result-card {
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        background: white;
        transition: all 0.2s;
    }
    
    .result-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .result-card.error {
        border-color: #f5c6cb;
        background: #f8d7da;
        color: #721c24;
    }
    
    .student-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .student-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #4e73df;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
    }
    
    .student-details h4 {
        margin: 0 0 5px 0;
        color: #2e59d9;
    }
    
    .student-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin: 5px 0;
    }
    
    .meta-item {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    .scan-history {
        margin-top: 20px;
    }
    
    .history-table {
        font-size: 0.875rem;
    }
    
    .debug-info {
        background: #f8f9fc;
        border: 1px solid #e3e6f0;
        border-radius: 5px;
        padding: 10px;
        font-family: 'Courier New', monospace;
        font-size: 0.75rem;
        max-height: 200px;
        overflow-y: auto;
        margin-top: 10px;
    }
    
    .camera-selector {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .device-info {
        font-size: 0.75rem;
        color: #6c757d;
        font-style: italic;
    }
    
    .torch-control {
        display: none;
    }
    
    .torch-control.available {
        display: inline-block;
    }
    
    @media (max-width: 768px) {
        .enhanced-scanner-container {
            margin: 0 10px;
        }
        
        .control-group {
            margin-bottom: 10px;
        }
        
        .camera-selector {
            flex-direction: column;
            align-items: stretch;
        }
        
        .status-bar {
            flex-direction: column;
            gap: 5px;
            align-items: stretch;
        }
    }
</style>
<!-- QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-qrcode mr-2"></i>QR Code Scanner - Enhanced
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.qrcode.validate.scanner') }}" class="btn btn-outline-primary">
                <i class="fas fa-camera mr-1"></i>Standard Scanner
            </a>
            <a href="{{ route('admin.qrcode.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-video mr-2"></i>Enhanced QR Scanner
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="enhanced-scanner-container">
                        
                        <!-- Status Bar -->
                        <div class="status-bar">
                            <div class="status-indicator">
                                <span class="control-label">Status:</span>
                                <span id="scanner-status" class="status-badge">Siap</span>
                            </div>
                            <div class="status-indicator">
                                <span id="device-info" class="device-info">Memuat info perangkat...</span>
                                <span id="network-status" class="badge badge-secondary">Offline</span>
                            </div>
                        </div>

                        <!-- Scanner Area -->
                        <div class="scanner-wrapper">
                            <div id="qr-reader"></div>
                            <div id="scanner-overlay" class="scanner-overlay">
                                <div id="overlay-content">
                                    <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
                                    <div>Memulai scanner...</div>
                                </div>
                            </div>
                        </div>

                        <!-- Scanner Controls -->
                        <div class="scanner-controls">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="control-group">
                                        <label class="control-label">Kontrol Scanner</label>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button id="start-scanner" class="btn btn-success">
                                                <i class="fas fa-play"></i> Mulai
                                            </button>
                                            <button id="stop-scanner" class="btn btn-danger" disabled>
                                                <i class="fas fa-stop"></i> Berhenti
                                            </button>
                                            <button id="switch-camera" class="btn btn-info" disabled>
                                                <i class="fas fa-sync"></i> Ganti Kamera
                                            </button>
                                            <button id="torch-toggle" class="btn btn-warning torch-control">
                                                <i class="fas fa-lightbulb"></i> Senter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="control-group">
                                        <label class="control-label">Kamera yang Tersedia</label>
                                        <select id="camera-selector" class="form-control form-control-sm" disabled>
                                            <option>Mencari kamera...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Manual Input -->                            
                            <div class="control-group">
                                <label class="control-label">Input Manual QR Code / NISN</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" id="manual-qr-input" class="form-control" placeholder="Masukkan QR Code atau NISN siswa...">
                                    <div class="input-group-append">
                                        <button id="manual-validate" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Validasi
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Anda dapat memasukkan kode QR atau NISN siswa untuk validasi manual
                                </small>
                            </div>

                            <!-- Debug Control -->
                            <div class="control-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="debug-mode">
                                    <label class="form-check-label" for="debug-mode">
                                        Mode Debug (Tampilkan log detail)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Section -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-list mr-2"></i>Hasil Validasi
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="results-container">
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-qrcode fa-3x mb-3"></i>
                                    <p>Belum ada QR code yang dipindai.<br>Mulai scanner dan arahkan kamera ke QR code.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <!-- Scan History -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-info">
                                <i class="fas fa-history mr-2"></i>Riwayat Scan
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm history-table">
                                    <thead>
                                        <tr>
                                            <th>Waktu</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="scan-history">
                                        <tr>
                                            <td colspan="2" class="text-center text-muted">
                                                <em>Belum ada riwayat</em>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Debug Info -->
                    <div id="debug-section" class="card shadow mb-4" style="display: none;">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-warning">
                                <i class="fas fa-bug mr-2"></i>Debug Log
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="debug-log" class="debug-info"></div>
                            <button id="clear-debug" class="btn btn-sm btn-outline-warning mt-2">
                                <i class="fas fa-trash"></i> Clear Log
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
class EnhancedQRScanner {
    constructor() {
        this.scanner = null;
        this.cameras = [];
        this.currentCameraIndex = 0;
        this.isScanning = false;
        this.debugMode = false;
        this.torchSupported = false;
        this.torchOn = false;
        this.scanHistory = [];
        
        this.initializeElements();
        this.bindEvents();
        this.detectDeviceInfo();
        this.checkNetworkStatus();
    }

    // Function to play beep sound
    playBeepSound() {
        try {
            const audio = new Audio('/sounds/beep.mp3');
            audio.volume = 0.5; // Set volume to 50%
            audio.play().catch(err => {
                console.log('Could not play beep sound:', err);
            });
        } catch (error) {
            console.log('Error creating audio:', error);
        }
    }

    initializeElements() {
        this.elements = {
            qrReader: document.getElementById('qr-reader'),
            scannerOverlay: document.getElementById('scanner-overlay'),
            overlayContent: document.getElementById('overlay-content'),
            statusBadge: document.getElementById('scanner-status'),
            deviceInfo: document.getElementById('device-info'),
            networkStatus: document.getElementById('network-status'),
            startBtn: document.getElementById('start-scanner'),
            stopBtn: document.getElementById('stop-scanner'),
            switchBtn: document.getElementById('switch-camera'),
            torchBtn: document.getElementById('torch-toggle'),
            cameraSelector: document.getElementById('camera-selector'),
            manualInput: document.getElementById('manual-qr-input'),
            manualValidateBtn: document.getElementById('manual-validate'),
            debugCheckbox: document.getElementById('debug-mode'),
            debugSection: document.getElementById('debug-section'),
            debugLog: document.getElementById('debug-log'),
            clearDebugBtn: document.getElementById('clear-debug'),
            resultsContainer: document.getElementById('results-container'),
            scanHistory: document.getElementById('scan-history')
        };
    }

    bindEvents() {
        this.elements.startBtn.addEventListener('click', () => this.startScanning());
        this.elements.stopBtn.addEventListener('click', () => this.stopScanning());
        this.elements.switchBtn.addEventListener('click', () => this.switchCamera());
        this.elements.torchBtn.addEventListener('click', () => this.toggleTorch());
        this.elements.cameraSelector.addEventListener('change', (e) => this.selectCamera(e.target.value));
        this.elements.manualValidateBtn.addEventListener('click', () => this.validateManualInput());
        this.elements.manualInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.validateManualInput();
        });
        this.elements.debugCheckbox.addEventListener('change', (e) => this.toggleDebugMode(e.target.checked));
        this.elements.clearDebugBtn.addEventListener('click', () => this.clearDebugLog());

        // Network status monitoring
        window.addEventListener('online', () => this.updateNetworkStatus(true));
        window.addEventListener('offline', () => this.updateNetworkStatus(false));
    }

    async detectDeviceInfo() {
        const userAgent = navigator.userAgent.toLowerCase();
        const isMobile = /android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/.test(userAgent);
        const isIOS = /iphone|ipad|ipod/.test(userAgent);
        
        this.deviceInfo = {
            isMobile,
            isIOS,
            userAgent: navigator.userAgent,
            platform: navigator.platform
        };

        this.elements.deviceInfo.textContent = `${isMobile ? 'Mobile' : 'Desktop'} - ${navigator.platform}`;
        this.debugLog('Device Info', this.deviceInfo);

        // Load cameras
        await this.loadCameras();
    }

    async loadCameras() {
        try {
            this.debugLog('Loading available cameras...');
            this.cameras = await Html5Qrcode.getCameras();
            
            if (this.cameras.length === 0) {
                throw new Error('No cameras found');
            }

            this.populateCameraSelector();
            this.updateStatus('Kamera tersedia: ' + this.cameras.length, 'success');
            this.debugLog('Cameras loaded', { count: this.cameras.length, cameras: this.cameras });

        } catch (error) {
            this.debugLog('Failed to load cameras', error.message);
            this.updateStatus('Gagal memuat kamera: ' + error.message, 'error');
            this.elements.deviceInfo.textContent += ' (No camera access)';
        }
    }

    populateCameraSelector() {
        this.elements.cameraSelector.innerHTML = '';
        this.cameras.forEach((camera, index) => {
            const option = document.createElement('option');
            option.value = index;
            option.textContent = camera.label || `Camera ${index + 1}`;
            this.elements.cameraSelector.appendChild(option);
        });
        this.elements.cameraSelector.disabled = false;
    }

    async startScanning() {
        if (this.cameras.length === 0) {
            alert('Tidak ada kamera yang tersedia');
            return;
        }

        try {
            this.showOverlay('Memulai scanner...');
            this.updateStatus('Memulai scanner...', 'scanning');

            if (this.scanner) {
                await this.scanner.stop();
            }

            this.scanner = new Html5Qrcode("qr-reader");
            
            const cameraId = this.cameras[this.currentCameraIndex].id;
            const cameraLabel = this.cameras[this.currentCameraIndex].label || 'Unknown Camera';
            
            this.debugLog('Starting scanner with camera', { 
                cameraId, 
                label: cameraLabel,
                index: this.currentCameraIndex 
            });

            // Scanner configuration
            const config = {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0,
                disableFlip: false,
                videoConstraints: {
                    facingMode: this.currentCameraIndex === 0 ? "environment" : "user"
                }
            };

            await this.scanner.start(
                cameraId,
                config,
                (decodedText, decodedResult) => {
                    this.handleScanSuccess(decodedText, decodedResult);
                },
                (errorMessage) => {
                    // Silent error handling - scanner is working fine
                    // Only log critical errors
                    if (errorMessage.includes('permission') || errorMessage.includes('NotAllowed')) {
                        this.debugLog('Scanner permission error', errorMessage);
                    }
                }
            );

            this.isScanning = true;
            this.updateStatus(`Scanner aktif (${cameraLabel}) - Arahkan QR Code ke kamera`, 'scanning');
            this.updateButtons();
            this.hideOverlay();
            this.debugLog('Scanner started successfully');

            // Check for torch support
            this.checkTorchSupport();

        } catch (error) {
            this.debugLog('Failed to start scanner', error.message);
            this.hideOverlay();
            
            let errorMessage = error.message;
            if (error.name === 'NotAllowedError') {
                errorMessage = 'Izin kamera ditolak. Refresh halaman dan berikan izin kamera.';
            }
            
            this.updateStatus('Gagal memulai scanner: ' + errorMessage, 'error');
            alert('Gagal memulai scanner: ' + errorMessage);
        }
    }

    async stopScanning() {
        try {
            if (this.scanner && this.isScanning) {
                await this.scanner.stop();
                this.scanner = null;
            }
            
            this.isScanning = false;
            
            this.updateStatus('Scanner dihentikan', 'error');
            this.updateButtons();
            this.debugLog('Scanner stopped successfully');

        } catch (error) {
            this.debugLog('Error stopping scanner', error.message);
        }
    }

    async switchCamera() {
        if (this.cameras.length <= 1) {
            this.debugLog('No other cameras available');
            return;
        }

        await this.stopScanning();
        
        this.currentCameraIndex = (this.currentCameraIndex + 1) % this.cameras.length;
        this.elements.cameraSelector.value = this.currentCameraIndex;
        this.debugLog('Switching to camera', { index: this.currentCameraIndex });
        
        setTimeout(() => {
            this.startScanning();
        }, 500);
    }

    selectCamera(index) {
        const newIndex = parseInt(index);
        if (newIndex !== this.currentCameraIndex) {
            this.currentCameraIndex = newIndex;
            this.debugLog('Camera selected from dropdown', { index: newIndex });
            
            if (this.isScanning) {
                this.stopScanning().then(() => {
                    setTimeout(() => this.startScanning(), 500);
                });
            }
        }
    }

    checkTorchSupport() {
        // This is a basic check - torch support detection is limited in web browsers
        if (this.deviceInfo.isMobile && !this.deviceInfo.isIOS) {
            this.torchSupported = true;
            this.elements.torchBtn.classList.add('available');
            this.debugLog('Torch support detected (Android mobile)');
        } else {
            this.torchSupported = false;
            this.elements.torchBtn.classList.remove('available');
            this.debugLog('Torch not supported on this device');
        }
    }

    async toggleTorch() {
        if (!this.torchSupported || !this.scanner) return;

        try {
            // Note: Torch control via Html5Qrcode is limited
            // This is a placeholder for torch functionality
            this.torchOn = !this.torchOn;
            this.elements.torchBtn.classList.toggle('btn-warning', !this.torchOn);
            this.elements.torchBtn.classList.toggle('btn-light', this.torchOn);
            
            this.debugLog('Torch toggled', { torchOn: this.torchOn });
            
        } catch (error) {
            this.debugLog('Failed to toggle torch', error.message);
        }
    }    handleScanSuccess(decodedText, decodedResult) {
        this.debugLog('QR Code scanned successfully', { text: decodedText.substring(0, 50) + '...' });
        
        // Play beep sound when QR code is successfully scanned
        this.playBeepSound();
        
        // Stop scanner temporarily to prevent multiple scans
        this.stopScanning();
        
        // Add to scan history
        this.addToScanHistory(decodedText, 'SCANNED');
        
        // Show success status
        this.updateStatus('QR Code berhasil dipindai! Memvalidasi...', 'success');
        
        // Validate the QR code
        this.validateQRCode(decodedText);
    }

    async validateQRCode(qrText) {
        try {
            this.debugLog('Validating QR code', { length: qrText.length });
            
            const response = await fetch(`/admin/qrcode/validate/${encodeURIComponent(qrText)}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();
            this.debugLog('Validation response received', { valid: data.valid });

            this.displayValidationResult(data);
            this.addToHistory(qrText, data);

            // Auto-restart scanner after 3 seconds
            setTimeout(() => {
                this.startScanning();
            }, 3000);

        } catch (error) {
            this.debugLog('Validation request failed', error.message);
            this.updateStatus('Gagal memvalidasi QR Code', 'error');
            
            setTimeout(() => {
                this.startScanning();
            }, 2000);
        }
    }

    displayValidationResult(data) {
        const container = this.elements.resultsContainer;
        
        // Clear placeholder
        if (container.querySelector('.text-center')) {
            container.innerHTML = '';
        }
        
        const resultCard = document.createElement('div');
        resultCard.className = `result-card ${data.valid ? '' : 'error'}`;
        
        if (data.valid && data.siswa) {
            resultCard.innerHTML = `
                <div class="student-info">
                    <div class="student-avatar">
                        ${data.siswa.nama.charAt(0).toUpperCase()}
                    </div>
                    <div class="student-details">
                        <h4>${data.siswa.nama}</h4>
                        <div class="student-meta">
                            <span class="meta-item"><i class="fas fa-id-card mr-1"></i>${data.siswa.nisn}</span>
                            <span class="meta-item"><i class="fas fa-users mr-1"></i>${data.siswa.kelas}</span>
                            ${data.status ? `<span class="badge badge-${data.status === 'hadir' ? 'success' : 'warning'}">${data.status.toUpperCase()}</span>` : ''}
                        </div>
                        ${data.message ? `<p class="mt-2 mb-0"><small>${data.message}</small></p>` : ''}
                        ${data.jadwal ? `<p class="mt-1 mb-0"><small><i class="fas fa-clock mr-1"></i>${data.jadwal.pelajaran?.nama_pelajaran} (${data.jadwal.jam_mulai} - ${data.jadwal.jam_selesai})</small></p>` : ''}
                    </div>
                </div>
            `;
        } else {
            resultCard.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-times-circle fa-2x text-danger mr-3"></i>
                    <div>
                        <h5 class="mb-1">QR Code Tidak Valid</h5>
                        <p class="mb-0">${data.message || 'Kode QR tidak dapat divalidasi.'}</p>
                    </div>
                </div>
            `;
        }
        
        container.insertBefore(resultCard, container.firstChild);
        
        // Keep only last 5 results
        const results = container.querySelectorAll('.result-card');
        if (results.length > 5) {
            for (let i = 5; i < results.length; i++) {
                results[i].remove();
            }
        }
    }

    addToScanHistory(qrText, status) {
        const time = new Date().toLocaleTimeString();
        const historyBody = this.elements.scanHistory;
        
        // Remove placeholder
        if (historyBody.querySelector('em')) {
            historyBody.innerHTML = '';
        }
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${time}</td>
            <td><span class="badge badge-${status === 'SCANNED' ? 'primary' : (status === 'VALID' ? 'success' : 'danger')}">${status}</span></td>
        `;
        
        historyBody.insertBefore(row, historyBody.firstChild);
        
        // Keep only last 10 entries
        const rows = historyBody.querySelectorAll('tr');
        if (rows.length > 10) {
            for (let i = 10; i < rows.length; i++) {
                rows[i].remove();
            }
        }
    }

    addToHistory(qrText, data) {
        this.addToScanHistory(qrText, data.valid ? 'VALID' : 'INVALID');
    }

    validateManualInput() {
        const qrText = this.elements.manualInput.value.trim();
        if (!qrText) {
            alert('Silakan masukkan kode QR terlebih dahulu');
            return;
        }

        this.debugLog('Manual QR validation', { text: qrText.substring(0, 20) + '...' });
        this.validateQRCode(qrText);
        this.elements.manualInput.value = '';
    }

    updateButtons() {
        this.elements.startBtn.disabled = this.isScanning;
        this.elements.stopBtn.disabled = !this.isScanning;
        this.elements.switchBtn.disabled = !this.isScanning || this.cameras.length <= 1;
        this.elements.cameraSelector.disabled = this.isScanning;
    }

    updateStatus(message, type) {
        this.elements.statusBadge.textContent = message;
        this.elements.statusBadge.className = `status-badge ${type}`;
    }

    showOverlay(message) {
        this.elements.overlayContent.innerHTML = `
            <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
            <div>${message}</div>
        `;
        this.elements.scannerOverlay.style.display = 'block';
    }

    hideOverlay() {
        this.elements.scannerOverlay.style.display = 'none';
    }

    checkNetworkStatus() {
        this.updateNetworkStatus(navigator.onLine);
    }

    updateNetworkStatus(isOnline) {
        this.elements.networkStatus.textContent = isOnline ? 'Online' : 'Offline';
        this.elements.networkStatus.className = `badge badge-${isOnline ? 'success' : 'danger'}`;
        this.debugLog('Network status', { online: isOnline });
    }

    toggleDebugMode(enabled) {
        this.debugMode = enabled;
        this.elements.debugSection.style.display = enabled ? 'block' : 'none';
        this.debugLog('Debug mode toggled', { enabled });
    }

    debugLog(message, data = null) {
        const timestamp = new Date().toLocaleTimeString();
        const logEntry = `[${timestamp}] ${message}`;
        
        console.log(logEntry, data || '');
        
        if (this.debugMode) {
            const logDiv = this.elements.debugLog;
            const entry = document.createElement('div');
            entry.textContent = logEntry + (data ? ': ' + JSON.stringify(data) : '');
            logDiv.appendChild(entry);
            logDiv.scrollTop = logDiv.scrollHeight;
        }
    }

    clearDebugLog() {
        this.elements.debugLog.innerHTML = '';
    }
}

// Initialize the enhanced scanner when the page loads
$(document).ready(function() {
    window.qrScanner = new EnhancedQRScanner();
});
</script>
@endsection
