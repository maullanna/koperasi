[README_Koperasi.md](https://github.com/user-attachments/files/28192477/README_Koperasi.md)
# Kopkar Sejahtera — Payroll & Cooperative Management System

> A full-stack web-based cooperative management system built for an internal company cooperative (Koperasi Karyawan), featuring member management, savings & loans, installment tracking, payroll, and financial reporting — with role-based access for Employee, Staff, and Super Admin.

[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-3.x-EF4223?style=flat&logo=codeigniter&logoColor=white)](https://codeigniter.com)
[![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)](https://mysql.com)
[![Status](https://img.shields.io/badge/Status-Live%20(Private%20Network)-orange)](https://payrolkopkarsejahtera.com)
[![Role](https://img.shields.io/badge/Role-Solo%20Developer-blue)](https://maullanna.github.io/Portofolio)

---

## 📌 Overview

Kopkar Sejahtera is a web-based cooperative and payroll management system developed independently for a company's internal cooperative (Koperasi Karyawan). The system digitizes and automates the entire cooperative workflow — from member registration and savings management to loan processing, installment tracking, payroll calculation, and financial reporting.

The system is deployed on the client's private internal network and accessible only within the company's local infrastructure.

> **Note:** This system is deployed on a private internal network. A live demo is not publicly accessible for security reasons.

---

## ✨ Features

### Member Management
| Feature | Description |
|---|---|
| 👥 **Member Registration** | Register and manage cooperative members |
| 📋 **Member Profiles** | Complete member data with employment details |
| 🔍 **Member Directory** | Search and filter member records |

### Savings & Loans (Simpan Pinjam)
| Feature | Description |
|---|---|
| 💰 **Savings Management** | Track mandatory and voluntary savings per member |
| 📝 **Loan Applications** | Submit and process loan requests |
| ✅ **Loan Approval Workflow** | Multi-level approval process for loan disbursement |
| 📊 **Loan History** | Full loan history per member |

### Installment Management (Cicilan)
| Feature | Description |
|---|---|
| 📅 **Installment Scheduling** | Automatic installment schedule generation |
| 💳 **Payment Tracking** | Record and track monthly installment payments |
| ⚠️ **Overdue Alerts** | Flag overdue installments |
| 📈 **Installment Reports** | Detailed reports per member and period |

### Payroll Management (Gaji)
| Feature | Description |
|---|---|
| 💵 **Salary Calculation** | Automated salary calculation per employee |
| ➕ **Deduction Integration** | Auto-deduct loan installments from payroll |
| 📄 **Payslip Generation** | Generate payslips per employee per period |
| 📆 **Payroll Period** | Monthly payroll cycle management |

### Financial Reporting (Laporan Keuangan)
| Feature | Description |
|---|---|
| 📊 **Financial Summary** | Overview of cooperative financial position |
| 📋 **Transaction Reports** | Detailed income and expense reports |
| 📅 **Period-based Reports** | Filter reports by date range or month |
| 🖨️ **Export/Print** | Print-ready financial report layouts |

### Role-Based Access Control
| Role | Access Level |
|---|---|
| 👤 **Employee (Karyawan)** | View own payslip, savings balance, loan status, installment schedule |
| 🧑‍💼 **Staff** | Manage members, process transactions, generate reports |
| 👑 **Super Admin** | Full system access — all modules, user management, system configuration, dashboard |

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| **Backend** | PHP 8.0+, CodeIgniter 3.x |
| **Frontend** | HTML5, CSS3, JavaScript, Bootstrap |
| **Database** | MySQL 8.0 |
| **Architecture** | MVC (Model-View-Controller) |
| **Deployment** | Private internal network / local server |
| **Version Control** | Git & GitHub |

---

## 🗂️ Project Structure

```
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
```

---

## ⚙️ Installation

### Prerequisites
- PHP 8.0+
- MySQL 8.0
- Apache/Nginx web server

### Setup

```bash
# Clone repository
git clone https://github.com/maullanna/koperasi.git
cd koperasi
```

### Database Configuration

Edit `application/config/database.php`:

```php
$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'your_db_user',
    'password' => 'your_db_password',
    'database' => 'kopkar_sejahtera',
    'dbdriver' => 'mysqli',
);
```

### Base URL Configuration

Edit `application/config/config.php`:

```php
$config['base_url'] = 'http://localhost/koperasi/';
```

### Import Database

```bash
mysql -u root -p kopkar_sejahtera < database/kopkar_sejahtera.sql
```

### Default Admin Account

```
URL    : http://localhost/koperasi/auth/login
Email  : admin@kopkar.com
Password : admin123
```

> ⚠️ Change the default password immediately after first login.

---

## 🔐 Security

- **CSRF Protection** — Enabled on all form submissions
- **XSS Prevention** — Input filtering via CodeIgniter Security class
- **SQL Injection Prevention** — Active Record / Query Builder for all queries
- **Role-based Access Control** — Middleware-level route protection per role
- **Session Management** — Secure session handling with timeout
- **Password Hashing** — Passwords stored with secure hashing

---

## 👨‍💻 Developer

**Yusuf Maulana** — Solo Developer (Design, Backend, Deployment)

- 🌐 Portfolio: [maullanna.github.io/Portofolio](https://maullanna.github.io/Portofolio)
- 💼 LinkedIn: [linkedin.com/in/yusuf-maulana-a3888736a](https://www.linkedin.com/in/yusuf-maulana-a3888736a)
- 📧 Email: maullanna35@gmail.com

---

## 📄 License

This project is licensed under the MIT License.
