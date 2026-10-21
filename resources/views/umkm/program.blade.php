<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Program & Pembinaan - UMKM Hub KADIN</title>
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

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
          <div>
            <h1 class="h4 fw-bold text-navy mb-1">Program & Pembinaan KADIN</h1>
            <p class="text-secondary small mb-0">Tingkatkan kapasitas usaha dan Skor KADIN Lestari Anda melalui pelatihan & pendampingan kurator.</p>
          </div>
        </div>

        <!-- Section AI Rekomendasi Program (Fase 3) -->
        <div class="card-clean p-4 mb-4 border-start border-4 border-warning shadow-xs" style="background: linear-gradient(135deg, #ffffff 0%, #fefcf3 100%);">
          <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
              <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-stars"></i> Rekomendasi AI</span>
              <h5 class="fw-bold text-navy mb-0" style="font-size: 1rem;">Program yang Paling Relevan Untuk Anda</h5>
            </div>
            <span class="small text-muted" id="aiSkorInfo">Analisis Skor Lestari & Profil Sektor</span>
          </div>
          <div class="row g-3" id="aiRekomendasiProgramCards">
            <div class="col-12 text-center py-3">
              <div class="spinner-border spinner-border-sm text-warning me-2"></div>
              <span class="small text-muted">Menganalisis rekomendasi terbaik...</span>
            </div>
          </div>
        </div>

        <ul class="nav nav-pills mb-4" id="programTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold" id="tab-tersedia" data-bs-toggle="pill" data-bs-target="#pills-tersedia" type="button" role="tab">
              <i class="bi bi-grid me-1"></i> Semua Program Tersedia
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="tab-terdaftar" data-bs-toggle="pill" data-bs-target="#pills-terdaftar" type="button" role="tab">
              <i class="bi bi-check2-circle me-1"></i> Riwayat Partisipasi Saya
            </button>
          </li>
        </ul>

        <div class="tab-content" id="programTabContent">
          <!-- Program Tersedia -->
          <div class="tab-pane fade show active" id="pills-tersedia" role="tabpanel">
            <div id="programListContainer" class="row g-4">
              <div class="col-12 text-center py-5 text-muted">
                <div class="spinner-border spinner-border-sm text-navy me-2"></div> Memuat program...
              </div>
            </div>
          </div>

          <!-- Riwayat Partisipasi -->
          <div class="tab-pane fade" id="pills-terdaftar" role="tabpanel">
            <div class="card-clean p-0 overflow-hidden bg-white">
              <div class="table-responsive-clean">
                <table class="table-clean">
                  <thead>
                    <tr>
                      <th>Nama Program</th>
                      <th>Jenis</th>
                      <th>Jadwal Pelaksanaan</th>
                      <th>Status Partisipasi</th>
                    </tr>
                  </thead>
                  <tbody id="riwayatTableBody">
                    <tr>
                      <td colspan="4" class="text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm text-navy me-2"></div> Memuat riwayat...
                      </td>
                    </tr>
                  </tbody>
                </table>
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
      if (!auth.guard(['umkm'])) return;

      document.getElementById('sidebar-container').innerHTML = ui.renderDashboardSidebar('program');
      document.getElementById('topbar-container').innerHTML = ui.renderDashboardTopbar('Program & Pembinaan');

      async function loadAiRecommendations() {
        try {
          const res = await api.get('/umkm/rekomendasi-program');
          const recs = res.data || [];
          const container = document.getElementById('aiRekomendasiProgramCards');

          if (recs.length > 0) {
            container.innerHTML = recs.map(r => {
              const p = r.program;
              return `
                <div class="col-md-4">
                  <div class="card h-100 border rounded-3 p-3 shadow-xs bg-white d-flex flex-column justify-content-between">
                    <div>
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold text-capitalize">${p.jenis || 'Pelatihan'}</span>
                        <span class="small text-muted" style="font-size: 0.75rem;"><i class="bi bi-calendar3 me-1"></i>${p.tanggal_mulai ? p.tanggal_mulai.substring(0, 10) : '-'}</span>
                      </div>
                      <h6 class="fw-bold text-navy mb-2" style="font-size: 0.95rem;">${p.judul}</h6>
                      <div class="p-2 rounded bg-light border-start border-3 border-warning mb-3">
                        <small class="text-dark d-block fw-semibold" style="font-size: 0.78rem;"><i class="bi bi-magic text-warning me-1"></i> Alasan AI:</small>
                        <small class="text-secondary" style="font-size: 0.76rem;">${r.alasan}</small>
                      </div>
                    </div>
                    <button class="btn btn-sm btn-kadin-gold w-100 py-1" onclick="daftarProgram(${p.id}, '${p.judul}')">
                      <i class="bi bi-check-circle me-1"></i> Daftar Program (1-Klik)
                    </button>
                  </div>
                </div>
              `;
            }).join('');
          } else {
            container.innerHTML = `
              <div class="col-12">
                <div class="alert alert-light border text-center py-3 mb-0 small text-secondary">
                  Belum ada rekomendasi khusus saat ini. Anda dapat menjelajahi daftar program di bawah.
                </div>
              </div>
            `;
          }
        } catch (e) {
          console.error('Error load AI recommendations:', e);
          document.getElementById('aiRekomendasiProgramCards').innerHTML = `
            <div class="col-12">
              <div class="alert alert-light border text-center py-2 mb-0 small text-muted">
                Daftar program pembinaan resmi KADIN tersedia di bawah.
              </div>
            </div>
          `;
        }
      }

      async function loadData() {
        try {
          const res = await api.get('/umkm/program');
          const allPrograms = res.data || [];
          renderPrograms(allPrograms);
          renderRiwayat(allPrograms.filter(p => p.is_registered));
        } catch (err) {
          ui.showAlert(err.message || 'Gagal memuat program', 'danger');
        }
      }

      function renderPrograms(programs) {
        const container = document.getElementById('programListContainer');
        if (!programs.length) {
          container.innerHTML = `
            <div class="col-12 text-center py-5">
              <i class="bi bi-calendar-x text-muted fs-1 d-block mb-3"></i>
              <h6 class="text-muted">Belum ada program pembinaan aktif saat ini.</h6>
            </div>
          `;
          return;
        }

        container.innerHTML = programs.map(p => {
          const isRegistered = p.is_registered;
          const jenisBadge = p.jenis === 'pelatihan' ? 'bg-primary' : (p.jenis === 'pendampingan' ? 'bg-info text-dark' : 'bg-secondary');

          return `
            <div class="col-md-6 col-lg-4">
              <div class="card-clean h-100 p-4 d-flex flex-column justify-content-between shadow-xs bg-white">
                <div>
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge ${jenisBadge} text-capitalize">${p.jenis}</span>
                    <span class="badge bg-light text-dark border">
                      <i class="bi bi-people me-1"></i> ${p.peserta_count || 0} Terdaftar
                    </span>
                  </div>
                  <h5 class="fw-bold text-navy mb-2" style="font-size: 1.05rem;">${p.judul}</h5>
                  <p class="text-secondary small mb-3">${p.deskripsi ? p.deskripsi.substring(0, 130) + '...' : '-'}</p>

                  <div class="small text-muted mb-3 border-top pt-3">
                    <div class="mb-1"><i class="bi bi-calendar-event text-warning me-2"></i><strong>Mulai:</strong> ${ui.formatDate(p.tanggal_mulai)}</div>
                    <div><i class="bi bi-calendar-check text-primary me-2"></i><strong>Selesai:</strong> ${ui.formatDate(p.tanggal_selesai)}</div>
                  </div>
                </div>

                <div class="mt-auto">
                  ${isRegistered ? `
                    <button class="btn btn-outline-success btn-sm w-100 disabled" disabled>
                      <i class="bi bi-check-circle-fill me-1"></i> Terdaftar (${p.status_pendaftaran || 'Aktif'})
                    </button>
                  ` : `
                    <button class="btn btn-kadin-primary btn-sm w-100" onclick="daftarProgram(${p.id}, '${p.judul}')">
                      <i class="bi bi-pencil-square me-1"></i> Daftar Program (1-Klik)
                    </button>
                  `}
                </div>
              </div>
            </div>
          `;
        }).join('');
      }

      function renderRiwayat(programs) {
        const tbody = document.getElementById('riwayatTableBody');
        if (!programs.length) {
          tbody.innerHTML = `
            <tr>
              <td colspan="4" class="text-center py-4 text-muted">Anda belum mendaftar di program pembinaan apapun.</td>
            </tr>
          `;
          return;
        }

        tbody.innerHTML = programs.map(p => {
          return `
            <tr>
              <td class="fw-bold text-navy">${p.judul}</td>
              <td><span class="badge bg-light text-dark border text-capitalize">${p.jenis}</span></td>
              <td>
                <div class="small">${ui.formatDate(p.tanggal_mulai)}</div>
                ${p.tanggal_selesai ? `<div class="small text-muted">s/d ${ui.formatDate(p.tanggal_selesai)}</div>` : ''}
              </td>
              <td>
                ${ui.renderBadge(p.status_pendaftaran || 'terdaftar')}
              </td>
            </tr>
          `;
        }).join('');
      }

      window.daftarProgram = async (programId, nama) => {
        if (!(await ui.confirm(`Konfirmasi pendaftaran program "${nama || ''}"?`, {
          title: 'Daftar Program', confirmText: 'Ya, daftar'
        }))) return;
        try {
          await api.post(`/umkm/program/${programId}/daftar`, {});
          ui.showAlert('Pendaftaran program berhasil!', 'success');
          await loadData();
          await loadAiRecommendations();
        } catch (err) {
          ui.showAlert(err.data?.message || err.message || 'Gagal mendaftar program', 'danger');
        }
      };

      await loadAiRecommendations();
      await loadData();
    });
  </script>
</body>
</html>
