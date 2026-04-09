<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Pharma ERP | Smart Pharmacy Management System</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 (free CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(145deg, #f8fafd 0%, #f0f4fa 100%);
            color: #1e2a3e;
            overflow-x: hidden;
        }

        /* Glassmorphism & card styles */
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 2rem;
            box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(156, 188, 226, 0.2);
            transition: all 0.3s ease;
        }

        .card-hover {
            transition: transform 0.25s ease, box-shadow 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-6px);
            box-shadow: 0 25px 40px -14px rgba(0, 80, 120, 0.2);
        }

        .feature-card {
            background: white;
            border-radius: 1.5rem;
            border: none;
            transition: all 0.25s;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.02), 0 2px 6px rgba(0, 0, 0, 0.03);
        }

        .feature-card:hover {
            background: #ffffff;
            box-shadow: 0 20px 30px -12px rgba(37, 99, 235, 0.12);
            border-bottom: 2px solid #3b82f6;
        }

        .btn-gradient {
            background: linear-gradient(105deg, #2563eb 0%, #1e40af 100%);
            border: none;
            padding: 0.75rem 1.8rem;
            font-weight: 600;
            color: white;
            border-radius: 2.5rem;
            transition: all 0.2s;
            box-shadow: 0 8px 18px -6px rgba(37, 99, 235, 0.4);
        }

        .btn-gradient:hover {
            transform: scale(1.02);
            background: linear-gradient(105deg, #3b82f6 0%, #1e3a8a 100%);
            box-shadow: 0 12px 24px -8px rgba(37, 99, 235, 0.6);
            color: white;
        }

        .btn-outline-light-custom {
            background: transparent;
            border: 1.5px solid #2563eb;
            color: #2563eb;
            border-radius: 2.5rem;
            padding: 0.7rem 1.8rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-outline-light-custom:hover {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
            box-shadow: 0 8px 18px -8px #3b82f6;
        }

        .hero-illustration {
            background: linear-gradient(135deg, #eef2ff, #e0e7ff);
            border-radius: 2rem;
            padding: 0.8rem;
            box-shadow: 0 25px 40px -12px rgba(0, 0, 0, 0.1);
        }

        .dashboard-mock {
            background: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            transition: all 0.2s;
        }

        .badge-soft {
            background: #dbeafe;
            color: #1e40af;
            padding: 0.3rem 0.9rem;
            border-radius: 2rem;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .footer-link {
            text-decoration: none;
            color: #4b5563;
            transition: 0.2s;
        }

        .footer-link:hover {
            color: #2563eb;
        }

        .gradient-bg-soft {
            background: radial-gradient(circle at 10% 30%, rgba(56, 189, 248, 0.08), rgba(37, 99, 235, 0.02));
        }

        .section-title {
            font-weight: 700;
            letter-spacing: -0.02em;
            background: linear-gradient(120deg, #0f2b3d, #1e3a8a);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
        }

        hr {
            opacity: 0.3;
        }

        @media (max-width: 768px) {
            .hero-illustration {
                margin-top: 2rem;
            }
            .btn-gradient, .btn-outline-light-custom {
                padding: 0.6rem 1.4rem;
            }
        }
    </style>
</head>
<body>

    <!-- Hero Section -->
    <section class="container pt-5 pb-4">
        <div class="row align-items-center g-5 py-3 py-lg-5">
            <div class="col-lg-6">
               <div class="pe-lg-4">
    <span class="badge-soft d-inline-block mb-3">
        <i class="fas fa-capsules me-1"></i> Next-Gen Pharmacy Tech
    </span>

    <h1 class="display-4 fw-bold mb-3" style="line-height: 1.2;">
        Pharma ERP – <br>Smart Pharmacy Management System
    </h1>

    <p class="lead text-secondary mb-4">
        Manage inventory, billing, purchase, sales, and reports in one powerful system
    </p>

    <div class="d-flex flex-wrap gap-3 mt-2">
        <!-- User Login -->
        <a href="{{ route('login') }}" class="btn btn-gradient px-4 py-2">
            <i class="fas fa-sign-in-alt me-2"></i>Retailer Login
        </a>

        <!-- Supplier Login -->
        <a href="{{ route('supplier.login') }}" class="btn btn-outline-primary px-4 py-2">
            <i class="fas fa-truck me-2"></i>Supplier Login
        </a>

        <!-- Get Started -->
        <button class="btn btn-outline-light-custom">
            <i class="fas fa-rocket me-2"></i>Get Started
        </button>
    </div>

    <div class="mt-4 d-flex gap-3 small text-muted">
        <span><i class="fas fa-check-circle text-primary"></i> 14-day trial</span>
        <span><i class="fas fa-shield-alt text-primary"></i> HIPAA | GST Ready</span>
    </div>
</div>
            </div>
            <div class="col-lg-6">
                <div class="hero-illustration p-3 p-lg-4">
                    <img src="https://placehold.co/600x400/eef2ff/2563eb?text=Pharma+Dashboard+Mock&font=montserrat" alt="Pharma Dashboard Preview" class="img-fluid rounded-4 shadow-sm" style="width:100%; object-fit: cover;">
                    <div class="mt-3 text-center small text-primary fw-semibold"><i class="fas fa-chart-line me-1"></i> Real-time analytics & stock alerts</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section (6 cards) -->
    <section class="container py-5 my-3">
        <div class="text-center mb-5">
            <span class="badge-soft"><i class="fas fa-microchip me-1"></i> Core Modules</span>
            <h2 class="display-6 fw-bold mt-2 section-title">Everything you need, seamlessly integrated</h2>
            <p class="text-secondary col-md-8 mx-auto">Designed for modern pharmacies, retail chains, and wholesale operations</p>
        </div>
        <div class="row g-4">
            <!-- Feature 1 -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card card-hover p-4 h-100">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex p-3 mb-3" style="width: 56px; height: 56px;">
                        <i class="fas fa-boxes fa-2x text-primary m-auto"></i>
                    </div>
                    <h5 class="fw-bold">Inventory Management</h5>
                    <p class="text-secondary">Batch + Expiry tracking, low-stock alerts, stock valuation, and real-time stock movement.</p>
                    <span class="badge-soft mt-2 w-auto">Batch & Expiry ready</span>
                </div>
            </div>
            <!-- Feature 2 -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card card-hover p-4 h-100">
                    <div class="rounded-circle bg-info bg-opacity-10 d-inline-flex p-3 mb-3" style="width: 56px; height: 56px;">
                        <i class="fas fa-cash-register fa-2x text-info m-auto"></i>
                    </div>
                    <h5 class="fw-bold">Sales & Billing</h5>
                    <p class="text-secondary">Fast counter billing, digital receipts, returns, discounts, and GST invoice generation.</p>
                </div>
            </div>
            <!-- Feature 3 -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card card-hover p-4 h-100">
                    <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex p-3 mb-3" style="width: 56px; height: 56px;">
                        <i class="fas fa-truck fa-2x text-success m-auto"></i>
                    </div>
                    <h5 class="fw-bold">Purchase Management</h5>
                    <p class="text-secondary">Manage vendor orders, purchase returns, landed cost, and payment tracking.</p>
                </div>
            </div>
            <!-- Feature 4 -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card card-hover p-4 h-100">
                    <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex p-3 mb-3" style="width: 56px; height: 56px;">
                        <i class="fas fa-file-invoice-dollar fa-2x text-warning m-auto"></i>
                    </div>
                    <h5 class="fw-bold">GST & Reports</h5>
                    <p class="text-secondary">GSTR-1, GSTR-3B, sales summary, P&L, and custom financial reports.</p>
                </div>
            </div>
            <!-- Feature 5 -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card card-hover p-4 h-100">
                    <div class="rounded-circle bg-secondary bg-opacity-10 d-inline-flex p-3 mb-3" style="width: 56px; height: 56px;">
                        <i class="fas fa-users fa-2x text-secondary m-auto"></i>
                    </div>
                    <h5 class="fw-bold">Customer & Supplier Ledger</h5>
                    <p class="text-secondary">Track credit limits, outstanding dues, payment history, and statement of accounts.</p>
                </div>
            </div>
            <!-- Feature 6 -->
            <div class="col-md-6 col-lg-4">
                <div class="feature-card card-hover p-4 h-100">
                    <div class="rounded-circle bg-cyan bg-opacity-10 d-inline-flex p-3 mb-3" style="width: 56px; height: 56px;">
                        <i class="fas fa-chart-pie fa-2x text-cyan m-auto" style="color:#0891b2;"></i>
                    </div>
                    <h5 class="fw-bold">Real-time Dashboard</h5>
                    <p class="text-secondary">Interactive KPIs, revenue charts, top-selling drugs, and instant alerts.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section (grid of 4 reasons) -->
    <section class="container py-5 my-2">
        <div class="glass-card p-4 p-lg-5">
            <div class="row text-center g-4">
                <div class="col-12">
                    <span class="badge-soft"><i class="fas fa-star-of-life me-1"></i> Why Pharma ERP</span>
                    <h2 class="display-6 fw-bold mt-2 mb-4 section-title">Why choose us for your pharmacy?</h2>
                </div>
                <div class="col-md-3">
                    <div class="p-3">
                        <div class="bg-white d-inline-flex p-3 rounded-3 shadow-sm mb-3" style="border-radius: 20px;">
                            <i class="fas fa-bolt fa-2x text-primary"></i>
                        </div>
                        <h5 class="fw-bold">Fast</h5>
                        <p class="text-secondary">Lightning-fast billing & inventory sync under 2 seconds.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3">
                        <div class="bg-white d-inline-flex p-3 rounded-3 shadow-sm mb-3" style="border-radius: 20px;">
                            <i class="fas fa-check-double fa-2x text-primary"></i>
                        </div>
                        <h5 class="fw-bold">Accurate</h5>
                        <p class="text-secondary">Zero-error inventory counts & tax calculations (GST compliant).</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3">
                        <div class="bg-white d-inline-flex p-3 rounded-3 shadow-sm mb-3" style="border-radius: 20px;">
                            <i class="fas fa-hand-peace fa-2x text-primary"></i>
                        </div>
                        <h5 class="fw-bold">Easy to use</h5>
                        <p class="text-secondary">Intuitive interface, no training needed for pharmacists.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3">
                        <div class="bg-white d-inline-flex p-3 rounded-3 shadow-sm mb-3" style="border-radius: 20px;">
                            <i class="fas fa-chart-line fa-2x text-primary"></i>
                        </div>
                        <h5 class="fw-bold">Indian Pharmacy Workflow</h5>
                        <p class="text-secondary">Designed for schedule H1, generic medicines, GST & e-way bill.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dashboard Preview Section (mock UI card) -->
    <section class="container py-5 my-2">
        <div class="row justify-content-center">
            <div class="col-12 text-center mb-4">
                <span class="badge-soft"><i class="fas fa-desktop me-1"></i> Live Dashboard Preview</span>
                <h2 class="display-6 fw-bold mt-2 section-title">Command center for your pharmacy</h2>
                <p class="text-secondary">Real-time stock, revenue, expiry alerts — all at a glance</p>
            </div>
            <div class="col-lg-10">
                <div class="dashboard-mock p-0">
                    <div class="bg-light p-3 border-bottom d-flex justify-content-between align-items-center">
                        <div><i class="fas fa-chart-simple me-2 text-primary"></i><strong>Dashboard Overview</strong></div>
                        <div><span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill">Today: ₹1,28,450</span></div>
                    </div>
                    <div class="p-4 bg-white">
                        <div class="row g-3 mb-4">
                            <div class="col-md-3 col-6">
                                <div class="p-3 bg-light rounded-3">
                                    <small class="text-muted">Total Sales</small>
                                    <h4 class="fw-bold mb-0">₹ 28,420</h4>
                                    <span class="badge bg-success-subtle text-success"><i class="fas fa-arrow-up"></i> +12%</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="p-3 bg-light rounded-3">
                                    <small class="text-muted">Stock Items</small>
                                    <h4 class="fw-bold mb-0">1,284</h4>
                                    <span class="badge bg-warning-subtle text-warning">4 expiring soon</span>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="p-3 bg-light rounded-3">
                                    <small class="text-muted">Pending Orders</small>
                                    <h4 class="fw-bold mb-0">16</h4>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="p-3 bg-light rounded-3">
                                    <small class="text-muted">Net Profit (MTD)</small>
                                    <h4 class="fw-bold mb-0">₹ 1,82,300</h4>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-borderless align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Medicine</th><th>Batch</th><th>Expiry</th><th>Stock</th><th>Sales (Today)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr><td>Paracetamol 500mg</td><td>B23A19</td><td>Dec 2025</td><td>340</td><td>85</td></tr>
                                    <tr><td>Amoxicillin 250mg</td><td>B24C22</td><td>Aug 2026</td><td>120</td><td>42</td></tr>
                                    <tr><td>Azithromycin 500mg</td><td>B22F11</td><td class="text-danger fw-semibold">May 2024</td><td>56</td><td>18</td></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-2"><span class="badge-soft"><i class="fas fa-chart-line me-1"></i> Interactive demo view</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="container py-5 my-4">
        <div class="glass-card p-5 text-center" style="background: linear-gradient(115deg, rgba(37,99,235,0.08), rgba(6,182,212,0.05)); backdrop-filter: blur(8px);">
            <h2 class="display-6 fw-bold">Start Managing Your Pharmacy Today</h2>
            <p class="lead text-secondary mt-2 mb-4">Join 2000+ pharmacies that trust Pharma ERP for efficiency & growth.</p>
            <a href="{{ route('login') }}" class="btn btn-gradient btn-lg px-5 py-3"><i class="fas fa-key me-2"></i>Login to Dashboard</a>
            <p class="mt-4 small text-muted">No credit card required • Free 14-day trial</p>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="container-fluid bg-white mt-5 pt-5 pb-4 border-top">
        <div class="container">
            <div class="row gy-4">
                <div class="col-md-4">
                    <h4 class="fw-bold" style="background: linear-gradient(145deg, #1e3a8a, #2563eb); background-clip: text; -webkit-background-clip: text; color: transparent;">Pharma ERP</h4>
                    <p class="text-secondary small">Smart Pharmacy Management System — the complete ERP for modern pharmacies, retail chains and wholesalers in India.</p>
                    <div class="d-flex gap-3 mt-2">
                        <a href="#" class="text-secondary"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="text-secondary"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-secondary"><i class="fab fa-facebook-f"></i></a>
                    </div>
                </div>
                <div class="col-md-2">
                    <h6 class="fw-bold">Product</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="footer-link">Features</a></li>
                        <li><a href="#" class="footer-link">Pricing</a></li>
                        <li><a href="#" class="footer-link">Integrations</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h6 class="fw-bold">Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="footer-link">Help Center</a></li>
                        <li><a href="#" class="footer-link">API Docs</a></li>
                        <li><a href="#" class="footer-link">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="fw-bold">Stay updated</h6>
                    <p class="text-secondary small">Get the latest product updates & insights.</p>
                    <div class="input-group">
                        <input type="email" class="form-control rounded-pill border-0 bg-light" placeholder="Email address">
                        <button class="btn btn-primary rounded-pill px-4" type="button">Subscribe</button>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center text-secondary small d-flex flex-wrap justify-content-between align-items-center">
                <span>Pharma ERP © 2026. All rights reserved.</span>
                <span>
                    <a href="#" class="footer-link me-3">Privacy</a>
                    <a href="#" class="footer-link">Terms</a>
                </span>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS bundle (optional for toggles) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>