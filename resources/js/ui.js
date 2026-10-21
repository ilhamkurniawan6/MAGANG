/**
 * KADIN Digital Hub - Shared UI Components & Helpers
 * Mengelola komponen UI konsisten di semua halaman & dashboard
 */

const ui = {
  /**
   * Render badge status dengan warna konsisten dan ikon rapi
   */
  renderBadge(status) {
    if (!status) return '-';
    const s = status.toLowerCase();
    let label = status.replace(/_/g, ' ');
    label = label.charAt(0).toUpperCase() + label.slice(1);

    if (s === 'verified' || s === 'disetujui' || s === 'aktif' || s === 'hadir' || s === 'terbuka') {
      return `<span class="badge-status status-verified"><i class="bi bi-check-circle-fill"></i> ${label}</span>`;
    } else if (s === 'pending' || s === 'menunggu_moderasi' || s === 'terdaftar' || s === 'published') {
      return `<span class="badge-status status-pending"><i class="bi bi-clock-fill"></i> ${label}</span>`;
    } else if (s === 'rejected' || s === 'ditolak' || s === 'nonaktif' || s === 'tidak_hadir' || s === 'ditutup') {
      return `<span class="badge-status status-rejected"><i class="bi bi-x-circle-fill"></i> ${label}</span>`;
    }
    return `<span class="badge-status bg-light text-dark border">${label}</span>`;
  },

  /**
   * Render Badge Level Skor KADIN Lestari (Bronze/Silver/Gold)
   */
  renderLestariBadge(level, score = null) {
    if (!level) return '<span class="badge bg-light text-muted border px-2 py-1">Belum Dinilai</span>';
    const lvl = level.toLowerCase();
    const scoreText = score !== null ? `(${score}/100)` : '';

    if (lvl === 'gold') {
      return `<span class="badge-lestari badge-lestari-gold"><i class="bi bi-trophy-fill text-warning"></i> KADIN Lestari Gold ${scoreText}</span>`;
    } else if (lvl === 'silver') {
      return `<span class="badge-lestari badge-lestari-silver"><i class="bi bi-award-fill text-secondary"></i> KADIN Lestari Silver ${scoreText}</span>`;
    } else if (lvl === 'bronze') {
      return `<span class="badge-lestari badge-lestari-bronze"><i class="bi bi-shield-fill text-warning"></i> KADIN Lestari Bronze ${scoreText}</span>`;
    }
    return `<span class="badge bg-light text-dark">${level}</span>`;
  },

  /**
   * Helper format label tag ramah lingkungan
   */
  renderTagPraktik(tagKey) {
    const labels = {
      'bahan_baku_lokal': 'Bahan Baku Lokal',
      'kemasan_minim_plastik': 'Kemasan Minim Plastik',
      'hemat_energi': 'Efisiensi Energi',
      'pengolahan_limbah': 'Pengolahan Limbah',
      'pewarna_alami': 'Pewarna Alami',
      'organik_alami': 'Organik & Alami',
      'zero_waste': 'Zero Waste / Daur Ulang',
    };
    const label = labels[tagKey] || tagKey.replace(/_/g, ' ');
    return `<span class="tag-ramah-lingkungan"><i class="bi bi-tree-fill"></i> ${label}</span>`;
  },

  /**
   * Format mata uang Rupiah
   */
  formatRupiah(amount) {
    if (amount === null || amount === undefined) return 'Rp 0';
    const num = Number(amount);
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0
    }).format(num);
  },

  /**
   * Format tanggal Indonesia
   */
  formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', {
      day: 'numeric',
      month: 'short',
      year: 'numeric'
    }).format(date);
  },

  /**
   * Render Alert Toast dengan animasi halus
   */
  showAlert(message, type = 'success', containerId = 'alert-container') {
    const container = document.getElementById(containerId);
    if (!container) return;

    const bgClass = type === 'success' ? 'alert-success' : type === 'danger' ? 'alert-danger' : 'alert-warning';
    const icon = type === 'success' ? 'check-circle-fill text-success' : type === 'danger' ? 'exclamation-circle-fill text-danger' : 'info-circle-fill text-warning';

    container.innerHTML = `
      <div class="alert ${bgClass} alert-dismissible fade show d-flex align-items-center shadow-sm mb-4 animate-fade-in" role="alert">
        <i class="bi bi-${icon} me-2 fs-5"></i>
        <div class="fw-medium">${message}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    `;

    setTimeout(() => {
      if (container.firstElementChild) {
        container.innerHTML = '';
      }
    }, 6000);
  },

  /**
   * Toggle mobile sidebar drawer
   */
  toggleSidebar() {
    const sidebar = document.querySelector('.dashboard-sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    if (sidebar) sidebar.classList.toggle('show');
    if (overlay) overlay.classList.toggle('show');
  },

  /**
   * Render Sidebar Dashboard Sesuai Role (Admin, UMKM, Buyer)
   */
  renderDashboardSidebar(activeKey = '') {
    const user = auth.getUser();
    const role = (user?.role?.nama_role || '').toLowerCase();
    const basePath = auth.getRelativePath('');

    let sections = [];

    if (role === 'admin') {
      sections = [
        {
          title: 'MENU UTAMA',
          items: [
            { key: 'dashboard', label: 'Dashboard', icon: 'bi-grid-1x2-fill', href: basePath + 'admin/dashboard.html' },
            { key: 'verifikasi-umkm', label: 'Verifikasi UMKM', icon: 'bi-building-check', href: basePath + 'admin/verifikasi-umkm.html' },
            { key: 'moderasi-produk', label: 'Moderasi Produk', icon: 'bi-box-seam-fill', href: basePath + 'admin/moderasi-produk.html' }
          ]
        },
        {
          title: 'PROGRAM & INISIATIF',
          items: [
            { key: 'skor-lestari', label: 'Skor KADIN Lestari', icon: 'bi-tree-fill', href: basePath + 'admin/skor-lestari.html' },
            { key: 'program', label: 'Program & Pelatihan', icon: 'bi-mortarboard-fill', href: basePath + 'admin/program.html' },
            { key: 'peluang-bisnis', label: 'Peluang Bisnis B2B', icon: 'bi-briefcase-fill', href: basePath + 'admin/peluang-bisnis.html' },
            { key: 'wa-bot-simulator', label: 'WA Bot Simulator (AI)', icon: 'bi-whatsapp', href: basePath + 'admin/wa-bot-simulator.html' }
          ]
        }
      ];
    } else if (role === 'umkm') {
      sections = [
        {
          title: 'MENU UTAMA',
          items: [
            { key: 'dashboard', label: 'Dashboard', icon: 'bi-grid-1x2-fill', href: basePath + 'umkm/dashboard.html' },
            { key: 'profil', label: 'Profil Usaha', icon: 'bi-shop', href: basePath + 'umkm/profil.html' },
            { key: 'produk', label: 'Kelola Produk', icon: 'bi-boxes', href: basePath + 'umkm/produk.html' }
          ]
        },
        {
          title: 'PENGEMBANGAN & B2B',
          items: [
            { key: 'skor-lestari', label: 'Skor KADIN Lestari', icon: 'bi-tree-fill', href: basePath + 'umkm/skor-lestari.html' },
            { key: 'program', label: 'Program Pembinaan', icon: 'bi-mortarboard-fill', href: basePath + 'umkm/program.html' },
            { key: 'peluang-bisnis', label: 'Peluang Bisnis B2B', icon: 'bi-briefcase-fill', href: basePath + 'umkm/peluang-bisnis.html' }
          ]
        }
      ];
    } else if (role === 'buyer') {
      sections = [
        {
          title: 'MENU UTAMA',
          items: [
            { key: 'dashboard', label: 'Dashboard Buyer', icon: 'bi-grid-1x2-fill', href: basePath + 'buyer/dashboard.html' },
            { key: 'peluang-bisnis', label: 'Kelola Pengadaan (RFQ)', icon: 'bi-briefcase-fill', href: basePath + 'buyer/peluang-bisnis.html' }
          ]
        }
      ];
    }

    const menuHtml = sections.map(sec => `
      <div class="sidebar-heading">${sec.title}</div>
      ${sec.items.map(item => `
        <li class="sidebar-item">
          <a href="${item.href}" class="sidebar-link ${item.key === activeKey ? 'active' : ''}">
            <i class="bi ${item.icon}"></i>
            <span>${item.label}</span>
          </a>
        </li>
      `).join('')}
    `).join('');

    const initialLetter = (user?.nama || 'U').charAt(0).toUpperCase();

    return `
      <div class="sidebar-overlay" onclick="ui.toggleSidebar()"></div>
      <aside class="dashboard-sidebar">
        <a href="${basePath}index.html" class="sidebar-brand">
          <i class="bi bi-shield-shaded me-2 text-warning fs-4"></i>
          <div>
            <div class="lh-1"><span>KADIN</span> Hub</div>
            <small style="font-size: 0.65rem; letter-spacing: 0.08em; color: rgba(255,255,255,0.5);">KOTA YOGYAKARTA</small>
          </div>
        </a>
        <ul class="sidebar-menu">
          ${menuHtml}
        </ul>
        <div class="sidebar-footer">
          <div class="d-flex align-items-center mb-2">
            <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center fw-bold me-2 shadow-sm" style="width: 36px; height: 36px; font-size: 0.95rem;">
              ${initialLetter}
            </div>
            <div class="overflow-hidden me-auto">
              <div class="text-white text-truncate fw-semibold" style="font-size: 0.85rem;">${user?.nama || 'User'}</div>
              <div class="text-secondary text-capitalize" style="font-size: 0.72rem;">${role}</div>
            </div>
          </div>
          <button onclick="auth.logout()" class="btn btn-sm btn-outline-light w-100 d-flex align-items-center justify-content-center gap-2 mt-2" style="font-size: 0.8rem; border-color: rgba(255,255,255,0.2);">
            <i class="bi bi-box-arrow-right"></i> Keluar
          </button>
        </div>
      </aside>
    `;
  },

  /**
   * Render Topbar Dashboard
   */
  renderDashboardTopbar(title = '') {
    const user = auth.getUser();
    return `
      <header class="dashboard-topbar">
        <div class="d-flex align-items-center gap-3">
          <button class="btn btn-sm btn-light border d-lg-none" type="button" onclick="ui.toggleSidebar()" aria-label="Menu">
            <i class="bi bi-list fs-5"></i>
          </button>
          <h1 class="h5 mb-0 fw-bold text-navy">${title}</h1>
        </div>
        <div class="d-flex align-items-center gap-2 gap-md-3">
          <a href="${auth.getRelativePath('katalog.html')}" target="_blank" class="btn btn-sm btn-kadin-outline d-none d-sm-inline-flex align-items-center gap-1">
            <i class="bi bi-globe2"></i> <span class="d-none d-md-inline">Katalog Publik</span>
          </a>
          <span class="text-muted d-none d-sm-inline">|</span>
          <div class="d-flex align-items-center gap-2">
            <span class="badge text-white px-2 py-1 text-capitalize fw-bold" style="background-color: var(--kadin-navy); font-size: 0.75rem;">
              ${user?.role?.nama_role || ''}
            </span>
          </div>
        </div>
      </header>
    `;
  },

  /**
   * Dialog konfirmasi bergaya aplikasi (pengganti window.confirm)
   * Pemakaian: if (!(await ui.confirm('Hapus produk ini?', { variant: 'danger', confirmText: 'Ya, hapus' }))) return;
   */
  confirm(message, { title = 'Konfirmasi', confirmText = 'Ya, lanjutkan', cancelText = 'Batal', variant = 'primary' } = {}) {
    if (typeof bootstrap === 'undefined') return Promise.resolve(window.confirm(message));
    return new Promise((resolve) => {
      const id = 'ui-confirm-modal';
      document.getElementById(id)?.remove();
      document.body.insertAdjacentHTML('beforeend', `
        <div class="modal fade" id="${id}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius:16px">
              <div class="modal-body p-4 text-center">
                <h5 class="fw-bold mb-2" data-role="title"></h5>
                <p class="text-muted mb-4" style="white-space:pre-line" data-role="message"></p>
                <div class="d-flex gap-2 justify-content-center">
                  <button type="button" class="btn btn-light px-4" data-role="cancel"></button>
                  <button type="button" class="btn btn-${variant} px-4" data-role="ok"></button>
                </div>
              </div>
            </div>
          </div>
        </div>`);
      const el = document.getElementById(id);
      el.querySelector('[data-role=title]').textContent = title;
      el.querySelector('[data-role=message]').textContent = message;
      el.querySelector('[data-role=cancel]').textContent = cancelText;
      el.querySelector('[data-role=ok]').textContent = confirmText;
      el.style.zIndex = 1065; // di atas modal lain yang sedang terbuka
      let result = false;
      const modal = new bootstrap.Modal(el);
      el.querySelector('[data-role=ok]').onclick = () => { result = true; modal.hide(); };
      el.querySelector('[data-role=cancel]').onclick = () => modal.hide();
      el.addEventListener('shown.bs.modal', () => {
        const b = document.querySelectorAll('.modal-backdrop');
        b[b.length - 1]?.style.setProperty('z-index', '1060');
      }, { once: true });
      el.addEventListener('hidden.bs.modal', () => { el.remove(); resolve(result); }, { once: true });
      modal.show();
    });
  },

  /**
   * Toast melayang di pojok kanan atas (pengganti window.alert)
   * type: success | danger | warning | info
   */
  toast(message, type = 'success') {
    if (typeof bootstrap === 'undefined') { this.showAlert(message, type); return; }
    let box = document.getElementById('ui-toast-box');
    if (!box) {
      box = document.createElement('div');
      box.id = 'ui-toast-box';
      box.className = 'toast-container position-fixed top-0 end-0 p-3';
      box.style.zIndex = 2000;
      document.body.appendChild(box);
    }
    const bg = { success: 'text-bg-success', danger: 'text-bg-danger', warning: 'text-bg-warning', info: 'text-bg-info' }[type] || 'text-bg-success';
    const el = document.createElement('div');
    el.className = `toast align-items-center border-0 ${bg}`;
    el.setAttribute('role', 'alert');
    el.innerHTML = `<div class="d-flex"><div class="toast-body"></div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button></div>`;
    el.querySelector('.toast-body').textContent = message;
    box.appendChild(el);
    el.addEventListener('hidden.bs.toast', () => el.remove());
    new bootstrap.Toast(el, { delay: 4000 }).show();
  },

  /**
   * Helper alias for menu toggle button in custom headers
   */
  initMenuToggle() {
    const btn = document.getElementById('menu-toggle');
    if (btn) {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        this.toggleSidebar();
      });
    }
  },

  /**
   * Helper alias for renderSidebar
   */
  renderSidebar(role = '') {
    const container = document.getElementById('sidebar-container') || document.getElementById('sidebarMenu');
    if (container) {
      if (container.id === 'sidebar-container') {
        container.innerHTML = this.renderDashboardSidebar('');
      }
    }
  }
};

window.ui = ui;
window.UI = ui;
