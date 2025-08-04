@extends('layouts.app')

@section('title', 'QR Scanner - Fazztrack')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 flex items-center">
            <i class="fas fa-qrcode mr-3 text-primary-500"></i>
            QR Scanner
        </h1>
        <p class="mt-2 text-gray-600">Scan QR codes to start and end production jobs.</p>
    </div>

    <!-- Scanner Container -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <!-- Scanner Status -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div id="scanner-status" class="w-3 h-3 bg-red-500 rounded-full"></div>
                        <span id="status-text" class="text-sm font-medium text-gray-700">Scanner Ready</span>
                    </div>
                    <button id="start-scanner" class="px-4 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600 transition-colors">
                        <i class="fas fa-play mr-2"></i>Start Scanner
                    </button>
                </div>
            </div>

            <!-- Video Container -->
            <div class="relative">
                <video id="qr-video" class="w-full h-64 bg-gray-100 rounded-lg" autoplay></video>
                <div id="scanner-overlay" class="absolute inset-0 flex items-center justify-center hidden">
                    <div class="bg-white p-4 rounded-lg shadow-lg">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-500"></div>
                        <p class="mt-2 text-sm text-gray-600">Processing QR Code...</p>
                    </div>
                </div>
            </div>

            <!-- Manual QR Input -->
            <div class="mt-6">
                <div class="space-y-3">
                    <div class="flex space-x-3">
                        <input type="text" 
                               id="manual-qr" 
                               placeholder="Enter QR code manually (e.g., QR_123 or job ID)" 
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               maxlength="50">
                        <button id="manual-scan" 
                                class="px-4 py-2 bg-primary-500 text-white rounded-md hover:bg-primary-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-search mr-2"></i>Scan
                        </button>
                    </div>
                    <div class="text-xs text-gray-500 flex items-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        You can enter QR code directly or job ID number
                    </div>
                </div>
            </div>

            <!-- Recent Scans -->
            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Scans</h3>
                <div id="recent-scans" class="space-y-2">
                    <!-- Recent scans will be populated here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Job Details Modal -->
    <div id="job-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Job Details</h3>
                    <button id="close-modal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="job-details">
                    <!-- Job details will be populated here -->
                </div>
                <div class="flex space-x-3 mt-6">
                    <button id="start-job" class="flex-1 px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors">
                        <i class="fas fa-play mr-2"></i>Start Job
                    </button>
                    <button id="end-job" class="flex-1 px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors hidden">
                        <i class="fas fa-stop mr-2"></i>End Job
                    </button>
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

// Initialize scanner
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
        });
}

// Handle QR code data with enhanced parsing
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
            // Format: QR_123
            jobId = qrData.split('_')[2];
        } else if (qrData.startsWith('JOB_')) {
            // Format: JOB_123
            jobId = qrData.split('_')[2];
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
    
    // Validate job ID is numeric
    if (!/^\d+$/.test(jobId)) {
        hideOverlay();
        showError('Invalid job ID format. Please enter a valid job number.');
        return;
    }
    
    // Fetch job details with enhanced error handling
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
                showError(data.message || 'Job not found or access denied');
            }
        })
        .catch(error => {
            hideOverlay();
            console.error('QR scan error:', error);
            if (error.message.includes('404')) {
                showError('Job not found. Please check the job ID.');
            } else if (error.message.includes('403')) {
                showError('Access denied. This job is not assigned to you.');
            } else {
                showError('Error fetching job details. Please try again.');
            }
        });
}

// Show/hide overlay
function showOverlay() {
    document.getElementById('scanner-overlay').classList.remove('hidden');
}

function hideOverlay() {
    document.getElementById('scanner-overlay').classList.add('hidden');
}

// Show job modal
function showJobModal(job) {
    currentJob = job;
    const modal = document.getElementById('job-modal');
    const details = document.getElementById('job-details');
    const startBtn = document.getElementById('start-job');
    const endBtn = document.getElementById('end-job');

    details.innerHTML = `
        <div class="space-y-3">
            <div>
                <span class="text-sm font-medium text-gray-500">Job ID:</span>
                <span class="text-sm text-gray-900">${job.job_id}</span>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500">Phase:</span>
                <span class="text-sm text-gray-900">${job.phase}</span>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500">Order:</span>
                <span class="text-sm text-gray-900">${job.order.job_name}</span>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500">Status:</span>
                <span class="text-sm text-gray-900">${job.status}</span>
            </div>
            <div>
                <span class="text-sm font-medium text-gray-500">Client:</span>
                <span class="text-sm text-gray-900">${job.order.client.name}</span>
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

    // Check if user can start this job
    fetch(`/jobs/${job.job_id}/workflow`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (job.status === 'Pending' && data.can_start) {
                    startBtn.classList.remove('hidden');
                    endBtn.classList.add('hidden');
                    startBtn.disabled = false;
                } else if (job.status === 'In Progress') {
                    startBtn.classList.add('hidden');
                    endBtn.classList.remove('hidden');
                    endBtn.disabled = false;
                } else if (job.status === 'Pending' && !data.can_start) {
                    startBtn.classList.remove('hidden');
                    endBtn.classList.add('hidden');
                    startBtn.disabled = true;
                    startBtn.textContent = 'Cannot Start - Previous Phase Required';
                    startBtn.className = 'flex-1 px-4 py-2 bg-gray-400 text-white rounded-md cursor-not-allowed';
                } else {
                    startBtn.classList.add('hidden');
                    endBtn.classList.add('hidden');
                }
            }
        })
        .catch(error => {
            console.error('Error checking workflow:', error);
            // Fallback to basic status check
            if (job.status === 'Pending') {
                startBtn.classList.remove('hidden');
                endBtn.classList.add('hidden');
            } else if (job.status === 'In Progress') {
                startBtn.classList.add('hidden');
                endBtn.classList.remove('hidden');
            }
        });

    modal.classList.remove('hidden');
}

// Start job
document.getElementById('start-job').addEventListener('click', function() {
    if (currentJob) {
        fetch(`/jobs/${currentJob.job_id}/start`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('Job started successfully');
                document.getElementById('job-modal').classList.add('hidden');
                addRecentScan(currentJob.job_id, 'Started');
            } else {
                // Show detailed error message for workflow issues
                if (data.error && data.previous_job) {
                    showError(`${data.message} Previous phase: ${data.previous_job} (${data.previous_status})`);
                } else {
                    showError(data.message || 'Error starting job');
                }
            }
        })
        .catch(error => {
            showError('Error starting job');
        });
    }
});

// End job
document.getElementById('end-job').addEventListener('click', function() {
    if (currentJob) {
        fetch(`/jobs/${currentJob.job_id}/end`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show completion message with time tracking
                let message = 'Job completed successfully';
                if (data.duration_formatted) {
                    message += ` (Time taken: ${data.duration_formatted})`;
                }
                showSuccess(message);
                document.getElementById('job-modal').classList.add('hidden');
                addRecentScan(currentJob.job_id, 'Completed');
            } else {
                showError(data.message || 'Error completing job');
            }
        })
        .catch(error => {
            showError('Error completing job');
        });
    }
});

// Manual QR input with enhanced functionality
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
    
    // Process the QR code
    handleQRCode(qrCode);
    
    // Clear input and re-enable button
    manualInput.value = '';
    setTimeout(() => {
        scanButton.disabled = false;
        scanButton.innerHTML = '<i class="fas fa-search mr-2"></i>Scan';
    }, 2000);
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

// Add recent scan
function addRecentScan(jobId, action) {
    const recentScans = document.getElementById('recent-scans');
    const scanItem = document.createElement('div');
    scanItem.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg';
    scanItem.innerHTML = `
        <div>
            <span class="text-sm font-medium text-gray-900">Job #${jobId}</span>
            <span class="text-xs text-gray-500 ml-2">${action}</span>
        </div>
        <span class="text-xs text-gray-400">${new Date().toLocaleTimeString()}</span>
    `;
    recentScans.insertBefore(scanItem, recentScans.firstChild);
    
    // Keep only last 5 scans
    if (recentScans.children.length > 5) {
        recentScans.removeChild(recentScans.lastChild);
    }
}

// Show success/error messages
function showSuccess(message) {
    // You can implement a toast notification here
    alert('Success: ' + message);
}

function showError(message) {
    // You can implement a toast notification here
    alert('Error: ' + message);
}

// Initialize scanner on page load
document.addEventListener('DOMContentLoaded', function() {
    // Auto-start scanner for production staff
    if (document.body.classList.contains('production-staff')) {
        initScanner();
    }
});
</script>
@endsection 