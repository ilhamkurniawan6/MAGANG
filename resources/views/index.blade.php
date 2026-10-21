<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KADIN Digital Hub - Berdayakan UMKM Binaan Kota Yogyakarta</title>
  <meta name="description" content="Platform resmi KADIN Kota Yogyakarta untuk kurasi, pembinaan, dan etalase digital produk unggulan UMKM binaan.">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  @vite(['resources/css/kadin.css', 'resources/js/app.js'])
</head>
<body>

  <!-- Navbar Publik -->
  <nav class="navbar navbar-expand-lg public-navbar sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="index.html">
        <i class="bi bi-shield-shaded text-warning fs-3"></i>
        <div>
          <div class="fw-bold text-navy lh-1" style="font-size: 1.2rem; color: var(--kadin-navy);">KADIN DIGITAL HUB</div>
          <small class="text-secondary" style="font-size: 0.72rem; letter-spacing: 0.05em;">KOTA YOGYAKARTA</small>
        </div>
      </a>
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active fw-semibold" href="index.html">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="katalog.html">Katalog Produk</a></li>
          <li class="nav-item"><a class="nav-link" href="program.html">Program & Pembinaan</a></li>
          <li class="nav-item"><a class="nav-link" href="#tentang">Tentang Hub</a></li>
        </ul>
        <div class="d-flex align-items-center gap-2" id="authNavButtons">
          <a href="login.html" class="btn btn-kadin-outline btn-sm px-3">Masuk</a>
          <a href="register-umkm.html" class="btn btn-kadin-gold btn-sm px-3">Daftar UMKM</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="py-5" style="background: linear-gradient(135deg, #0B2545 0%, #133E6D 100%); color: #FFFFFF;">
    <div class="container py-4">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white bg-opacity-10 border border-white border-opacity-20 text-warning mb-3" style="font-size: 0.85rem;">
            <i class="bi bi-patch-check-fill"></i> Platform Resmi KADIN Kota Yogyakarta
          </div>
          <h1 class="display-5 fw-bold text-white mb-3">
            Pusat Kurasi & Etalase Digital Produk Unggulan UMKM Binaan
          </h1>
          <p class="lead text-light text-opacity-75 mb-4" style="font-size: 1.05rem;">
            Menghubungkan pelaku usaha mikro, kecil, dan menengah dengan pasar yang lebih luas melalui proses verifikasi resmi dan jaminan mutu dari KADIN.
          </p>
          <div class="d-flex flex-wrap gap-3">
            <a href="katalog.html" class="btn btn-kadin-gold px-4 py-2">
              <i class="bi bi-grid me-2"></i> Jelajahi Katalog Produk
            </a>
            <a href="register-umkm.html" class="btn btn-outline-light px-4 py-2">
              <i class="bi bi-building me-2"></i> Daftarkan Usaha Anda
            </a>
          </div>
        </div>
        <div class="col-lg-5 text-center">
          <div class="p-4 rounded-4 bg-white bg-opacity-10 border border-white border-opacity-20 backdrop-blur">
            <div class="row g-3">
              <div class="col-6">
                <div class="p-3 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-10 text-center">
                  <h3 class="fw-bold text-warning mb-1" id="statUmkmCount">-</h3>
                  <small class="text-light text-opacity-75">UMKM Terverifikasi</small>
                </div>
              </div>
              <div class="col-6">
                <div class="p-3 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-10 text-center">
                  <h3 class="fw-bold text-warning mb-1" id="statProdukCount">-</h3>
                  <small class="text-light text-opacity-75">Produk Terkurasi</small>
                </div>
              </div>
              <div class="col-12 mt-3">
                <div class="p-3 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-10 text-start">
                  <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-shield-check text-warning fs-4"></i>
                    <h6 class="text-white mb-0 fw-bold">Standar Kurasi Resmi</h6>
                  </div>
                  <p class="small text-light text-opacity-75 mb-0">
                    Setiap produk yang tayang telah diverifikasi legalitas dan kualitasnya oleh tim kurator KADIN.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Kategori Unggulan Section -->
  <section class="py-5 bg-light border-bottom">
    <div class="container">
      <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
          <small class="text-warning fw-bold text-uppercase" style="letter-spacing: 0.1em;">Kategori Pilihan</small>
          <h2 class="h3 fw-bold text-navy mb-0">Sektor Industri Binaan</h2>
        </div>
        <a href="katalog.html" class="btn btn-sm btn-kadin-outline">Lihat Semua Kategori <i class="bi bi-arrow-right"></i></a>
      </div>
      <div class="row g-3" id="kategoriList">
        <div class="col-12 text-center py-4">
          <div class="spinner-border text-navy" role="status"></div>
        </div>
      </div>
    </div>
  </section>

  <!-- Produk Unggulan Section -->
  <section class="py-5">
    <div class="container">
      <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
          <small class="text-warning fw-bold text-uppercase" style="letter-spacing: 0.1em;">Katalog Terbaru</small>
          <h2 class="h3 fw-bold text-navy mb-0">Produk Terkurasi Siap Transaksi</h2>
        </div>
        <a href="katalog.html" class="btn btn-sm btn-kadin-outline">Lihat Semua Produk <i class="bi bi-arrow-right"></i></a>
      </div>

      <div class="row g-4" id="produkTerbaruList">
        <div class="col-12 text-center py-5">
          <div class="spinner-border text-navy" role="status"></div>
        </div>
      </div>
    </div>
  </section>

  <!-- Tentang & Alur Onboarding Section -->
  <section class="py-5 bg-light border-top border-bottom" id="tentang">
    <div class="container">
      <div class="text-center max-w-700 mx-auto mb-5" style="max-width: 680px;">
        <small class="text-warning fw-bold text-uppercase" style="letter-spacing: 0.1em;">Alur KADIN Hub</small>
        <h2 class="h3 fw-bold text-navy">Bagaimana UMKM Bergabung?</h2>
        <p class="text-secondary">Proses terstruktur untuk memastikan mutu dan kredibilitas setiap mitra binaan KADIN.</p>
      </div>

      <div class="row g-4">
        <div class="col-md-3">
          <div class="card-clean p-4 h-100 text-center">
            <div class="rounded-circle bg-navy text-white d-inline-flex align-items-center justify-content-center mb-3 fw-bold fs-5" style="width: 48px; height: 48px; background-color: var(--kadin-navy);">1</div>
            <h5 class="fw-bold mb-2">Registrasi Mandiri</h5>
            <p class="small text-secondary mb-0">UMKM mendaftar akun dan memasukkan informasi legalitas dasar usaha.</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card-clean p-4 h-100 text-center">
            <div class="rounded-circle bg-navy text-white d-inline-flex align-items-center justify-content-center mb-3 fw-bold fs-5" style="width: 48px; height: 48px; background-color: var(--kadin-navy);">2</div>
            <h5 class="fw-bold mb-2">Verifikasi KADIN</h5>
            <p class="small text-secondary mb-0">Tim Administrator KADIN memverifikasi keabsahan data profil UMKM.</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card-clean p-4 h-100 text-center">
            <div class="rounded-circle bg-navy text-white d-inline-flex align-items-center justify-content-center mb-3 fw-bold fs-5" style="width: 48px; height: 48px; background-color: var(--kadin-navy);">3</div>
            <h5 class="fw-bold mb-2">Upload & Moderasi</h5>
            <p class="small text-secondary mb-0">UMKM mengunggah katalog produk yang kemudian dikurasi oleh admin.</p>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card-clean p-4 h-100 text-center">
            <div class="rounded-circle bg-warning text-dark d-inline-flex align-items-center justify-content-center mb-3 fw-bold fs-5" style="width: 48px; height: 48px;">4</div>
            <h5 class="fw-bold mb-2">Etalase Publik</h5>
            <p class="small text-secondary mb-0">Produk yang disetujui tayang resmi di etalase dan dapat dihubungi langsung via WhatsApp.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="public-footer">
    <div class="container">
      <div class="row g-4 mb-4">
        <div class="col-lg-5">
          <h5 class="text-white fw-bold mb-3">KADIN Kota Yogyakarta</h5>
          <p class="small text-light text-opacity-75 mb-3">
            Kamar Dagang dan Industri Indonesia (KADIN) Kota Yogyakarta berkomitmen memberdayakan potensi ekonomi lokal melalui akselerasi digital dan standardisasi produk UMKM.
          </p>
          <div class="text-light text-opacity-50 small">
            <i class="bi bi-geo-alt me-1"></i> Kota Yogyakarta, D.I. Yogyakarta
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <h6 class="text-white fw-semibold mb-3">Navigasi</h6>
          <ul class="list-unstyled small">
            <li class="mb-2"><a href="index.html" class="text-light text-opacity-75 text-decoration-none">Beranda</a></li>
            <li class="mb-2"><a href="katalog.html" class="text-light text-opacity-75 text-decoration-none">Katalog Produk</a></li>
            <li class="mb-2"><a href="register-umkm.html" class="text-light text-opacity-75 text-decoration-none">Pendaftaran UMKM</a></li>
            <li class="mb-2"><a href="login.html" class="text-light text-opacity-75 text-decoration-none">Masuk Sistem</a></li>
          </ul>
        </div>
        <div class="col-lg-4 col-6">
          <h6 class="text-white fw-semibold mb-3">Fase & Inisiatif</h6>
          <p class="small text-light text-opacity-75 mb-2">
            <strong>Fase 1:</strong> Fondasi KADIN Hub & Kurasi Produk.
          </p>
          <p class="small text-light text-opacity-50">
            Fase 2 (Skor KADIN Lestari, Program Pembinaan) & Fase 3 (Integrasi AI/WA Bot) segera hadir.
          </p>
        </div>
      </div>
      <div class="pt-3 border-top border-white border-opacity-10 text-center small text-light text-opacity-50">
        &copy; 2026 KADIN Kota Yogyakarta. Hak Cipta Dilindungi.
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', async () => {
      // Periksa auth state untuk navbar
      if (auth.isAuthenticated()) {
        const user = auth.getUser();
        const role = user?.role?.nama_role;
        const navContainer = document.getElementById('authNavButtons');
        navContainer.innerHTML = `
          <a href="${auth.getDashboardUrl(role)}" class="btn btn-kadin-primary btn-sm px-3 d-flex align-items-center gap-1">
            <i class="bi bi-speedometer2"></i> Dashboard (${role})
          </a>
        `;
      }

      // Load Kategori & Produk
      try {
        const [kategoriRes, katalogRes] = await Promise.all([
          api.get('/publik/kategori-produk'),
          api.get('/publik/katalog?limit=8')
        ]);

        // Render Kategori
        const katContainer = document.getElementById('kategoriList');
        if (kategoriRes.data && kategoriRes.data.length > 0) {
          katContainer.innerHTML = kategoriRes.data.map(k => `
            <div class="col-6 col-md-4 col-lg-3">
              <a href="katalog.html?kategori_id=${k.id}" class="card-clean card-interactive p-3 d-flex align-items-center gap-3 text-decoration-none h-100">
                <div class="rounded bg-light p-2 text-navy fs-4 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; color: var(--kadin-navy);">
                  <i class="bi bi-tag-fill text-warning"></i>
                </div>
                <div>
                  <h6 class="fw-bold text-navy mb-0" style="font-size: 0.95rem;">${k.nama_kategori}</h6>
                  <small class="text-secondary" style="font-size: 0.75rem;">${k.produk_count || 0} Produk</small>
                </div>
              </a>
            </div>
          `).join('');
        } else {
          katContainer.innerHTML = '<div class="col-12 text-center text-muted">Belum ada kategori.</div>';
        }

        // Render Produk Terbaru
        const prodContainer = document.getElementById('produkTerbaruList');
        if (katalogRes.data && katalogRes.data.length > 0) {
          prodContainer.innerHTML = katalogRes.data.map(p => `
            <div class="col-6 col-md-4 col-lg-3">
              <div class="card-clean card-interactive h-100 d-flex flex-column overflow-hidden">
                <div class="position-relative bg-light text-center" style="height: 180px; overflow: hidden;">
                  ${p.foto_produk ? 
                    `<img src="${p.foto_url || p.foto_produk}" class="w-100 h-100 object-fit-cover" alt="${p.nama_produk}">` : 
                    `<div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image fs-1"></i></div>`
                  }
                  <span class="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-75" style="font-size: 0.7rem;">
                    ${p.kategori?.nama_kategori || 'Umum'}
                  </span>
                </div>
                <div class="p-3 d-flex flex-column flex-grow-1">
                  <div class="small text-secondary mb-1 d-flex align-items-center gap-1">
                    <i class="bi bi-shop text-warning"></i> ${p.umkm?.nama_usaha || 'UMKM Binaan'}
                  </div>
                  <h6 class="fw-bold text-navy mb-2 text-truncate" title="${p.nama_produk}">
                    <a href="produk-detail.html?id=${p.id}" class="text-decoration-none text-navy">${p.nama_produk}</a>
                  </h6>
                  <div class="mt-auto pt-2 d-flex align-items-center justify-content-between border-top">
                    <span class="fw-bold text-navy" style="font-size: 1rem;">${ui.formatRupiah(p.harga)}</span>
                    <a href="produk-detail.html?id=${p.id}" class="btn btn-sm btn-kadin-outline py-1 px-2" style="font-size: 0.8rem;">
                      Detail
                    </a>
                  </div>
                </div>
              </div>
            </div>
          `).join('');
        } else {
          prodContainer.innerHTML = '<div class="col-12 text-center text-muted py-4">Belum ada produk yang disetujui.</div>';
        }

        // Stats
        if (katalogRes.meta) {
          document.getElementById('statProdukCount').innerText = katalogRes.meta.total || katalogRes.data?.length || '0';
        }
        if (kategoriRes.total_umkm !== undefined) {
          document.getElementById('statUmkmCount').innerText = kategoriRes.total_umkm;
        } else {
          document.getElementById('statUmkmCount').innerText = '10+';
        }

      } catch (err) {
        console.error('Error loading landing data:', err);
      }
    });
  </script>
</body>
</html>
