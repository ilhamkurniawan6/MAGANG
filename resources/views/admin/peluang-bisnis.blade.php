<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pengawasan Peluang Bisnis (RFQ) - Admin KADIN</title>
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
            <h1 class="h4 fw-bold text-navy mb-1">Pengawasan Peluang Bisnis B2B (RFQ)</h1>
            <p class="text-secondary small mb-0">Pantau seluruh permintaan pengadaan (RFQ) dari mitra Buyer dan respon minat dari UMKM binaan.</p>
          </div>
        </div>

        <div class="alert alert-info d-flex align-items-center gap-3 mb-4 shadow-xs" role="alert">
          <div class="rounded-circle bg-info bg-opacity-20 text-info p-2 d-flex">
            <i class="bi bi-briefcase-fill fs-4"></i>
          </div>
          <div>
            <strong>Monitoring Ekosistem B2B Procurement</strong>
            <p class="mb-0 small text-secondary">Admin dapat memantau interaksi antara Buyer dan UMKM, meninjau rincian penawaran, serta mengelola status (terbuka / ditutup) pengadaan.</p>
          </div>
        </div>

        <!-- Filter & Search Card -->
        <div class="card-clean p-3 mb-4 bg-white">
          <div class="row g-3 align-items-center">
            <div class="col-md-6">
              <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari judul pengadaan atau nama buyer...">
              </div>
            </div>
            <div class="col-md-3">
              <select class="form-select" id="filterStatus">
                <option value="">Semua Status</option>
                <option value="terbuka">Terbuka (Open)</option>
                <option value="ditutup">Ditutup (Closed)</option>
              </select>
            </div>
            <div class="col-md-3 text-end">
              <span class="text-muted small fw-semibold" id="totalPeluangText">Memuat data...</span>
            </div>
          </div>
        </div>

        <!-- Table Card -->
        <div class="card-clean p-0 overflow-hidden bg-white">
          <div class="table-responsive-clean">
            <table class="table-clean">
              <thead>
                <tr>
                  <th>Judul Pengadaan</th>
                  <th>Buyer / Pembuat</th>
                  <th>Sektor Target</th>
                  <th>Volume & Kapasitas</th>
                  <th>Peminat UMKM</th>
                  <th>Status</th>
                  <th class="text-end">Aksi</th>
                </tr>
              </thead>
              <tbody id="peluangTableBody">
                <tr>
                  <td colspan="7" class="text-center py-5 text-muted">
                    <div class="spinner-border spinner-border-sm text-navy me-2"></div> Memuat data peluang...
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- Modal Detail Peminat UMKM -->
  <div class="modal fade" id="modalPeminat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold text-navy"><i class="bi bi-people-fill text-warning me-2"></i>Daftar UMKM Peminat RFQ</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3 p-3 bg-light rounded-3 border">
            <h6 class="fw-bold text-navy mb-1" id="detailJudulRfq">-</h6>
            <span class="text-secondary small" id="detailBuyerRfq">-</span>
          </div>

          <div class="table-responsive-clean">
            <table class="table-clean">
              <thead>
                <tr>
                  <th>Nama UMKM & Pemilik</th>
                  <th>Skor Lestari</th>
                  <th>Kontak WA</th>
                  <th>Catatan Penawaran</th>
                  <th>Tgl Pengajuan</th>
                </tr>
              </thead>
              <tbody id="peminatTableBody">
                <tr><td colspan="5" class="text-center py-3 text-muted">Memuat data peminat...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', async () => {
      if (!auth.guard(['admin'])) return;

      document.getElementById('sidebar-container').innerHTML = ui.renderDashboardSidebar('peluang-bisnis');
      document.getElementById('topbar-container').innerHTML = ui.renderDashboardTopbar('Pengawasan Peluang Bisnis B2B');

      let allPeluang = [];
      const modalPeminat = new bootstrap.Modal(document.getElementById('modalPeminat'));

      async function loadPeluang() {
        try {
          const res = await api.get('/admin/peluang-bisnis');
          allPeluang = res.data || [];
          document.getElementById('totalPeluangText').innerText = `Total: ${allPeluang.length} Pengadaan`;
          renderTable();
        } catch (err) {
          ui.showAlert(err.message || 'Gagal memuat peluang bisnis', 'danger');
        }
      }

      function renderTable() {
        const query = document.getElementById('searchInput').value.toLowerCase().trim();
        const statusFilter = document.getElementById('filterStatus').value;

        const filtered = allPeluang.filter(p => {
          const creator = p.creator || {};
          const creatorName = (creator.nama || creator.name || '').toLowerCase();
          const matchQuery = !query || p.judul.toLowerCase().includes(query) || creatorName.includes(query);
          const matchStatus = !statusFilter || p.status === statusFilter;
          return matchQuery && matchStatus;
        });

        const tbody = document.getElementById('peluangTableBody');
        if (!filtered.length) {
          tbody.innerHTML = `
            <tr>
              <td colspan="7" class="text-center py-5 text-muted">Tidak ada data RFQ yang cocok.</td>
            </tr>
          `;
          return;
        }

        tbody.innerHTML = filtered.map(p => {
          const creator = p.creator || {};
          const creatorName = creator.nama || creator.name || 'Mitra Buyer';
          const peminatCount = p.peminat_count || (p.peminat ? p.peminat.length : 0);

          return `
            <tr>
              <td>
                <div class="fw-bold text-navy">${p.judul}</div>
                <small class="text-secondary d-block text-truncate" style="max-width: 250px;">${p.deskripsi ? p.deskripsi : '-'}</small>
              </td>
              <td>
                <div class="fw-semibold text-navy">${creatorName}</div>
                <small class="text-muted">${creator.email || '-'}</small>
              </td>
              <td><span class="badge bg-light text-dark border">${p.kategori?.nama_kategori || 'Semua Sektor'}</span></td>
              <td>
                <div class="small fw-bold text-dark">${p.target_volume || '-'}</div>
                <small class="text-secondary">${p.kapasitas || '-'}</small>
              </td>
              <td>
                <button class="btn btn-sm btn-kadin-outline py-1 px-2 btn-view-peminat" data-id="${p.id}" data-judul="${p.judul.replace(/"/g, '&quot;')}" data-buyer="${creatorName.replace(/"/g, '&quot;')}">
                  <i class="bi bi-people-fill text-warning me-1"></i> ${peminatCount} UMKM
                </button>
              </td>
              <td>${ui.renderBadge(p.status)}</td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-secondary py-1 px-2 btn-toggle-status" data-id="${p.id}" data-status="${p.status}">
                  <i class="bi bi-power me-1"></i> ${p.status === 'terbuka' ? 'Tutup' : 'Buka'}
                </button>
              </td>
            </tr>
          `;
        }).join('');

        // Bind View Peminat
        document.querySelectorAll('.btn-view-peminat').forEach(btn => {
          btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const item = allPeluang.find(p => p.id == id);
            if (!item) return;

            document.getElementById('detailJudulRfq').innerText = item.judul;
            document.getElementById('detailBuyerRfq').innerText = `Buyer: ${btn.getAttribute('data-buyer')}`;
            renderPeminatList(item.peminat || []);
            modalPeminat.show();
          });
        });

        // Bind Toggle Status
        document.querySelectorAll('.btn-toggle-status').forEach(btn => {
          btn.addEventListener('click', async () => {
            const id = btn.getAttribute('data-id');
            const currentStatus = btn.getAttribute('data-status');
            const newStatus = currentStatus === 'terbuka' ? 'ditutup' : 'terbuka';

            try {
              await api.post(`/admin/peluang-bisnis/${id}/status`, { status: newStatus });
              ui.showAlert(`Status RFQ berhasil diubah menjadi ${newStatus}!`, 'success');
              await loadPeluang();
            } catch (err) {
              ui.showAlert(err.message || 'Gagal mengubah status', 'danger');
            }
          });
        });
      }

      function renderPeminatList(minatList) {
        const tbody = document.getElementById('peminatTableBody');
        if (!minatList.length) {
          tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Belum ada UMKM yang menyatakan minat pada pengadaan ini.</td></tr>';
          return;
        }

        tbody.innerHTML = minatList.map(m => {
          const umkm = m.umkm || {};
          const levelBadge = ui.renderLestariBadge(umkm.skor_lestari_level || 'bronze', umkm.skor_lestari || 0);
          const cleanWa = (umkm.kontak_wa || '').replace(/[^0-9]/g, '');

          return `
            <tr>
              <td>
                <div class="fw-bold text-navy">${umkm.nama_usaha || '-'}</div>
                <small class="text-secondary"><i class="bi bi-person me-1"></i>${umkm.user?.nama || umkm.user?.name || '-'}</small>
              </td>
              <td>${levelBadge}</td>
              <td>
                <a href="https://wa.me/${cleanWa}" target="_blank" class="text-success text-decoration-none fw-semibold small">
                  <i class="bi bi-whatsapp me-1"></i>${umkm.kontak_wa || '-'}
                </a>
              </td>
              <td style="max-width: 200px;"><span class="small text-dark">${m.catatan || '-'}</span></td>
              <td><small class="text-muted">${ui.formatDate(m.created_at)}</small></td>
            </tr>
          `;
        }).join('');
      }

      // Filter listeners
      document.getElementById('searchInput').addEventListener('input', renderTable);
      document.getElementById('filterStatus').addEventListener('change', renderTable);

      await loadPeluang();
    });
  </script>
</body>
</html>
