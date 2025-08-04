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
- **👥 Role-Based Access Control** - SuperAdmin, Admin, Sales Manager, Designer, Production Staff
- **📱 QR Code Tracking** - Real-time job tracking with QR code scanning
- **📶 Offline Support** - Production staff can work without internet connectivity
- **📊 Real-Time Analytics** - Comprehensive dashboards with charts and reports
- **🎨 Design Management** - Template system with approval workflow
- **⏱️ Time Tracking** - Automatic duration calculation for each production phase
- **📱 Mobile Responsive** - Works seamlessly on all devices

---

## 🏗️ System Architecture

### Technology Stack

- **Backend**: Laravel 10.x (PHP 8.2+)
- **Database**: MySQL 8.0
- **Frontend**: Tailwind CSS, Alpine.js
- **Containerization**: Docker & Docker Compose
- **QR Code**: SimpleSoftwareIO/simple-qrcode
- **Icons**: Font Awesome 6.4.0

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

# Copy environment file
cp .env.example .env

# Generate application key
docker-compose exec app php artisan key:generate

# Run database migrations
docker-compose exec app php artisan migrate

# Seed the database
docker-compose exec app php artisan db:seed

# Set storage permissions
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Access the Application

- **Web Application**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080
- **Default Login**: 
  - SuperAdmin: `superadmin@fazztrack.com` / `superadmin123`
  - Admin: `admin@fazztrack.com` / `admin123`
  - Sales Manager: `sales@fazztrack.com` / `sales123`
  - Designer: `designer@fazztrack.com` / `designer123`
  - Production Staff: `production@fazztrack.com` / `production123`

---

## 🎭 Role-Based Access Control

### SuperAdmin
- **Full System Access** - Complete control over all features
- **Advanced Analytics** - Comprehensive dashboards with charts
- **User Management** - Create, edit, and manage all users
- **System Configuration** - Global settings and configurations

### Admin
- **Order Approval** - Approve payments and designs
- **Design Review** - Review and approve/reject designs
- **Production Oversight** - Monitor production progress
- **Financial Tracking** - Revenue and payment tracking

### Sales Manager
- **Client Management** - Add and manage clients
- **Order Creation** - Create and manage orders
- **Payment Tracking** - Monitor payment status
- **Job QR Generation** - Generate QR codes for production jobs

### Designer
- **Design Upload** - Upload design files for orders
- **Design Templates** - Create and manage reusable templates
- **Design History** - Track design versions and feedback
- **Template Library** - Access shared design templates

### Production Staff
- **QR Code Scanning** - Scan job QR codes to start/end work
- **Offline Mode** - Work without internet connectivity
- **Time Tracking** - Record start/end times for each phase
- **Phase Management** - Complete production phases in sequence

---

## 📋 Order Status Workflow

### Complete Production Workflow

1. **Order Created** → Sales Manager creates new order
2. **Order Approved** → Admin approves payment with receipt
3. **Design Review** → Designer uploads design, gets feedback
4. **Design Approved** → Admin/Sales Manager approves design
5. **Job Created** → Sales Manager creates production jobs
6. **Job Start** → Production staff scans QR, starts first phase
7. **Job Complete** → All production phases completed
8. **Order Packaging** → IRON/PACKING phase starts
9. **Order Finished** → Shipping or self-collection

### Production Phases

- **PRINT** → T-shirt printing process
- **PRESS** → Heat press application
- **CUT** → Cutting and trimming
- **SEW** → Sewing and finishing
- **QC** → Quality control and inspection
- **IRON/PACKING** → Final ironing and packaging

---

## 🔧 Core Features

### QR Code Tracking System

- **Job Identification** - Unique QR codes for each production job
- **Phase Management** - QR codes contain phase and job information
- **Workflow Control** - Ensures phases are completed in sequence
- **Time Tracking** - Automatic start/end time recording
- **Status Updates** - Real-time job and order status updates

### Offline Support

- **Offline Mode** - Production staff can work without internet
- **Local Storage** - Actions saved to device storage
- **Auto Sync** - Data syncs when internet connection returns
- **Data Protection** - No data loss from battery/reload issues
- **Continuous Work** - Production doesn't stop for network issues

### Design Management System

- **Design Upload** - Multiple file formats (PSD, AI, JPG, PNG, PDF, ZIP)
- **Version Control** - Track design versions and feedback
- **Approval Workflow** - Admin/Sales Manager approval process
- **Template System** - Reusable design templates
- **Design History** - Complete audit trail of design changes

### Real-Time Analytics

- **Revenue Tracking** - Total and monthly revenue calculations
- **Order Statistics** - Order status distribution
- **Production Metrics** - Time tracking and efficiency
- **Client Analytics** - Top clients and order history
- **Chart Visualizations** - Interactive charts and graphs

---

## 📱 Mobile Features

### Responsive Design
- **Mobile-First** - Optimized for mobile devices
- **Touch-Friendly** - Large buttons and touch targets
- **Offline Capability** - Works without internet connection
- **QR Scanner** - Camera-based QR code scanning
- **Real-Time Updates** - Live status and progress updates

### Production Staff Mobile Workflow
1. **Receive QR Sheet** - From Sales Manager
2. **Scan QR Code** - Use mobile camera
3. **Start Job** - Record start time
4. **Work on Phase** - Complete production work
5. **End Job** - Record end time and duration
6. **Next Phase** - Pass to next production staff

---

## 🗄️ Database Schema

### Core Tables

- **users** - User accounts and role management
- **clients** - Client information and contacts
- **orders** - Order details and status tracking
- **production_jobs** - Individual production jobs
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

---

## 🔒 Security Features

- **Role-Based Access Control** - Granular permissions per role
- **Authentication** - Secure login system
- **CSRF Protection** - Cross-site request forgery protection
- **Input Validation** - Comprehensive form validation
- **File Upload Security** - Secure file handling
- **SQL Injection Protection** - Laravel's built-in protection

---

## 📊 API Endpoints

### Production Management
- `GET /jobs/scanner` - QR scanner interface
- `POST /jobs/{job}/start` - Start production job
- `POST /jobs/{job}/end` - End production job
- `GET /jobs/{job}/details` - Get job details
- `GET /jobs/{job}/workflow` - Get workflow information

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

# Start development server
php artisan serve
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

---

<p align="center">
  <strong>Made with ❤️ by Faiz Nasir for FazzPrint Sdn Bhd</strong>
</p>
