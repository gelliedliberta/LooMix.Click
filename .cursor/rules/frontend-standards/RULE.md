---
description: "JavaScript, CSS, HTML standartları ve best practices"
globs:
  - "assets/**"
  - "templates/**/*.php"
alwaysApply: false
---

# Frontend Standards

LooMix.Click projesi için JavaScript, CSS ve HTML standartları.

## 🎨 CSS Standards

### BEM Methodology

```css
/* Block */
.news-card { }

/* Element */
.news-card__image { }
.news-card__title { }
.news-card__content { }
.news-card__meta { }
.news-card__category { }

/* Modifier */
.news-card--featured { }
.news-card--large { }
.news-card__title--small { }
```

### Naming Conventions

- **kebab-case** kullan: `news-card`, `user-profile`, `category-list`
- **Semantic names**: `primary-button`, `hero-section`, `sidebar-widget`
- **BEM naming**: block__element--modifier

### CSS Organization

```css
/* 1. Variables */
:root {
    --primary-color: #007bff;
    --secondary-color: #6c757d;
    --font-family-base: 'Inter', sans-serif;
    --border-radius: 0.375rem;
    --spacing-unit: 1rem;
}

/* 2. Base styles */
body {
    font-family: var(--font-family-base);
    line-height: 1.6;
    color: #333;
}

/* 3. Components */
.news-card {
    background: white;
    border-radius: var(--border-radius);
    padding: var(--spacing-unit);
    
    &__title {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    &--featured {
        border: 2px solid var(--primary-color);
    }
}

/* 4. Utilities */
.text-center { text-align: center; }
.mt-1 { margin-top: 0.25rem; }
```

### Responsive Design (Mobile First)

```css
/* Base - Mobile styles */
.news-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}

/* Tablet - 768px+ */
@media (min-width: 768px) {
    .news-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Desktop - 1024px+ */
@media (min-width: 1024px) {
    .news-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }
}

/* Large Desktop - 1280px+ */
@media (min-width: 1280px) {
    .news-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}
```

### Bootstrap 5.3.2 Usage

Projede Bootstrap kullanılıyor, kendi CSS ile override ederken:

```css
/* DOĞRU - Bootstrap'i extend et */
.btn-primary {
    /* Bootstrap default stiller */
}

.btn-primary.btn-news {
    /* Özel eklentiler */
    border-radius: 0.5rem;
    font-weight: 600;
}

/* YANLIŞ - !important kullanma (gerekli değilse) */
.btn-primary {
    background: red !important; /* Avoid !important */
}
```

## 💻 JavaScript Standards

### ES6+ Syntax

```javascript
// DOĞRU - Modern syntax
const getUserData = async (userId) => {
    try {
        const response = await fetch(`/api/users/${userId}`);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
};

// YANLIŞ - Old syntax
var getUserData = function(userId) {
    return fetch('/api/users/' + userId)
        .then(function(response) {
            return response.json();
        });
};
```

### Naming Conventions

```javascript
// Variables & Functions - camelCase
const userName = 'John';
const isLoggedIn = true;
const newsCount = 10;

function getUserData() { }
function calculateTotal() { }

// Classes - PascalCase
class NewsManager { }
class UserService { }

// Constants - UPPER_SNAKE_CASE
const API_BASE_URL = '/api';
const MAX_NEWS_COUNT = 100;
const DEFAULT_PAGE_SIZE = 12;

// Private methods - _prefix (convention)
class NewsManager {
    _processData() { } // Private
    getData() { }      // Public
}
```

### Event Handling

```javascript
// DOĞRU - Event delegation
document.addEventListener('click', (e) => {
    // Share button
    if (e.target.matches('.news-share-btn')) {
        handleNewsShare(e.target);
    }
    
    // Like button
    if (e.target.matches('.news-like-btn')) {
        handleNewsLike(e.target);
    }
});

// YANLIŞ - Her elemana ayrı listener (performans sorunu)
document.querySelectorAll('.news-share-btn').forEach(btn => {
    btn.addEventListener('click', handleNewsShare);
});
```

### Async/Await

```javascript
// DOĞRU - Async/await ile clean code
const loadNews = async (page = 1) => {
    try {
        showLoading();
        
        const response = await fetch(`/api/news?page=${page}`);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        const data = await response.json();
        renderNews(data.news);
        renderPagination(data.pagination);
        
    } catch (error) {
        console.error('Error loading news:', error);
        showError('Haberler yüklenemedi');
    } finally {
        hideLoading();
    }
};

// YANLIŞ - Callback hell
loadNews(1, function(error, data) {
    if (error) {
        showError(error);
    } else {
        renderNews(data, function(error) {
            if (error) {
                showError(error);
            } else {
                loadComments(function(error, comments) {
                    // ...
                });
            }
        });
    }
});
```

### DOM Manipulation

```javascript
// DOĞRU - Efficient DOM manipulation
const newsContainer = document.getElementById('news-container');
const fragment = document.createDocumentFragment();

newsData.forEach(news => {
    const newsCard = createNewsCard(news);
    fragment.appendChild(newsCard);
});

newsContainer.appendChild(fragment); // Single DOM update

// YANLIŞ - Multiple DOM updates (slow)
newsData.forEach(news => {
    const newsCard = createNewsCard(news);
    newsContainer.appendChild(newsCard); // DOM update her iteration'da
});
```

### Error Handling

```javascript
// Detailed error logging
const handleApiError = (error, context) => {
    console.error(`Error in ${context}:`, {
        message: error.message,
        stack: error.stack,
        timestamp: new Date().toISOString()
    });
    
    // User-friendly message
    showNotification('Bir hata oluştu. Lütfen tekrar deneyin.', 'error');
};

// Usage
try {
    await loadNews();
} catch (error) {
    handleApiError(error, 'loadNews');
}
```

### Debounce & Throttle

```javascript
// Debounce - Search input
const debounce = (func, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func(...args), delay);
    };
};

const searchInput = document.getElementById('search');
const debouncedSearch = debounce((query) => {
    performSearch(query);
}, 300);

searchInput.addEventListener('input', (e) => {
    debouncedSearch(e.target.value);
});

// Throttle - Scroll event
const throttle = (func, limit) => {
    let inThrottle;
    return (...args) => {
        if (!inThrottle) {
            func(...args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
};

const handleScroll = throttle(() => {
    // Handle scroll
}, 100);

window.addEventListener('scroll', handleScroll);
```

## 🌐 HTML Standards

### Semantic HTML

```html
<!-- DOĞRU - Semantic tags -->
<article class="news-article">
    <header class="news-article__header">
        <h1 class="news-article__title">Haber Başlığı</h1>
        
        <div class="news-article__meta">
            <time datetime="2024-01-01T10:00:00Z">
                1 Ocak 2024, 10:00
            </time>
            <span class="news-article__category">
                <a href="/kategori/teknoloji">Teknoloji</a>
            </span>
        </div>
    </header>
    
    <figure class="news-article__featured-image">
        <img src="image.jpg" alt="Haber görseli açıklaması">
        <figcaption>Görsel açıklaması</figcaption>
    </figure>
    
    <div class="news-article__content">
        <p>Haber içeriği...</p>
    </div>
    
    <footer class="news-article__footer">
        <div class="news-article__tags">
            <a href="/etiket/yapay-zeka" class="tag">Yapay Zeka</a>
            <a href="/etiket/teknoloji" class="tag">Teknoloji</a>
        </div>
    </footer>
</article>

<!-- YANLIŞ - Non-semantic -->
<div class="news">
    <div class="header">
        <div class="title">Haber Başlığı</div>
        <div class="date">1 Ocak 2024</div>
    </div>
    <div class="image"><img src="image.jpg"></div>
    <div class="content">Haber içeriği...</div>
</div>
```

### Accessibility (a11y)

```html
<!-- Alt text for images -->
<img src="news-image.jpg" 
     alt="Teknoloji fuarında sergilenen yeni yapay zeka ürünleri">

<!-- Form labels -->
<label for="search-input">Haber ara:</label>
<input type="search" 
       id="search-input" 
       name="q" 
       placeholder="Aranacak kelime"
       aria-label="Haber arama">

<!-- ARIA attributes -->
<button aria-expanded="false" 
        aria-controls="mobile-menu"
        aria-label="Menüyü aç">
    <i class="fas fa-bars"></i>
</button>

<nav id="mobile-menu" 
     aria-hidden="true"
     aria-label="Ana menü">
    <!-- Menu items -->
</nav>

<!-- Skip links -->
<a href="#main-content" class="skip-link">İçeriğe geç</a>

<!-- Heading hierarchy -->
<h1>Ana Başlık</h1>
    <h2>Alt Başlık</h2>
        <h3>İkincil Alt Başlık</h3>
        
<!-- Don't skip levels -->
```

### Forms

```html
<!-- Accessible form -->
<form action="/admin/news/create" method="POST" enctype="multipart/form-data">
    <!-- CSRF token -->
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
    
    <!-- Text input -->
    <div class="form-group">
        <label for="news-title">Başlık <span class="required">*</span></label>
        <input type="text" 
               id="news-title" 
               name="title" 
               required 
               aria-required="true"
               class="form-control">
    </div>
    
    <!-- Textarea -->
    <div class="form-group">
        <label for="news-content">İçerik</label>
        <textarea id="news-content" 
                  name="content" 
                  rows="10"
                  class="form-control"></textarea>
    </div>
    
    <!-- Select -->
    <div class="form-group">
        <label for="news-category">Kategori</label>
        <select id="news-category" name="category_id" class="form-control">
            <option value="">Kategori seçin</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= $category['id'] ?>">
                    <?= escape($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <!-- File input -->
    <div class="form-group">
        <label for="news-image">Görsel</label>
        <input type="file" 
               id="news-image" 
               name="image" 
               accept="image/*"
               class="form-control">
        <small class="form-text">Maksimum 5MB</small>
    </div>
    
    <!-- Submit -->
    <button type="submit" class="btn btn-primary">
        Kaydet
    </button>
</form>
```

### PHP Template Syntax

```php
<!-- DOĞRU - Clean PHP syntax -->
<?php if (!empty($news)): ?>
    <div class="news-list">
        <?php foreach ($news as $item): ?>
            <article class="news-card">
                <h3><?= escape($item['title']) ?></h3>
                <p><?= truncateText($item['summary'], 150) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>Haber bulunamadı.</p>
<?php endif; ?>

<!-- YANLIŞ - Mixed syntax -->
<? if (!empty($news)) { ?>
    <?php echo $item['title']; ?>
<? } ?>
```

## 🎭 Performance Best Practices

### Image Optimization

```html
<!-- Responsive images -->
<img src="image-800w.jpg"
     srcset="image-400w.jpg 400w,
             image-800w.jpg 800w,
             image-1200w.jpg 1200w"
     sizes="(max-width: 600px) 400px,
            (max-width: 1000px) 800px,
            1200px"
     alt="Açıklama"
     loading="lazy">

<!-- WebP with fallback -->
<picture>
    <source srcset="image.webp" type="image/webp">
    <source srcset="image.jpg" type="image/jpeg">
    <img src="image.jpg" alt="Açıklama" loading="lazy">
</picture>
```

### Lazy Loading

```javascript
// Intersection Observer for lazy loading
const imageObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src;
            img.classList.remove('lazy');
            imageObserver.unobserve(img);
        }
    });
}, {
    rootMargin: '50px' // Load 50px before visible
});

// Observe all lazy images
document.querySelectorAll('img.lazy').forEach(img => {
    imageObserver.observe(img);
});
```

### Script Loading

```html
<!-- Defer non-critical scripts -->
<script src="app.js" defer></script>

<!-- Async for independent scripts -->
<script src="analytics.js" async></script>

<!-- Critical inline scripts -->
<script>
    // Critical JavaScript here
</script>
```

## 📱 Google AdSense Integration

### Ad Container

```html
<!-- Ad zone with proper structure -->
<div class="ad-container">
    <div class="ad-label">Reklam</div>
    <?= displayAd('header_banner') ?>
</div>
```

### Ad Blocker Detection

```javascript
// Ad blocker detection (from ad-detection.js)
(function() {
    const adBlockTest = document.createElement('div');
    adBlockTest.className = 'ad-banner';
    adBlockTest.style.cssText = 'height:1px;position:absolute;top:-999px;';
    document.body.appendChild(adBlockTest);
    
    setTimeout(() => {
        if (adBlockTest.offsetHeight === 0) {
            document.body.classList.add('adblock-detected');
            console.warn('Ad blocker detected');
        }
        adBlockTest.remove();
    }, 100);
})();
```

## ✅ Frontend Checklist

Her özellik eklerken kontrol et:

- [ ] BEM methodology kullanıldı mı?
- [ ] Responsive design (mobile first) uygulandı mı?
- [ ] Semantic HTML kullanıldı mı?
- [ ] Accessibility (ARIA, alt text) eklendi mi?
- [ ] ES6+ modern JavaScript syntax kullanıldı mı?
- [ ] Event delegation kullanıldı mı?
- [ ] Async/await kullanıldı mı?
- [ ] Error handling yapıldı mı?
- [ ] Images lazy loading var mı?
- [ ] Performance optimize edildi mi?

