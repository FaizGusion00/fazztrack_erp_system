<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #{{ $order->order_id }} - Fazztrack</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#1E90FF',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
        
        .progress-ring {
            transform: rotate(-90deg);
        }
        
        .progress-ring-circle {
            transition: stroke-dashoffset 0.35s;
            transform-origin: 50% 50%;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-sm shadow-sm border-b border-white/20 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="/" class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tshirt text-white text-sm"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900">Fazztrack</span>
                </a>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('tracking.search') }}" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors">
                        <i class="fas fa-search mr-2"></i>
                        <span class="hidden sm:inline">Track Another Order</span>
                        <span class="sm:hidden">Track</span>
                    </a>
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        <span class="hidden sm:inline">Staff Login</span>
                        <span class="sm:hidden">Login</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <!-- Order Header -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-white/20 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-white text-lg sm:text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Order #{{ $order->order_id }}</h1>
                        <p class="text-lg sm:text-xl text-gray-600">{{ $order->job_name }}</p>
                    </div>
                </div>
                <div class="mt-4 sm:mt-0">
                    @php
                        $statusColors = [
                            'Order Created' => 'bg-gray-100 text-gray-800',
                            'Order Approved' => 'bg-blue-100 text-blue-800',
                            'Design Review' => 'bg-yellow-100 text-yellow-800',
                            'Design Approved' => 'bg-purple-100 text-purple-800',
                            'Job Start' => 'bg-green-100 text-green-800',
                            'Job Complete' => 'bg-indigo-100 text-indigo-800',
                            'Order Finished' => 'bg-emerald-100 text-emerald-800',
                            'On Hold' => 'bg-red-100 text-red-800'
                        ];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $order->status }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Progress Overview -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-white/20 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-chart-line mr-2 text-blue-500"></i>
                    Order Progress
                </h2>
                <div id="last-updated" class="text-sm text-gray-500">
                    <i class="fas fa-clock mr-1"></i>
                    <span id="update-time">Just now</span>
                </div>
            </div>
            
            @php
                $totalPhases = 6; // PRINT, PRESS, CUT, SEW, QC, IRON/PACKING
                $completedJobs = $order->jobs->where('status', 'Completed')->count();
                $inProgressJobs = $order->jobs->where('status', 'In Progress')->count();
                $pendingJobs = $order->jobs->where('status', 'Pending')->count();
                $progressPercentage = $totalPhases > 0 ? ($completedJobs / $totalPhases) * 100 : 0;
            @endphp
            
            <!-- Progress Ring -->
            <div class="flex items-center justify-center mb-6">
                <div class="relative">
                    <svg class="w-32 h-32 sm:w-40 sm:h-40" viewBox="0 0 120 120">
                        <circle cx="60" cy="60" r="54" fill="none" stroke="#e5e7eb" stroke-width="8"/>
                        <circle cx="60" cy="60" r="54" fill="none" stroke="#1E90FF" stroke-width="8" 
                                stroke-dasharray="{{ 2 * pi() * 54 }}" 
                                stroke-dashoffset="{{ 2 * pi() * 54 * (1 - $progressPercentage / 100) }}"
                                class="progress-ring-circle"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-2xl sm:text-3xl font-bold text-gray-900">{{ round($progressPercentage) }}%</div>
                            <div class="text-sm text-gray-500">Complete</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Progress Stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-xl">
                    <div class="text-2xl font-bold text-blue-600">{{ $totalPhases }}</div>
                    <div class="text-sm text-blue-600 font-medium">Total Phases</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-xl">
                    <div class="text-2xl font-bold text-green-600">{{ $completedJobs }}</div>
                    <div class="text-sm text-green-600 font-medium">Completed</div>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-xl">
                    <div class="text-2xl font-bold text-yellow-600">{{ $inProgressJobs }}</div>
                    <div class="text-sm text-yellow-600 font-medium">In Progress</div>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-xl">
                    <div class="text-2xl font-bold text-gray-600">{{ $pendingJobs }}</div>
                    <div class="text-sm text-gray-600 font-medium">Pending</div>
                </div>
            </div>
        </div>

        <!-- Production Steps -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-white/20 p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-tasks mr-2 text-blue-500"></i>
                Production Steps
            </h2>
            
            <div class="space-y-4" id="production-steps">
                @php
                    $phases = ['PRINT', 'PRESS', 'CUT', 'SEW', 'QC', 'IRON/PACKING'];
                @endphp
                
                @foreach($phases as $index => $phase)
                    @php
                        $job = $order->jobs->where('phase', $phase)->first();
                        $status = $job ? $job->status : 'Pending';
                        
                        $stepClass = 'bg-gray-50 border-gray-200';
                        $stepIcon = 'fas fa-clock text-gray-400';
                        $stepText = 'text-gray-600';
                        
                        if ($status === 'Completed') {
                            $stepClass = 'bg-green-50 border-green-200';
                            $stepIcon = 'fas fa-check-circle text-green-500';
                            $stepText = 'text-green-700';
                        } elseif ($status === 'In Progress') {
                            $stepClass = 'bg-blue-50 border-blue-200';
                            $stepIcon = 'fas fa-play-circle text-blue-500 animate-pulse';
                            $stepText = 'text-blue-700';
                        }
                    @endphp
                    
                    <div class="border-2 rounded-xl p-4 {{ $stepClass }} transition-all duration-300" data-phase="{{ $phase }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm">
                                    <i class="{{ $stepIcon }} text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold {{ $stepText }}">{{ $phase }}</h3>
                                    <p class="text-sm text-gray-500">
                                        @if($job && $job->status === 'Completed')
                                            Completed on {{ $job->end_time ? $job->end_time->format('M d, Y H:i') : 'N/A' }}
                                            @if($job->duration)
                                                ({{ $job->duration }} min)
                                            @endif
                                        @elseif($job && $job->status === 'In Progress')
                                            Started on {{ $job->start_time ? $job->start_time->format('M d, Y H:i') : 'N/A' }}
                                        @else
                                            Pending
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($status === 'Completed') bg-green-100 text-green-800
                                    @elseif($status === 'In Progress') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $status }}
                                </span>
                                @if($job && $job->assignedUser)
                                    <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600 text-xs"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Order Details -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Client Information -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-white/20 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-user mr-2 text-blue-500"></i>
                    Client Information
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Name:</span>
                        <span class="font-medium">{{ $order->client->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Phone:</span>
                        <span class="font-medium">{{ $order->client->phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Email:</span>
                        <span class="font-medium">{{ $order->client->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Type:</span>
                        <span class="font-medium">{{ $order->client->customer_type }}</span>
                    </div>
                </div>
            </div>

            <!-- Order Information -->
            <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm border border-white/20 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                    Order Information
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Delivery:</span>
                        <span class="font-medium">{{ $order->delivery_method }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Design Due:</span>
                        <span class="font-medium">{{ $order->due_date_design->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Production Due:</span>
                        <span class="font-medium">{{ $order->due_date_production->format('M d, Y') }}</span>
                    </div>
                    @if($order->remarks)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Remarks:</span>
                            <span class="font-medium text-right max-w-xs">{{ $order->remarks }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white/80 backdrop-blur-sm border-t border-white/20 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center">
                <p class="text-gray-600 flex items-center justify-center">
                    <i class="fas fa-tshirt mr-2 text-blue-500"></i>
                    Fazztrack T-Shirt Printing Management System
                </p>
            </div>
        </div>
    </footer>

    <!-- Auto-refresh script -->
    <script>
        // Update timestamp
        function updateTimestamp() {
            const now = new Date();
            const timeString = now.toLocaleTimeString();
            document.getElementById('update-time').textContent = timeString;
        }

        // Auto-refresh data every 30 seconds
        function refreshData() {
            fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update progress stats
                const progressStats = document.querySelectorAll('.grid.grid-cols-2 .text-2xl');
                if (progressStats.length >= 4) {
                    progressStats[0].textContent = data.progress.total_phases;
                    progressStats[1].textContent = data.progress.completed;
                    progressStats[2].textContent = data.progress.in_progress;
                    progressStats[3].textContent = data.progress.pending;
                }
                
                // Update progress percentage
                const percentageElement = document.querySelector('.text-2xl.sm\\:text-3xl');
                if (percentageElement) {
                    percentageElement.textContent = Math.round(data.progress.percentage) + '%';
                }
                
                // Update production steps
                const phases = ['PRINT', 'PRESS', 'CUT', 'SEW', 'QC', 'IRON/PACKING'];
                phases.forEach((phase, index) => {
                    const stepElement = document.querySelector(`[data-phase="${phase}"]`);
                    if (stepElement) {
                        const job = data.jobs.find(j => j.phase === phase);
                        const status = job ? job.status : 'Pending';
                        
                        // Update status badge
                        const statusBadge = stepElement.querySelector('.badge');
                        if (statusBadge) {
                            statusBadge.textContent = status;
                            statusBadge.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                status === 'Completed' ? 'bg-green-100 text-green-800' :
                                status === 'In Progress' ? 'bg-blue-100 text-blue-800' :
                                'bg-gray-100 text-gray-800'
                            }`;
                        }
                        
                        // Update icon
                        const icon = stepElement.querySelector('.fas');
                        if (icon) {
                            icon.className = `fas ${
                                status === 'Completed' ? 'fa-check-circle text-green-500' :
                                status === 'In Progress' ? 'fa-play-circle text-blue-500 animate-pulse' :
                                'fa-clock text-gray-400'
                            } text-lg`;
                        }
                        
                        // Update step class
                        stepElement.className = `border-2 rounded-xl p-4 transition-all duration-300 ${
                            status === 'Completed' ? 'bg-green-50 border-green-200' :
                            status === 'In Progress' ? 'bg-blue-50 border-blue-200' :
                            'bg-gray-50 border-gray-200'
                        }`;
                        
                        // Update description
                        const description = stepElement.querySelector('.text-sm.text-gray-500');
                        if (description && job) {
                            if (status === 'Completed') {
                                const endTime = new Date(job.end_time).toLocaleDateString('en-US', {
                                    month: 'short',
                                    day: 'numeric',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });
                                description.textContent = `Completed on ${endTime}${job.duration ? ` (${job.duration} min)` : ''}`;
                            } else if (status === 'In Progress') {
                                const startTime = new Date(job.start_time).toLocaleDateString('en-US', {
                                    month: 'short',
                                    day: 'numeric',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });
                                description.textContent = `Started on ${startTime}`;
                            } else {
                                description.textContent = 'Pending';
                            }
                        }
                    }
                });
                
                updateTimestamp();
            })
            .catch(error => {
                console.log('Auto-refresh failed:', error);
            });
        }

        // Initialize
        updateTimestamp();
        
        // Set up auto-refresh every 30 seconds
        setInterval(refreshData, 30000);
        
        // Also refresh when page becomes visible
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                refreshData();
            }
        });
    </script>
</body>
</html> 