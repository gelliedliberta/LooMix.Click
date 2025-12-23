<!DOCTYPE html>
<html lang="tr">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-GNGNJ8C7LB"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-GNGNJ8C7LB');
    </script>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= escape($metaDescription ?? SITE_DESCRIPTION) ?>">
    <meta name="keywords" content="<?= escape($metaKeywords ?? SITE_KEYWORDS) ?>">
    <meta name="author" content="<?= escape(SITE_NAME) ?>">
    
    <title><?= escape($pageTitle ?? SITE_NAME) ?></title>
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?= escape($pageTitle ?? SITE_NAME) ?>">
    <meta property="og:description" content="<?= escape($metaDescription ?? SITE_DESCRIPTION) ?>">
    <meta property="og:image" content="<?= $metaImage ?? DEFAULT_META_IMAGE ?>">
    <meta property="og:url" content="<?= $canonicalUrl ?? url('/') ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= escape(SITE_NAME) ?>">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= escape($pageTitle ?? SITE_NAME) ?>">
    <meta name="twitter:description" content="<?= escape($metaDescription ?? SITE_DESCRIPTION) ?>">
    <meta name="twitter:image" content="<?= $metaImage ?? DEFAULT_META_IMAGE ?>">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?= $canonicalUrl ?? url('/') ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    
    <!-- Performance: Preconnect/DNS Prefetch -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">

    <!-- Performance: Preload LCP image (Home/Detail) -->
    <?php if (!empty($lcpImage)): ?>
        <link rel="preload" as="image" href="<?= escape($lcpImage) ?>">
    <?php endif; ?>

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= asset('css/style.css') ?>" rel="stylesheet">
    
    <!-- Google AdSense -->
    <?php if (ADS_ENABLED && defined('GOOGLE_ADSENSE_ID') && !empty(GOOGLE_ADSENSE_ID) && GOOGLE_ADSENSE_ID !== 'ca-pub-3967023544942784'): ?>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?= GOOGLE_ADSENSE_ID ?>" 
            crossorigin="anonymous"></script>
    <?php endif; ?>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .hero-section .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .news-card {
            transition: transform 0.2s;
        }
        
        .news-card:hover {
            transform: translateY(-5px);
        }
        
        .breaking-news {
            background: linear-gradient(45deg, #dc3545, #c82333);
            color: white;
            padding: 8px 0;
        }
        
        .breaking-news .marquee {
            overflow: hidden;
            white-space: nowrap;
            cursor: pointer;
        }
        
        .breaking-news .marquee span {
            display: inline-block;
            padding-left: 100%;
            animation: scroll 50s linear infinite;
            will-change: transform;
        }
        
        /* Mouse ile üzerine gelince animasyonu duraklat - çoklu selector */
        .breaking-news:hover .marquee span,
        .breaking-news .marquee:hover span {
            animation-play-state: paused !important;
        }
        
        /* Link hover efekti */
        .breaking-news .marquee a {
            transition: opacity 0.2s ease;
        }
        
        .breaking-news .marquee a:hover {
            opacity: 0.85;
        }
        
        @keyframes scroll {
            0% { transform: translate3d(0, 0, 0); }
            100% { transform: translate3d(-100%, 0, 0); }
        }
        
        .footer {
            background: #2c3e50;
            color: white;
        }
        
        .category-nav .nav-link {
            color: #495057;
            font-weight: 500;
        }
        
        .category-nav .nav-link:hover {
            color: #007bff;
        }
    </style>
</head>
<body>
    <!-- Breaking News -->
    <?php if (!empty($breakingNews)): ?>
    <div class="breaking-news">
        <div class="container">
            <div class="d-flex align-items-center">
                <strong class="me-3">
                    <i class="fas fa-bolt me-1"></i>SON DAKİKA
                </strong>
                <div class="marquee flex-grow-1">
                    <span>
                        <?php foreach ($breakingNews as $breaking): ?>
                            <a href="<?= url('/haber/' . $breaking['slug']) ?>" class="text-white text-decoration-none me-5">
                                <?= escape($breaking['title']) ?>
                            </a>
                        <?php endforeach; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Header -->
    <header class="bg-white shadow-sm sticky-top">
        <div class="container">
            <!-- Top Bar -->
            <div class="row py-2 border-bottom">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar me-2 text-muted"></i>
                        <small class="text-muted"><?= turkishDate('d F Y, l') ?></small>
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <?= displaySocialLinks('header', 'small') ?>
                </div>
            </div>
            
            <!-- Main Header -->
            <nav class="navbar navbar-expand-lg navbar-light">
                <a class="navbar-brand text-primary" href="<?= url('/') ?>">
                    <?= escape(SITE_NAME) ?>
                </a>
                
                <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-controls="mobileNav" aria-label="Menüyü aç">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Mobile Offcanvas Navigation -->
                <div class="offcanvas offcanvas-end d-lg-none" tabindex="-1" id="mobileNav" aria-labelledby="mobileNavLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="mobileNavLabel"><?= escape(SITE_NAME) ?></h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Kapat"></button>
                    </div>
                    <div class="offcanvas-body">
                        <!-- Mobile Search -->
                        <form class="mobile-search d-lg-none mb-3" method="GET" action="<?= url('/ara') ?>">
                            <div class="input-group">
                                <input class="form-control" type="search" name="q" placeholder="Haber ara..." value="<?= escape($_GET['q'] ?? '') ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>

                        <!-- Main Navigation (Mobile) -->
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <?php if (!empty($mainCategories)): ?>
                                <?php foreach (array_slice($mainCategories, 0, 10) as $category): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= url('/kategori/' . $category['slug']) ?>">
                                        <?= escape($category['name']) ?>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= url('/kategori/genel') ?>">
                                        <i class="fas fa-newspaper me-1 text-primary"></i>Genel
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= url('/kategori/teknoloji') ?>">
                                        <i class="fas fa-laptop me-1 text-success"></i>Teknoloji
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= url('/kategori/spor') ?>">
                                        <i class="fas fa-futbol me-1 text-warning"></i>Spor
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="<?= url('/kategori/ekonomi') ?>">
                                        <i class="fas fa-chart-line me-1 text-danger"></i>Ekonomi
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>

                        <!-- Quick Links (Mobile only) -->
                        <div class="d-lg-none mt-3 border-top pt-3">
                            <h6 class="text-muted mb-2">Sayfalar</h6>
                            <ul class="list-unstyled small mb-0">
                                <li><a class="nav-link px-0" href="<?= url('/hakkimizda') ?>">Hakkımızda</a></li>
                                <li><a class="nav-link px-0" href="<?= url('/iletisim') ?>">İletişim</a></li>
                                <li><a class="nav-link px-0" href="<?= url('/gizlilik-politikasi') ?>">Gizlilik Politikası</a></li>
                                <li><a class="nav-link px-0" href="<?= url('/site-haritasi') ?>">Site Haritası</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <!-- Main Navigation -->
                    <ul class="navbar-nav me-auto">
                        <?php if (!empty($mainCategories)): ?>
                            <?php foreach (array_slice($mainCategories, 0, 9) as $category): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('/kategori/' . $category['slug']) ?>">
                                    <?= escape($category['name']) ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('/kategori/genel') ?>">
                                    <i class="fas fa-newspaper me-1 text-primary"></i>Genel
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('/kategori/teknoloji') ?>">
                                    <i class="fas fa-laptop me-1 text-success"></i>Teknoloji
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('/kategori/spor') ?>">
                                    <i class="fas fa-futbol me-1 text-warning"></i>Spor
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= url('/kategori/ekonomi') ?>">
                                    <i class="fas fa-chart-line me-1 text-danger"></i>Ekonomi
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                    
                    <!-- Search -->
                    <form class="d-none d-lg-flex" method="GET" action="<?= url('/ara') ?>">
                        <div class="input-group">
                            <input class="form-control form-control-sm" type="search" name="q" placeholder="Haber ara..." value="<?= escape($_GET['q'] ?? '') ?>">
                            <button class="btn btn-outline-primary btn-sm" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </nav>
        </div>
    </header>
    
    <!-- Header Ad - Sadece reklam varsa gösterilir -->
    <?php if (ADS_ENABLED): ?>
        <?php 
        $headerAd = displayAd('header_banner');
        if (!empty($headerAd)): 
        ?>
        <div class="container-fluid bg-light py-2">
            <div class="container text-center">
                <?= $headerAd ?>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main id="main-content" role="main" class="min-vh-100 d-flex flex-column">
        <?= $content ?? '' ?>
    </main>
    
    <!-- Footer -->
    <footer class="footer mt-auto">
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-4">
                    <h5 class="text-white mb-3"><?= escape(SITE_NAME) ?></h5>
                    <p class="text-light"><?= escape(SITE_DESCRIPTION) ?></p>
                    <?= displaySocialLinks('footer', 'large') ?>
                </div>
                
                <div class="col-lg-2">
                    <h6 class="text-white mb-3">Kategoriler</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= url('/kategori/genel') ?>" class="text-light text-decoration-none">Genel</a></li>
                        <li><a href="<?= url('/kategori/teknoloji') ?>" class="text-light text-decoration-none">Teknoloji</a></li>
                        <li><a href="<?= url('/kategori/spor') ?>" class="text-light text-decoration-none">Spor</a></li>
                        <li><a href="<?= url('/kategori/ekonomi') ?>" class="text-light text-decoration-none">Ekonomi</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2">
                    <h6 class="text-white mb-3">Sayfalar</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= url('/hakkimizda') ?>" class="text-light text-decoration-none">Hakkımızda</a></li>
                        <li><a href="<?= url('/iletisim') ?>" class="text-light text-decoration-none">İletişim</a></li>
                        <li><a href="<?= url('/gizlilik-politikasi') ?>" class="text-light text-decoration-none">Gizlilik Politikası</a></li>
                        <li><a href="<?= url('/site-haritasi') ?>" class="text-light text-decoration-none">Site Haritası</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-4">
                    <h6 class="text-white mb-3">İletişim</h6>
                    <p class="text-light">
                        <i class="fas fa-envelope me-2"></i>
                        info@loomix.click
                    </p>
                    <p class="text-light">
                        <i class="fas fa-phone me-2"></i>
                        +90 (555) 123-4567
                    </p>
                    
                    <!-- Newsletter -->
                    <div class="newsletter mt-3">
                        <h6 class="text-white mb-2">Bülten</h6>
                        <form class="d-flex">
                            <input type="email" class="form-control form-control-sm me-2" placeholder="E-posta adresiniz">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <hr class="my-4" style="border-color: #495057;">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-light mb-0">
                        &copy; <?= date('Y') ?> <?= escape(SITE_NAME) ?>. Tüm hakları saklıdır.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-light mb-0">
                        Powered by <strong>LooMix.Click</strong>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Footer Ad - Sadece reklam varsa gösterilir -->
        <?php if (ADS_ENABLED): ?>
            <?php 
            $footerAd = displayAd('footer_banner');
            if (!empty($footerAd)): 
            ?>
            <div class="container-fluid bg-white py-3 border-top">
                <div class="container text-center">
                    <?= $footerAd ?>
                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </footer>
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.AppConfig = {
            baseUrl: "<?= rtrim(url('/'), '/') ?>",
            apiBaseUrl: "<?= rtrim(url('/api'), '/') ?>",
            adblockStrictMode: <?= ADBLOCK_STRICT_MODE ? 'true' : 'false' ?>,
            adsEnabled: <?= ADS_ENABLED ? 'true' : 'false' ?>
        };
    </script>
    <script src="<?= asset('js/app.js') ?>" defer></script>
    
    <!-- Ad Blocker Detection -->
    <script src="<?= asset('js/ad-detection.js') ?>" defer></script>
</body>
</html>