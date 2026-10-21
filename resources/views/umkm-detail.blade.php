<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil UMKM Binaan - KADIN Digital Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  @vite(['resources/css/kadin.css', 'resources/js/app.js'])
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg public-navbar sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="index.html">
        <i class="bi bi-shield-shaded text-warning fs-3"></i>
        <div>
          <div class="fw-bold text-navy lh-1" style="font-size: 1.2rem; color: var(--kadin-navy);">KADIN DIGITAL HUB</div>
          <small class="text-secondary" style="font-size: 0.72rem; letter-spacing: 0.05em;">KOTA YOGYAKARTA</small>
        </div>
      </a>
      <div class="d-flex align-items-center gap-2 ms-auto">
        <a href="katalog.html" class="btn btn-sm btn-kadin-outline"><i class="bi bi-arrow-left"></i> Katalog Produk</a>
      </div>
    </div>
  </nav>

  <!-- Profil UMKM Header -->
  <section class="py-5 bg-light border-bottom">
    <div class="container" id="umkmProfileHeader">
      <div class="row align-items-center g-4">
        <div class="col-auto">
          <div class="rounded-4 bg-navy text-white d-flex align-items-center justify-content-center fw-bold fs-1" style="width: 90px; height: 90px; background-color: var(--kadin-navy);" id="umkmLogo">
            U
          </div>
        </div>
        <div class="col">
          <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
            <h1 class="h3 fw-bold text-navy mb-0" id="umkmNamaUsaha">Memuat profil...</h1>
            <span class="badge-status status-verified"><i class="bi bi-patch-check-fill"></i> Terverifikasi KADIN</span>
            <span id="badgeSkorLestari"></span>
          </div>
          <div class="text-secondary small mb-2" id="umkmKategoriDeskripsi">-</div>
          <div class="d-flex flex-wrap gap-2 mb-3" id="tagsRamahLingkungan"></div>
          <div class="d-flex flex-wrap gap-3 small text-muted">
            <span><i class="bi bi-geo-alt-fill text-danger me-1"></i> <span id="umkmAlamat">-</span></span>
            <span><i class="bi bi-telephone-fill text-success me-1"></i> <span id="umkmKontak">-</span></span>
            <span><i class="bi bi-envelope-fill text-primary me-1"></i> <span id="umkmEmail">-</span></span>
          </div>
        </div>
        <div class="col-lg-auto">
          <a href="#" id="waDirectBtn" target="_blank" class="btn btn-success px-4 py-2 d-inline-flex align-items-center gap-2">
            <i class="bi bi-whatsapp"></i> Hubungi Pemilik Usaha
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Tentang Usaha & Produk Binaan -->
  <main class="py-5">
    <div class="container">
      <div class="row g-4">
        
        <div class="col-lg-4">
          <div class="card-clean p-4 mb-4">
            <h5 class="fw-bold text-navy mb-3">Tentang Usaha</h5>
            <p class="text-secondary small mb-0" id="umkmDeskripsi" style="white-space: pre-line;">-</p>
          </div>

          <div class="card-clean p-4 mb-4 border border-warning border-opacity-50">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <h6 class="fw-bold text-navy mb-0"><i class="bi bi-tree-fill text-success me-1"></i> Skor KADIN Lestari</h6>
              <span id="skorAngkaBadge" class="fw-bold text-navy">-</span>
            </div>
            <p class="small text-secondary mb-3">Indeks keberlanjutan berbasis pertumbuhan usaha, intensitas pendampingan kurator, dan praktik ramah lingkungan.</p>
            <div id="skorLestariDetailBox"></div>
          </div>

          <div class="card-clean p-4 bg-light">
            <h6 class="fw-bold text-navy mb-2"><i class="bi bi-shield-lock-fill text-warning me-1"></i> Verifikasi KADIN Hub</h6>
            <p class="small text-muted mb-0">
              Profil ini telah divalidasi keabsahannya oleh KADIN Kota Yogyakarta. Transaksi didukung komunikasi langsung via kontak resmi.
            </p>
          </div>
        </div>

        <div class="col-lg-8">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="h5 fw-bold text-navy mb-0">Daftar Produk Terkurasi (<span id="produkCount">0</span>)</h4>
          </div>

          <div class="row g-4" id="produkUmkmGrid">
            <div class="col-12 text-center py-5">
              <div class="spinner-border text-navy" role="status"></div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </main>

  <footer class="public-footer">
    <div class="container text-center small text-light text-opacity-50">
      &copy; 2026 KADIN Kota Yogyakarta. Hak Cipta Dilindungi.
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', async () => {
      const urlParams = new URLSearchParams(window.location.search);
      const umkmId = urlParams.get('id');

      if (!umkmId) {
        ui.toast('ID UMKM tidak valid.', 'warning');
        setTimeout(() => { window.location.href = 'katalog.html'; }, 1500);
        return;
      }

      try {
        const res = await api.get(`/publik/umkm/${umkmId}`);
        const u = res.data;

        if (!u) {
          ui.toast('UMKM tidak ditemukan atau belum terverifikasi', 'warning');
          setTimeout(() => { window.location.href = 'katalog.html'; }, 1500);
          return;
        }

        document.getElementById('umkmNamaUsaha').innerText = u.nama_usaha;
        document.getElementById('umkmKategoriDeskripsi').innerText = `Sektor: ${u.kategori?.nama_kategori || 'Umum'}`;
        document.getElementById('umkmAlamat').innerText = u.alamat || '-';
        document.getElementById('umkmKontak').innerText = u.kontak_wa || '-';
        document.getElementById('umkmEmail').innerText = u.email_usaha || u.user?.email || '-';
        document.getElementById('umkmDeskripsi').innerText = u.deskripsi || 'Belum ada deskripsi profil usaha.';
        document.getElementById('umkmLogo').innerText = (u.nama_usaha || 'U').charAt(0).toUpperCase();

        // Skor KADIN Lestari
        if (u.skor_lestari_level) {
          document.getElementById('badgeSkorLestari').innerHTML = ui.renderLestariBadge(u.skor_lestari_level, u.skor_lestari);
          document.getElementById('skorAngkaBadge').innerHTML = ui.renderLestariBadge(u.skor_lestari_level, u.skor_lestari);
          document.getElementById('skorLestariDetailBox').innerHTML = `
            <div class="p-2 bg-light rounded border small mb-2">
              <div class="d-flex justify-content-between mb-1">
                <span class="text-secondary">Level Keberlanjutan:</span>
                <span class="fw-bold text-capitalize text-navy">${u.skor_lestari_level}</span>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-secondary">Skor Indeks:</span>
                <span class="fw-bold text-navy">${u.skor_lestari || 0} / 100</span>
              </div>
            </div>
          `;
        } else {
          document.getElementById('badgeSkorLestari').innerHTML = ui.renderLestariBadge('bronze', 0);
          document.getElementById('skorAngkaBadge').innerText = '0/100';
        }

        // Tags Ramah Lingkungan
        const tagsContainer = document.getElementById('tagsRamahLingkungan');
        const tags = Array.isArray(u.praktik_ramah_lingkungan) ? u.praktik_ramah_lingkungan : [];
        if (tags.length > 0) {
          tagsContainer.innerHTML = tags.map(t => ui.renderTagPraktik(t)).join('');
        } else {
          tagsContainer.innerHTML = '';
        }

        // WA Direct
        let phone = u.kontak_wa ? u.kontak_wa.replace(/[^0-9]/g, '') : '';
        if (phone.startsWith('0')) phone = '62' + phone.substring(1);
        const msg = encodeURIComponent(`Halo ${u.nama_usaha}, saya melihat profil usaha Anda di KADIN Digital Hub Yogyakarta.`);
        document.getElementById('waDirectBtn').href = `https://wa.me/${phone}?text=${msg}`;

        // Render Produk
        const grid = document.getElementById('produkUmkmGrid');
        const produkList = u.produk || [];
        document.getElementById('produkCount').innerText = produkList.length;

        if (produkList.length === 0) {
          grid.innerHTML = '<div class="col-12 text-center text-muted py-5 card-clean">Belum ada produk yang disetujui untuk UMKM ini.</div>';
          return;
        }

        grid.innerHTML = produkList.map(p => `
          <div class="col-md-6">
            <div class="card-clean card-interactive h-100 d-flex flex-column overflow-hidden">
              <div class="position-relative bg-light text-center" style="height: 180px; overflow: hidden;">
                ${p.foto_produk ? 
                  `<img src="${p.foto_url || p.foto_produk}" class="w-100 h-100 object-fit-cover" alt="${p.nama_produk}">` : 
                  `<div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image fs-1"></i></div>`
                }
              </div>
              <div class="p-3 d-flex flex-column flex-grow-1">
                <h6 class="fw-bold text-navy mb-1 text-truncate">${p.nama_produk}</h6>
                <div class="fw-bold text-warning mb-2">${ui.formatRupiah(p.harga)}</div>
                <div class="mt-auto pt-2 border-top d-flex justify-content-end">
                  <a href="produk-detail.html?id=${p.id}" class="btn btn-sm btn-kadin-outline px-3">
                    Detail Produk
                  </a>
                </div>
              </div>
            </div>
          </div>
        `).join('');

      } catch (err) {
        console.error('Error load profil UMKM:', err);
        ui.toast('Gagal memuat profil UMKM.', 'danger');
      }
    });
  </script>
</body>
</html>
