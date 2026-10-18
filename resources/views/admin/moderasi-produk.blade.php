<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Moderasi Produk - Administrator KADIN Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  @vite(['resources/css/kadin.css', 'resources/js/app.js'])
</head>
<body>

  <div class="dashboard-wrapper">
    <div id="sidebar-container"></div>

    <div class="dashboard-main">
      <div id="topbar-container"></div>

      <main class="dashboard-content">
        <div id="alert-container"></div>

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
          <div>
            <h1 class="h4 fw-bold text-navy mb-1">Moderasi & Kurasi Produk</h1>
            <p class="text-secondary small mb-0">Pastikan seluruh produk yang tayang di etalase publik memenuhi standar mutu KADIN.</p>
          </div>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-kadin-outline active" data-filter="" onclick="filterStatus('', this)">Semua</button>
            <button type="button" class="btn btn-sm btn-kadin-outline" data-filter="menunggu_moderasi" onclick="filterStatus('menunggu_moderasi', this)">Menunggu Moderasi</button>
            <button type="button" class="btn btn-sm btn-kadin-outline" data-filter="disetujui" onclick="filterStatus('disetujui', this)">Disetujui (Tayang)</button>
            <button type="button" class="btn btn-sm btn-kadin-outline" data-filter="ditolak" onclick="filterStatus('ditolak', this)">Ditolak</button>
          </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="card-clean p-3 mb-4 bg-white">
          <div class="row g-2">
            <div class="col-md-8">
              <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari nama produk, nama UMKM...">
              </div>
            </div>
            <div class="col-md-4">
              <button class="btn btn-kadin-primary w-100" onclick="loadProdukList()">Cari Data</button>
            </div>
          </div>
        </div>

        <!-- Table Produk -->
        <div class="card-clean p-0 overflow-hidden bg-white">
          <div class="table-responsive">
            <table class="table-clean">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Produk</th>
                  <th>UMKM Pemilik</th>
                  <th>Kategori</th>
                  <th>Harga</th>
                  <th>Status</th>
                  <th>Tanggal Pengajuan</th>
                  <th class="text-end">Aksi Moderasi</th>
                </tr>
              </thead>
              <tbody id="produkTableBody">
                <tr><td colspan="8" class="text-center py-5"><div class="spinner-border text-navy"></div></td></tr>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- Modal Detail & Moderasi -->
  <div class="modal fade" id="moderasiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold text-navy"><i class="bi bi-box-seam text-warning me-2"></i> Tinjau Produk & Moderasi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="modalDetailBody">
          <div class="text-center py-4"><div class="spinner-border text-navy"></div></div>
        </div>
        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-danger btn-sm px-3" onclick="submitModerasi('ditolak')">
              <i class="bi bi-x-circle me-1"></i> Tolak Produk
            </button>
            <button type="button" class="btn btn-success btn-sm px-3" onclick="submitModerasi('disetujui')">
              <i class="bi bi-check-circle me-1"></i> Setujui & Tayangkan
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    let currentFilterStatus = '';
    let selectedProdukId = null;
    let modalInstance = null;

    document.addEventListener('DOMContentLoaded', () => {
      if (!auth.guard(['admin'])) return;

      document.getElementById('sidebar-container').innerHTML = ui.renderDashboardSidebar('moderasi-produk');
      document.getElementById('topbar-container').innerHTML = ui.renderDashboardTopbar('Moderasi Produk');
      modalInstance = new bootstrap.Modal(document.getElementById('moderasiModal'));

      const urlParams = new URLSearchParams(window.location.search);
      const statusParam = urlParams.get('status') || '';
      if (statusParam) {
        currentFilterStatus = statusParam;
        document.querySelectorAll('.btn-group button').forEach(b => {
          b.classList.toggle('active', b.dataset.filter === statusParam);
        });
      }

      loadProdukList();
    });

    function filterStatus(status, btn) {
      currentFilterStatus = status;
      document.querySelectorAll('.btn-group button').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      loadProdukList();
    }

    async function loadProdukList() {
      const tbody = document.getElementById('produkTableBody');
      tbody.innerHTML = '<tr><td colspan="8" class="text-center py-5"><div class="spinner-border text-navy"></div></td></tr>';

      const q = document.getElementById('searchInput').value;
      const params = new URLSearchParams({
        ...(currentFilterStatus ? { status: currentFilterStatus } : {}),
        ...(q ? { q } : {})
      });

      try {
        const res = await api.get(`/admin/produk?${params.toString()}`);
        const list = res.data || [];

        if (list.length === 0) {
          tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada produk ditemukan.</td></tr>';
          return;
        }

        tbody.innerHTML = list.map((p, i) => `
          <tr>
            <td class="fw-semibold text-muted">${i + 1}</td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="rounded bg-light d-flex align-items-center justify-content-center overflow-hidden flex-shrink-0" style="width: 44px; height: 44px;">
                  ${p.foto_produk ? 
                    `<img src="${p.foto_url || p.foto_produk}" class="w-100 h-100 object-fit-cover">` : 
                    `<i class="bi bi-image text-muted"></i>`
                  }
                </div>
                <div>
                  <div class="fw-bold text-navy">${p.nama_produk}</div>
                </div>
              </div>
            </td>
            <td>
              <div class="fw-semibold text-navy">${p.umkm?.nama_usaha || '-'}</div>
              <small class="text-secondary">${p.umkm?.kontak_wa || '-'}</small>
            </td>
            <td><span class="badge bg-light text-dark border">${p.kategori?.nama_kategori || '-'}</span></td>
            <td><span class="fw-bold text-navy">${ui.formatRupiah(p.harga)}</span></td>
            <td>${ui.renderBadge(p.status)}</td>
            <td><small class="text-muted">${ui.formatDate(p.created_at)}</small></td>
            <td class="text-end">
              <button class="btn btn-sm btn-kadin-primary" onclick="bukaModalModerasi(${p.id})">
                <i class="bi bi-search me-1"></i> Tinjau & Aksi
              </button>
            </td>
          </tr>
        `).join('');

      } catch (err) {
        console.error('Error load produk admin:', err);
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-4 text-danger">Gagal memuat data produk.</td></tr>';
      }
    }

    async function bukaModalModerasi(id) {
      selectedProdukId = id;
      modalInstance.show();
      const body = document.getElementById('modalDetailBody');
      body.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-navy"></div></div>';

      try {
        const res = await api.get(`/admin/produk/${id}`);
        const p = res.data;

        body.innerHTML = `
          <div class="row g-4">
            <div class="col-md-5">
              <div class="card-clean p-2 bg-light text-center">
                ${p.foto_produk ? 
                  `<img src="${p.foto_url || p.foto_produk}" class="img-fluid rounded" style="max-height: 250px; object-fit: contain;">` : 
                  `<div class="py-5 text-muted"><i class="bi bi-image fs-1 d-block"></i><span>Tidak ada foto</span></div>`
                }
              </div>
            </div>
            <div class="col-md-7">
              <div class="mb-2">${ui.renderBadge(p.status)}</div>
              <h4 class="fw-bold text-navy mb-1">${p.nama_produk}</h4>
              <div class="h5 fw-bold text-warning mb-3">${ui.formatRupiah(p.harga)}</div>
              
              <div class="small text-secondary mb-1">Kategori: <strong>${p.kategori?.nama_kategori || '-'}</strong></div>
              <div class="small text-secondary mb-3">UMKM: <strong>${p.umkm?.nama_usaha || '-'}</strong> (${p.umkm?.kontak_wa || '-'})</div>

              <label class="small text-secondary fw-semibold">Deskripsi Produk</label>
              <div class="p-2 bg-light rounded border small" style="white-space: pre-line;">${p.deskripsi || 'Tidak ada deskripsi.'}</div>
            </div>
          </div>
        `;
      } catch (err) {
        console.error('Error load detail produk:', err);
        body.innerHTML = '<div class="alert alert-danger">Gagal memuat detail produk.</div>';
      }
    }

    async function submitModerasi(status) {
      if (!selectedProdukId) return;

      const confirmMsg = status === 'disetujui' 
        ? 'Apakah Anda yakin ingin menyetujui produk ini agar tayang di etalase publik?' 
        : 'Apakah Anda yakin ingin menolak pengajuan produk ini?';

      if (!confirm(confirmMsg)) return;

      try {
        await api.post(`/admin/produk/${selectedProdukId}/moderasi`, { status });
        ui.showAlert(`Status produk berhasil diubah menjadi ${status}.`, 'success');
        modalInstance.hide();
        loadProdukList();
      } catch (err) {
        console.error('Error submit moderasi:', err);
        alert(err.message || 'Gagal memproses moderasi produk.');
      }
    }
  </script>
</body>
</html>
