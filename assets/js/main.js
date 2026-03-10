/**
 * Żłobki Polska — Main JavaScript
 * @package ZlobkiPolska
 */

(function () {
	'use strict';

	/* =============================================
	   MOBILE MENU
	   ============================================= */
	const menuToggle = document.getElementById('menu-toggle');
	const siteNav    = document.getElementById('site-nav');

	if (menuToggle && siteNav) {
		menuToggle.addEventListener('click', function () {
			const isOpen = siteNav.classList.toggle('open');
			menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
		});

		// Close on outside click
		document.addEventListener('click', function (e) {
			if (!siteNav.contains(e.target) && !menuToggle.contains(e.target)) {
				siteNav.classList.remove('open');
				menuToggle.setAttribute('aria-expanded', 'false');
			}
		});
	}

	/* =============================================
	   SMOOTH COUNTER ANIMATION (Hero stats)
	   ============================================= */
	function animateCounter(el, target, duration = 1500) {
		const start  = performance.now();
		const from   = 0;

		function update(now) {
			const elapsed  = now - start;
			const progress = Math.min(elapsed / duration, 1);
			const eased    = 1 - Math.pow(1 - progress, 3); // ease-out-cubic
			el.textContent = Math.round(from + (target - from) * eased).toLocaleString('pl-PL');
			if (progress < 1) requestAnimationFrame(update);
		}
		requestAnimationFrame(update);
	}

	// Observe hero stat numbers
	const statNums = document.querySelectorAll('.hero__stat-num, .stat-item__num');
	if (statNums.length && 'IntersectionObserver' in window) {
		const observer = new IntersectionObserver(entries => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					const el  = entry.target;
					const raw = el.textContent.replace(/[^\d]/g, '');
					const num = parseInt(raw, 10);
					if (!isNaN(num) && num > 0) {
						const suffix = el.textContent.replace(/[\d\s]/g, '');
						animateCounter(el, num);
						setTimeout(() => { el.textContent += suffix; }, 1550);
					}
					observer.unobserve(el);
				}
			});
		}, { threshold: 0.5 });

		statNums.forEach(el => observer.observe(el));
	}

	/* =============================================
	   AVAILABILITY BAR ANIMATION
	   ============================================= */
	const bars = document.querySelectorAll('.availability-bar__fill');
	if (bars.length && 'IntersectionObserver' in window) {
		const barObserver = new IntersectionObserver(entries => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					const bar = entry.target;
					const w   = bar.style.width;
					bar.style.width = '0%';
					requestAnimationFrame(() => {
						setTimeout(() => { bar.style.width = w; }, 100);
					});
					barObserver.unobserve(bar);
				}
			});
		}, { threshold: 0.3 });

		bars.forEach(bar => barObserver.observe(bar));
	}

	/* =============================================
	   CARD ENTRANCE ANIMATIONS
	   ============================================= */
	const cards = document.querySelectorAll('.nursery-card, .feature-card, .region-card');
	if (cards.length && 'IntersectionObserver' in window) {
		cards.forEach((card, i) => {
			card.style.opacity = '0';
			card.style.transform = 'translateY(20px)';
			card.style.transition = `opacity 0.4s ease ${i * 0.04}s, transform 0.4s ease ${i * 0.04}s`;
		});

		const cardObserver = new IntersectionObserver(entries => {
			entries.forEach(entry => {
				if (entry.isIntersecting) {
					entry.target.style.opacity = '1';
					entry.target.style.transform = 'translateY(0)';
					cardObserver.unobserve(entry.target);
				}
			});
		}, { threshold: 0.1 });

		cards.forEach(card => cardObserver.observe(card));
	}

	/* =============================================
	   FILTERS PANEL TOGGLE (Archive)
	   ============================================= */
	const toggleFiltersBtn = document.getElementById('toggle-filters-btn');
	const filtersPanel     = document.getElementById('filters-panel');

	if (toggleFiltersBtn && filtersPanel) {
		toggleFiltersBtn.addEventListener('click', function () {
			filtersPanel.classList.toggle('open');
			this.textContent = filtersPanel.classList.contains('open') ? '✕ Zamknij filtry' : '🎛️ Filtry';
		});
	}

	/* =============================================
	   PRICE RANGE DISPLAY (Archive Filters)
	   ============================================= */
	const priceRange = document.getElementById('f-price');
	const priceVal   = document.getElementById('price-val');

	if (priceRange && priceVal) {
		priceRange.addEventListener('input', function () {
			if (parseInt(this.value) >= 5000) {
				priceVal.textContent = 'Brak limitu';
				this.name = '';
			} else {
				priceVal.textContent = parseInt(this.value).toLocaleString('pl-PL') + ' zł';
				this.name = 'max_price';
			}
		});
	}

	/* =============================================
	   STICKY HEADER SHRINK
	   ============================================= */
	const header = document.querySelector('.site-header');
	if (header) {
		window.addEventListener('scroll', () => {
			header.classList.toggle('scrolled', window.scrollY > 50);
		}, { passive: true });
	}

	/* =============================================
	   SEARCH AUTO-SUGGEST (Simple)
	   ============================================= */
	const heroSearch = document.getElementById('hero-search');
	if (heroSearch) {
		let timeout;
		heroSearch.addEventListener('input', function () {
			clearTimeout(timeout);
			// Could implement live suggestions via AJAX here
		});
	}

})();
