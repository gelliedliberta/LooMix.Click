/**
 * LooMix.Click - Ana JavaScript Dosyası
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Header Breaking News Ticker - Instant Animation
    initHeaderBreakingNews();
    
    // 2. Smooth Scrolling
    const links = document.querySelectorAll('a[href^="#"]');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
    
    // 3. Search Enhancement + Tag Autocomplete
    const searchInput = document.querySelector('input[name="q"]');
    if (searchInput) {
        // Basic: keep placeholder debounce for now
        searchInput.addEventListener('input', debounce(function(e) {
            const query = e.target.value;
        }, 300));

        // Tag autocomplete dropdown
        const ac = document.createElement('div');
        ac.className = 'autocomplete-dropdown list-group position-absolute shadow';
        ac.style.display = 'none';
        ac.style.zIndex = '1050';
        searchInput.parentElement.style.position = 'relative';
        searchInput.parentElement.appendChild(ac);

        const hideDropdown = () => { ac.style.display = 'none'; ac.innerHTML = ''; };

        const fetchTags = debounce(async (q) => {
            if (!q || q.length < 2) { hideDropdown(); return; }
            try {
                const params = new URLSearchParams({ q, limit: 8 });
                const res = await fetch(`${window.AppConfig?.baseUrl || ''}/api/tags/search?` + params.toString());
                const data = await res.json();
                const items = (data && data.success) ? data.data : [];
                if (!items.length) { hideDropdown(); return; }
                ac.innerHTML = '';
                items.forEach(item => {
                    const a = document.createElement('a');
                    a.href = item.url;
                    a.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
                    a.innerHTML = `<span><i class="fas fa-tag me-2" style="color:${item.color}"></i>${item.name}</span><span class="badge bg-light text-dark">${item.usage_count}</span>`;
                    ac.appendChild(a);
                });
                ac.style.width = '100%';
                ac.style.top = (searchInput.offsetTop + searchInput.offsetHeight) + 'px';
                ac.style.left = searchInput.offsetLeft + 'px';
                ac.style.display = 'block';
            } catch (err) {
                hideDropdown();
            }
        }, 250);

        searchInput.addEventListener('input', (e) => fetchTags(e.target.value.trim()));
        document.addEventListener('click', (e) => { if (!ac.contains(e.target) && e.target !== searchInput) hideDropdown(); });
    }
    
    // 4. News Card Hover Effects
    const newsCards = document.querySelectorAll('.news-card');
    newsCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // 5. Back to Top Button
    const backToTop = document.createElement('button');
    backToTop.innerHTML = '<i class="fas fa-arrow-up"></i>';
    backToTop.classList.add('back-to-top');
    backToTop.style.cssText = `
        position: fixed; bottom: 20px; right: 20px; width: 50px; height: 50px;
        border-radius: 50%; background: #007bff; color: white; border: none;
        cursor: pointer; opacity: 0; visibility: hidden; transition: all 0.3s;
        z-index: 1000;
    `;
    document.body.appendChild(backToTop);
    
    backToTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            backToTop.style.opacity = '1';
            backToTop.style.visibility = 'visible';
        } else {
            backToTop.style.opacity = '0';
            backToTop.style.visibility = 'hidden';
        }
    });

    // 6. Mobile offcanvas: close on link click
    const offcanvasEl = document.getElementById('mobileNav');
    if (offcanvasEl) {
        const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
        offcanvasEl.addEventListener('click', (e) => {
            const target = e.target.closest('a.nav-link');
            if (target && target.getAttribute('href')) {
                // Close after small delay to allow navigation for same-page anchors
                setTimeout(() => offcanvas.hide(), 50);
            }
        });
    }
});

// Utility Functions
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

/**
 * Header Breaking News Animasyon Optimizasyonu
 * Sayfa yüklenir yüklenmez animasyonu başlatır
 */
function initHeaderBreakingNews() {
    // Header'daki breaking news (.marquee)
    const marquees = document.querySelectorAll('.breaking-news .marquee');
    
    marquees.forEach(marquee => {
        const span = marquee.querySelector('span');
        if (!span) return;
        
        // İçeriği duplicate et (seamless loop için)
        const links = span.querySelectorAll('a');
        if (links.length > 0) {
            links.forEach(link => {
                const clone = link.cloneNode(true);
                span.appendChild(clone);
            });
        }
        
        // Force animation start (GPU acceleration)
        span.style.transform = 'translate3d(0, 0, 0)';
        
        // Animasyonu instant tetikle
        requestAnimationFrame(() => {
            span.style.animationPlayState = 'running';
        });
        
        // Hover pause event listeners (ekstra güvence)
        marquee.addEventListener('mouseenter', () => {
            span.style.animationPlayState = 'paused';
        });
        
        marquee.addEventListener('mouseleave', () => {
            span.style.animationPlayState = 'running';
        });
    });
}