@extends('layouts.app')

@section('title', 'QR Scanner - Fazztrack')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <div class="max-w-4xl mx-auto px-3 sm:px-4 lg:px-6 py-4 sm:py-6">
        <!-- Enhanced Header -->
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold bg-gradient-to-r from-gray-900 via-blue-800 to-indigo-900 bg-clip-text text-transparent flex items-center">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg sm:rounded-xl flex items-center justify-center mr-2 sm:mr-3 lg:mr-4 shadow-lg">
                            <i class="fas fa-qrcode text-white text-sm sm:text-base lg:text-xl"></i>
                        </div>
                        <span class="hidden sm:inline">QR Scanner</span>
                        <span class="sm:hidden">Scanner</span>
        </h1>
                    <p class="mt-2 sm:mt-3 text-sm sm:text-base lg:text-lg text-gray-600">Scan QR codes to start and end production jobs.</p>
                </div>
            </div>
    </div>

        <!-- Enhanced Scanner Container -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl sm:rounded-2xl shadow-sm border border-white/20 overflow-hidden">
            <div class="p-4 sm:p-6 lg:p-8">
            <!-- Scanner Status -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                            <div id="scanner-status" class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                            <span id="status-text" class="text-sm sm:text-base font-medium text-gray-700">Scanner Ready</span>
                        </div>
                        <button id="start-scanner" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 text-sm font-medium shadow-lg">
                            <i class="fas fa-play mr-2"></i>
                            <span class="hidden sm:inline">Start Scanner</span>
                            <span class="sm:hidden">Start</span>
                        </button>
                    </div>
                </div>

                <!-- Enhanced Video Container -->
                <div id="camera-container" class="relative mb-6">
                    <video id="qr-video" class="w-full h-48 sm:h-64 lg:h-80 bg-gray-100 rounded-xl sm:rounded-2xl hidden" autoplay playsinline></video>
                    <div id="camera-placeholder" class="w-full h-48 sm:h-64 lg:h-80 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl sm:rounded-2xl flex items-center justify-center border-2 border-dashed border-gray-300">
                        <div class="text-center p-6">
                            <i class="fas fa-camera text-4xl text-gray-400 mb-4"></i>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Camera Ready</h3>
                            <p class="text-sm text-gray-500 mb-4">Click "Start Scanner" to begin scanning QR codes</p>
                        </div>
                    </div>
                    <div id="scanner-overlay" class="absolute inset-0 flex items-center justify-center hidden bg-black/20 rounded-xl sm:rounded-2xl z-10">
                        <div class="bg-white p-6 rounded-xl shadow-lg">
                            <div class="flex items-center space-x-3">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Processing QR Code...</p>
                                    <p class="text-xs text-gray-500">Please wait</p>
                                </div>
                </div>
                    </div>
                </div>
            </div>

                <!-- Enhanced Manual QR Input -->
                <div class="mb-6">
                    <div class="space-y-4">
                        <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                            <div class="flex-1">
                                <label for="manual-qr" class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-keyboard mr-1"></i>
                                    Manual QR Code / Job ID
                                </label>
                        <input type="text" 
                               id="manual-qr" 
                                       placeholder="Enter QR code or job ID (e.g., 25, QR_EVLrykvkjc_PRINT)" 
                                       class="block w-full px-4 py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300"
                               maxlength="50">
                            </div>
                            <div class="flex items-end">
                        <button id="manual-scan" 
                                        class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all duration-300 text-sm font-medium shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fas fa-search mr-2"></i>
                                    <span class="hidden sm:inline">Scan</span>
                                    <span class="sm:hidden">Go</span>
                                </button>
                            </div>
                        </div>
                        <div class="text-xs sm:text-sm text-gray-500 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                            Supported formats: Job ID (25)
                        </div>
                    
                    </div>
                </div>

                <!-- Enhanced Debug Section -->
                <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
                    <h4 class="text-sm sm:text-base font-medium text-blue-800 mb-3 flex items-center">
                        <i class="fas fa-tools mr-2"></i>
                        Quick Actions
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-1 gap-3">
                    
                        <button onclick="testScanner()" 
                                class="inline-flex items-center justify-center px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-all duration-300 text-xs sm:text-sm font-medium">
                            <i class="fas fa-vial mr-2"></i>
                            Test Scanner
                        </button>
                    </div>
                </div>

                <!-- Simple Camera Help -->
                <div class="mb-6 p-4 bg-yellow-50 rounded-xl border border-yellow-200">
                    <div class="text-xs text-yellow-700">
                        <p><strong>Camera not working?</strong> No problem! Use manual input below - it works perfectly!</p>
                        <p class="mt-1">You can type job ID (25, 26) or QR code (QR_EVLrykvkjc_PRINT) directly.</p>
                        <p class="mt-1">All functionality works the same with manual input!</p>
                </div>
            </div>

                <!-- Enhanced Recent Scans -->
                <div class="mb-6">
                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-history mr-2 text-gray-600"></i>
                        Recent Scans
                    </h3>
                <div id="recent-scans" class="space-y-2">
                    <!-- Recent scans will be populated here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Job Details Modal -->
    <div id="job-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-4 w-full max-w-md">
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-tasks mr-2 text-blue-500"></i>
                            Job Details
                        </h3>
                        <button id="close-modal" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                            <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                    
                    <div id="job-details" class="mb-6">
                    <!-- Job details will be populated here -->
                </div>
                    
                    <div id="workflow-status" class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200 hidden">
                        <div id="workflow-info" class="text-sm text-blue-800">
                            <!-- Workflow info will be populated here -->
                        </div>
                    </div>
                    
                    <div id="time-tracking" class="mb-4 p-3 bg-green-50 rounded-lg border border-green-200 hidden">
                        <div id="time-info" class="text-sm text-green-800">
                            <!-- Time tracking info will be populated here -->
                        </div>
                    </div>
                    
                    <div class="flex space-x-3">
                        <button id="start-job" class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all duration-300 text-sm font-medium shadow-lg hidden">
                            <i class="fas fa-play mr-2"></i>
                            Start Job
                        </button>
                        <button id="end-job" class="flex-1 inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-lg hover:from-red-600 hover:to-pink-700 transition-all duration-300 text-sm font-medium shadow-lg hidden">
                            <i class="fas fa-stop mr-2"></i>
                            End Job
                        </button>
                    </div>
                    
                    <!-- Start Job Form (hidden by default) - For CUT and QC phases -->
                    <div id="start-job-form" class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200 hidden">
                        <h4 class="text-sm font-semibold text-gray-800 mb-3">Job Start Details</h4>
                        
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Start Quantity <span class="text-red-500">*</span></label>
                            <input type="number" id="start-quantity" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter start quantity" required>
                            <p class="text-xs text-gray-500 mt-1">Enter the quantity you are starting with for this phase</p>
                        </div>
                        
                        <div class="flex space-x-3">
                            <button id="confirm-start-job" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all duration-300 text-sm font-medium">
                                <i class="fas fa-check mr-2"></i>Confirm Start Job
                            </button>
                            <button id="cancel-start-job" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-300 text-sm font-medium">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </button>
                        </div>
                    </div>
                    
                    <!-- End Job Form (hidden by default) -->
                    <div id="end-job-form" class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200 hidden">
                        <h4 class="text-sm font-semibold text-gray-800 mb-3">Job Completion Details</h4>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">End Quantity</label>
                                <input type="number" id="end-quantity" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter quantity">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Reject Quantity</label>
                                <input type="number" id="reject-quantity" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter reject quantity">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Reject Status</label>
                            <select id="reject-status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select reject status</option>
                                <!-- Options will be populated dynamically based on phase -->
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Required when reject quantity is entered</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Remarks</label>
                            <textarea id="remarks" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter any remarks about the job completion..."></textarea>
                        </div>
                        
                        <div class="flex space-x-3">
                            <button id="confirm-end-job" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-lg hover:from-red-600 hover:to-pink-700 transition-all duration-300 text-sm font-medium">
                                <i class="fas fa-check mr-2"></i>Confirm End Job
                    </button>
                            <button id="cancel-end-job" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-all duration-300 text-sm font-medium">
                                <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
let scanner = null;
let currentJob = null;
let videoStream = null;

// Initialize scanner with proper permission handling
function initScanner() {
    console.log('🔵 [SCANNER] initScanner() called');
    const video = document.getElementById('qr-video');
    const statusIndicator = document.getElementById('scanner-status');
    const statusText = document.getElementById('status-text');
    const cameraContainer = document.getElementById('camera-container');
    
    console.log('🔵 [SCANNER] Video element:', video ? 'Found' : 'Not found');
    console.log('🔵 [SCANNER] Status indicator:', statusIndicator ? 'Found' : 'Not found');
    console.log('🔵 [SCANNER] Camera container:', cameraContainer ? 'Found' : 'Not found');
    
    // Stop any existing stream
    if (videoStream) {
        console.log('🔵 [SCANNER] Stopping existing video stream');
        videoStream.getTracks().forEach(track => track.stop());
        videoStream = null;
    }

    // Reset video element
    if (video) {
        video.srcObject = null;
    }
    
    // Check if getUserMedia is available
    console.log('🔵 [SCANNER] Checking getUserMedia support...');
    console.log('🔵 [SCANNER] navigator.mediaDevices:', navigator.mediaDevices ? 'Available' : 'Not available');
    console.log('🔵 [SCANNER] getUserMedia:', navigator.mediaDevices?.getUserMedia ? 'Available' : 'Not available');
    
    // Check protocol (HTTPS required for camera in most browsers, except localhost)
    const isHTTPS = window.location.protocol === 'https:' || window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
    console.log('🔵 [SCANNER] Protocol:', window.location.protocol);
    console.log('🔵 [SCANNER] Hostname:', window.location.hostname);
    console.log('🔵 [SCANNER] HTTPS/Localhost:', isHTTPS);
    
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        console.error('❌ [SCANNER] Camera API not supported');
        if (statusIndicator) {
            statusIndicator.className = 'w-3 h-3 bg-red-500 rounded-full';
        }
        if (statusText) {
            statusText.textContent = 'Camera Not Supported';
        }
        if (video) {
            video.classList.add('hidden');
        }
        const placeholder = document.getElementById('camera-placeholder');
        if (placeholder) {
            placeholder.classList.remove('hidden');
            let protocolWarning = '';
            if (!isHTTPS) {
                protocolWarning = `
                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-left">
                        <p class="text-sm font-semibold text-yellow-800 mb-2">Note:</p>
                        <p class="text-xs text-yellow-700">Camera access requires HTTPS in most browsers. Please use HTTPS or access via localhost.</p>
                    </div>
                `;
            }
            placeholder.innerHTML = `
                <div class="text-center p-6">
                    <i class="fas fa-camera-slash text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Camera Not Supported</h3>
                    <p class="text-sm text-gray-600 mb-2">Your browser does not support camera access.</p>
                    ${protocolWarning}
                    <button onclick="document.getElementById('manual-qr').focus()" 
                            class="mt-4 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                        <i class="fas fa-keyboard mr-2"></i>Use Manual Input
                    </button>
                </div>
            `;
        }
        return;
    }

    // Check camera permission status first (if supported)
    if (navigator.permissions && navigator.permissions.query) {
        console.log('🔵 [SCANNER] Checking camera permission status...');
        navigator.permissions.query({ name: 'camera' })
            .then(function(result) {
                console.log('🔵 [SCANNER] Camera permission status:', result.state);
                if (result.state === 'denied') {
                    console.error('❌ [SCANNER] Camera permission is denied');
                    showPermissionDeniedError();
                    return;
                }
                // If permission is 'prompt' or 'granted', proceed with request
                requestCameraAccess();
            })
            .catch(function(err) {
                console.warn('⚠️ [SCANNER] Permission query not supported, proceeding with request:', err);
                // If permission query is not supported, proceed with request
                requestCameraAccess();
            });
    } else {
        console.log('🔵 [SCANNER] Permission query API not available, proceeding with request');
        requestCameraAccess();
    }
    
    // Function to request camera access
    function requestCameraAccess() {
        // Request camera access with better error handling
        const constraints = {
            video: {
                facingMode: 'environment', // Use back camera on mobile
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        };

        console.log('🔵 [SCANNER] Requesting camera access with constraints:', constraints);
        navigator.mediaDevices.getUserMedia(constraints)
        .then(function(stream) {
            console.log('✅ [SCANNER] Camera access granted, stream received');
            videoStream = stream;
            console.log('🔵 [SCANNER] Video stream tracks:', stream.getTracks().length);
            
            if (video) {
                video.srcObject = stream;
                
                // Hide placeholder and show video
                const placeholder = document.getElementById('camera-placeholder');
                if (placeholder) {
                    placeholder.classList.add('hidden');
                }
                video.classList.remove('hidden');
                
                // Wait for video to be ready
                video.onloadedmetadata = function() {
                    console.log('✅ [SCANNER] Video metadata loaded');
                    console.log('🔵 [SCANNER] Video dimensions:', video.videoWidth, 'x', video.videoHeight);
                    video.play().then(() => {
                        console.log('✅ [SCANNER] Video playback started');
                        if (statusIndicator) {
                            statusIndicator.className = 'w-3 h-3 bg-green-500 rounded-full animate-pulse';
                        }
                        if (statusText) {
                            statusText.textContent = 'Scanner Active';
                        }
                        
                        // Start scanning
                        console.log('🔵 [SCANNER] Starting QR code scanning...');
                        startScanning();
                    }).catch(err => {
                        console.error('❌ [SCANNER] Error playing video:', err);
                        if (statusIndicator) {
                            statusIndicator.className = 'w-3 h-3 bg-yellow-500 rounded-full';
                        }
                        if (statusText) {
                            statusText.textContent = 'Camera Ready (Click to Start)';
                        }
                    });
                };
            } else {
                console.error('❌ [SCANNER] Video element not found');
            }
        })
        .catch(function(err) {
            console.error('❌ [SCANNER] Error accessing camera:', err);
            console.error('❌ [SCANNER] Error name:', err.name);
            console.error('❌ [SCANNER] Error message:', err.message);
            
            if (statusIndicator) {
                statusIndicator.className = 'w-3 h-3 bg-red-500 rounded-full';
            }
            if (statusText) {
                statusText.textContent = 'Camera Error';
            }
            
            // Check if we're on HTTP (camera requires HTTPS in most browsers)
            const isHTTPS = window.location.protocol === 'https:' || window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
            
            let errorMessage = 'Camera access failed. Please use manual input below.';
            let instructions = '';
            
            if (err.name === 'NotAllowedError') {
                errorMessage = 'Camera permission denied.';
                instructions = `
                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-left">
                        <p class="text-sm font-semibold text-yellow-800 mb-2">How to enable camera access:</p>
                        <ol class="text-xs text-yellow-700 space-y-1 list-decimal list-inside">
                            <li>Look for the camera icon in your browser's address bar</li>
                            <li>Click it and select "Allow" for camera access</li>
                            <li>Or go to your browser settings → Privacy → Site Settings → Camera</li>
                            <li>Make sure this site is allowed to use the camera</li>
                        </ol>
                    </div>
                `;
                console.error('❌ [SCANNER] Permission denied by user');
            } else if (err.name === 'NotFoundError') {
                errorMessage = 'No camera found.';
                instructions = '<p class="text-sm text-gray-600 mt-2">Please connect a camera device or use manual input below.</p>';
                console.error('❌ [SCANNER] No camera device found');
            } else if (err.name === 'NotReadableError') {
                errorMessage = 'Camera is being used by another application.';
                instructions = '<p class="text-sm text-gray-600 mt-2">Please close other applications using the camera and try again.</p>';
                console.error('❌ [SCANNER] Camera is busy');
            } else if (err.message && (err.message.includes('Permissions policy') || err.message.includes('permissions policy'))) {
                errorMessage = 'Camera permissions policy violation.';
                instructions = `
                    <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg text-left">
                        <p class="text-sm font-semibold text-red-800 mb-2">⚠️ Permissions Policy Error:</p>
                        <p class="text-xs text-red-700 mb-2">The browser is blocking camera access due to security policies.</p>
                        <p class="text-sm font-semibold text-red-800 mt-3 mb-2">Solutions:</p>
                        <ul class="text-xs text-red-700 mt-2 space-y-1 list-disc list-inside">
                            <li><strong>Use HTTPS:</strong> Camera access requires HTTPS (except localhost)</li>
                            <li><strong>Check URL:</strong> Make sure you're accessing via <code class="bg-red-100 px-1 rounded">https://</code> or <code class="bg-red-100 px-1 rounded">http://localhost</code></li>
                            <li><strong>Browser Settings:</strong> Check if your browser has strict security policies enabled</li>
                            <li><strong>Try Different Browser:</strong> Some browsers have different security policies</li>
                        </ul>
                        <p class="text-xs text-red-600 mt-3 italic">Current URL: ${window.location.href}</p>
                    </div>
                `;
                console.error('❌ [SCANNER] Permissions policy violation');
                console.error('❌ [SCANNER] Current URL:', window.location.href);
                console.error('❌ [SCANNER] Protocol:', window.location.protocol);
            } else {
                console.error('❌ [SCANNER] Unknown camera error:', err);
                if (!isHTTPS && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
                    instructions = `
                        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg text-left">
                            <p class="text-sm font-semibold text-blue-800 mb-2">Note:</p>
                            <p class="text-xs text-blue-700">Most browsers require HTTPS for camera access. If you're on HTTP, please use HTTPS or access via localhost.</p>
                        </div>
                    `;
                }
            }
            
            // Hide video and show error message
            if (video) {
                video.classList.add('hidden');
            }
            const placeholder = document.getElementById('camera-placeholder');
            if (placeholder) {
                placeholder.classList.remove('hidden');
                placeholder.innerHTML = `
                    <div class="text-center p-6">
                        <i class="fas fa-camera-slash text-4xl text-red-500 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Camera Access Failed</h3>
                        <p class="text-sm text-gray-600 mb-4">${errorMessage}</p>
                        ${instructions}
                        <div class="mt-4 flex flex-col sm:flex-row gap-2 justify-center">
                            <button onclick="initScanner()" 
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                                <i class="fas fa-redo mr-2"></i>Try Again
                            </button>
                            <button onclick="document.getElementById('manual-qr').focus()" 
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                                <i class="fas fa-keyboard mr-2"></i>Use Manual Input
                            </button>
                        </div>
                    </div>
                `;
            }
        });
    }
    
    // Function to show permission denied error with detailed instructions
    function showPermissionDeniedError() {
        if (statusIndicator) {
            statusIndicator.className = 'w-3 h-3 bg-red-500 rounded-full';
        }
        if (statusText) {
            statusText.textContent = 'Camera Permission Denied';
        }
        
        if (video) {
            video.classList.add('hidden');
        }
        const placeholder = document.getElementById('camera-placeholder');
        if (placeholder) {
            placeholder.classList.remove('hidden');
            placeholder.innerHTML = `
                <div class="text-center p-6">
                    <i class="fas fa-camera-slash text-4xl text-red-500 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Camera Permission Denied</h3>
                    <p class="text-sm text-gray-600 mb-4">Please enable camera access in your browser settings.</p>
                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-left">
                        <p class="text-sm font-semibold text-yellow-800 mb-2">How to enable camera access:</p>
                        <div class="text-xs text-yellow-700 space-y-2">
                            <div>
                                <strong>Chrome/Edge:</strong>
                                <ol class="list-decimal list-inside ml-2 mt-1 space-y-1">
                                    <li>Click the lock/camera icon in the address bar</li>
                                    <li>Select "Allow" for Camera</li>
                                    <li>Or go to Settings → Privacy → Site Settings → Camera</li>
                                    <li>Find this site and set to "Allow"</li>
                                </ol>
                            </div>
                            <div class="mt-2">
                                <strong>Firefox:</strong>
                                <ol class="list-decimal list-inside ml-2 mt-1 space-y-1">
                                    <li>Click the lock icon in the address bar</li>
                                    <li>Click "More Information"</li>
                                    <li>Go to Permissions tab</li>
                                    <li>Set Camera to "Allow"</li>
                                </ol>
                            </div>
                            <div class="mt-2">
                                <strong>Safari:</strong>
                                <ol class="list-decimal list-inside ml-2 mt-1 space-y-1">
                                    <li>Safari → Settings → Websites → Camera</li>
                                    <li>Find this site and set to "Allow"</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-col sm:flex-row gap-2 justify-center">
                        <button onclick="initScanner()" 
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                            <i class="fas fa-redo mr-2"></i>Try Again
                        </button>
                        <button onclick="document.getElementById('manual-qr').focus()" 
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors text-sm">
                            <i class="fas fa-keyboard mr-2"></i>Use Manual Input
                        </button>
                    </div>
                </div>
            `;
        }
    }
}

// Start scanning QR codes from video stream
let scanning = false;
function startScanning() {
    console.log('🔵 [SCANNER] startScanning() called');
    if (scanning) {
        console.log('⚠️ [SCANNER] Already scanning, skipping...');
        return;
    }
    
    scanning = true;
    console.log('✅ [SCANNER] Scanning started');
    const video = document.getElementById('qr-video');
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    let lastScanTime = 0;
    const scanInterval = 200; // Scan every 200ms to avoid too frequent scans
    let scanCount = 0;

    function scanQR() {
        if (!scanning || !video || video.readyState !== video.HAVE_ENOUGH_DATA) {
            if (scanning) {
                requestAnimationFrame(scanQR);
            }
            return;
        }

        const now = Date.now();
        if (now - lastScanTime < scanInterval) {
            requestAnimationFrame(scanQR);
            return;
        }
        lastScanTime = now;
        scanCount++;
        
        // Log every 50 scans (every 10 seconds)
        if (scanCount % 50 === 0) {
            console.log('🔵 [SCANNER] Scanning... (scan #' + scanCount + ')');
        }

        try {
            canvas.height = video.videoHeight;
            canvas.width = video.videoWidth;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height);
            
            if (code && code.data) {
                console.log('✅ [SCANNER] QR Code detected:', code.data);
                scanning = false; // Stop scanning temporarily
                handleQRCode(code.data);
                // Resume scanning after 2 seconds
                setTimeout(() => {
                    if (videoStream && videoStream.active) {
                        console.log('🔵 [SCANNER] Resuming scanning...');
                        scanning = true;
                        scanQR();
                    }
                }, 2000);
                return;
            }
        } catch (err) {
            console.error('❌ [SCANNER] Scan error:', err);
        }
        
        requestAnimationFrame(scanQR);
    }

    scanQR();
}

// Stop scanning
function stopScanning() {
    scanning = false;
    if (videoStream) {
        videoStream.getTracks().forEach(track => track.stop());
        videoStream = null;
    }
    const video = document.getElementById('qr-video');
    if (video) {
        video.srcObject = null;
    }
}

// Enhanced QR code handling
function handleQRCode(qrData) {
    console.log('🔵 [QR] handleQRCode() called with data:', qrData);
    showOverlay();
    
    // Enhanced QR code parsing
    let jobId = null;
    try {
        // Try to parse as JSON first (for complex QR codes)
        console.log('🔵 [QR] Attempting to parse as JSON...');
        const qrDataObj = JSON.parse(qrData);
        jobId = qrDataObj.job_id;
        console.log('✅ [QR] Parsed as JSON, job ID:', jobId);
    } catch (e) {
        console.log('🔵 [QR] Not JSON, trying other formats...');
        // Handle different QR code formats
        if (qrData.startsWith('QR_')) {
            // Check for format: QR_EVLrykvkjc_PRINT (QR_randomstring_phase)
            // We need to find the job by QR code since the QR doesn't contain the job ID directly
            jobId = qrData; // Pass the full QR code to backend
            console.log('🔵 [QR] Detected QR_ format, using full QR code:', jobId);
        } else if (qrData.startsWith('JOB_')) {
            // Format: JOB_123
            jobId = qrData.split('_')[1];
            console.log('🔵 [QR] Detected JOB_ format, extracted ID:', jobId);
        } else if (/^\d+$/.test(qrData)) {
            // Direct job ID number
            jobId = qrData;
            console.log('🔵 [QR] Detected direct numeric ID:', jobId);
        } else {
            // Try to extract job ID from any format
            const match = qrData.match(/(\d+)/);
            if (match) {
                jobId = match[1];
                console.log('🔵 [QR] Extracted job ID from mixed format:', jobId);
            } else {
                console.warn('⚠️ [QR] Could not extract job ID from:', qrData);
            }
        }
    }
    
    if (!jobId) {
        console.error('❌ [QR] No job ID found in QR data');
        hideOverlay();
        showError('Invalid QR code format. Please check the code and try again.');
        return;
    }
    
    console.log('🔵 [QR] Processing job ID/QR:', jobId);
    
    // If it's a QR code (starts with QR_), we need to find the job by QR code
    if (jobId.startsWith('QR_')) {
        const qrUrl = `/jobs/qr/${encodeURIComponent(jobId)}/details`;
        console.log('🔵 [QR] Fetching job by QR code from:', qrUrl);
        
        fetch(qrUrl)
            .then(response => {
                console.log('🔵 [QR] QR response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ [QR] QR job details received:', data);
                hideOverlay();
                if (data.success) {
                    console.log('✅ [QR] Job found by QR code:', data.job);
                    showJobModal(data.job);
                    addRecentScan(data.job.job_id, 'Scanned');
                } else {
                    console.error('❌ [QR] Job details error:', data.message);
                    showError(data.message || 'Job not found or access denied');
                }
            })
            .catch(error => {
                console.error('❌ [QR] QR scan error:', error);
                console.error('❌ [QR] Error details:', error.message);
                hideOverlay();
                if (error.message.includes('404')) {
                    showError('Job not found. Please check the QR code.');
                } else if (error.message.includes('403')) {
                    showError('Access denied. This job is not assigned to you or does not match your phase.');
                } else {
                    showError('Error fetching job details. Please try again. Error: ' + error.message);
                }
            });
    } else {
        // Direct job ID - use existing endpoint
        const jobUrl = `/jobs/${jobId}/details`;
        console.log('🔵 [QR] Fetching job by ID from:', jobUrl);
        
        fetch(jobUrl)
            .then(response => {
                console.log('🔵 [QR] Job response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('✅ [QR] Job details received:', data);
                hideOverlay();
                if (data.success) {
                    console.log('✅ [QR] Job found:', data.job);
                    showJobModal(data.job);
                    addRecentScan(jobId, 'Scanned');
                } else {
                    console.error('❌ [QR] Job details error:', data.message);
                    showError(data.message || 'Job not found or access denied');
                }
            })
            .catch(error => {
                console.error('❌ [QR] QR scan error:', error);
                console.error('❌ [QR] Error details:', error.message);
                hideOverlay();
                if (error.message.includes('404')) {
                    showError('Job not found. Please check the job ID.');
                } else if (error.message.includes('403')) {
                    showError('Access denied. This job is not assigned to you or does not match your phase.');
                } else {
                    showError('Error fetching job details. Please try again. Error: ' + error.message);
                }
            });
    }
}

// Test scanner function
function testScanner() {
    console.log('=== QR Scanner Debug ===');
    console.log('Testing scanner with job ID 25...');
    console.log('Current user phase:', '{{ auth()->user()->phase ?? "N/A" }}');
    console.log('Current user role:', '{{ auth()->user()->role ?? "N/A" }}');
    console.log('Current user ID:', '{{ auth()->user()->id ?? "N/A" }}');
    
    // Test the job details endpoint directly
    fetch('/jobs/25/details')
        .then(response => {
            return response.json();
        })
        .then(data => {
            console.log('Job details response:', data);
            if (data.success) {
                console.log('✅ Job found and accessible');
                showJobModal(data.job);
                addRecentScan('25', 'Test Success');
            } else {
                console.log('❌ Job access denied:', data.message);
                showError(data.message || 'Job not found or access denied');
            }
        })
        .catch(error => {
            console.error('❌ Network error:', error);
            showError('Network error: ' + error.message);
        });
}

// Test with specific job ID or QR code
function testWithJobId(jobIdOrQR) {
    console.log('Testing with:', jobIdOrQR);
    
    // Set the manual input value
    document.getElementById('manual-qr').value = jobIdOrQR;
    
    // Trigger the manual scan
    document.getElementById('manual-scan').click();
}

// Show/hide overlay
function showOverlay() {
    document.getElementById('scanner-overlay').classList.remove('hidden');
}

function hideOverlay() {
    document.getElementById('scanner-overlay').classList.add('hidden');
}

// Enhanced job modal
function showJobModal(job) {
    currentJob = job;
    const modal = document.getElementById('job-modal');
    const details = document.getElementById('job-details');
    const startBtn = document.getElementById('start-job');
    const endBtn = document.getElementById('end-job');
    const endJobForm = document.getElementById('end-job-form');
    const confirmEndJobBtn = document.getElementById('confirm-end-job');
    const cancelEndJobBtn = document.getElementById('cancel-end-job');

    details.innerHTML = `
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-xs font-medium text-gray-500">Job ID:</span>
                    <p class="text-sm font-bold text-gray-900">${job.job_id}</p>
                </div>
            <div>
                    <span class="text-xs font-medium text-gray-500">Phase:</span>
                    <p class="text-sm font-bold text-gray-900">${job.phase}</p>
                </div>
            </div>
            <div>
                <span class="text-xs font-medium text-gray-500">Order:</span>
                <p class="text-sm font-bold text-gray-900">${job.order.job_name}</p>
            </div>
            <div>
                <span class="text-xs font-medium text-gray-500">Client:</span>
                <p class="text-sm font-bold text-gray-900">${job.order.client.name}</p>
            </div>
            <div>
                <span class="text-xs font-medium text-gray-500">Status:</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    ${job.status === 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                      job.status === 'In Progress' ? 'bg-blue-100 text-blue-800' :
                      job.status === 'Completed' ? 'bg-green-100 text-green-800' :
                      'bg-red-100 text-red-800'}">
                    ${job.status}
                </span>
            </div>
            <div>
                <span class="text-xs font-medium text-gray-500">Order Status:</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    ${job.order.status === 'Job Created' ? 'bg-gray-100 text-gray-800' :
                      job.order.status === 'Job Start' ? 'bg-blue-100 text-blue-800' :
                      job.order.status === 'Job Complete' ? 'bg-green-100 text-green-800' :
                      job.order.status === 'Order Finished' ? 'bg-purple-100 text-purple-800' :
                      'bg-yellow-100 text-yellow-800'}">
                    ${job.order.status}
                </span>
            </div>
        </div>
    `;

    // Show workflow status
    const workflowStatus = document.getElementById('workflow-status');
    const workflowInfo = document.getElementById('workflow-info');
    workflowStatus.classList.remove('hidden');
    
    // Get workflow information
    fetch(`/jobs/${job.job_id}/workflow`)
        .then(response => response.json())
        .then(data => {
            if (data.previous_job) {
                workflowInfo.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-info-circle text-blue-500"></i>
                        <span>Previous phase: <strong>${data.previous_job.phase}</strong> (${data.previous_job.status})</span>
                    </div>
                `;
            } else {
                workflowInfo.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>Ready to start - no previous phase required</span>
                    </div>
                `;
            }
        })
        .catch(error => {
            workflowInfo.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                    <span>Unable to load workflow information</span>
                </div>
            `;
        });

    // Show time tracking if job is in progress
    const timeTracking = document.getElementById('time-tracking');
    const timeInfo = document.getElementById('time-info');
    
    if (job.status === 'In Progress' && job.start_time) {
        timeTracking.classList.remove('hidden');
        const startTime = new Date(job.start_time);
        const now = new Date();
        const duration = Math.floor((now - startTime) / (1000 * 60)); // minutes
        
        timeInfo.innerHTML = `
            <div class="space-y-1">
                <div>Started: <strong>${startTime.toLocaleString()}</strong></div>
                <div>Duration: <strong>${duration} minutes</strong></div>
            </div>
        `;
    } else if (job.status === 'Completed' && job.start_time && job.end_time) {
        timeTracking.classList.remove('hidden');
        const startTime = new Date(job.start_time);
        const endTime = new Date(job.end_time);
        const duration = Math.floor((endTime - startTime) / (1000 * 60)); // minutes
        
        timeInfo.innerHTML = `
            <div class="space-y-1">
                <div>Started: <strong>${startTime.toLocaleString()}</strong></div>
                <div>Completed: <strong>${endTime.toLocaleString()}</strong></div>
                <div>Total Time: <strong>${duration} minutes</strong></div>
            </div>
        `;
    } else {
        timeTracking.classList.add('hidden');
    }

    // Update button states based on job status
    updateButtonStates(job);
    
    // Update reject status options based on phase
    updateRejectStatusOptions(job.phase);

    modal.classList.remove('hidden');
}

// Function to update reject status options based on phase
function updateRejectStatusOptions(phase) {
    const rejectStatusSelect = document.getElementById('reject-status');
    if (!rejectStatusSelect) return;
    
    // Clear existing options except the first one
    rejectStatusSelect.innerHTML = '<option value="">Select reject status</option>';
    
    // Define reject status options for different phases
    const rejectOptions = {
        'CUT': [
            'Cutting Error',
            'Measurement Issue',
            'Size Mismatch',
            'Material Defect',
            'Pattern Error',
            'Other'
        ],
        'QC': [
            'Quality Issue',
            'Print Error',
            'Color Issue',
            'Size Mismatch',
            'Stitching Issue',
            'Finishing Defect',
            'Other'
        ],
        'PRINT': [
            'Print Error',
            'Color Issue',
            'Alignment Issue',
            'Material Defect',
            'Other'
        ],
        'PRESS': [
            'Pressing Error',
            'Temperature Issue',
            'Material Defect',
            'Other'
        ],
        'SEW': [
            'Stitching Error',
            'Thread Issue',
            'Measurement Issue',
            'Other'
        ],
        'IRON/PACKING': [
            'Packing Error',
            'Label Issue',
            'Quality Issue',
            'Other'
        ]
    };
    
    // Get options for current phase or default options
    const options = rejectOptions[phase] || [
        'Quality Issue',
        'Material Defect',
        'Other'
    ];
    
    // Add options to select
    options.forEach(option => {
        const optionElement = document.createElement('option');
        optionElement.value = option;
        optionElement.textContent = option;
        rejectStatusSelect.appendChild(optionElement);
    });
}

// Function to update button states
function updateButtonStates(job) {
    const startBtn = document.getElementById('start-job');
    const endBtn = document.getElementById('end-job');
    const endJobForm = document.getElementById('end-job-form');
    const confirmEndJobBtn = document.getElementById('confirm-end-job');
    const cancelEndJobBtn = document.getElementById('cancel-end-job');
    
    // Reset button states
    startBtn.className = 'flex-1 inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all duration-300 text-sm font-medium shadow-lg';
    endBtn.className = 'flex-1 inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-lg hover:from-red-600 hover:to-pink-700 transition-all duration-300 text-sm font-medium shadow-lg';
    
    // Hide all forms by default
    const startJobForm = document.getElementById('start-job-form');
    const confirmStartJobBtn = document.getElementById('confirm-start-job');
    const cancelStartJobBtn = document.getElementById('cancel-start-job');
    if (startJobForm) startJobForm.classList.add('hidden');
    if (confirmStartJobBtn) confirmStartJobBtn.classList.add('hidden');
    if (cancelStartJobBtn) cancelStartJobBtn.classList.add('hidden');
    
    endJobForm.classList.add('hidden'); // Hide end job form by default
    confirmEndJobBtn.classList.add('hidden'); // Hide confirm end job button by default
    cancelEndJobBtn.classList.add('hidden'); // Hide cancel end job button by default
    
    if (job.status === 'Pending') {
        // Check if can start
        fetch(`/jobs/${job.job_id}/workflow`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.can_start) {
                    startBtn.classList.remove('hidden');
                    endBtn.classList.add('hidden');
                    startBtn.disabled = false;
                    startBtn.innerHTML = '<i class="fas fa-play mr-2"></i>Start Job';
                } else {
                    startBtn.classList.remove('hidden');
                    endBtn.classList.add('hidden');
                    startBtn.disabled = true;
                    startBtn.innerHTML = 'Cannot Start - Previous Phase Required';
                    startBtn.className = 'flex-1 inline-flex items-center justify-center px-4 py-3 bg-gray-400 text-white rounded-lg cursor-not-allowed text-sm font-medium';
                }
            })
            .catch(error => {
                console.error('Error checking workflow:', error);
                startBtn.classList.remove('hidden');
                endBtn.classList.add('hidden');
                startBtn.disabled = false;
                startBtn.innerHTML = '<i class="fas fa-play mr-2"></i>Start Job';
            });
    } else if (job.status === 'In Progress') {
        startBtn.classList.add('hidden');
        endBtn.classList.remove('hidden');
        endBtn.disabled = false;
        endBtn.innerHTML = '<i class="fas fa-stop mr-2"></i>End Job';
        endJobForm.classList.add('hidden'); // Hide form for in-progress jobs
    } else if (job.status === 'Completed') {
        // Disable both buttons instead of hiding them
        startBtn.classList.remove('hidden');
        endBtn.classList.remove('hidden');
        startBtn.disabled = true;
        endBtn.disabled = true;
        startBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Start Done';
        endBtn.innerHTML = '<i class="fas fa-check mr-2"></i>End Done';
        startBtn.className = 'flex-1 inline-flex items-center justify-center px-4 py-3 bg-gray-400 text-white rounded-lg cursor-not-allowed text-sm font-medium';
        endBtn.className = 'flex-1 inline-flex items-center justify-center px-4 py-3 bg-gray-400 text-white rounded-lg cursor-not-allowed text-sm font-medium';
        endJobForm.classList.add('hidden'); // Hide form for completed jobs
        
        // Show phase completed message
        const modalBody = document.querySelector('#job-modal .bg-white');
        if (modalBody) {
            // Remove any existing completion indicator
            const existingIndicator = modalBody.querySelector('.completion-indicator');
            if (existingIndicator) {
                existingIndicator.remove();
            }
            
            // Add phase completed indicator
            const completionIndicator = document.createElement('div');
            completionIndicator.className = 'completion-indicator mt-4 p-4 bg-green-50 border border-green-200 rounded-lg';
            completionIndicator.innerHTML = `
                <div class="flex items-center space-x-2 mb-2">
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    <span class="text-lg font-semibold text-green-800">Phase Completed!</span>
                </div>
                <p class="text-sm text-green-700 mb-2">Your ${job.phase} phase has been completed successfully.</p>
                <p class="text-xs text-green-600">You can close this modal or scan another job.</p>
            `;
            modalBody.appendChild(completionIndicator);
        }
    } else {
        startBtn.classList.add('hidden');
        endBtn.classList.add('hidden');
        endJobForm.classList.add('hidden');
    }
}

// Function to start job immediately (for phases that don't need start quantity)
function startJobImmediately(startQuantity) {
    console.log('🔵 [JOB] startJobImmediately() called with startQuantity:', startQuantity);
    if (!currentJob) {
        console.error('❌ [JOB] No current job available');
        showError('No job selected');
        return;
    }
    
    console.log('🔵 [JOB] Starting job:', currentJob.job_id);
    const button = document.getElementById('start-job');
    if (!button) {
        console.error('❌ [JOB] Start job button not found');
        return;
    }
    
    const originalText = button.innerHTML;
    
    // Show loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Starting...';
    
    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        console.error('❌ [JOB] CSRF token not found');
        showError('Security token not found. Please refresh the page.');
        button.disabled = false;
        button.innerHTML = originalText;
        return;
    }
    
    const startUrl = `/jobs/${currentJob.job_id}/start`;
    console.log('🔵 [JOB] Sending start job request to:', startUrl);
    console.log('🔵 [JOB] Request body:', { start_quantity: startQuantity || null });
    
    fetch(startUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            start_quantity: startQuantity || null
        })
    })
        .then(response => {
            console.log('🔵 [JOB] Start job response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ [JOB] Start job response data:', data);
            if (data.message) {
                let message = 'Job started successfully';
                if (data.order_status) {
                    message += ` (Order status: ${data.order_status})`;
                }
                showSuccess(message);
                
                // Immediately update the current job status
                currentJob.status = 'In Progress';
                currentJob.start_time = data.start_time;
                
                // Hide start job form if it was shown
                const startJobForm = document.getElementById('start-job-form');
                const confirmStartJobBtn = document.getElementById('confirm-start-job');
                const cancelStartJobBtn = document.getElementById('cancel-start-job');
                startJobForm.classList.add('hidden');
                confirmStartJobBtn.classList.add('hidden');
                cancelStartJobBtn.classList.add('hidden');
                
                // Update button states immediately
                updateButtonStates(currentJob);
                
                // Update time tracking
                const timeTracking = document.getElementById('time-tracking');
                const timeInfo = document.getElementById('time-info');
                timeTracking.classList.remove('hidden');
                
                const startTime = new Date(data.start_time);
                const now = new Date();
                const duration = Math.floor((now - startTime) / (1000 * 60)); // minutes
                
                timeInfo.innerHTML = `
                    <div class="space-y-1">
                        <div>Started: <strong>${startTime.toLocaleString()}</strong></div>
                        <div>Duration: <strong>${duration} minutes</strong></div>
                    </div>
                `;
                
                // Update status badge in modal
                const statusBadge = document.querySelector('#job-details .inline-flex.items-center.px-2\\.5.py-0\\.5.rounded-full');
                if (statusBadge) {
                    statusBadge.textContent = 'In Progress';
                    statusBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800';
                }
                
                addRecentScan(currentJob.job_id, 'Started');
                
                // Refresh the job data to show updated status (but keep modal open)
                setTimeout(() => {
                    fetch(`/jobs/${currentJob.job_id}/details`)
                        .then(response => response.json())
                        .then(jobData => {
                            if (jobData.success) {
                                currentJob = jobData.job;
                                updateButtonStates(currentJob);
                            }
                        });
                }, 1000);
            } else {
                // Show detailed error message for workflow issues
                if (data.error && data.previous_job) {
                    showError(`${data.message} Previous phase: ${data.previous_job} (${data.previous_status})`);
                } else if (data.error && data.job_phase) {
                    showError(`${data.error} Job phase: ${data.job_phase}, Your phase: ${data.user_phase}`);
                } else if (data.error && data.assigned_user_id) {
                    showError(`${data.error} This job is assigned to user ID: ${data.assigned_user_id}`);
                } else {
                    showError(data.error || data.message || 'Error starting job');
                }
            }
        })
        .catch(error => {
            console.error('Start job error:', error);
            showError('Error starting job: ' + error.message);
        })
        .finally(() => {
            // Only restore button state if job wasn't successfully started
            if (!currentJob || currentJob.status !== 'In Progress') {
                const button = document.getElementById('start-job');
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-play mr-2"></i>Start Job';
            }
        });
}

// Event listeners for job modal buttons are set up in DOMContentLoaded

// Enhanced manual QR input - will be set up in DOMContentLoaded

// Start scanner button - will be set up in DOMContentLoaded

// Close modal - will be set up in DOMContentLoaded

// Enhanced recent scan
function addRecentScan(jobId, action) {
    const recentScans = document.getElementById('recent-scans');
    const scanItem = document.createElement('div');
    scanItem.className = 'flex items-center justify-between p-3 bg-white/50 rounded-lg border border-gray-200 hover:bg-white/70 transition-all duration-200';
    scanItem.innerHTML = `
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-qrcode text-blue-600 text-sm"></i>
            </div>
        <div>
            <span class="text-sm font-medium text-gray-900">Job #${jobId}</span>
                <span class="text-xs text-gray-500 block">${action}</span>
            </div>
        </div>
        <span class="text-xs text-gray-400">${new Date().toLocaleTimeString()}</span>
    `;
    recentScans.insertBefore(scanItem, recentScans.firstChild);
    
    // Keep only last 5 scans
    if (recentScans.children.length > 5) {
        recentScans.removeChild(recentScans.lastChild);
    }
}

// Enhanced success/error messages
function showSuccess(message) {
    // Create a toast notification
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full';
    toast.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="fas fa-check-circle"></i>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

function showError(message) {
    // Create a toast notification
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full';
    toast.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="fas fa-exclamation-triangle"></i>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Remove after 5 seconds
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 5000);
}

// Function to refresh job status from database
function refreshJobStatus() {
    if (currentJob) {
        fetch(`/jobs/${currentJob.job_id}/details`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const oldStatus = currentJob.status;
                    currentJob = data.job;
                    
                    // Only update UI if status changed
                    if (oldStatus !== currentJob.status) {
                        updateButtonStates(currentJob);
                        
                        // Update status badge
                        const statusBadge = document.querySelector('.inline-flex.items-center.px-2\\.5.py-0\\.5.rounded-full');
                        if (statusBadge) {
                            statusBadge.textContent = currentJob.status;
                            statusBadge.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                currentJob.status === 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                                currentJob.status === 'In Progress' ? 'bg-blue-100 text-blue-800' :
                                currentJob.status === 'Completed' ? 'bg-green-100 text-green-800' :
                                'bg-red-100 text-red-800'
                            }`;
                        }
                        
                        // Update time tracking if needed
                        const timeTracking = document.getElementById('time-tracking');
                        const timeInfo = document.getElementById('time-info');
                        
                        if (currentJob.status === 'In Progress' && currentJob.start_time) {
                            timeTracking.classList.remove('hidden');
                            const startTime = new Date(currentJob.start_time);
                            const now = new Date();
                            const duration = Math.floor((now - startTime) / (1000 * 60));
                            
                            timeInfo.innerHTML = `
                                <div class="space-y-1">
                                    <div>Started: <strong>${startTime.toLocaleString()}</strong></div>
                                    <div>Duration: <strong>${duration} minutes</strong></div>
                                </div>
                            `;
                        } else if (currentJob.status === 'Completed' && currentJob.start_time && currentJob.end_time) {
                            timeTracking.classList.remove('hidden');
                            const startTime = new Date(currentJob.start_time);
                            const endTime = new Date(currentJob.end_time);
                            const duration = Math.floor((endTime - startTime) / (1000 * 60));
                            
                            timeInfo.innerHTML = `
                                <div class="space-y-1">
                                    <div>Started: <strong>${startTime.toLocaleString()}</strong></div>
                                    <div>Completed: <strong>${endTime.toLocaleString()}</strong></div>
                                    <div>Total Time: <strong>${duration} minutes</strong></div>
                                </div>
                            `;
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error refreshing job status:', error);
            });
    }
}

// Set up periodic refresh every 10 seconds
setInterval(refreshJobStatus, 10000);

// Also refresh when page becomes visible
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        refreshJobStatus();
    }
});

// Initialize scanner on page load (but don't auto-start camera)
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔵 [INIT] ========================================');
    console.log('🔵 [INIT] DOM Content Loaded - Initializing scanner page');
    console.log('🔵 [INIT] ========================================');
    
    // Don't auto-start camera - let user click button
    // initScanner();
    
    // Check if required elements exist
    const startScannerBtn = document.getElementById('start-scanner');
    const manualScanBtn = document.getElementById('manual-scan');
    const manualInput = document.getElementById('manual-qr');
    const closeModalBtn = document.getElementById('close-modal');
    const jobModal = document.getElementById('job-modal');
    
    console.log('🔵 [INIT] Start scanner button:', startScannerBtn ? 'Found' : 'Not found');
    console.log('🔵 [INIT] Manual scan button:', manualScanBtn ? 'Found' : 'Not found');
    console.log('🔵 [INIT] Manual input:', manualInput ? 'Found' : 'Not found');
    console.log('🔵 [INIT] Close modal button:', closeModalBtn ? 'Found' : 'Not found');
    console.log('🔵 [INIT] Job modal:', jobModal ? 'Found' : 'Not found');
    
    // Set up start scanner button
    if (startScannerBtn) {
        startScannerBtn.addEventListener('click', function() {
            console.log('🔵 [SCANNER] Start Scanner button clicked');
            const button = this;
            const originalText = button.innerHTML;
            
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Starting...';
            
            console.log('🔵 [SCANNER] Calling initScanner()...');
            try {
                initScanner();
            } catch (error) {
                console.error('❌ [SCANNER] Error in initScanner:', error);
                button.disabled = false;
                button.innerHTML = originalText;
                showError('Error starting scanner: ' + error.message);
            }
            
            // Re-enable button after a moment
            setTimeout(() => {
                button.disabled = false;
                button.innerHTML = originalText;
            }, 1000);
        });
        console.log('✅ [INIT] Start scanner button event listener attached');
    } else {
        console.error('❌ [INIT] Start scanner button not found!');
    }
    
    // Set up manual scan button
    if (manualScanBtn) {
        manualScanBtn.addEventListener('click', function() {
            console.log('🔵 [MANUAL] Manual scan button clicked');
            const manualInput = document.getElementById('manual-qr');
            const qrCode = manualInput ? manualInput.value.trim() : '';
            const scanButton = this;
            
            console.log('🔵 [MANUAL] Input value:', qrCode);
            console.log('🔵 [MANUAL] Input length:', qrCode.length);
            
            if (!qrCode) {
                console.warn('⚠️ [MANUAL] Empty input, showing error');
                showError('Please enter a QR code or job ID');
                if (manualInput) {
                    manualInput.focus();
                }
                return;
            }
            
            // Disable button during processing
            scanButton.disabled = true;
            scanButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
            
            // Show overlay while processing
            showOverlay();
            
            // Try direct job ID first (most common case - numeric)
            if (/^\d+$/.test(qrCode)) {
                console.log('🔵 [MANUAL] Detected numeric job ID:', qrCode);
                // Direct job ID - use job details endpoint
                const jobIdUrl = `/jobs/${qrCode}/details`;
                console.log('🔵 [MANUAL] Fetching job details from:', jobIdUrl);
                
                fetch(jobIdUrl)
                    .then(response => {
                        console.log('🔵 [MANUAL] Response status:', response.status);
                        console.log('🔵 [MANUAL] Response ok:', response.ok);
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('✅ [MANUAL] Job details received:', data);
                        hideOverlay();
                        if (data.success) {
                            console.log('✅ [MANUAL] Job found:', data.job);
                            showJobModal(data.job);
                            addRecentScan(qrCode, 'Manual Scan Success');
                            if (manualInput) {
                                manualInput.value = ''; // Clear input on success
                            }
                        } else {
                            console.error('❌ [MANUAL] Job not found or access denied:', data.message);
                            showError(data.message || 'Job not found or access denied');
                        }
                    })
                    .catch(error => {
                        hideOverlay();
                        console.error('❌ [MANUAL] Error fetching job details:', error);
                        console.error('❌ [MANUAL] Error message:', error.message);
                        if (error.message.includes('404')) {
                            console.error('❌ [MANUAL] Job not found (404)');
                            showError('Job not found. Please check the job ID.');
                        } else if (error.message.includes('403')) {
                            console.error('❌ [MANUAL] Access denied (403)');
                            showError('Access denied. This job is not assigned to you or does not match your phase.');
                        } else {
                            console.error('❌ [MANUAL] Unknown error');
                            showError('Error fetching job details. Please try again.');
                        }
                    })
                    .finally(() => {
                        console.log('🔵 [MANUAL] Re-enabling scan button');
                        // Re-enable button
                        scanButton.disabled = false;
                        scanButton.innerHTML = '<i class="fas fa-search mr-2"></i><span class="hidden sm:inline">Scan</span><span class="sm:hidden">Go</span>';
                    });
            } else if (qrCode.startsWith('QR_')) {
                console.log('🔵 [MANUAL] Detected QR code format:', qrCode);
                // QR code format - use QR code endpoint
                const qrUrl = `/jobs/qr/${encodeURIComponent(qrCode)}/details`;
                console.log('🔵 [MANUAL] Fetching job by QR code from:', qrUrl);
                
                fetch(qrUrl)
                    .then(response => {
                        console.log('🔵 [MANUAL] QR response status:', response.status);
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('✅ [MANUAL] QR job details received:', data);
                        hideOverlay();
                        if (data.success) {
                            console.log('✅ [MANUAL] Job found by QR code:', data.job);
                            showJobModal(data.job);
                            addRecentScan(data.job.job_id, 'QR Scan Success');
                            if (manualInput) {
                                manualInput.value = ''; // Clear input on success
                            }
                        } else {
                            console.error('❌ [MANUAL] QR job not found:', data.message);
                            showError(data.message || 'Job not found or access denied');
                        }
                    })
                    .catch(error => {
                        hideOverlay();
                        console.error('❌ [MANUAL] QR scan error:', error);
                        if (error.message.includes('404')) {
                            showError('Job not found. Please check the QR code.');
                        } else if (error.message.includes('403')) {
                            showError('Access denied. This job is not assigned to you or does not match your phase.');
                        } else {
                            showError('Error fetching job details. Please try again.');
                        }
                    })
                    .finally(() => {
                        console.log('🔵 [MANUAL] Re-enabling scan button');
                        // Re-enable button
                        scanButton.disabled = false;
                        scanButton.innerHTML = '<i class="fas fa-search mr-2"></i><span class="hidden sm:inline">Scan</span><span class="sm:hidden">Go</span>';
                    });
            } else {
                console.log('🔵 [MANUAL] Trying to extract job ID from input:', qrCode);
                // Try to extract job ID from the input
                const jobIdMatch = qrCode.match(/(\d+)/);
                if (jobIdMatch) {
                    const jobId = jobIdMatch[1];
                    console.log('🔵 [MANUAL] Extracted job ID:', jobId);
                    const extractedUrl = `/jobs/${jobId}/details`;
                    console.log('🔵 [MANUAL] Fetching job details from:', extractedUrl);
                    
                    fetch(extractedUrl)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('✅ [MANUAL] Extracted job details received:', data);
                            hideOverlay();
                            if (data.success) {
                                console.log('✅ [MANUAL] Job found by extracted ID:', data.job);
                                showJobModal(data.job);
                                addRecentScan(jobId, 'Manual Scan Success');
                                if (manualInput) {
                                    manualInput.value = ''; // Clear input on success
                                }
                            } else {
                                console.error('❌ [MANUAL] Job not found:', data.message);
                                showError(data.message || 'Job not found or access denied');
                            }
                        })
                        .catch(error => {
                            hideOverlay();
                            console.error('❌ [MANUAL] Error fetching extracted job:', error);
                            showError('Error fetching job details. Please try again.');
                        })
                        .finally(() => {
                            console.log('🔵 [MANUAL] Re-enabling scan button');
                            // Re-enable button
                            scanButton.disabled = false;
                            scanButton.innerHTML = '<i class="fas fa-search mr-2"></i><span class="hidden sm:inline">Scan</span><span class="sm:hidden">Go</span>';
                        });
                } else {
                    console.error('❌ [MANUAL] Invalid format, no job ID found');
                    hideOverlay();
                    showError('Invalid format. Please enter a job ID (number) or QR code (QR_...)');
                    scanButton.disabled = false;
                    scanButton.innerHTML = '<i class="fas fa-search mr-2"></i><span class="hidden sm:inline">Scan</span><span class="sm:hidden">Go</span>';
                }
            }
        });
        console.log('✅ [INIT] Manual scan button event listener attached');
    } else {
        console.error('❌ [INIT] Manual scan button not found!');
    }
    
    // Set up Enter key for manual input
    if (manualInput) {
        manualInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                console.log('🔵 [MANUAL] Enter key pressed');
                e.preventDefault();
                if (manualScanBtn) {
                    manualScanBtn.click();
                }
            }
        });
        console.log('✅ [INIT] Enter key listener attached to manual input');
        
        // Real-time validation for manual input
        manualInput.addEventListener('input', function() {
            const value = this.value.trim();
            const scanButton = document.getElementById('manual-scan');
            
            console.log('🔵 [MANUAL] Input changed, value:', value);
            if (value.length > 0) {
                if (scanButton) {
                    scanButton.disabled = false;
                    scanButton.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            } else {
                if (scanButton) {
                    scanButton.disabled = true;
                    scanButton.classList.add('opacity-50', 'cursor-not-allowed');
                }
            }
        });
        console.log('✅ [INIT] Real-time validation listener attached to manual input');
    }
    
    // Add close modal functionality
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function() {
            console.log('🔵 [MODAL] Close button clicked');
            if (jobModal) {
                jobModal.classList.add('hidden');
            }
            // Clear current job when modal is closed
            currentJob = null;
        });
    }
    
    // Also close modal when clicking outside
    if (jobModal) {
        jobModal.addEventListener('click', function(e) {
            if (e.target === this) {
                console.log('🔵 [MODAL] Clicked outside modal, closing');
                this.classList.add('hidden');
                currentJob = null;
            }
        });
    }
    
    // Focus on manual input
    if (manualInput) {
        manualInput.focus();
        console.log('🔵 [INIT] Focused on manual input');
    }
    
    // Clean up camera on page unload
    window.addEventListener('beforeunload', function() {
        console.log('🔵 [INIT] Page unloading, stopping scanner');
        stopScanning();
    });
    
    // Set up job modal event listeners (these elements exist in the modal)
    const startJobBtn = document.getElementById('start-job');
    const endJobBtn = document.getElementById('end-job');
    const confirmStartJobBtn = document.getElementById('confirm-start-job');
    const cancelStartJobBtn = document.getElementById('cancel-start-job');
    const confirmEndJobBtn = document.getElementById('confirm-end-job');
    const cancelEndJobBtn = document.getElementById('cancel-end-job');
    const endQuantityInput = document.getElementById('end-quantity');
    const rejectQuantityInput = document.getElementById('reject-quantity');
    const rejectStatusSelect = document.getElementById('reject-status');
    
    console.log('🔵 [INIT] Setting up job modal event listeners...');
    
    if (startJobBtn) {
        startJobBtn.addEventListener('click', function() {
            console.log('🔵 [JOB] Start job button clicked');
            if (currentJob) {
                // Check if this is CUT or QC phase - show form for these phases
                const phasesWithStartQuantity = ['CUT', 'QC'];
                if (phasesWithStartQuantity.includes(currentJob.phase)) {
                    // Show start job form
                    const startBtn = document.getElementById('start-job');
                    const startJobForm = document.getElementById('start-job-form');
                    const confirmStartJobBtn = document.getElementById('confirm-start-job');
                    const cancelStartJobBtn = document.getElementById('cancel-start-job');
                    
                    startBtn.classList.add('hidden');
                    startJobForm.classList.remove('hidden');
                    confirmStartJobBtn.classList.remove('hidden');
                    cancelStartJobBtn.classList.remove('hidden');
                    
                    // Focus on the start quantity input
                    document.getElementById('start-quantity').focus();
                    
                    // Add visual feedback
                    showSuccess('Please enter the start quantity for this phase');
                } else {
                    // For other phases, start immediately without form
                    startJobImmediately(null);
                }
            }
        });
        console.log('✅ [INIT] Start job button listener attached');
    }
    
    if (confirmStartJobBtn) {
        confirmStartJobBtn.addEventListener('click', function() {
            console.log('🔵 [JOB] Confirm start job button clicked');
            if (currentJob) {
                const button = this;
                const startQuantity = document.getElementById('start-quantity').value;
                
                // Validate start quantity
                if (!startQuantity || startQuantity <= 0) {
                    showError('Please enter a valid start quantity (must be greater than 0)');
                    document.getElementById('start-quantity').focus();
                    return;
                }
                
                // Show loading state
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Starting...';
                
                // Start the job with the entered quantity
                startJobImmediately(parseInt(startQuantity));
            }
        });
        console.log('✅ [INIT] Confirm start job button listener attached');
    }
    
    if (cancelStartJobBtn) {
        cancelStartJobBtn.addEventListener('click', function() {
            console.log('🔵 [JOB] Cancel start job button clicked');
            // Hide the form and buttons
            const startJobForm = document.getElementById('start-job-form');
            const startBtn = document.getElementById('start-job');
            const confirmStartJobBtn = document.getElementById('confirm-start-job');
            const cancelStartJobBtn = document.getElementById('cancel-start-job');
            
            startJobForm.classList.add('hidden');
            confirmStartJobBtn.classList.add('hidden');
            cancelStartJobBtn.classList.add('hidden');
            
            // Only show the start button if the job is still pending
            if (currentJob && currentJob.status === 'Pending') {
                startBtn.classList.remove('hidden');
            }
            
            // Clear form field
            document.getElementById('start-quantity').value = '';
            
            // Show feedback
            showSuccess('Start job cancelled. You can try again.');
        });
        console.log('✅ [INIT] Cancel start job button listener attached');
    }
    
    if (endJobBtn) {
        endJobBtn.addEventListener('click', function() {
            console.log('🔵 [JOB] End job button clicked');
            if (currentJob) {
                const endBtn = document.getElementById('end-job');
                const endJobForm = document.getElementById('end-job-form');
                const confirmEndJobBtn = document.getElementById('confirm-end-job');
                const cancelEndJobBtn = document.getElementById('cancel-end-job');
                
                // Update reject status options based on current phase
                updateRejectStatusOptions(currentJob.phase);
                
                // Hide the end button and show the form
                endBtn.classList.add('hidden');
                endJobForm.classList.remove('hidden');
                confirmEndJobBtn.classList.remove('hidden');
                cancelEndJobBtn.classList.remove('hidden');
                
                // Focus on the first input field
                document.getElementById('end-quantity').focus();
                
                // Add visual feedback
                showSuccess('Please fill in the job completion details below');
            }
        });
        console.log('✅ [INIT] End job button listener attached');
    }
    
    if (confirmEndJobBtn) {
        confirmEndJobBtn.addEventListener('click', function() {
            console.log('🔵 [JOB] Confirm end job button clicked');
            if (currentJob) {
                const button = this;
                const originalText = button.innerHTML;
                
                // Get form data
                const endQuantity = document.getElementById('end-quantity').value;
                const rejectQuantity = document.getElementById('reject-quantity').value;
                const rejectStatus = document.getElementById('reject-status').value;
                const remarks = document.getElementById('remarks').value;
                
                console.log('🔵 [JOB] End job form data:', { endQuantity, rejectQuantity, rejectStatus, remarks });
                
                // Validate form data
                if (!endQuantity && !rejectQuantity) {
                    showError('Please enter either end quantity or reject quantity');
                    return;
                }
                
                if (rejectQuantity > 0 && !rejectStatus) {
                    showError('Please select a reject status when there are rejected items');
                    return;
                }
                
                // Show loading state
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Ending...';
                
                // Get CSRF token from meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                console.log('🔵 [JOB] Sending end job request...');
                fetch(`/jobs/${currentJob.job_id}/end`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        end_quantity: endQuantity || null,
                        reject_quantity: rejectQuantity || null,
                        reject_status: rejectStatus || null,
                        remarks: remarks || null
                    })
                })
                .then(response => {
                    console.log('🔵 [JOB] End job response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('✅ [JOB] End job response data:', data);
                    if (data.message) {
                        // Show completion message with time tracking
                        let message = 'Job completed successfully';
                        if (data.duration_formatted) {
                            message += ` (Time taken: ${data.duration_formatted})`;
                        }
                        if (data.order_status) {
                            message += ` (Order status: ${data.order_status})`;
                        }
                        showSuccess(message);
                        
                        // Immediately update the current job status
                        currentJob.status = 'Completed';
                        currentJob.end_time = data.end_time;
                        currentJob.duration = data.duration;
                        
                        // Update button states immediately
                        updateButtonStates(currentJob);
                        
                        // Update time tracking
                        const timeTracking = document.getElementById('time-tracking');
                        const timeInfo = document.getElementById('time-info');
                        timeTracking.classList.remove('hidden');
                        
                        const startTime = new Date(currentJob.start_time);
                        const endTime = new Date(data.end_time);
                        const duration = Math.floor((endTime - startTime) / (1000 * 60)); // minutes
                        
                        timeInfo.innerHTML = `
                            <div class="space-y-1">
                                <div>Started: <strong>${startTime.toLocaleString()}</strong></div>
                                <div>Completed: <strong>${endTime.toLocaleString()}</strong></div>
                                <div>Total Time: <strong>${duration} minutes</strong></div>
                            </div>
                        `;
                        
                        // Update status badge in modal
                        const statusBadge = document.querySelector('#job-details .inline-flex.items-center.px-2\\.5.py-0\\.5.rounded-full');
                        if (statusBadge) {
                            statusBadge.textContent = 'Completed';
                            statusBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800';
                        }
                        
                        addRecentScan(currentJob.job_id, 'Completed');
                        
                        // Refresh the job data to show updated status (but keep modal open)
                        setTimeout(() => {
                            fetch(`/jobs/${currentJob.job_id}/details`)
                                .then(response => response.json())
                                .then(jobData => {
                                    if (jobData.success) {
                                        currentJob = jobData.job;
                                        updateButtonStates(currentJob);
                                    }
                                });
                        }, 1000);
                    } else {
                        // Show detailed error message
                        if (data.error && data.job_phase) {
                            showError(`${data.error} Job phase: ${data.job_phase}, Your phase: ${data.user_phase}`);
                        } else if (data.error && data.assigned_user_id) {
                            showError(`${data.error} This job is assigned to user ID: ${data.assigned_user_id}`);
                        } else {
                            showError(data.error || data.message || 'Error ending job');
                        }
                    }
                })
                .catch(error => {
                    console.error('❌ [JOB] End job error:', error);
                    showError('Error ending job: ' + error.message);
                })
                .finally(() => {
                    // Only restore button state if job wasn't successfully completed
                    if (!currentJob || currentJob.status !== 'Completed') {
                        button.disabled = false;
                        button.innerHTML = originalText;
                    }
                });
            }
        });
        console.log('✅ [INIT] Confirm end job button listener attached');
    }
    
    if (cancelEndJobBtn) {
        cancelEndJobBtn.addEventListener('click', function() {
            console.log('🔵 [JOB] Cancel end job button clicked');
            // Hide the form and buttons
            const endJobForm = document.getElementById('end-job-form');
            const endBtn = document.getElementById('end-job');
            const confirmEndJobBtn = document.getElementById('confirm-end-job');
            const cancelEndJobBtn = document.getElementById('cancel-end-job');
            
            endJobForm.classList.add('hidden');
            confirmEndJobBtn.classList.add('hidden');
            cancelEndJobBtn.classList.add('hidden');
            
            // Only show the end button if the job is still in progress
            if (currentJob && currentJob.status === 'In Progress') {
                endBtn.classList.remove('hidden');
            }
            
            // Clear form fields
            document.getElementById('end-quantity').value = '';
            document.getElementById('reject-quantity').value = '';
            document.getElementById('reject-status').value = '';
            document.getElementById('remarks').value = '';
            
            // Show feedback
            showSuccess('End job cancelled. You can try again.');
        });
        console.log('✅ [INIT] Cancel end job button listener attached');
    }
    
    // Set up form validation listeners
    if (endQuantityInput) {
        endQuantityInput.addEventListener('input', function() {
            validateEndJobForm();
        });
        console.log('✅ [INIT] End quantity input listener attached');
    }
    
    if (rejectQuantityInput) {
        rejectQuantityInput.addEventListener('input', function() {
            validateEndJobForm();
        });
        console.log('✅ [INIT] Reject quantity input listener attached');
    }
    
    if (rejectStatusSelect) {
        rejectStatusSelect.addEventListener('change', function() {
            validateEndJobForm();
        });
        console.log('✅ [INIT] Reject status select listener attached');
    }
    
    console.log('✅ [INIT] ========================================');
    console.log('✅ [INIT] Scanner page initialization complete');
    console.log('✅ [INIT] All event listeners attached');
    console.log('✅ [INIT] Ready to use!');
    console.log('✅ [INIT] ========================================');
});

// Event listeners for end job form are set up in DOMContentLoaded

function validateEndJobForm() {
    const endQuantity = document.getElementById('end-quantity').value;
    const rejectQuantity = document.getElementById('reject-quantity').value;
    const rejectStatus = document.getElementById('reject-status').value;
    const confirmBtn = document.getElementById('confirm-end-job');
    
    // Check if at least one quantity is entered
    const hasQuantity = (endQuantity && endQuantity > 0) || (rejectQuantity && rejectQuantity > 0);
    
    // Check if reject status is selected when reject quantity is entered
    const hasRejectStatus = !rejectQuantity || rejectQuantity == 0 || rejectStatus;
    
    // Visual feedback for form fields
    const endQuantityField = document.getElementById('end-quantity');
    const rejectQuantityField = document.getElementById('reject-quantity');
    const rejectStatusField = document.getElementById('reject-status');
    
    // Reset all field styles
    endQuantityField.classList.remove('border-red-500', 'border-green-500');
    rejectQuantityField.classList.remove('border-red-500', 'border-green-500');
    rejectStatusField.classList.remove('border-red-500', 'border-green-500');
    
    // Add visual feedback
    if (endQuantity && endQuantity > 0) {
        endQuantityField.classList.add('border-green-500');
    }
    
    if (rejectQuantity && rejectQuantity > 0) {
        rejectQuantityField.classList.add('border-green-500');
        if (rejectStatus) {
            rejectStatusField.classList.add('border-green-500');
        } else {
            rejectStatusField.classList.add('border-red-500');
        }
    }
    
    if (hasQuantity && hasRejectStatus) {
        confirmBtn.disabled = false;
        confirmBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
        confirmBtn.disabled = true;
        confirmBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
}
</script>
@endsection 