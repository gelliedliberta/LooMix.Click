<?php
/**
 * Maintenance Mode Page - Bakım Modu
 * LooMix.Click
 */
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <title><?= escape($pageTitle ?? 'Site Bakımda - ' . SITE_NAME) ?></title>
    <meta name="description" content="<?= escape($metaDescription ?? 'Sitemiz şu anda bakım aşamasında. Kısa süre sonra tekrar açılacak.') ?>">
    <meta name="robots" content="noindex, nofollow">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= asset('images/favicon.ico') ?>">
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom Maintenance CSS -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .maintenance-container {
            position: relative;
            z-index: 1;
        }
        
        .maintenance-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .maintenance-icon {
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
        
        .progress-bar-animated {
            background: linear-gradient(45deg, 
                rgba(255,255,255,.15) 25%, 
                transparent 25%, 
                transparent 50%, 
                rgba(255,255,255,.15) 50%, 
                rgba(255,255,255,.15) 75%, 
                transparent 75%, 
                transparent);
            background-size: 1rem 1rem;
            animation: progress-bar-stripes 1s linear infinite;
        }
        
        @keyframes progress-bar-stripes {
            0% {
                background-position-x: 1rem;
            }
        }
        
        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: white;
            text-decoration: none;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .social-links a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            color: white;
        }
        
        .countdown {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .countdown-item {
            text-align: center;
            color: white;
        }
        
        .countdown-number {
            font-size: 2rem;
            font-weight: 700;
            display: block;
            line-height: 1;
        }
        
        .countdown-label {
            font-size: 0.875rem;
            opacity: 0.8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        @media (max-width: 768px) {
            .maintenance-card {
                margin: 1rem;
                border-radius: 15px;
            }
            
            .countdown-number {
                font-size: 1.5rem;
            }
        }
        
        .fade-in {
            animation: fadeIn 1s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid maintenance-container">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-lg-6 col-xl-5">
                <div class="maintenance-card p-5 text-center fade-in">
                    <!-- Logo/Icon -->
                    <div class="maintenance-icon mb-4">
                        <i class="fas fa-tools text-primary" style="font-size: 4rem;"></i>
                    </div>
                    
                    <!-- Title -->
                    <h1 class="h2 fw-bold mb-4">Sitemiz Bakımda</h1>
                    
                    <!-- Description -->
                    <p class="lead text-muted mb-4">
                        Size daha iyi hizmet verebilmek için sistemimizi güncelliyoruz. 
                        Kısa süre sonra yeniden hizmetinizdeyiz.
                    </p>
                    
                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Güncelleme İlerlemesi</small>
                            <small class="text-muted">%85</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar progress-bar-animated bg-primary" 
                                 role="progressbar" 
                                 style="width: 85%"
                                 aria-valuenow="85" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Countdown Timer -->
                    <div class="countdown p-4 mb-4">
                        <h5 class="text-white mb-3">Tahmini Açılış Süresi</h5>
                        <div class="row">
                            <div class="col-3">
                                <div class="countdown-item">
                                    <span class="countdown-number" id="hours">02</span>
                                    <span class="countdown-label">Saat</span>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="countdown-item">
                                    <span class="countdown-number" id="minutes">30</span>
                                    <span class="countdown-label">Dakika</span>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="countdown-item">
                                    <span class="countdown-number" id="seconds">00</span>
                                    <span class="countdown-label">Saniye</span>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="countdown-item">
                                    <span class="countdown-number" id="milliseconds">00</span>
                                    <span class="countdown-label">Ms</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- What's Being Updated -->
                    <div class="updates-info mb-4">
                        <h6 class="mb-3">Neler Güncelleniyor?</h6>
                        <div class="row text-start">
                            <div class="col-6 mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Güvenlik güncellemeleri
                                </small>
                            </div>
                            <div class="col-6 mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Performans iyileştirmeleri
                                </small>
                            </div>
                            <div class="col-6 mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-check text-success me-2"></i>
                                    Yeni özellikler
                                </small>
                            </div>
                            <div class="col-6 mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-spinner fa-spin text-warning me-2"></i>
                                    Veritabanı optimizasyonu
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contact Info -->
                    <div class="contact-info">
                        <p class="mb-3 small text-muted">
                            Acil durumlarda bizimle iletişime geçebilirsiniz:
                        </p>
                        <p class="mb-3">
                            <a href="mailto:info@loomix.click" class="text-decoration-none">
                                <i class="fas fa-envelope me-2"></i>
                                info@loomix.click
                            </a>
                        </p>
                    </div>
                    
                    <!-- Social Links -->
                    <div class="social-links mt-4">
                        <a href="#" title="Facebook" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" title="Twitter" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" title="Instagram" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" title="YouTube" aria-label="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Countdown -->
    <script>
        // Simple countdown timer (for demo purposes)
        function updateCountdown() {
            const now = new Date().getTime();
            const countDownDate = now + (2.5 * 60 * 60 * 1000); // 2.5 hours from now
            
            const x = setInterval(function() {
                const now = new Date().getTime();
                const distance = countDownDate - now;
                
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                const milliseconds = Math.floor((distance % 1000) / 10);
                
                document.getElementById("hours").innerHTML = hours.toString().padStart(2, '0');
                document.getElementById("minutes").innerHTML = minutes.toString().padStart(2, '0');
                document.getElementById("seconds").innerHTML = seconds.toString().padStart(2, '0');
                document.getElementById("milliseconds").innerHTML = milliseconds.toString().padStart(2, '0');
                
                if (distance < 0) {
                    clearInterval(x);
                    document.getElementById("hours").innerHTML = "00";
                    document.getElementById("minutes").innerHTML = "00";
                    document.getElementById("seconds").innerHTML = "00";
                    document.getElementById("milliseconds").innerHTML = "00";
                }
            }, 100);
        }
        
        // Auto refresh page every 5 minutes to check if maintenance is over
        setTimeout(function() {
            window.location.reload();
        }, 5 * 60 * 1000);
        
        // Initialize countdown
        updateCountdown();
        
        // Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effect to maintenance icon
            const icon = document.querySelector('.maintenance-icon i');
            if (icon) {
                icon.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.1) rotate(15deg)';
                    this.style.transition = 'all 0.3s ease';
                });
                
                icon.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1) rotate(0deg)';
                });
            }
        });
    </script>
</body>
</html>
