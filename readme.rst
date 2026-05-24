Kopkar Sejahtera — Payroll & Cooperative Management System

A full-stack web-based cooperative management system built for an internal company cooperative (Koperasi Karyawan), featuring member management, savings & loans, installment tracking, payroll, and financial reporting — with role-based access for Employee, Staff, and Super Admin.

Show Image
Show Image
Show Image
Show Image
Show Image

📌 Overview
Kopkar Sejahtera is a web-based cooperative and payroll management system developed independently for a company's internal cooperative (Koperasi Karyawan). The system digitizes and automates the entire cooperative workflow — from member registration and savings management to loan processing, installment tracking, payroll calculation, and financial reporting.
The system is deployed on the client's private internal network and accessible only within the company's local infrastructure.

Note: This system is deployed on a private internal network. A live demo is not publicly accessible for security reasons.


✨ Features
Member Management
FeatureDescription👥 Member RegistrationRegister and manage cooperative members📋 Member ProfilesComplete member data with employment details🔍 Member DirectorySearch and filter member records
Savings & Loans (Simpan Pinjam)
FeatureDescription💰 Savings ManagementTrack mandatory and voluntary savings per member📝 Loan ApplicationsSubmit and process loan requests✅ Loan Approval WorkflowMulti-level approval process for loan disbursement📊 Loan HistoryFull loan history per member
Installment Management (Cicilan)
FeatureDescription📅 Installment SchedulingAutomatic installment schedule generation💳 Payment TrackingRecord and track monthly installment payments⚠️ Overdue AlertsFlag overdue installments📈 Installment ReportsDetailed reports per member and period
Payroll Management (Gaji)
FeatureDescription💵 Salary CalculationAutomated salary calculation per employee➕ Deduction IntegrationAuto-deduct loan installments from payroll📄 Payslip GenerationGenerate payslips per employee per period📆 Payroll PeriodMonthly payroll cycle management
Financial Reporting (Laporan Keuangan)
FeatureDescription📊 Financial SummaryOverview of cooperative financial position📋 Transaction ReportsDetailed income and expense reports📅 Period-based ReportsFilter reports by date range or month🖨️ Export/PrintPrint-ready financial report layouts
Role-Based Access Control
RoleAccess Level👤 Employee (Karyawan)View own payslip, savings balance, loan status, installment schedule🧑‍💼 StaffManage members, process transactions, generate reports👑 Super AdminFull system access — all modules, user management, system configuration, dashboard

🛠️ Tech Stack
LayerTechnologyBackendPHP 7.4+, CodeIgniter 3.xFrontendHTML5, CSS3, JavaScript, BootstrapDatabaseMySQL 8.0ArchitectureMVC (Model-View-Controller)DeploymentPrivate internal network / local serverVersion ControlGit & GitHub

🗂️ Project Structure
koperasi/
├── application/
│   ├── controllers/
│   │   ├── Auth.php                  # Login & session management
│   │   ├── Dashboard.php             # Role-based dashboard
│   │   ├── Anggota.php               # Member management
│   │   ├── SimpanPinjam.php          # Savings & loans
│   │   ├── Cicilan.php               # Installment tracking
│   │   ├── Gaji.php                  # Payroll management
│   │   ├── Laporan.php               # Financial reports
│   │   └── Admin/
│   │       └── UserManagement.php    # User & role management (Super Admin)
│   ├── models/
│   │   ├── Anggota_model.php
│   │   ├── Simpan_model.php
│   │   ├── Pinjam_model.php
│   │   ├── Cicilan_model.php
│   │   ├── Gaji_model.php
│   │   └── Laporan_model.php
│   └── views/
│       ├── auth/                     # Login pages
│       ├── dashboard/                # Role-based dashboards
│       ├── anggota/                  # Member views
│       ├── simpanpinjam/             # Savings & loan views
│       ├── cicilan/                  # Installment views
│       ├── gaji/                     # Payroll views
│       └── laporan/                  # Report views
├── assets/
│   ├── css/
│   └── js/
└── README.md

⚙️ Installation
Prerequisites

PHP 7.4+
MySQL 8.0
Apache/Nginx web server

Setup
bash# Clone repository
git clone https://github.com/maullanna/koperasi.git
cd koperasi
Database Configuration
Edit application/config/database.php:
php$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'your_db_user',
    'password' => 'your_db_password',
    'database' => 'kopkar_sejahtera',
    'dbdriver' => 'mysqli',
);
Base URL Configuration
Edit application/config/config.php:
php$config['base_url'] = 'http://localhost/koperasi/';
Import Database
bashmysql -u root -p kopkar_sejahtera < database/kopkar_sejahtera.sql
Default Admin Account
URL    : http://localhost/koperasi/auth/login
Email  : admin@kopkar.com
Password : admin123

⚠️ Change the default password immediately after first login.


🔐 Security

CSRF Protection — Enabled on all form submissions
XSS Prevention — Input filtering via CodeIgniter Security class
SQL Injection Prevention — Active Record / Query Builder for all queries
Role-based Access Control — Middleware-level route protection per role
Session Management — Secure session handling with timeout
Password Hashing — Passwords stored with secure hashing


👨‍💻 Developer
Yusuf Maulana — Solo Developer (Design, Backend, Deployment)

🌐 Portfolio: maullanna.github.io/Portofolio
💼 LinkedIn: linkedin.com/in/yusuf-maulana-a3888736a
📧 Email: maullanna35@gmail.com


📄 License
This project is licensed under the MIT License.
