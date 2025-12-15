<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape($pageTitle ?? 'Admin Panel - ' . SITE_NAME) ?></title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Admin CSS -->
    <style>
        :root {
            --sidebar-width: 280px;
            --header-height: 70px;
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-color);
        }
        
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .admin-sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }
        
        .sidebar-brand h4 {
            color: white;
            margin: 0;
            font-weight: 600;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-section-title {
            padding: 0.75rem 1.25rem;
            color: rgba(255,255,255,0.6);
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 600;
            margin-top: 1rem;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            padding: 0.75rem 1.25rem;
            border-radius: 0;
            transition: all 0.2s;
            display: flex;
            align-items: center;
        }
        
        .nav-link:hover {
            color: white !important;
            background-color: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        
        .nav-link.active {
            color: white !important;
            background-color: var(--primary-color);
            border-right: 3px solid #fff;
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }
        
        /* Main Content */
        .admin-main {
            flex: 1;
            margin-left: var(--sidebar-width);
            background-color: var(--light-color);
        }
        
        .admin-header {
            background: white;
            height: var(--header-height);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-bottom: 1px solid #e9ecef;
        }
        
        .admin-content {
            padding: 2rem;
            min-height: calc(100vh - var(--header-height));
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .page-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }
        
        /* Stats Cards */
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: none;
            transition: transform 0.2s;
        }
        
        .stats-card:hover {
            transform: translateY(-2px);
        }
        
        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }
        
        .stats-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            color: var(--dark-color);
        }
        
        .stats-label {
            color: var(--secondary-color);
            font-weight: 500;
            margin: 0;
        }
        
        /* Data Table */
        .data-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .data-table .table {
            margin-bottom: 0;
        }
        
        .data-table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            color: var(--dark-color);
            font-weight: 600;
            padding: 1rem;
        }
        
        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .data-table tr:hover {
            background-color: #f8f9fa;
        }
        
        /* Buttons */
        .btn {
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-1px);
        }
        
        /* Forms */
        .form-control, .form-select {
            border-radius: 6px;
            border: 1px solid #ddd;
            padding: 0.75rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        /* Status badges */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-published {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-draft {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-archived {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            
            .admin-sidebar.show {
                transform: translateX(0);
            }
            
            .admin-main {
                margin-left: 0;
            }
            
            .admin-content {
                padding: 1rem;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }
        
        /* Loading States */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }
        
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
        
        /* Alerts */
        .alert {
            border-radius: 8px;
            border: none;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid var(--info-color);
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid var(--danger-color);
        }
        
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border-left: 4px solid var(--warning-color);
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <nav class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-brand">
                <h4><i class="fas fa-newspaper me-2"></i><?= escape(SITE_NAME) ?></h4>
                <small class="text-muted">Admin Panel</small>
            </div>
            
            <div class="sidebar-nav">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= ($pageTitle ?? '') === 'Admin Dashboard - ' . SITE_NAME ? 'active' : '' ?>" href="<?= url('/admin') ?>">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                    </li>
                </ul>
                
                <div class="nav-section-title">İçerik Yönetimi</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($pageTitle ?? '', 'Haber') !== false ? 'active' : '' ?>" href="<?= url('/admin/haberler') ?>">
                            <i class="fas fa-newspaper"></i>
                            Haberler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($pageTitle ?? '', 'Kategori') !== false ? 'active' : '' ?>" href="<?= url('/admin/kategoriler') ?>">
                            <i class="fas fa-folder"></i>
                            Kategoriler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/admin/etiketler') ?>">
                            <i class="fas fa-tags"></i>
                            Etiketler
                        </a>
                    </li>
                </ul>
                
                <div class="nav-section-title">Reklam & Gelir</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($pageTitle ?? '', 'Reklam') !== false ? 'active' : '' ?>" href="<?= url('/admin/reklam-alanlari') ?>">
                            <i class="fas fa-ad"></i>
                            Reklam Alanları
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($pageTitle ?? '', 'Sosyal Medya') !== false ? 'active' : '' ?>" href="<?= url('/admin/sosyal-medya') ?>">
                            <i class="fas fa-share-alt"></i>
                            Sosyal Medya
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/admin/gelir-raporlari') ?>">
                            <i class="fas fa-chart-line"></i>
                            Gelir Raporları
                        </a>
                    </li>
                </ul>
                
                <div class="nav-section-title">Kullanıcı & Sistem</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($pageTitle ?? '', 'Kullanıcı') !== false ? 'active' : '' ?>" href="<?= url('/admin/kullanicilar') ?>">
                            <i class="fas fa-users"></i>
                            Kullanıcılar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($pageTitle ?? '', 'İstatistikler') !== false ? 'active' : '' ?>" href="<?= url('/admin/istatistikler') ?>">
                            <i class="fas fa-chart-bar"></i>
                            İstatistikler
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/admin/ayarlar') ?>">
                            <i class="fas fa-cog"></i>
                            Site Ayarları
                        </a>
                    </li>
                </ul>
                
                <div class="nav-section-title">Diğer</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/') ?>" target="_blank">
                            <i class="fas fa-external-link-alt"></i>
                            Siteyi Görüntüle
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/admin/cikis') ?>" onclick="return confirm('Çıkış yapmak istediğinizden emin misiniz?')">
                            <i class="fas fa-sign-out-alt"></i>
                            Çıkış Yap
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        
        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <div class="d-flex align-items-center w-100 justify-content-between">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-link d-md-none me-2" id="sidebarToggle">
                            <i class="fas fa-bars"></i>
                        </button>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="<?= url('/admin') ?>">Admin</a></li>
                                <?php if (isset($breadcrumb) && !empty($breadcrumb)): ?>
                                    <?php foreach ($breadcrumb as $item): ?>
                                        <?php if (!empty($item['url'])): ?>
                                            <li class="breadcrumb-item"><a href="<?= $item['url'] ?>"><?= escape($item['title']) ?></a></li>
                                        <?php else: ?>
                                            <li class="breadcrumb-item active" aria-current="page"><?= escape($item['title']) ?></li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ol>
                        </nav>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <div class="dropdown">
                            <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-2"></i>
                                <?= escape($currentUser['full_name'] ?? 'Admin') ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= url('/admin/profil') ?>"><i class="fas fa-user me-2"></i>Profil</a></li>
                                <li><a class="dropdown-item" href="<?= url('/admin/ayarlar') ?>"><i class="fas fa-cog me-2"></i>Ayarlar</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= url('/admin/cikis') ?>" onclick="return confirm('Çıkış yapmak istediğinizden emin misiniz?')"><i class="fas fa-sign-out-alt me-2"></i>Çıkış Yap</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <div class="admin-content">
                <!-- Flash Messages -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?= escape($_SESSION['success']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?= escape($_SESSION['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['warning'])): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?= escape($_SESSION['warning']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['warning']); ?>
                <?php endif; ?>
                
                <!-- Page Content -->
                <?= $content ?? '' ?>
            </div>
        </main>
    </div>
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Admin Panel JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle for mobile
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('adminSidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
                
                // Close sidebar when clicking outside on mobile
                document.addEventListener('click', function(e) {
                    if (window.innerWidth <= 768 && 
                        !sidebar.contains(e.target) && 
                        !sidebarToggle.contains(e.target)) {
                        sidebar.classList.remove('show');
                    }
                });
            }
            
            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 5000);
            });
            
            // Confirm delete actions
            const deleteLinks = document.querySelectorAll('[data-action="delete"]');
            deleteLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (!confirm('Bu işlem geri alınamaz. Emin misiniz?')) {
                        e.preventDefault();
                    }
                });
            });
            
            // Form validation feedback
            const forms = document.querySelectorAll('.needs-validation');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!form.checkValidity()) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });
            });
        });
        
        // Global AJAX setup
        function makeAjaxRequest(url, options = {}) {
            const defaultOptions = {
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            };
            
            const finalOptions = { ...defaultOptions, ...options };
            
            return fetch(url, finalOptions)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .catch(error => {
                    console.error('AJAX Error:', error);
                    throw error;
                });
        }
        
        // Show loading state
        function showLoading(element, text = 'Yükleniyor...') {
            const originalHtml = element.innerHTML;
            element.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>${text}`;
            element.disabled = true;
            return originalHtml;
        }
        
        // Hide loading state
        function hideLoading(element, originalHtml) {
            element.innerHTML = originalHtml;
            element.disabled = false;
        }
        
        // Show notification
        function showNotification(message, type = 'success') {
            const alertClass = type === 'error' ? 'alert-danger' : `alert-${type}`;
            const icon = type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle';
            
            const alert = document.createElement('div');
            alert.className = `alert ${alertClass} alert-dismissible fade show`;
            alert.innerHTML = `
                <i class="fas ${icon} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            const container = document.querySelector('.admin-content');
            container.insertBefore(alert, container.firstChild);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 5000);
        }
    </script>
</body>
</html>
