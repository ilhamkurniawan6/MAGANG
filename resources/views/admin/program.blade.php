<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Program & Pembinaan - Admin KADIN</title>
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
            <h1 class="h4 fw-bold text-navy mb-1">Kelola Program Pembinaan & Inkubasi</h1>
            <p class="text-secondary small mb-0">Buat pelatihan, lokakarya, kurasi pendaftar UMKM, dan rekap presensi kehadiran.</p>
          </div>
          <div>
            <button class="btn btn-kadin-gold btn-sm d-flex align-items-center gap-1" id="btnTambahProgram">
              <i class="bi bi-plus-circle"></i> Buat Program Baru
            </button>
          </div>
        </div>

        <!-- Table Card -->
        <div class="card-clean p-0 overflow-hidden bg-white mb-4">
          <div class="table-responsive-clean">
            <table class="table-clean">
              <thead>
                <tr>
                  <th>Nama Program</th>
                  <th>Jenis</th>
                  <th>Jadwal Pelaksanaan</th>
                  <th>Peserta UMKM</th>
                  <th>Status</th>
                  <th class="text-end">Aksi</th>
                </tr>
              </thead>
              <tbody id="programTableBody">
                <tr>
                  <td colspan="6" class="text-center py-5 text-muted">
                    <div class="spinner-border spinner-border-sm text-navy me-2"></div> Memuat data program...
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- Modal Form Program (Create / Edit) -->
  <div class="modal fade" id="modalProgram" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold text-navy" id="modalProgramTitle"><i class="bi bi-calendar-plus text-warning me-2"></i>Buat Program Pembinaan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="formProgram">
          <div class="modal-body p-4">
            <input type="hidden" id="programId">

            <div class="mb-3">
              <label class="form-label">Judul Program <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="programJudul" placeholder="Contoh: Lokakarya Standarisasi Kemasan Ramah Lingkungan & Ekspor" required>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label">Jenis Program <span class="text-danger">*</span></label>
                <select class="form-select" id="programJenis" required>
                  <option value="pelatihan">Pelatihan</option>
                  <option value="seminar">Seminar / Workshop</option>
                  <option value="pendampingan">Pendampingan & Inkubasi</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Status Publikasi <span class="text-danger">*</span></label>
                <select class="form-select" id="programStatus" required>
                  <option value="draft">Draft (Belum Tampil)</option>
                  <option value="published">Published (Pendaftaran Buka)</option>
                  <option value="selesai">Selesai</option>
                </select>
              </div>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="programTglMulai" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="programTglSelesai" required>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Deskripsi & Materi Program <span class="text-danger">*</span></label>
              <textarea class="form-control" id="programDeskripsi" rows="4" placeholder="Jelaskan materi, fasilitator, manfaat, dan syarat peserta..." required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-kadin-primary btn-sm" id="btnSubmitProgram">
              <i class="bi bi-save me-1"></i> Simpan Program
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Presensi & Daftar Peserta -->
  <div class="modal fade" id="modalPeserta" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold text-navy"><i class="bi bi-people-fill text-warning me-2"></i>Daftar Peserta & Presensi Kehadiran</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-3 p-3 bg-light rounded-3 border">
            <h6 class="fw-bold text-navy mb-1" id="pesertaProgramNama">-</h6>
            <span class="text-secondary small" id="pesertaProgramSub">-</span>
          </div>

          <div class="table-responsive-clean">
            <table class="table-clean">
              <thead>
                <tr>
                  <th>Nama UMKM</th>
                  <th>Pemilik</th>
                  <th>Kontak WA</th>
                  <th>Tgl Daftar</th>
                  <th class="text-center">Status Kehadiran</th>
                </tr>
              </thead>
              <tbody id="pesertaTableBody">
                <tr><td colspan="5" class="text-center py-4 text-muted">Memuat peserta...</td></tr>
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

      document.getElementById('sidebar-container').innerHTML = ui.renderDashboardSidebar('program');
      document.getElementById('topbar-container').innerHTML = ui.renderDashboardTopbar('Kelola Program Pembinaan');

      let allPrograms = [];
      const modalProgram = new bootstrap.Modal(document.getElementById('modalProgram'));
      const modalPeserta = new bootstrap.Modal(document.getElementById('modalPeserta'));

      async function loadPrograms() {
        try {
          const res = await api.get('/admin/program');
          allPrograms = res.data || [];
          renderPrograms();
        } catch (err) {
          ui.showAlert(err.message || 'Gagal memuat program', 'danger');
        }
      }

      function renderPrograms() {
        const tbody = document.getElementById('programTableBody');
        if (!allPrograms.length) {
          tbody.innerHTML = `
            <tr>
              <td colspan="6" class="text-center py-5 text-muted">Belum ada program pembinaan. Klik "Buat Program Baru" untuk menambahkan.</td>
            </tr>
          `;
          return;
        }

        tbody.innerHTML = allPrograms.map(p => {
          const jenisBadge = p.jenis === 'pelatihan' ? 'bg-primary' : (p.jenis === 'pendampingan' ? 'bg-info text-dark' : 'bg-secondary');

          return `
            <tr>
              <td>
                <div class="fw-bold text-navy">${p.judul}</div>
                <small class="text-secondary d-block text-truncate" style="max-width: 250px;">${p.deskripsi ? p.deskripsi : '-'}</small>
              </td>
              <td><span class="badge ${jenisBadge} text-capitalize">${p.jenis}</span></td>
              <td>
                <div class="small fw-semibold">${ui.formatDate(p.tanggal_mulai)}</div>
                ${p.tanggal_selesai ? `<small class="text-muted">s/d ${ui.formatDate(p.tanggal_selesai)}</small>` : ''}
              </td>
              <td>
                <span class="badge bg-light text-dark border">
                  ${p.peserta_count || (p.peserta ? p.peserta.length : 0)} UMKM
                </span>
              </td>
              <td>${ui.renderBadge(p.status)}</td>
              <td class="text-end">
                <div class="btn-group btn-group-sm">
                  <button class="btn btn-outline-info btn-peserta" data-id="${p.id}" data-name="${p.judul.replace(/"/g, '&quot;')}" title="Presensi Peserta">
                    <i class="bi bi-people-fill"></i>
                  </button>
                  <button class="btn btn-outline-primary btn-edit" data-id="${p.id}" title="Edit Program">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <button class="btn btn-outline-danger btn-delete" data-id="${p.id}" data-name="${p.judul.replace(/"/g, '&quot;')}" title="Hapus">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          `;
        }).join('');

        // Bind Edit
        document.querySelectorAll('.btn-edit').forEach(btn => {
          btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const p = allPrograms.find(item => item.id == id);
            if (!p) return;

            document.getElementById('modalProgramTitle').innerHTML = '<i class="bi bi-pencil-square text-warning me-2"></i>Edit Program Pembinaan';
            document.getElementById('programId').value = p.id;
            document.getElementById('programJudul').value = p.judul;
            document.getElementById('programJenis').value = p.jenis;
            document.getElementById('programStatus').value = p.status;
            document.getElementById('programTglMulai').value = p.tanggal_mulai ? p.tanggal_mulai.split('T')[0] : '';
            document.getElementById('programTglSelesai').value = p.tanggal_selesai ? p.tanggal_selesai.split('T')[0] : '';
            document.getElementById('programDeskripsi').value = p.deskripsi || '';

            modalProgram.show();
          });
        });

        // Bind Delete
        document.querySelectorAll('.btn-delete').forEach(btn => {
          btn.addEventListener('click', async () => {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            if (!confirm(`Hapus program "${name}"? Data pendaftaran terkait akan ikut terhapus.`)) return;

            try {
              await api.delete(`/admin/program/${id}`);
              ui.showAlert('Program berhasil dihapus', 'success');
              await loadPrograms();
            } catch (err) {
              ui.showAlert(err.message || 'Gagal menghapus program', 'danger');
            }
          });
        });

        // Bind Presensi Modal
        document.querySelectorAll('.btn-peserta').forEach(btn => {
          btn.addEventListener('click', async () => {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            document.getElementById('pesertaProgramNama').innerText = name;
            document.getElementById('pesertaProgramSub').innerText = 'Memuat daftar pendaftar...';
            modalPeserta.show();

            await loadPeserta(id);
          });
        });
      }

      async function loadPeserta(programId) {
        const tbody = document.getElementById('pesertaTableBody');
        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-navy me-2"></div>Memuat data peserta...</td></tr>';

        try {
          const res = await api.get(`/admin/program/${programId}/peserta`);
          const pesertaList = res.data || [];
          document.getElementById('pesertaProgramSub').innerText = `Total: ${pesertaList.length} UMKM Terdaftar`;

          if (!pesertaList.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Belum ada UMKM yang mendaftar di program ini.</td></tr>';
            return;
          }

          tbody.innerHTML = pesertaList.map(item => {
            const umkm = item.umkm || {};
            const isHadir = item.status === 'hadir';
            const ownerName = umkm.user?.nama || umkm.user?.name || '-';
            const cleanWa = (umkm.kontak_wa || '').replace(/[^0-9]/g, '');

            return `
              <tr>
                <td class="fw-bold text-navy">${umkm.nama_usaha || '-'}</td>
                <td><small class="text-secondary">${ownerName}</small></td>
                <td>
                  <a href="https://wa.me/${cleanWa}" target="_blank" class="text-success text-decoration-none fw-semibold small">
                    <i class="bi bi-whatsapp me-1"></i>${umkm.kontak_wa || '-'}
                  </a>
                </td>
                <td><small class="text-muted">${ui.formatDate(item.created_at)}</small></td>
                <td class="text-center">
                  <div class="form-check form-switch d-inline-block">
                    <input class="form-check-input check-kehadiran" type="checkbox" data-progid="${programId}" data-pesertaid="${item.id}" ${isHadir ? 'checked' : ''}>
                    <label class="form-check-label small ms-1">${isHadir ? 'Hadir' : 'Terdaftar'}</label>
                  </div>
                </td>
              </tr>
            `;
          }).join('');

          // Bind Kehadiran switch
          document.querySelectorAll('.check-kehadiran').forEach(chk => {
            chk.addEventListener('change', async (e) => {
              const progId = chk.getAttribute('data-progid');
              const pesertaId = chk.getAttribute('data-pesertaid');
              const status = chk.checked ? 'hadir' : 'terdaftar';

              try {
                await api.post(`/admin/program/${progId}/peserta/${pesertaId}/kehadiran`, { status });
                ui.showAlert(`Status kehadiran peserta berhasil diubah ke ${status}!`, 'success');
              } catch (err) {
                ui.showAlert(err.message || 'Gagal mengubah presensi', 'danger');
                chk.checked = !chk.checked;
              }
            });
          });

        } catch (err) {
          tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-danger">${err.message || 'Gagal memuat peserta'}</td></tr>`;
        }
      }

      // Add New Button
      document.getElementById('btnTambahProgram').addEventListener('click', () => {
        document.getElementById('modalProgramTitle').innerHTML = '<i class="bi bi-calendar-plus text-warning me-2"></i>Buat Program Pembinaan Baru';
        document.getElementById('programId').value = '';
        document.getElementById('programJudul').value = '';
        document.getElementById('programJenis').value = 'pelatihan';
        document.getElementById('programStatus').value = 'published';
        document.getElementById('programTglMulai').value = '';
        document.getElementById('programTglSelesai').value = '';
        document.getElementById('programDeskripsi').value = '';
        modalProgram.show();
      });

      // Submit Form Program
      document.getElementById('formProgram').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('programId').value;
        const submitBtn = document.getElementById('btnSubmitProgram');

        const payload = {
          judul: document.getElementById('programJudul').value.trim(),
          jenis: document.getElementById('programJenis').value,
          status: document.getElementById('programStatus').value,
          tanggal_mulai: document.getElementById('programTglMulai').value,
          tanggal_selesai: document.getElementById('programTglSelesai').value,
          deskripsi: document.getElementById('programDeskripsi').value.trim(),
        };

        submitBtn.disabled = true;
        try {
          if (id) {
            await api.put(`/admin/program/${id}`, payload);
            ui.showAlert('Program berhasil diperbarui!', 'success');
          } else {
            await api.post('/admin/program', payload);
            ui.showAlert('Program berhasil dibuat!', 'success');
          }
          modalProgram.hide();
          await loadPrograms();
        } catch (err) {
          ui.showAlert(err.message || 'Gagal menyimpan program', 'danger');
        } finally {
          submitBtn.disabled = false;
        }
      });

      await loadPrograms();
    });
  </script>
</body>
</html>
