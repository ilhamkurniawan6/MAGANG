<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Mitra & Buyer - KADIN Digital Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  @vite(['resources/css/kadin.css', 'resources/js/app.js'])
</head>
<body>

  <div class="dashboard-wrapper">
    <!-- Sidebar Container -->
    <div id="sidebar-container"></div>

    <div class="dashboard-main">
      <!-- Topbar Container -->
      <div id="topbar-container"></div>

      <!-- Main Content -->
      <main class="dashboard-content">
        <div id="alert-container"></div>

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
          <div>
            <h1 class="h4 fw-bold text-navy mb-1">Portal Pengadaan & Mitra Buyer</h1>
            <p class="text-secondary small mb-0">Kelola permintaan pengadaan (RFQ) dan temukan UMKM binaan KADIN yang siap memasok kebutuhan Anda.</p>
          </div>
          <div>
            <a href="peluang-bisnis.html" class="btn btn-kadin-gold btn-sm d-flex align-items-center gap-1">
              <i class="bi bi-plus-circle"></i> Buat RFQ Baru
            </a>
          </div>
        </div>

        <div class="alert alert-info d-flex align-items-center gap-3 mb-4 shadow-xs" role="alert">
          <i class="bi bi-patch-check-fill text-info fs-3"></i>
          <div>
            <strong>Selamat Datang di Portal Mitra KADIN!</strong>
            <p class="mb-0 small text-secondary">Akses katalog produk UMKM binaan Kota Yogyakarta terkurasi dan terbitkan kebutuhan pengadaan (RFQ) langsung ke pelaku usaha lokal.</p>
          </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="row g-3 mb-4">
          <div class="col-md-4">
            <div class="stat-card h-100">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary small fw-semibold">Total RFQ Dipublikasikan</span>
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-file-earmark-text-fill"></i></div>
              </div>
              <h3 class="fw-bold text-navy mb-1" id="statTotalRfq">0</h3>
              <span class="small text-muted">Kebutuhan pengadaan Anda</span>
            </div>
          </div>

          <div class="col-md-4">
            <div class="stat-card h-100">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary small fw-semibold">RFQ Masih Terbuka</span>
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-broadcast"></i></div>
              </div>
              <h3 class="fw-bold text-navy mb-1" id="statOpenRfq">0</h3>
              <span class="small text-muted">Sedang menerima penawaran</span>
            </div>
          </div>

          <div class="col-md-4">
            <div class="stat-card h-100">
              <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary small fw-semibold">Total Respon UMKM</span>
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-people-fill"></i></div>
              </div>
              <h3 class="fw-bold text-navy mb-1" id="statTotalMinat">0</h3>
              <span class="small text-muted">Pernyataan minat masuk</span>
            </div>
          </div>
        </div>

        <!-- Quick Navigation / Action Grid -->
        <div class="row g-4 mb-4">
          <div class="col-lg-8">
            <div class="card-clean p-4 h-100">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-navy mb-0" style="font-size: 1rem;"><i class="bi bi-clock-history text-warning me-1"></i> RFQ Terbaru Anda</h5>
                <a href="peluang-bisnis.html" class="btn btn-sm btn-kadin-outline py-1 px-2" style="font-size: 0.8rem;">Kelola Semua</a>
              </div>
              <div class="table-responsive-clean">
                <table class="table-clean">
                  <thead>
                    <tr>
                      <th>Judul Pengadaan</th>
                      <th>Kategori Target</th>
                      <th>Peminat</th>
                      <th class="text-end">Status</th>
                    </tr>
                  </thead>
                  <tbody id="recentRfqTableBody">
                    <tr><td colspan="4" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-navy"></div></td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="card-clean p-4 h-100 bg-light">
              <h5 class="fw-bold text-navy mb-2" style="font-size: 1rem;"><i class="bi bi-grid-fill text-warning me-1"></i> Jelajahi Ekosistem</h5>
              <p class="small text-secondary mb-3">Temukan UMKM terbaik dengan indeks keberlanjutan terverifikasi:</p>
              
              <div class="d-flex flex-column gap-2">
                <a href="../katalog.html" class="p-3 bg-white rounded-3 border text-decoration-none text-dark d-flex align-items-center justify-content-between shadow-xs">
                  <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex"><i class="bi bi-shop fs-5"></i></div>
                    <div>
                      <div class="fw-bold text-navy" style="font-size: 0.9rem;">Katalog Produk UMKM</div>
                      <small class="text-muted" style="font-size: 0.78rem;">Jelajahi produk lokal siap kirim</small>
                    </div>
                  </div>
                  <i class="bi bi-chevron-right text-muted"></i>
                </a>

                <a href="../program.html" class="p-3 bg-white rounded-3 border text-decoration-none text-dark d-flex align-items-center justify-content-between shadow-xs">
                  <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-2 d-flex"><i class="bi bi-award fs-5"></i></div>
                    <div>
                      <div class="fw-bold text-navy" style="font-size: 0.9rem;">Program & CSR KADIN</div>
                      <small class="text-muted" style="font-size: 0.78rem;">Kolaborasi pembinaan berkelanjutan</small>
                    </div>
                  </div>
                  <i class="bi bi-chevron-right text-muted"></i>
                </a>
              </div>
            </div>
          </div>
        </div>

      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', async () => {
      if (!auth.guard(['buyer'])) return;

      document.getElementById('sidebar-container').innerHTML = ui.renderDashboardSidebar('dashboard');
      document.getElementById('topbar-container').innerHTML = ui.renderDashboardTopbar('Dashboard Buyer');

      try {
        const res = await api.get('/buyer/peluang-bisnis');
        const list = res.data || [];

        const totalRfq = list.length;
        const openRfq = list.filter(p => p.status === 'terbuka').length;
        let totalMinat = 0;
        list.forEach(p => {
          totalMinat += (p.minat ? p.minat.length : (p.minat_count || 0));
        });

        document.getElementById('statTotalRfq').innerText = totalRfq;
        document.getElementById('statOpenRfq').innerText = openRfq;
        document.getElementById('statTotalMinat').innerText = totalMinat;

        // Render recent RFQs
        const tbody = document.getElementById('recentRfqTableBody');
        if (!list.length) {
          tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted">Belum ada RFQ. Klik "Buat RFQ Baru" untuk mempublikasikan kebutuhan.</td></tr>';
          return;
        }

        tbody.innerHTML = list.slice(0, 5).map(p => {
          const count = p.minat ? p.minat.length : (p.minat_count || 0);

          return `
            <tr>
              <td>
                <div class="fw-bold text-navy">${p.judul}</div>
                <small class="text-muted">${p.jumlah_kebutuhan || '-'}</small>
              </td>
              <td><span class="badge bg-light text-dark border text-capitalize">${p.kategori_target || 'Semua'}</span></td>
              <td><span class="badge bg-info bg-opacity-10 text-info border">${count} UMKM</span></td>
              <td class="text-end">${ui.renderBadge(p.status)}</td>
            </tr>
          `;
        }).join('');

      } catch (err) {
        console.error('Error load buyer dashboard:', err);
        ui.showAlert(err.message || 'Gagal memuat dashboard buyer', 'danger');
      }
    });
  </script>
</body>
</html>
