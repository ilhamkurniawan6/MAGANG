<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Katalog Produk UMKM - KADIN Digital Hub</title>
  <meta name="description" content="Jelajahi produk-produk unggulan terkurasi dari UMKM Binaan KADIN Kota Yogyakarta.">
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
          <li class="nav-item"><a class="nav-link active fw-semibold" href="katalog.html">Katalog Produk</a></li>
          <li class="nav-item"><a class="nav-link" href="index.html#tentang">Tentang Hub</a></li>
        </ul>
        <div class="d-flex align-items-center gap-2" id="authNavButtons">
          <a href="login.html" class="btn btn-kadin-outline btn-sm px-3">Masuk</a>
          <a href="register-umkm.html" class="btn btn-kadin-gold btn-sm px-3">Daftar UMKM</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Page Header -->
  <header class="py-4 bg-light border-bottom">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h1 class="h3 fw-bold text-navy mb-1">Katalog Produk Terkurasi</h1>
          <p class="text-secondary small mb-0">Hanya menampilkan produk yang telah disetujui & diverifikasi oleh KADIN.</p>
        </div>
        <div class="col-md-6 mt-3 mt-md-0 text-md-end">
          <span class="badge bg-white text-dark border px-3 py-2" id="totalCountBadge">
            Memuat produk...
          </span>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="py-5">
    <div class="container">
      <div class="row g-4">
        
        <!-- Filter Sidebar -->
        <div class="col-lg-3">
          <div class="card-clean p-4 sticky-top" style="top: 90px;">
            <h5 class="fw-bold mb-3 text-navy">Filter & Pencarian</h5>
            
            <form id="filterForm">
              <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Cari Produk / Usaha</label>
                <div class="input-group">
                  <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                  <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Nama produk/usaha...">
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Kategori Produk</label>
                <select class="form-select" id="kategoriSelect">
                  <option value="">Semua Kategori</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-semibold text-secondary">Urutkan</label>
                <select class="form-select" id="sortSelect">
                  <option value="terbaru">Terbaru</option>
                  <option value="harga_asc">Harga: Terendah ke Tertinggi</option>
                  <option value="harga_desc">Harga: Tertinggi ke Terendah</option>
                  <option value="nama_asc">Nama: A - Z</option>
                </select>
              </div>

              <button type="submit" class="btn btn-kadin-primary w-100 mb-2">Terapkan Filter</button>
              <button type="button" id="resetBtn" class="btn btn-kadin-outline w-100 btn-sm">Reset</button>
            </form>
          </div>
        </div>

        <!-- Product Grid -->
        <div class="col-lg-9">
          <div class="row g-4" id="produkGrid">
            <div class="col-12 text-center py-5">
              <div class="spinner-border text-navy" role="status"></div>
            </div>
          </div>

          <!-- Pagination Container -->
          <div class="d-flex justify-content-center mt-5" id="paginationContainer"></div>
        </div>

      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="public-footer">
    <div class="container text-center small text-light text-opacity-50">
      &copy; 2026 KADIN Kota Yogyakarta. Hak Cipta Dilindungi.
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    let currentPage = 1;

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

      // Pre-fill dari URL params
      const urlParams = new URLSearchParams(window.location.search);
      const initialKategori = urlParams.get('kategori_id') || '';
      const initialSearch = urlParams.get('q') || '';

      // Load Kategori
      try {
        const katRes = await api.get('/publik/kategori-produk');
        const katSelect = document.getElementById('kategoriSelect');
        if (katRes.data) {
          katRes.data.forEach(k => {
            const opt = document.createElement('option');
            opt.value = k.id;
            opt.textContent = k.nama_kategori;
            if (String(k.id) === String(initialKategori)) opt.selected = true;
            katSelect.appendChild(opt);
          });
        }
      } catch (err) {
        console.error('Gagal load kategori:', err);
      }

      if (initialSearch) {
        document.getElementById('searchInput').value = initialSearch;
      }

      loadKatalog(1);

      document.getElementById('filterForm').addEventListener('submit', (e) => {
        e.preventDefault();
        loadKatalog(1);
      });

      document.getElementById('resetBtn').addEventListener('click', () => {
        document.getElementById('searchInput').value = '';
        document.getElementById('kategoriSelect').value = '';
        document.getElementById('sortSelect').value = 'terbaru';
        loadKatalog(1);
      });
    });

    async function loadKatalog(page = 1) {
      currentPage = page;
      const q = document.getElementById('searchInput').value;
      const kategori_id = document.getElementById('kategoriSelect').value;
      const sort = document.getElementById('sortSelect').value;

      const grid = document.getElementById('produkGrid');
      grid.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-navy" role="status"></div></div>';

      try {
        const params = new URLSearchParams({
          page,
          limit: 12,
          ...(q ? { q } : {}),
          ...(kategori_id ? { kategori_id } : {}),
          ...(sort ? { sort } : {})
        });

        const res = await api.get(`/publik/katalog?${params.toString()}`);
        
        const total = res.meta?.total !== undefined ? res.meta.total : (res.data ? res.data.length : 0);
        document.getElementById('totalCountBadge').innerHTML = `Menampilkan <strong>${total}</strong> produk disetujui`;

        if (!res.data || res.data.length === 0) {
          grid.innerHTML = `
            <div class="col-12 text-center py-5">
              <div class="p-5 card-clean">
                <i class="bi bi-box-seam fs-1 text-muted mb-3 d-block"></i>
                <h5 class="fw-bold text-navy">Tidak ada produk ditemukan</h5>
                <p class="text-secondary small mb-0">Coba ubah kata kunci atau filter kategori Anda.</p>
              </div>
            </div>
          `;
          document.getElementById('paginationContainer').innerHTML = '';
          return;
        }

        grid.innerHTML = res.data.map(p => `
          <div class="col-md-6 col-xl-4">
            <div class="card-clean card-interactive h-100 d-flex flex-column overflow-hidden">
              <div class="position-relative bg-light text-center" style="height: 190px; overflow: hidden;">
                ${p.foto_produk ? 
                  `<img src="${p.foto_url || p.foto_produk}" class="w-100 h-100 object-fit-cover" alt="${p.nama_produk}">` : 
                  `<div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image fs-1"></i></div>`
                }
                <span class="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-75" style="font-size: 0.72rem;">
                  ${p.kategori?.nama_kategori || 'Umum'}
                </span>
              </div>
              <div class="p-3 d-flex flex-column flex-grow-1">
                <div class="small text-secondary mb-1 d-flex align-items-center gap-1">
                  <i class="bi bi-shop text-warning"></i>
                  <a href="umkm-detail.html?id=${p.umkm?.id}" class="text-secondary text-decoration-none hover-gold">
                    ${p.umkm?.nama_usaha || 'UMKM Binaan'}
                  </a>
                </div>
                <h5 class="fw-bold text-navy mb-2 text-truncate" title="${p.nama_produk}">
                  <a href="produk-detail.html?id=${p.id}" class="text-decoration-none text-navy">${p.nama_produk}</a>
                </h5>
                <p class="small text-secondary mb-3 text-truncate-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 38px;">
                  ${p.deskripsi || 'Tidak ada deskripsi'}
                </p>
                <div class="mt-auto pt-2 d-flex align-items-center justify-content-between border-top">
                  <div>
                    <span class="d-block text-secondary" style="font-size: 0.75rem;">Harga</span>
                    <span class="fw-bold text-navy" style="font-size: 1.05rem;">${ui.formatRupiah(p.harga)}</span>
                  </div>
                  <a href="produk-detail.html?id=${p.id}" class="btn btn-sm btn-kadin-primary px-3">
                    Detail <i class="bi bi-arrow-right"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        `).join('');

        // Render Pagination
        renderPagination(res.meta);

      } catch (err) {
        console.error('Error load katalog:', err);
        grid.innerHTML = '<div class="col-12 text-center text-danger py-4">Gagal memuat katalog. Pastikan backend server aktif.</div>';
      }
    }

    function renderPagination(meta) {
      const container = document.getElementById('paginationContainer');
      if (!meta || meta.last_page <= 1) {
        container.innerHTML = '';
        return;
      }

      let html = '<ul class="pagination">';
      // Prev
      html += `<li class="page-item ${meta.current_page === 1 ? 'disabled' : ''}">
        <button class="page-link" onclick="loadKatalog(${meta.current_page - 1})">Sebelumnya</button>
      </li>`;

      for (let i = 1; i <= meta.last_page; i++) {
        html += `<li class="page-item ${meta.current_page === i ? 'active' : ''}">
          <button class="page-link" onclick="loadKatalog(${i})">${i}</button>
        </li>`;
      }

      // Next
      html += `<li class="page-item ${meta.current_page === meta.last_page ? 'disabled' : ''}">
        <button class="page-link" onclick="loadKatalog(${meta.current_page + 1})">Berikutnya</button>
      </li>`;
      html += '</ul>';

      container.innerHTML = html;
    }
  </script>
</body>
</html>
