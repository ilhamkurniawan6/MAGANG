<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Program & Pembinaan UMKM - KADIN Digital Hub</title>
  <meta name="description" content="Daftar pelatihan, seminar, dan pendampingan resmi dari KADIN Kota Yogyakarta untuk UMKM binaan.">
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
      <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="index.html">Beranda</a></li>
          <li class="nav-item"><a class="nav-link" href="katalog.html">Katalog Produk</a></li>
          <li class="nav-item"><a class="nav-link active fw-semibold" href="program.html">Program & Pembinaan</a></li>
          <li class="nav-item"><a class="nav-link" href="index.html#tentang">Tentang Hub</a></li>
        </ul>
        <div class="d-flex align-items-center gap-2" id="authNavButtons">
          <a href="login.html" class="btn btn-kadin-outline btn-sm px-3">Masuk</a>
          <a href="register-umkm.html" class="btn btn-kadin-gold btn-sm px-3">Daftar UMKM</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Header -->
  <header class="py-5 bg-light border-bottom">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-8">
          <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white border border-secondary border-opacity-25 text-navy small mb-2">
            <i class="bi bi-mortarboard-fill text-warning"></i> Pengembangan Kapasitas Usaha
          </div>
          <h1 class="h3 fw-bold text-navy mb-2">Program Pelatihan & Pembinaan UMKM</h1>
          <p class="text-secondary mb-0">Inisiatif resmi KADIN Kota Yogyakarta untuk meningkatkan daya saing, standarisasi mutu, dan literasi ekspor mitra binaan.</p>
        </div>
        <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
          <a href="register-umkm.html" class="btn btn-kadin-primary btn-sm px-3">
            <i class="bi bi-building me-1"></i> Gabung Jadi UMKM Binaan
          </a>
        </div>
      </div>
    </div>
  </header>

  <main class="py-5">
    <div class="container">
      <div class="row g-4" id="programListContainer">
        <div class="col-12 text-center py-5">
          <div class="spinner-border text-navy" role="status"></div>
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
      if (auth.isAuthenticated()) {
        const user = auth.getUser();
        const role = user?.role?.nama_role;
        document.getElementById('authNavButtons').innerHTML = `
          <a href="${auth.getDashboardUrl(role)}" class="btn btn-kadin-primary btn-sm px-3 d-flex align-items-center gap-1">
            <i class="bi bi-speedometer2"></i> Dashboard (${role})
          </a>
        `;
      }

      const container = document.getElementById('programListContainer');

      try {
        const res = await api.get('/publik/program');
        const list = res.data || [];

        if (list.length === 0) {
          container.innerHTML = `
            <div class="col-12 text-center py-5">
              <div class="card-clean p-5">
                <i class="bi bi-calendar-x fs-1 text-muted d-block mb-3"></i>
                <h5 class="fw-bold text-navy">Belum ada program yang dibuka saat ini</h5>
                <p class="text-secondary small mb-0">Nantikan informasi pelatihan dan workshop KADIN periode berikutnya.</p>
              </div>
            </div>
          `;
          return;
        }

        container.innerHTML = list.map(p => `
          <div class="col-md-6 col-lg-4">
            <div class="card-clean card-interactive h-100 d-flex flex-column p-4">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-navy text-white text-capitalize px-2 py-1" style="background-color: var(--kadin-navy); font-size: 0.72rem;">
                  <i class="bi bi-tag-fill text-warning me-1"></i> ${p.jenis}
                </span>
                <small class="text-muted"><i class="bi bi-people-fill text-primary"></i> ${p.peserta_count || 0} Terdaftar</small>
              </div>
              <h5 class="fw-bold text-navy mb-2" style="font-size: 1.05rem;">${p.judul}</h5>
              <div class="small text-secondary mb-3">
                <i class="bi bi-calendar3 text-warning me-1"></i> ${ui.formatDate(p.tanggal_mulai)} s.d ${ui.formatDate(p.tanggal_selesai)}
              </div>
              <p class="small text-secondary flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                ${p.deskripsi}
              </p>
              <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                <span class="small text-success fw-semibold"><i class="bi bi-check-circle-fill"></i> Pendaftaran Buka</span>
                <a href="${auth.isAuthenticated() ? 'umkm/program.html' : 'login.html'}" class="btn btn-sm btn-kadin-primary px-3">
                  ${auth.isAuthenticated() ? 'Daftar Ikut' : 'Masuk & Daftar'}
                </a>
              </div>
            </div>
          </div>
        `).join('');

      } catch (err) {
        console.error('Error load public programs:', err);
        container.innerHTML = '<div class="col-12 text-center text-danger py-4">Gagal memuat daftar program.</div>';
      }
    });
  </script>
</body>
</html>
