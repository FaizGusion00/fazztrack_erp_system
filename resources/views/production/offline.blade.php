@extends('layouts.app')

@section('title', 'Offline Mode - Fazztrack')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-wifi-slash mr-3 text-orange-500"></i>
                    Offline Mode
                </h1>
                <p class="mt-2 text-gray-600">Work on jobs without internet connectivity</p>
            </div>
            <div class="flex items-center space-x-4">
                <div id="connection-status" class="flex items-center space-x-2">
                    <div id="status-indicator" class="w-3 h-3 rounded-full bg-red-500"></div>
                    <span id="status-text" class="text-sm font-medium text-red-600">Offline</span>
                </div>
                <button id="sync-button" onclick="syncOfflineData()" 
                        class="hidden inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                    <i class="fas fa-sync mr-2"></i>
                    Sync Data
                </button>
            </div>
        </div>
    </div>

    <!-- Offline Jobs List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Available Jobs</h2>
            <p class="mt-1 text-sm text-gray-600">Jobs you can work on offline</p>
        </div>
        
        <div id="jobs-container" class="p-6">
            <div id="loading-jobs" class="text-center py-8">
                <i class="fas fa-spinner fa-spin text-gray-400 text-2xl mb-4"></i>
                <p class="text-gray-500">Loading jobs...</p>
            </div>
            
            <div id="no-jobs" class="hidden text-center py-8">
                <i class="fas fa-clipboard-list text-gray-400 text-2xl mb-4"></i>
                <p class="text-gray-500">No jobs available for offline work</p>
            </div>
            
            <div id="jobs-list" class="hidden space-y-4">
                <!-- Jobs will be populated here -->
            </div>
        </div>
    </div>

    <!-- Offline Logs -->
    <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Offline Actions</h2>
            <p class="mt-1 text-sm text-gray-600">Actions performed while offline</p>
        </div>
        
        <div class="p-6">
            <div id="offline-logs-container">
                <div id="loading-logs" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin text-gray-400 text-lg"></i>
                    <p class="text-gray-500 text-sm">Loading offline logs...</p>
                </div>
                
                <div id="no-logs" class="hidden text-center py-4">
                    <i class="fas fa-check-circle text-green-400 text-lg mb-2"></i>
                    <p class="text-gray-500 text-sm">No offline actions to sync</p>
                </div>
                
                <div id="logs-list" class="hidden space-y-3">
                    <!-- Offline logs will be populated here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Job Action Modal -->
<div id="job-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="modal-title">Job Action</h3>
                <button onclick="closeJobModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div id="modal-content">
                <!-- Modal content will be populated here -->
            </div>
            
            <div class="flex justify-end space-x-3 mt-6">
                <button onclick="closeJobModal()" 
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </button>
                <button id="modal-action-btn" onclick="performJobAction()" 
                        class="px-4 py-2 bg-primary-600 text-white rounded-md text-sm font-medium hover:bg-primary-700">
                    Confirm Action
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let offlineJobs = [];
let offlineLogs = [];
let currentJob = null;
let isOnline = false;

// Initialize offline mode
document.addEventListener('DOMContentLoaded', function() {
    loadOfflineJobs();
    loadOfflineLogs();
    checkConnectionStatus();
    
    // Check connection status every 30 seconds
    setInterval(checkConnectionStatus, 30000);
});

// Load jobs for offline work
async function loadOfflineJobs() {
    try {
        const response = await fetch('/offline/jobs');
        if (response.ok) {
            const data = await response.json();
            offlineJobs = data.jobs;
            displayJobs();
        } else {
            console.error('Failed to load offline jobs');
            // Show no jobs message
            showNoJobs();
        }
    } catch (error) {
        console.error('Error loading offline jobs:', error);
        // Show no jobs message
        showNoJobs();
    }
}

// Show no jobs message
function showNoJobs() {
    const container = document.getElementById('jobs-container');
    const loading = document.getElementById('loading-jobs');
    const noJobs = document.getElementById('no-jobs');
    const jobsList = document.getElementById('jobs-list');
    
    loading.classList.add('hidden');
    noJobs.classList.remove('hidden');
    jobsList.classList.add('hidden');
}

// Display jobs in the UI
function displayJobs() {
    const container = document.getElementById('jobs-container');
    const loading = document.getElementById('loading-jobs');
    const noJobs = document.getElementById('no-jobs');
    const jobsList = document.getElementById('jobs-list');
    
    loading.classList.add('hidden');
    
    if (offlineJobs.length === 0) {
        noJobs.classList.remove('hidden');
        jobsList.classList.add('hidden');
        return;
    }
    
    noJobs.classList.add('hidden');
    jobsList.classList.remove('hidden');
    
    jobsList.innerHTML = offlineJobs.map(job => `
        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-qrcode text-primary-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">${job.phase}</h3>
                            <p class="text-sm text-gray-600">Order: ${job.order_number} - ${job.client_name}</p>
                            <p class="text-sm text-gray-500">${job.description}</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(job.status)}">
                        ${job.status}
                    </span>
                    <button onclick="showJobActions(${job.id})" 
                            class="inline-flex items-center px-3 py-1 border border-primary-300 text-sm font-medium rounded-md text-primary-700 bg-primary-50 hover:bg-primary-100">
                        <i class="fas fa-cog mr-1"></i>
                        Actions
                    </button>
                </div>
            </div>
        </div>
    `).join('');
    
    // Save to localStorage for offline access
    localStorage.setItem('offlineJobs', JSON.stringify(offlineJobs));
}

// Load jobs from localStorage (for offline mode)
function loadJobsFromStorage() {
    const stored = localStorage.getItem('offlineJobs');
    if (stored) {
        offlineJobs = JSON.parse(stored);
        displayJobs();
    }
}

// Show job action modal
function showJobActions(jobId) {
    const job = offlineJobs.find(j => j.id === jobId);
    if (!job) return;
    
    currentJob = job;
    const modal = document.getElementById('job-modal');
    const title = document.getElementById('modal-title');
    const content = document.getElementById('modal-content');
    const actionBtn = document.getElementById('modal-action-btn');
    
    title.textContent = `${job.phase} - ${job.order_number}`;
    
    let actions = '';
    if (job.status === 'Pending') {
        actions = `
            <div class="space-y-3">
                <p class="text-sm text-gray-600">What would you like to do with this job?</p>
                <div class="space-y-2">
                    <button onclick="selectAction('start')" class="w-full text-left p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex items-center">
                            <i class="fas fa-play text-green-600 mr-3"></i>
                            <div>
                                <div class="font-medium text-gray-900">Start Job</div>
                                <div class="text-sm text-gray-500">Begin working on this job</div>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        `;
        actionBtn.textContent = 'Start Job';
        actionBtn.onclick = () => performJobAction('start');
    } else if (job.status === 'In Progress') {
        actions = `
            <div class="space-y-3">
                <p class="text-sm text-gray-600">What would you like to do with this job?</p>
                <div class="space-y-2">
                    <button onclick="selectAction('end')" class="w-full text-left p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex items-center">
                            <i class="fas fa-check text-green-600 mr-3"></i>
                            <div>
                                <div class="font-medium text-gray-900">Complete Job</div>
                                <div class="text-sm text-gray-500">Finish this job</div>
                            </div>
                        </div>
                    </button>
                    <button onclick="selectAction('pause')" class="w-full text-left p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex items-center">
                            <i class="fas fa-pause text-yellow-600 mr-3"></i>
                            <div>
                                <div class="font-medium text-gray-900">Pause Job</div>
                                <div class="text-sm text-gray-500">Temporarily pause this job</div>
                            </div>
                        </div>
                    </button>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Notes (optional)</label>
                    <textarea id="job-notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"></textarea>
                </div>
            </div>
        `;
    } else if (job.status === 'Paused') {
        actions = `
            <div class="space-y-3">
                <p class="text-sm text-gray-600">What would you like to do with this job?</p>
                <div class="space-y-2">
                    <button onclick="selectAction('resume')" class="w-full text-left p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex items-center">
                            <i class="fas fa-play text-green-600 mr-3"></i>
                            <div>
                                <div class="font-medium text-gray-900">Resume Job</div>
                                <div class="text-sm text-gray-500">Continue working on this job</div>
                            </div>
                        </div>
                    </button>
                </div>
            </div>
        `;
        actionBtn.textContent = 'Resume Job';
        actionBtn.onclick = () => performJobAction('resume');
    }
    
    content.innerHTML = actions;
    modal.classList.remove('hidden');
}

// Select action in modal
function selectAction(action) {
    const actionBtn = document.getElementById('modal-action-btn');
    actionBtn.onclick = () => performJobAction(action);
    
    if (action === 'end') {
        actionBtn.textContent = 'Complete Job';
    } else if (action === 'pause') {
        actionBtn.textContent = 'Pause Job';
    } else if (action === 'resume') {
        actionBtn.textContent = 'Resume Job';
    }
}

// Perform job action
async function performJobAction(action) {
    if (!currentJob) return;
    
    const notes = document.getElementById('job-notes')?.value || '';
    const actionTime = new Date().toISOString();
    
    // Create offline log
    const logData = {
        job_id: currentJob.id,
        action: action,
        action_time: actionTime,
        notes: notes,
        offline_data: {
            phase: currentJob.phase,
            order_number: currentJob.order_number,
            client_name: currentJob.client_name
        }
    };
    
    // Add to offline logs
    offlineLogs.push(logData);
    localStorage.setItem('offlineLogs', JSON.stringify(offlineLogs));
    
    // Update job status locally
    updateJobStatusLocally(currentJob.id, action);
    
    // Try to sync if online
    if (isOnline) {
        try {
            await syncOfflineData();
        } catch (error) {
            console.error('Failed to sync:', error);
        }
    }
    
    closeJobModal();
    displayJobs();
    displayOfflineLogs();
}

// Update job status locally
function updateJobStatusLocally(jobId, action) {
    const jobIndex = offlineJobs.findIndex(j => j.id === jobId);
    if (jobIndex === -1) return;
    
    const job = offlineJobs[jobIndex];
    
    switch (action) {
        case 'start':
            job.status = 'In Progress';
            job.start_time = new Date().toISOString();
            break;
        case 'end':
            job.status = 'Completed';
            job.end_time = new Date().toISOString();
            break;
        case 'pause':
            job.status = 'Paused';
            break;
        case 'resume':
            job.status = 'In Progress';
            break;
    }
    
    localStorage.setItem('offlineJobs', JSON.stringify(offlineJobs));
}

// Close job modal
function closeJobModal() {
    document.getElementById('job-modal').classList.add('hidden');
    currentJob = null;
}

// Load offline logs
async function loadOfflineLogs() {
    try {
        const response = await fetch('/offline/unsynced-logs');
        if (response.ok) {
            const data = await response.json();
            offlineLogs = data.logs;
        } else {
            // Load from localStorage if offline
            const stored = localStorage.getItem('offlineLogs');
            offlineLogs = stored ? JSON.parse(stored) : [];
        }
    } catch (error) {
        // Load from localStorage if offline
        const stored = localStorage.getItem('offlineLogs');
        offlineLogs = stored ? JSON.parse(stored) : [];
    }
    
    displayOfflineLogs();
}

// Display offline logs
function displayOfflineLogs() {
    const container = document.getElementById('offline-logs-container');
    const loading = document.getElementById('loading-logs');
    const noLogs = document.getElementById('no-logs');
    const logsList = document.getElementById('logs-list');
    
    loading.classList.add('hidden');
    
    if (offlineLogs.length === 0) {
        noLogs.classList.remove('hidden');
        logsList.classList.add('hidden');
        return;
    }
    
    noLogs.classList.add('hidden');
    logsList.classList.remove('hidden');
    
    logsList.innerHTML = offlineLogs.map(log => `
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center ${getActionColor(log.action)}">
                    <i class="fas ${getActionIcon(log.action)} text-white text-sm"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">${log.action.charAt(0).toUpperCase() + log.action.slice(1)} Job</p>
                    <p class="text-xs text-gray-500">${log.job?.order_number || 'Unknown'} - ${log.job?.client_name || 'Unknown'}</p>
                    <p class="text-xs text-gray-400">${new Date(log.action_time).toLocaleString()}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    <i class="fas fa-clock mr-1"></i>
                    Pending Sync
                </span>
            </div>
        </div>
    `).join('');
}

// Sync offline data
async function syncOfflineData() {
    if (offlineLogs.length === 0) {
        alert('No offline data to sync');
        return;
    }
    
    try {
        const response = await fetch('/offline/sync-logs', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ logs: offlineLogs })
        });
        
        if (response.ok) {
            const data = await response.json();
            alert(`Successfully synced ${data.synced_count} actions`);
            
            // Clear offline logs
            offlineLogs = [];
            localStorage.removeItem('offlineLogs');
            displayOfflineLogs();
            
            // Reload jobs
            loadOfflineJobs();
        } else {
            throw new Error('Sync failed');
        }
    } catch (error) {
        console.error('Sync error:', error);
        alert('Failed to sync offline data. Will retry when online.');
    }
}

// Check connection status
async function checkConnectionStatus() {
    try {
        const response = await fetch('/offline/check-status');
        if (response.ok) {
            const data = await response.json();
            isOnline = data.online;
            
            const indicator = document.getElementById('status-indicator');
            const text = document.getElementById('status-text');
            const syncBtn = document.getElementById('sync-button');
            
            if (isOnline) {
                indicator.className = 'w-3 h-3 rounded-full bg-green-500';
                text.textContent = 'Online';
                text.className = 'text-sm font-medium text-green-600';
                
                if (offlineLogs.length > 0) {
                    syncBtn.classList.remove('hidden');
                }
            } else {
                indicator.className = 'w-3 h-3 rounded-full bg-red-500';
                text.textContent = 'Offline';
                text.className = 'text-sm font-medium text-red-600';
                syncBtn.classList.add('hidden');
            }
        }
    } catch (error) {
        // Try a simple connection test
        try {
            const testResponse = await fetch('/api/health', { method: 'HEAD' });
            isOnline = testResponse.ok;
        } catch (testError) {
            isOnline = false;
        }
        
        const indicator = document.getElementById('status-indicator');
        const text = document.getElementById('status-text');
        const syncBtn = document.getElementById('sync-button');
        
        if (isOnline) {
            indicator.className = 'w-3 h-3 rounded-full bg-green-500';
            text.textContent = 'Online';
            text.className = 'text-sm font-medium text-green-600';
        } else {
            indicator.className = 'w-3 h-3 rounded-full bg-red-500';
            text.textContent = 'Offline';
            text.className = 'text-sm font-medium text-red-600';
        }
        syncBtn.classList.add('hidden');
    }
}

// Helper functions
function getStatusColor(status) {
    switch (status) {
        case 'Pending': return 'bg-yellow-100 text-yellow-800';
        case 'In Progress': return 'bg-blue-100 text-blue-800';
        case 'Completed': return 'bg-green-100 text-green-800';
        case 'Paused': return 'bg-orange-100 text-orange-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function getActionColor(action) {
    switch (action) {
        case 'start': return 'bg-green-500';
        case 'end': return 'bg-blue-500';
        case 'pause': return 'bg-yellow-500';
        case 'resume': return 'bg-green-500';
        default: return 'bg-gray-500';
    }
}

function getActionIcon(action) {
    switch (action) {
        case 'start': return 'fa-play';
        case 'end': return 'fa-check';
        case 'pause': return 'fa-pause';
        case 'resume': return 'fa-play';
        default: return 'fa-cog';
    }
}
</script>
@endsection 