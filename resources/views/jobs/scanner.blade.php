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
                    <video id="qr-video" class="w-full h-48 sm:h-64 lg:h-80 bg-gray-100 rounded-xl sm:rounded-2xl" autoplay></video>
                    <div id="scanner-overlay" class="absolute inset-0 flex items-center justify-center hidden bg-black/20 rounded-xl sm:rounded-2xl">
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
    const video = document.getElementById('qr-video');
    const statusIndicator = document.getElementById('scanner-status');
    const statusText = document.getElementById('status-text');

    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
        .then(function(stream) {
            videoStream = stream;
            video.srcObject = stream;
            video.play();

            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');

            function scanQR() {
                if (video.readyState === video.HAVE_ENOUGH_DATA) {
                    canvas.height = video.videoHeight;
                    canvas.width = video.videoWidth;
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);
                    
                    const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height);
                    
                    if (code) {
                        handleQRCode(code.data);
                    }
                }
                requestAnimationFrame(scanQR);
            }

            scanQR();
            statusIndicator.className = 'w-3 h-3 bg-green-500 rounded-full';
            statusText.textContent = 'Scanner Active';
        })
        .catch(function(err) {
            console.error('Error accessing camera:', err);
            statusIndicator.className = 'w-3 h-3 bg-red-500 rounded-full';
            statusText.textContent = 'Camera Error';
            
            // Show error message but keep it simple
            const cameraContainer = document.getElementById('camera-container');
            cameraContainer.innerHTML = `
                <div class="flex items-center justify-center h-full bg-red-50 rounded-xl">
                    <div class="text-center p-6">
                        <i class="fas fa-camera-slash text-4xl text-red-500 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Camera Access Failed</h3>
                        <p class="text-sm text-gray-600 mb-4">Please use manual input below</p>
                        <button onclick="initScanner()" 
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-redo mr-2"></i>Try Again
                        </button>
                    </div>
                </div>
            `;
        });
}

// Enhanced QR code handling
function handleQRCode(qrData) {
    showOverlay();
    
    // Enhanced QR code parsing
    let jobId = null;
    try {
        // Try to parse as JSON first (for complex QR codes)
        const qrDataObj = JSON.parse(qrData);
        jobId = qrDataObj.job_id;
    } catch (e) {
        // Handle different QR code formats
        if (qrData.startsWith('QR_')) {
            // Check for format: QR_EVLrykvkjc_PRINT (QR_randomstring_phase)
            // We need to find the job by QR code since the QR doesn't contain the job ID directly
            jobId = qrData; // Pass the full QR code to backend
        } else if (qrData.startsWith('JOB_')) {
            // Format: JOB_123
            jobId = qrData.split('_')[1];
        } else if (/^\d+$/.test(qrData)) {
            // Direct job ID number
            jobId = qrData;
        } else {
            // Try to extract job ID from any format
            const match = qrData.match(/(\d+)/);
            if (match) {
                jobId = match[1];
            }
        }
    }
    
    if (!jobId) {
        hideOverlay();
        showError('Invalid QR code format. Please check the code and try again.');
        return;
    }
    
    // If it's a QR code (starts with QR_), we need to find the job by QR code
    if (jobId.startsWith('QR_')) {
        fetch(`/jobs/qr/${encodeURIComponent(jobId)}/details`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
        hideOverlay();
                if (data.success) {
                    showJobModal(data.job);
                    addRecentScan(data.job.job_id, 'Scanned');
                } else {
                    console.error('Job details error:', data.message);
                    showError(data.message || 'Job not found or access denied');
                }
            })
            .catch(error => {
                console.error('QR scan error:', error);
                console.error('Error details:', error.message);
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
    fetch(`/jobs/${jobId}/details`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            hideOverlay();
            if (data.success) {
                showJobModal(data.job);
                addRecentScan(jobId, 'Scanned');
            } else {
                    console.error('Job details error:', data.message);
                showError(data.message || 'Job not found or access denied');
            }
        })
        .catch(error => {
            console.error('QR scan error:', error);
                console.error('Error details:', error.message);
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

// Enhanced start job
document.getElementById('start-job').addEventListener('click', function() {
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

// Function to start job immediately (for phases that don't need start quantity)
function startJobImmediately(startQuantity) {
    if (currentJob) {
        const button = document.getElementById('start-job');
        const originalText = button.innerHTML;
        
        // Show loading state
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Starting...';
        
        // Get CSRF token from meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/jobs/${currentJob.job_id}/start`, {
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
            return response.json();
        })
        .then(data => {
            console.log('Start job response data:', data);
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

// Confirm start job (for CUT and QC phases)
document.getElementById('confirm-start-job').addEventListener('click', function() {
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

// Cancel start job
document.getElementById('cancel-start-job').addEventListener('click', function() {
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

// Enhanced end job
document.getElementById('end-job').addEventListener('click', function() {
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

// Enhanced manual QR input
document.getElementById('manual-scan').addEventListener('click', function() {
    const manualInput = document.getElementById('manual-qr');
    const qrCode = manualInput.value.trim();
    const scanButton = this;
    
    if (!qrCode) {
        showError('Please enter a QR code or job ID');
        manualInput.focus();
        return;
    }
    
    // Disable button during processing
    scanButton.disabled = true;
    scanButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    
    // Try direct job ID first (most common case)
    if (/^\d+$/.test(qrCode)) {
        fetch(`/jobs/${qrCode}/details`)
            .then(response => {
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showJobModal(data.job);
                    addRecentScan(qrCode, 'Manual Scan Success');
                } else {
                    showError(data.message || 'Job not found or access denied');
                }
            })
            .catch(error => {
                console.error('Manual scan error:', error);
                showError('Error fetching job details. Please try again.');
            })
            .finally(() => {
                // Re-enable button
                scanButton.disabled = false;
                scanButton.innerHTML = '<i class="fas fa-search mr-2"></i><span class="hidden sm:inline">Scan</span><span class="sm:hidden">Go</span>';
            });
    } else {
        // Process as QR code
    handleQRCode(qrCode);
    
    // Clear input and re-enable button
    manualInput.value = '';
    setTimeout(() => {
        scanButton.disabled = false;
            scanButton.innerHTML = '<i class="fas fa-search mr-2"></i><span class="hidden sm:inline">Scan</span><span class="sm:hidden">Go</span>';
    }, 2000);
    }
});

// Allow Enter key to trigger manual scan
document.getElementById('manual-qr').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('manual-scan').click();
    }
});

// Real-time validation for manual input
document.getElementById('manual-qr').addEventListener('input', function() {
    const value = this.value.trim();
    const scanButton = document.getElementById('manual-scan');
    
    if (value.length > 0) {
        scanButton.disabled = false;
        scanButton.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
        scanButton.disabled = true;
        scanButton.classList.add('opacity-50', 'cursor-not-allowed');
    }
});

// Start scanner button
document.getElementById('start-scanner').addEventListener('click', function() {
    initScanner();
});

// Close modal
document.getElementById('close-modal').addEventListener('click', function() {
    document.getElementById('job-modal').classList.add('hidden');
});

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

// Initialize scanner on page load
document.addEventListener('DOMContentLoaded', function() {
        initScanner();
    
    // Add close modal functionality
    document.getElementById('close-modal').addEventListener('click', function() {
        document.getElementById('job-modal').classList.add('hidden');
        // Clear current job when modal is closed
        currentJob = null;
    });
    
    // Also close modal when clicking outside
    document.getElementById('job-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
            currentJob = null;
        }
    });
    
    // Focus on manual input
    document.getElementById('manual-qr').focus();
});

// Confirm end job
document.getElementById('confirm-end-job').addEventListener('click', function() {
    if (currentJob) {
        const button = this;
        const originalText = button.innerHTML;
        
        // Get form data
        const endQuantity = document.getElementById('end-quantity').value;
        const rejectQuantity = document.getElementById('reject-quantity').value;
        const rejectStatus = document.getElementById('reject-status').value;
        const remarks = document.getElementById('remarks').value;
        
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
            return response.json();
        })
        .then(data => {
            console.log('End job response data:', data);
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
            console.error('End job error:', error);
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

// Cancel end job
document.getElementById('cancel-end-job').addEventListener('click', function() {
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

// Real-time validation for end job form
document.getElementById('end-quantity').addEventListener('input', function() {
    validateEndJobForm();
});

document.getElementById('reject-quantity').addEventListener('input', function() {
    validateEndJobForm();
});

document.getElementById('reject-status').addEventListener('change', function() {
    validateEndJobForm();
});

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