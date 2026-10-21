<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verifikasi UMKM - Administrator KADIN Hub</title>
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
            <h1 class="h4 fw-bold text-navy mb-1">Verifikasi Pendaftaran UMKM</h1>
            <p class="text-secondary small mb-0">Tinjau legalitas dan validitas data UMKM sebelum diberikan status binaan resmi.</p>
          </div>
          <!-- Filter status pills -->
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-sm btn-kadin-outline active" data-filter="" onclick="filterStatus('', this)">Semua</button>
            <button type="button" class="btn btn-sm btn-kadin-outline" data-filter="pending" onclick="filterStatus('pending', this)">Pending</button>
            <button type="button" class="btn btn-sm btn-kadin-outline" data-filter="verified" onclick="filterStatus('verified', this)">Verified</button>
            <button type="button" class="btn btn-sm btn-kadin-outline" data-filter="rejected" onclick="filterStatus('rejected', this)">Rejected</button>
          </div>
        </div>

        <!-- Filter Search Bar -->
        <div class="card-clean p-3 mb-4 bg-white">
          <div class="row g-2">
            <div class="col-md-8">
              <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari nama usaha, pemilik, atau kontak WA...">
              </div>
            </div>
            <div class="col-md-4">
              <button class="btn btn-kadin-primary w-100" onclick="loadUmkmList()">Cari Data</button>
            </div>
          </div>
        </div>

        <!-- Table UMKM -->
        <div class="card-clean p-0 overflow-hidden bg-white">
          <div class="table-responsive">
            <table class="table-clean">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Usaha & Pemilik</th>
                  <th>Sektor Kategori</th>
                  <th>Kontak & Alamat</th>
                  <th>Status</th>
                  <th>Tanggal Daftar</th>
                  <th class="text-end">Aksi Verifikasi</th>
                </tr>
              </thead>
              <tbody id="umkmTableBody">
                <tr><td colspan="7" class="text-center py-5"><div class="spinner-border text-navy"></div></td></tr>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- Modal Detail & Verifikasi -->
  <div class="modal fade" id="verifikasiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold text-navy"><i class="bi bi-patch-check-fill text-warning me-2"></i> Tinjau Profil UMKM</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="modalDetailBody">
          <div class="text-center py-4"><div class="spinner-border text-navy"></div></div>
        </div>
        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-danger btn-sm px-3" onclick="submitVerifikasi('rejected')">
              <i class="bi bi-x-circle me-1"></i> Tolak (Reject)
            </button>
            <button type="button" class="btn btn-success btn-sm px-3" onclick="submitVerifikasi('verified')">
              <i class="bi bi-check-circle me-1"></i> Verifikasi (Approve)
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    let currentFilterStatus = '';
    let selectedUmkmId = null;
    let modalInstance = null;

    document.addEventListener('DOMContentLoaded', () => {
      if (!auth.guard(['admin'])) return;

      document.getElementById('sidebar-container').innerHTML = ui.renderDashboardSidebar('verifikasi-umkm');
      document.getElementById('topbar-container').innerHTML = ui.renderDashboardTopbar('Verifikasi UMKM');
      modalInstance = new bootstrap.Modal(document.getElementById('verifikasiModal'));

      // Check URL param status
      const urlParams = new URLSearchParams(window.location.search);
      const statusParam = urlParams.get('status') || '';
      const idParam = urlParams.get('id');

      if (statusParam) {
        currentFilterStatus = statusParam;
        document.querySelectorAll('.btn-group button').forEach(b => {
          b.classList.toggle('active', b.dataset.filter === statusParam);
        });
      }

      loadUmkmList();

      if (idParam) {
        bukaModalVerifikasi(idParam);
      }
    });

    function filterStatus(status, btn) {
      currentFilterStatus = status;
      document.querySelectorAll('.btn-group button').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      loadUmkmList();
    }

    async function loadUmkmList() {
      const tbody = document.getElementById('umkmTableBody');
      tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5"><div class="spinner-border text-navy"></div></td></tr>';

      const q = document.getElementById('searchInput').value;
      const params = new URLSearchParams({
        ...(currentFilterStatus ? { status: currentFilterStatus } : {}),
        ...(q ? { q } : {})
      });

      try {
        const res = await api.get(`/admin/umkm?${params.toString()}`);
        const list = res.data || [];

        if (list.length === 0) {
          tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada data UMKM ditemukan.</td></tr>';
          return;
        }

        tbody.innerHTML = list.map((u, i) => `
          <tr>
            <td class="fw-semibold text-muted">${i + 1}</td>
            <td>
              <div class="fw-bold text-navy">${u.nama_usaha}</div>
              <small class="text-secondary"><i class="bi bi-person me-1"></i> ${u.user?.nama || '-'}</small>
            </td>
            <td><span class="badge bg-light text-dark border">${u.kategori?.nama_kategori || '-'}</span></td>
            <td>
              <div class="small fw-semibold text-navy"><i class="bi bi-whatsapp text-success me-1"></i> ${u.kontak_wa || '-'}</div>
              <small class="text-secondary text-truncate d-block" style="max-width: 180px;">${u.alamat || '-'}</small>
            </td>
            <td>${ui.renderBadge(u.status_verifikasi)}</td>
            <td><small class="text-muted">${ui.formatDate(u.created_at)}</small></td>
            <td class="text-end">
              <button class="btn btn-sm btn-kadin-primary" onclick="bukaModalVerifikasi(${u.id})">
                <i class="bi bi-search me-1"></i> Periksa & Aksi
              </button>
            </td>
          </tr>
        `).join('');

      } catch (err) {
        console.error('Error load umkm admin:', err);
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-danger">Gagal memuat data UMKM.</td></tr>';
      }
    }

    async function bukaModalVerifikasi(id) {
      selectedUmkmId = id;
      modalInstance.show();
      const body = document.getElementById('modalDetailBody');
      body.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-navy"></div></div>';

      try {
        const res = await api.get(`/admin/umkm/${id}`);
        const u = res.data;

        body.innerHTML = `
          <div class="row g-3">
            <div class="col-md-6">
              <label class="small text-secondary fw-semibold">Nama Usaha</label>
              <div class="fw-bold text-navy fs-5">${u.nama_usaha}</div>
            </div>
            <div class="col-md-6">
              <label class="small text-secondary fw-semibold">Status Saat Ini</label>
              <div>${ui.renderBadge(u.status_verifikasi)}</div>
            </div>
            <div class="col-md-6">
              <label class="small text-secondary fw-semibold">Sektor Kategori</label>
              <div class="fw-medium">${u.kategori?.nama_kategori || '-'}</div>
            </div>
            <div class="col-md-6">
              <label class="small text-secondary fw-semibold">PIC / Pemilik Akun</label>
              <div class="fw-medium">${u.user?.nama || '-'} (${u.user?.email || '-'})</div>
            </div>
            <div class="col-md-6">
              <label class="small text-secondary fw-semibold">Kontak WhatsApp Resmi</label>
              <div class="fw-medium text-success"><i class="bi bi-whatsapp"></i> ${u.kontak_wa || '-'}</div>
            </div>
            <div class="col-md-6">
              <label class="small text-secondary fw-semibold">Email Usaha</label>
              <div class="fw-medium">${u.email_usaha || '-'}</div>
            </div>
            <div class="col-12">
              <label class="small text-secondary fw-semibold">Alamat Lengkap Workshop / Gerai</label>
              <div class="p-2 bg-light rounded border small">${u.alamat || '-'}</div>
            </div>
            <div class="col-12">
              <label class="small text-secondary fw-semibold">Deskripsi Profil Usaha</label>
              <div class="p-2 bg-light rounded border small" style="white-space: pre-line;">${u.deskripsi || 'Tidak ada deskripsi.'}</div>
            </div>
          </div>
        `;
      } catch (err) {
        console.error('Error load detail umkm:', err);
        body.innerHTML = '<div class="alert alert-danger">Gagal memuat detail UMKM.</div>';
      }
    }

    async function submitVerifikasi(status) {
      if (!selectedUmkmId) return;

      const confirmMsg = status === 'verified' 
        ? 'Apakah Anda yakin ingin menyetujui (VERIFY) UMKM ini?' 
        : 'Apakah Anda yakin ingin menolak (REJECT) pendaftaran UMKM ini?';

      const verif = status === 'verified';
      const ok = await ui.confirm(confirmMsg, {
        title: verif ? 'Verifikasi UMKM' : 'Tolak UMKM',
        confirmText: verif ? 'Ya, verifikasi' : 'Ya, tolak',
        variant: verif ? 'success' : 'danger'
      });
      if (!ok) return;

      try {
        await api.post(`/admin/umkm/${selectedUmkmId}/verifikasi`, { status_verifikasi: status });
        ui.showAlert(`Berhasil memperbarui status UMKM menjadi ${status}.`, 'success');
        modalInstance.hide();
        loadUmkmList();
      } catch (err) {
        console.error('Error submit verifikasi:', err);
        ui.toast(err.message || 'Gagal memproses verifikasi UMKM.', 'danger');
      }
    }
  </script>
</body>
</html>
