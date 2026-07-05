// ── Header scroll effect ──────────────────────────────────
const header = document.getElementById('site-header');
if (header) {
  window.addEventListener('scroll', () => {
    header.classList.toggle('scrolled', window.scrollY > 40);
  });
}

// ── Mobile nav toggle ─────────────────────────────────────
const navToggle = document.getElementById('nav-toggle');
const mobileNav = document.getElementById('mobile-nav');
if (navToggle && mobileNav) {
  navToggle.addEventListener('click', () => {
    mobileNav.classList.toggle('open');
  });
}

// ── Listings: filter tabs ─────────────────────────────────
document.querySelectorAll('.tab').forEach(tab => {
  tab.addEventListener('click', () => {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    const filter = tab.dataset.filter;
    document.querySelectorAll('.property-card').forEach(card => {
      const match = filter === 'all' || card.dataset.type === filter;
      card.style.display = match ? '' : 'none';
    });
  });
});

// ── Mobile filters sidebar toggle ────────────────────────
const toggleFilters = document.getElementById('toggle-filters');
const filtersSidebar = document.getElementById('filters-sidebar');
if (toggleFilters && filtersSidebar) {
  toggleFilters.addEventListener('click', () => {
    filtersSidebar.classList.toggle('open');
  });
  // Close on outside click
  document.addEventListener('click', (e) => {
    if (!filtersSidebar.contains(e.target) && e.target !== toggleFilters) {
      filtersSidebar.classList.remove('open');
    }
  });
}

// ── Wishlist / save property ──────────────────────────────
document.querySelectorAll('.btn-wishlist').forEach(btn => {
  const saved = JSON.parse(localStorage.getItem('saved_properties') || '[]');
  const id = btn.dataset.id;
  if (saved.includes(id)) { btn.textContent = '♥'; btn.classList.add('active'); }

  btn.addEventListener('click', (e) => {
    e.preventDefault(); e.stopPropagation();
    const saved = JSON.parse(localStorage.getItem('saved_properties') || '[]');
    const idx = saved.indexOf(id);
    if (idx > -1) {
      saved.splice(idx, 1);
      btn.textContent = '♡'; btn.classList.remove('active');
    } else {
      saved.push(id);
      btn.textContent = '♥'; btn.classList.add('active');
      showToast('Property saved!');
    }
    localStorage.setItem('saved_properties', JSON.stringify(saved));
  });
});

// ── Photo preview for post-property ──────────────────────
const photoInput = document.getElementById('photo-input');
const photoPreview = document.getElementById('photo-preview');
if (photoInput && photoPreview) {
  photoInput.addEventListener('change', () => {
    photoPreview.innerHTML = '';
    Array.from(photoInput.files).slice(0, 10).forEach(file => {
      const reader = new FileReader();
      reader.onload = (e) => {
        const div = document.createElement('div');
        div.className = 'photo-preview-item';
        div.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
        photoPreview.appendChild(div);
      };
      reader.readAsDataURL(file);
    });
  });
}

// ── Toast notification ────────────────────────────────────
function showToast(msg, type = 'success') {
  const existing = document.querySelector('.sn-toast');
  if (existing) existing.remove();
  const toast = document.createElement('div');
  toast.className = 'sn-toast';
  toast.textContent = msg;
  toast.style.cssText = `
    position: fixed; bottom: 28px; right: 28px; z-index: 9999;
    background: ${type === 'success' ? '#2D6A4F' : '#DC2626'};
    color: #fff; padding: 12px 22px; border-radius: 10px;
    font-size: .9rem; font-weight: 500; font-family: 'DM Sans', sans-serif;
    box-shadow: 0 4px 20px rgba(0,0,0,.2);
    animation: slideInToast .3s ease;
  `;
  document.body.appendChild(toast);
  setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity .3s'; setTimeout(() => toast.remove(), 300); }, 3000);
}

// Inject toast animation
const style = document.createElement('style');
style.textContent = '@keyframes slideInToast { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }';
document.head.appendChild(style);

// ── Budget range live display (listings sidebar) ──────────
const budgetRange = document.getElementById('budget-range');
const budgetDisplay = document.getElementById('budget-display');
if (budgetRange && budgetDisplay) {
  budgetRange.addEventListener('input', () => {
    budgetDisplay.textContent = '₹' + parseInt(budgetRange.value).toLocaleString('en-IN');
  });
}

// ── Animate cards on scroll ───────────────────────────────
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = '1';
      entry.target.style.transform = 'translateY(0)';
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.1 });

document.querySelectorAll('.property-card, .city-card, .step, .testimonial-card').forEach((el, i) => {
  el.style.opacity = '0';
  el.style.transform = 'translateY(20px)';
  el.style.transition = `opacity .4s ease ${i * 0.05}s, transform .4s ease ${i * 0.05}s`;
  observer.observe(el);
});

// ── Auto-submit filter form on select change ──────────────
document.querySelectorAll('#filter-form select').forEach(sel => {
  sel.addEventListener('change', () => {
    // Small debounce
    clearTimeout(sel._timer);
    sel._timer = setTimeout(() => {
      document.getElementById('filter-form')?.submit();
    }, 400);
  });
});

// ── Gallery image click (basic lightbox hint) ─────────────
document.querySelectorAll('.gallery-thumb').forEach(thumb => {
  thumb.style.cursor = 'pointer';
  thumb.addEventListener('click', () => {
    showToast('Gallery viewer available in full deployment');
  });
});
