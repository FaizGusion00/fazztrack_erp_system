<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #{{ $order->order_id }} - Fazztrack</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #1E90FF;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
        }
        
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .progress-custom {
            height: 25px;
            border-radius: 15px;
        }
        
        .status-step {
            position: relative;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        
        .status-step.completed {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }
        
        .status-step.current {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
        }
        
        .status-step.pending {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-tshirt me-2"></i>Fazztrack
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('tracking.search') }}">
                    <i class="fas fa-search me-1"></i>Track Another Order
                </a>
                <a class="nav-link" href="{{ route('login') }}">
                    <i class="fas fa-sign-in-alt me-1"></i>Staff Login
                </a>
            </div>
        </div>
    </nav>

    <!-- Order Details -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Order Header -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h2 class="mb-2">
                                        <i class="fas fa-shopping-cart me-2 text-primary"></i>
                                        Order #{{ $order->order_id }}
                                    </h2>
                                    <p class="text-muted mb-0">{{ $order->job_name }}</p>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    @php
                                        $statusColors = [
                                            'Pending' => 'warning',
                                            'Approved' => 'info',
                                            'On Hold' => 'danger',
                                            'In Progress' => 'primary',
                                            'Completed' => 'success'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }} fs-6">
                                        {{ $order->status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="fas fa-chart-line me-2"></i>Order Progress
                            </h5>
                            
                            @php
                                $totalJobs = $order->jobs->count();
                                $completedJobs = $order->jobs->where('status', 'Completed')->count();
                                $progressPercentage = $totalJobs > 0 ? ($completedJobs / $totalJobs) * 100 : 0;
                            @endphp
                            
                            <div class="progress progress-custom mb-3">
                                <div class="progress-bar bg-success" 
                                     role="progressbar" 
                                     style="width: {{ $progressPercentage }}%" 
                                     aria-valuenow="{{ $progressPercentage }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    {{ round($progressPercentage) }}%
                                </div>
                            </div>
                            
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <h4 class="text-primary">{{ $totalJobs }}</h4>
                                    <small class="text-muted">Total Steps</small>
                                </div>
                                <div class="col-md-3">
                                    <h4 class="text-success">{{ $completedJobs }}</h4>
                                    <small class="text-muted">Completed</small>
                                </div>
                                <div class="col-md-3">
                                    <h4 class="text-warning">{{ $order->jobs->where('status', 'In Progress')->count() }}</h4>
                                    <small class="text-muted">In Progress</small>
                                </div>
                                <div class="col-md-3">
                                    <h4 class="text-secondary">{{ $order->jobs->where('status', 'Pending')->count() }}</h4>
                                    <small class="text-muted">Pending</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Job Steps -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="fas fa-tasks me-2"></i>Production Steps
                            </h5>
                            
                            @foreach($order->jobs as $job)
                                @php
                                    $stepClass = 'pending';
                                    $stepIcon = 'fas fa-clock';
                                    if ($job->status === 'Completed') {
                                        $stepClass = 'completed';
                                        $stepIcon = 'fas fa-check-circle';
                                    } elseif ($job->status === 'In Progress') {
                                        $stepClass = 'current';
                                        $stepIcon = 'fas fa-play-circle';
                                    }
                                @endphp
                                
                                <div class="status-step {{ $stepClass }}">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="mb-1">
                                                <i class="{{ $stepIcon }} me-2"></i>
                                                {{ $job->phase }}
                                            </h6>
                                            <small class="text-muted">
                                                @if($job->status === 'Completed')
                                                    Completed on {{ $job->end_time ? $job->end_time->format('M d, Y H:i') : 'N/A' }}
                                                    @if($job->duration)
                                                        (Duration: {{ $job->duration }} minutes)
                                                    @endif
                                                @elseif($job->status === 'In Progress')
                                                    Started on {{ $job->start_time ? $job->start_time->format('M d, Y H:i') : 'N/A' }}
                                                @else
                                                    Pending
                                                @endif
                                            </small>
                                        </div>
                                        <div class="col-md-4 text-md-end">
                                            <span class="badge bg-{{ $statusColors[$job->status] ?? 'secondary' }}">
                                                {{ $job->status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Order Details -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-user me-2"></i>Client Information
                                    </h6>
                                    <p class="mb-1"><strong>Name:</strong> {{ $order->client->name }}</p>
                                    <p class="mb-1"><strong>Phone:</strong> {{ $order->client->phone }}</p>
                                    <p class="mb-1"><strong>Email:</strong> {{ $order->client->email }}</p>
                                    <p class="mb-0"><strong>Type:</strong> {{ $order->client->customer_type }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-info-circle me-2"></i>Order Information
                                    </h6>
                                    <p class="mb-1"><strong>Delivery:</strong> {{ $order->delivery_method }}</p>
                                    <p class="mb-1"><strong>Design Due:</strong> {{ $order->due_date_design->format('M d, Y') }}</p>
                                    <p class="mb-1"><strong>Production Due:</strong> {{ $order->due_date_production->format('M d, Y') }}</p>
                                    @if($order->remarks)
                                        <p class="mb-0"><strong>Remarks:</strong> {{ $order->remarks }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container text-center">
            <p class="mb-0">
                <i class="fas fa-tshirt me-2"></i>
                Fazztrack T-Shirt Printing Management System
            </p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 