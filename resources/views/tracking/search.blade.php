<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Order - Fazztrack</title>
    
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
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0066cc 100%);
            color: white;
            padding: 80px 0;
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
                <a class="nav-link" href="{{ route('login') }}">
                    <i class="fas fa-sign-in-alt me-1"></i>Staff Login
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">
                        <i class="fas fa-search me-3"></i>Track Your Order
                    </h1>
                    <p class="lead mb-5">
                        Enter your order ID to track the progress of your T-shirt printing order
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Form -->
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <i class="fas fa-qrcode fa-3x text-primary mb-3"></i>
                                <h3 class="fw-bold">Order Tracking</h3>
                                <p class="text-muted">Find your order by entering the order ID</p>
                            </div>

                            <form method="POST" action="{{ route('tracking.search.post') }}">
                                @csrf
                                
                                <div class="mb-4">
                                    <label for="order_id" class="form-label">Order ID</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-hashtag"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control @error('order_id') is-invalid @enderror" 
                                               id="order_id" 
                                               name="order_id" 
                                               value="{{ old('order_id') }}" 
                                               placeholder="Enter your order ID (e.g., 1, 2, 3...)" 
                                               required 
                                               autofocus>
                                        @error('order_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-search me-2"></i>Track Order
                                    </button>
                                </div>
                            </form>

                            <div class="text-center mt-4">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Don't have your order ID? Contact our sales team.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <i class="fas fa-clock fa-2x text-primary mb-3"></i>
                            <h5 class="fw-bold">Real-time Updates</h5>
                            <p class="text-muted">Track your order progress in real-time with detailed status updates.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <i class="fas fa-mobile-alt fa-2x text-primary mb-3"></i>
                            <h5 class="fw-bold">Mobile Friendly</h5>
                            <p class="text-muted">Access your order status from any device, anywhere, anytime.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <i class="fas fa-shield-alt fa-2x text-primary mb-3"></i>
                            <h5 class="fw-bold">Secure Tracking</h5>
                            <p class="text-muted">Your order information is secure and only accessible with your order ID.</p>
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