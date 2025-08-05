# Fazztrack - T-Shirt Printing Management System

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10.x-red.svg" alt="Laravel Version">
  <img src="https://img.shields.io/badge/PHP-8.2+-blue.svg" alt="PHP Version">
  <img src="https://img.shields.io/badge/MySQL-8.0-orange.svg" alt="MySQL Version">
  <img src="https://img.shields.io/badge/Docker-Ready-green.svg" alt="Docker Ready">
  <img src="https://img.shields.io/badge/License-MIT-yellow.svg" alt="License">
</p>

<p align="center">
  <strong>A comprehensive web-based management system for T-shirt printing businesses</strong>
</p>

---

## 🎯 Overview

**Fazztrack** is a professional T-shirt printing management system designed for **FazzPrint Sdn Bhd**. The system streamlines the entire production workflow from client management to order fulfillment, featuring role-based access control, QR code tracking, offline support, and real-time status monitoring.

### ✨ Key Features

- **🎨 Complete Order Management** - From client registration to order fulfillment
- **👥 Role-Based Access Control** - SuperAdmin, Sales Manager, Designer, Production Staff
- **📱 QR Code Tracking** - Real-time job tracking with QR code scanning and manual input
- **📶 Offline Support** - Production staff can work without internet connectivity
- **📊 Real-Time Analytics** - Comprehensive dashboards with live data updates
- **🎨 Design Management** - Template system with approval workflow and file storage
- **⏱️ Time Tracking** - Automatic duration calculation for each production phase
- **📱 Mobile Responsive** - Modern UI with Tailwind CSS
- **🖨️ Work Orders** - Printable job sheets with design images and QR codes
- **📈 Live Progress Tracking** - Real-time order status and production progress

---

## 🏗️ System Architecture

### Technology Stack

- **Backend**: Laravel 10.x (PHP 8.2+)
- **Database**: MySQL 8.0
- **Frontend**: Tailwind CSS, Alpine.js, Chart.js
- **Containerization**: Docker & Docker Compose
- **QR Code**: SimpleSoftwareIO/simple-qrcode
- **Icons**: Font Awesome 6.4.0
- **Asset Building**: Vite

### System Requirements

- PHP 8.2 or higher
- MySQL 8.0 or higher
- Docker & Docker Compose
- Composer
- Node.js (for asset compilation)

---

## 🚀 Quick Start

### Prerequisites

```bash
# Install Docker and Docker Compose
# Clone the repository
git clone https://github.com/faiznasir/fazztrack.git
cd fazztrack
```

### Installation

```bash
# Start Docker containers
docker-compose up -d

# Install PHP dependencies
docker-compose exec app composer install

# Install Node.js dependencies
docker-compose exec app npm install

# Copy environment file
cp .env.example .env

# Generate application key
docker-compose exec app php artisan key:generate

# Run database migrations
docker-compose exec app php artisan migrate

# Seed the database
docker-compose exec app php artisan db:seed

# Build frontend assets
docker-compose exec app npm run build

# Set storage permissions
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Access the Application

- **Web Application**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080
- **Default Login**: 
  - SuperAdmin: `superadmin@fazztrack.com` / `superadmin123`
  - Sales Manager: `sales@fazztrack.com` / `sales123`
  - Designer: `designer@fazztrack.com` / `designer123`
  - Production Staff: `production@fazztrack.com` / `production123`

---

## 🎭 Role-Based Access Control

### SuperAdmin
- **Full System Access** - Complete control over all features
- **Advanced Analytics** - Comprehensive dashboards with live charts
- **User Management** - Create, edit, and manage all users
- **System Configuration** - Global settings and configurations
- **Job Management** - Create, edit, and assign production jobs

### Sales Manager
- **Client Management** - Add and manage clients
- **Order Creation** - Create and manage orders with design uploads
- **Payment Tracking** - Monitor payment status
- **Job Creation** - Create production jobs for each phase
- **Order Status Management** - Monitor and update order progress

### Designer
- **Design Upload** - Upload design files (front, back, left, right views)
- **Design Templates** - Create and manage reusable templates
- **Design History** - Track design versions and feedback
- **Template Library** - Access shared design templates

### Production Staff
- **QR Code Scanning** - Scan job QR codes to start/end work
- **Manual Input** - Enter job IDs manually when camera unavailable
- **Time Tracking** - Record start/end times with duration calculation
- **Phase Management** - Complete production phases in sequence
- **Remarks & Quantities** - Add notes and track quantities for QC phase
- **Offline Mode** - Work without internet connectivity

---

## 📋 Enhanced Order Status Workflow

### Complete Production Workflow

1. **Order Created** → Sales Manager creates new order
2. **Order Approved** → Admin approves payment with receipt
3. **Design Review** → Designer uploads design files (front, back, left, right)
4. **Design Approved** → Admin/Sales Manager approves design
5. **Job Created** → Sales Manager creates production jobs for each phase
6. **Job Start** → Production staff scans QR, starts first phase (PRINT)
7. **Job Complete** → All production phases completed
8. **Order Packaging** → IRON/PACKING phase starts
9. **Order Finished** → Shipping or self-collection

### Enhanced Order Status Progression

- **"Design Approved"** → **"Job Start"** (when first production job starts)
- **"Job Start"** → **"Job Complete"** (when QC phase completes)
- **"Job Complete"** → **"Order Packaging"** (when IRON/PACKING starts)
- **"Order Packaging"** → **"Order Finished"** (when all phases complete)

### Production Phases (Sequential Workflow)

- **PRINT** → T-shirt printing process
- **PRESS** → Heat press application
- **CUT** → Cutting and trimming
- **SEW** → Sewing and finishing
- **QC** → Quality control and inspection (with remarks & quantities)
- **IRON/PACKING** → Final ironing and packaging

---

## 🔧 Core Features

### Enhanced QR Code Tracking System

- **Job Identification** - Unique QR codes for each production job
- **QR Format** - `QR_{randomstring}_{PHASE}` (e.g., `QR_EVLrykvkjc_PRINT`)
- **Manual Input Fallback** - Enter job IDs directly when camera unavailable
- **Backend Lookup** - QR codes linked to jobs via database lookup
- **Test Buttons** - Quick testing with predefined job IDs
- **Phase Management** - QR codes contain phase and job information
- **Workflow Control** - Ensures phases are completed in sequence
- **Time Tracking** - Automatic start/end time recording with duration calculation
- **Status Updates** - Real-time job and order status updates

### Production Job Management

- **Start/End Jobs** - Production staff can start and end jobs with timestamps
- **Duration Calculation** - Automatic calculation of job duration in minutes
- **Remarks System** - Production staff can add notes when ending jobs
- **Quantity Tracking** - Track start/reject quantities for QC phase
- **Button State Management** - Buttons disabled when phase completed
- **Real-Time Updates** - Live status updates without page refresh
- **Error Handling** - Detailed error messages for access control

### Work Order System

- **Printable Job Sheets** - Professional work orders with design images
- **Design Display** - Show front, back, left, right design views
- **QR Code Integration** - QR codes displayed on work orders
- **Manual Job ID** - Prominent display of job ID for manual entry
- **Print/Download** - PDF generation and printing functionality
- **Customer Information** - Complete customer and order details
- **Job Specifications** - Phase, status, assigned user information

### Enhanced Design Management System

- **Multi-View Uploads** - Upload front, back, left, right design views
- **Laravel Storage** - Secure file storage with proper file management
- **JSON Field Storage** - Design files stored in JSON format
- **Accessor Methods** - Easy retrieval of individual design files
- **Drag-and-Drop Interface** - Modern upload interface with previews
- **File Validation** - Secure file type and size validation
- **Version Control** - Track design versions and feedback
- **Approval Workflow** - Admin/Sales Manager approval process
- **Template System** - Reusable design templates
- **Design History** - Complete audit trail of design changes

### Real-Time Analytics & Progress Tracking

- **Live Data Updates** - AJAX-powered real-time updates
- **Progress Calculation** - Based on 6 standard phases (not just existing jobs)
- **Order Status Sync** - Automatic order status updates based on production
- **Revenue Tracking** - Total and monthly revenue calculations
- **Order Statistics** - Order status distribution with live updates
- **Production Metrics** - Time tracking and efficiency metrics
- **Client Analytics** - Top clients and order history
- **Chart Visualizations** - Interactive charts and graphs

### Offline Support

- **Offline Mode** - Production staff can work without internet
- **Local Storage** - Actions saved to device storage
- **Auto Sync** - Data syncs when internet connection returns
- **Data Protection** - No data loss from battery/reload issues
- **Continuous Work** - Production doesn't stop for network issues

---

## 📱 Mobile Features

### Responsive Design
- **Mobile-First** - Optimized for mobile devices
- **Touch-Friendly** - Large buttons and touch targets
- **Modern UI** - Tailwind CSS with glass morphism effects
- **Offline Capability** - Works without internet connection
- **QR Scanner** - Camera-based QR code scanning with manual fallback
- **Real-Time Updates** - Live status and progress updates

### Production Staff Mobile Workflow
1. **Receive Work Order** - From Sales Manager with QR code and job ID
2. **Scan QR Code** - Use mobile camera or manual input
3. **Start Job** - Record start time and begin work
4. **Work on Phase** - Complete production work
5. **End Job** - Record end time, duration, remarks, and quantities
6. **Next Phase** - Pass to next production staff

### Enhanced Scanner Interface
- **Camera Integration** - Direct camera access with error handling
- **Manual Input** - Type job IDs directly (25, 26, etc.)
- **Test Buttons** - Quick testing with predefined job IDs
- **Error Messages** - Clear feedback for access control issues
- **Modal Interface** - Non-closing modals for better UX
- **Button States** - Dynamic button enable/disable based on job status

---

## 🗄️ Database Schema

### Core Tables

- **users** - User accounts and role management
- **clients** - Client information and contacts
- **orders** - Order details and status tracking
- **production_jobs** - Individual production jobs with timestamps
- **designs** - Design files and approval status
- **design_templates** - Reusable design templates
- **offline_job_logs** - Offline action tracking
- **contacts** - Client contact information

### Key Relationships

- **Orders** → **Clients** (Many-to-One)
- **Jobs** → **Orders** (Many-to-One)
- **Jobs** → **Users** (Many-to-One, assigned production staff)
- **Designs** → **Orders** (Many-to-One)
- **Designs** → **Users** (Many-to-One, designer)

### Enhanced Fields

- **orders.design_files** - JSON field storing design file paths
- **production_jobs.duration** - Calculated duration in minutes
- **production_jobs.remarks** - Production staff notes
- **production_jobs.start_quantity** - Initial quantity for QC phase
- **production_jobs.reject_quantity** - Rejected quantity for QC phase

---

## 🔒 Security Features

- **Role-Based Access Control** - Granular permissions per role
- **Authentication** - Secure login system
- **CSRF Protection** - Cross-site request forgery protection
- **Input Validation** - Comprehensive form validation
- **File Upload Security** - Secure file handling with validation
- **SQL Injection Protection** - Laravel's built-in protection
- **Access Control** - Production staff can only access assigned jobs

---

## 📊 API Endpoints

### Production Management
- `GET /jobs/scanner` - QR scanner interface
- `POST /jobs/{job}/start` - Start production job
- `POST /jobs/{job}/end` - End production job with remarks/quantities
- `GET /jobs/{job}/details` - Get job details
- `GET /jobs/qr/{qrCode}/details` - Get job details by QR code
- `GET /jobs/{job}/workflow` - Get workflow information

### Order Management
- `GET /orders/{order}/status` - Get real-time order status
- `POST /orders/{order}/jobs` - Create production jobs
- `GET /orders/{order}/tracking` - Get order tracking data

### Offline Support
- `GET /offline/jobs` - Get jobs for offline work
- `POST /offline/log-action` - Log offline action
- `POST /offline/sync-logs` - Sync offline data
- `GET /offline/check-status` - Check connection status

### Design Management
- `GET /designs` - List designs
- `POST /designs/{design}/approve` - Approve design
- `POST /designs/{design}/reject` - Reject design
- `GET /design-templates` - List design templates

---

## 🛠️ Development

### Local Development Setup

```bash
# Clone repository
git clone https://github.com/faiznasir/fazztrack.git
cd fazztrack

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Build assets
npm run build

# Start development server
php artisan serve
```

### Asset Building

```bash
# Development mode
npm run dev

# Production build
npm run build

# Watch for changes
npm run watch
```

### Testing

```bash
# Run tests
php artisan test

# Run specific test suite
php artisan test --filter=JobControllerTest
```

### Code Quality

```bash
# Run PHP CS Fixer
./vendor/bin/php-cs-fixer fix

# Run PHPStan
./vendor/bin/phpstan analyse

# Run PHPUnit
./vendor/bin/phpunit
```

---

## 📝 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

**Developed by:** Faiz Nasir  
**Company:** FazzPrint Sdn Bhd  
**Version:** 1.0.0  
**Last Updated:** December 2024

---

## 📞 Support

For support and questions:

- **Email:** fazzprint@gmail.com
- **Phone:** +03 3122 4889
- **Website:** https://fazzprint.com
- **Documentation:** https://docs.fazzprint.com

---

## 🙏 Acknowledgments

- **Laravel Framework** - For the robust PHP framework
- **Tailwind CSS** - For the beautiful UI components
- **Font Awesome** - For the comprehensive icon library
- **Docker** - For the containerization platform
- **MySQL** - For the reliable database system
- **Vite** - For the modern asset building
- **Chart.js** - For the interactive charts

---

<p align="center">
  <strong>Made with ❤️ by Faiz Nasir for FazzPrint Sdn Bhd</strong>
</p>
