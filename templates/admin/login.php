<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape($pageTitle) ?></title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 3rem;
            text-align: center;
        }
        
        .login-logo {
            margin-bottom: 2rem;
        }
        
        .login-logo i {
            font-size: 3rem;
            color: #007bff;
            margin-bottom: 1rem;
        }
        
        .login-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .login-subtitle {
            color: #6c757d;
            margin-bottom: 2rem;
        }
        
        .form-floating {
            margin-bottom: 1rem;
        }
        
        .form-control {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 1rem 0.75rem;
            height: auto;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 1rem;
            font-weight: 600;
            width: 100%;
            margin-top: 1rem;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        .alert {
            border-radius: 12px;
            border: none;
            margin-bottom: 1.5rem;
        }
        
        .forgot-password {
            margin-top: 1.5rem;
            color: #6c757d;
            text-decoration: none;
        }
        
        .forgot-password:hover {
            color: #007bff;
        }
        
        .login-footer {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 0.875rem;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-card {
            animation: fadeInUp 0.8s ease-out;
        }
        
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: none;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
        }
        
        .loading-overlay.show {
            display: flex;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card position-relative">
            <div class="loading-overlay" id="loadingOverlay">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Yükleniyor...</span>
                </div>
            </div>
            
            <div class="login-logo">
                <i class="fas fa-newspaper"></i>
                <h1 class="login-title"><?= escape(SITE_NAME) ?></h1>
                <p class="login-subtitle">Admin Panel Girişi</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= escape($error) ?>
                </div>
            <?php endif; ?>
            
            <form action="<?= url('/admin/auth') ?>" method="POST" id="loginForm" novalidate>
                <div class="form-floating">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Kullanıcı Adı" required>
                    <label for="username"><i class="fas fa-user me-2"></i>Kullanıcı Adı</label>
                    <div class="invalid-feedback">
                        Kullanıcı adı gereklidir.
                    </div>
                </div>
                
                <div class="form-floating">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Şifre" required>
                    <label for="password"><i class="fas fa-lock me-2"></i>Şifre</label>
                    <div class="invalid-feedback">
                        Şifre gereklidir.
                    </div>
                </div>
                
                <div class="form-check text-start">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                    <label class="form-check-label" for="remember">
                        Beni Hatırla
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Giriş Yap
                </button>
            </form>
            
            <div class="login-footer">
                <a href="<?= url('/') ?>" class="forgot-password">
                    <i class="fas fa-arrow-left me-1"></i>
                    Ana Siteye Dön
                </a>
            </div>
            
            <div class="login-footer">
                <small>&copy; <?= date('Y') ?> <?= escape(SITE_NAME) ?>. Tüm hakları saklıdır.</small>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const loadingOverlay = document.getElementById('loadingOverlay');
            
            // Form validation
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                } else {
                    // Show loading overlay
                    loadingOverlay.classList.add('show');
                }
                
                form.classList.add('was-validated');
            });
            
            // Focus on username field
            document.getElementById('username').focus();
            
            // Enter key handling
            document.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    form.dispatchEvent(new Event('submit'));
                }
            });
            
            // Password visibility toggle (optional enhancement)
            const passwordField = document.getElementById('password');
            const toggleButton = document.createElement('button');
            toggleButton.type = 'button';
            toggleButton.className = 'btn btn-link position-absolute top-50 end-0 translate-middle-y me-3 text-muted';
            toggleButton.innerHTML = '<i class="fas fa-eye"></i>';
            toggleButton.style.zIndex = '10';
            
            passwordField.parentNode.classList.add('position-relative');
            passwordField.parentNode.appendChild(toggleButton);
            
            toggleButton.addEventListener('click', function() {
                const type = passwordField.type === 'password' ? 'text' : 'password';
                passwordField.type = type;
                
                const icon = toggleButton.querySelector('i');
                icon.className = type === 'password' ? 'fas fa-eye' : 'fas fa-eye-slash';
            });
        });
        
        // Demo credentials hint (remove in production)
        <?php if (DEBUG_MODE): ?>
        setTimeout(function() {
            const alert = document.createElement('div');
            alert.className = 'alert alert-info mt-3';
            alert.innerHTML = `
                <small>
                    <strong>Demo Bilgileri:</strong><br>
                    Kullanıcı: admin<br>
                    Şifre: admin123
                </small>
            `;
            form.appendChild(alert);
        }, 2000);
        <?php endif; ?>
    </script>
</body>
</html>
