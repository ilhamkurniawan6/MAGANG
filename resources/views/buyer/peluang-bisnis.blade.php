<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Pengadaan (RFQ) - Buyer Hub KADIN</title>
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
            <h1 class="h4 fw-bold text-navy mb-1">Kelola Permintaan Pengadaan (RFQ)</h1>
            <p class="text-secondary small mb-0">Publikasikan kebutuhan pengadaan B2B Anda dan hubungi langsung UMKM yang menyatakan kesanggupan memasok.</p>
          </div>
          <div>
            <button class="btn btn-kadin-gold btn-sm d-flex align-items-center gap-1" id="btnTambahRfq">
              <i class="bi bi-plus-circle"></i> Publikasikan RFQ Baru
            </button>
          </div>
        </div>

        <div class="alert alert-info d-flex align-items-center gap-3 mb-4 shadow-xs" role="alert">
          <i class="bi bi-chat-dots-fill text-info fs-3"></i>
          <div>
            <strong>Business Matching B2B Terkurasi</strong>
            <p class="mb-0 small text-secondary">Tinjau kesanggupan dan skor keberlanjutan UMKM mitra. Hubungi langsung melalui WhatsApp resmi untuk negosiasi cepat.</p>
          </div>
        </div>

        <!-- Table RFQ -->
        <div class="card-clean p-0 overflow-hidden bg-white mb-4">
          <div class="table-responsive-clean">
            <table class="table-clean">
              <thead>
                <tr>
                  <th>Judul Pengadaan (RFQ)</th>
                  <th>Target Sektor</th>
                  <th>Target Volume</th>
                  <th>Kapasitas / Waktu</th>
                  <th>Respon UMKM</th>
                  <th>Status</th>
                  <th class="text-end">Aksi</th>
                </tr>
              </thead>
              <tbody id="rfqTableBody">
                <tr>
                  <td colspan="7" class="text-center py-5 text-muted">
                    <div class="spinner-border spinner-border-sm text-navy me-2"></div> Memuat data RFQ...
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- Modal Tambah RFQ -->
  <div class="modal fade" id="modalRfq" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold text-navy"><i class="bi bi-send-plus text-warning me-2"></i>Publikasikan Permintaan Pengadaan (RFQ)</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="formRfq">
          <div class="modal-body p-4">
            <div class="mb-3">
              <label class="form-label">Judul Kebutuhan Pengadaan <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="rfqJudul" placeholder="Contoh: Pengadaan 500 Box Hampers Batik & Kopi Lokal untuk Seminar Nasional" required>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label class="form-label">Kategori Sektor Target <span class="text-danger">*</span></label>
                <select class="form-select" id="rfqKategori" required>
                  <option value="">Pilih Kategori...</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Target Volume / Kuantitas</label>
                <input type="text" class="form-control" id="rfqTargetVolume" placeholder="Contoh: 500 Pcs / 100 Kg / 20 Paket">
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Kapasitas / Waktu Pengadaan</label>
              <input type="text" class="form-control" id="rfqKapasitas" placeholder="Contoh: 50-100 pcs/minggu atau Siap kirim Mei 2026">
            </div>

            <div class="mb-3">
              <label class="form-label">Spesifikasi Detail & Ketentuan <span class="text-danger">*</span></label>
              <textarea class="form-control" id="rfqDeskripsi" rows="4" placeholder="Jelaskan spesifikasi mutu, syarat kemasan ramah lingkungan (jika ada), dan kriteria yang dicari..." required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-kadin-primary btn-sm" id="btnSubmitRfq">
              <i class="bi bi-broadcast me-1"></i> Publikasikan RFQ
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Peminat UMKM -->
  <div class="modal fade" id="modalPeminat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold text-navy"><i class="bi bi-people-fill text-warning me-2"></i>Daftar UMKM Peminat & Kontak Bisnis</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="mb-4 bg-light p-3 rounded-3 border">
            <h6 class="fw-bold text-navy mb-1" id="viewRfqJudul">-</h6>
            <div class="text-secondary small" id="viewRfqSub">-</div>
          </div>

          <div class="table-responsive-clean">
            <table class="table-clean">
              <thead>
                <tr>
                  <th>Profil UMKM</th>
                  <th>Skor KADIN Lestari</th>
                  <th>Praktik Ramah Lingkungan</th>
                  <th>Catatan Penawaran</th>
                  <th>Tgl Respon</th>
                  <th class="text-end">Kontak Langsung</th>
                </tr>
              </thead>
              <tbody id="peminatBuyerTableBody">
                <tr><td colspan="6" class="text-center py-4 text-muted">Memuat data respon UMKM...</td></tr>
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

  <!-- Modal AI Matchmaking UMKM (Fase 3) -->
  <div class="modal fade" id="modalAiMatching" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header" style="background: linear-gradient(135deg, #0b1e38 0%, #173866 100%); color: #fff;">
          <h5 class="modal-title fw-bold text-white"><i class="bi bi-stars text-warning me-2"></i>AI Business Matchmaking KADIN Hub</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          <div class="alert alert-light border d-flex align-items-center gap-3 mb-4">
            <div class="rounded-circle bg-warning bg-opacity-20 text-warning p-2 d-flex"><i class="bi bi-robot fs-4"></i></div>
            <div>
              <div class="fw-bold text-navy" id="aiMatchingRfqJudul">-</div>
              <small class="text-secondary">Sistem AI menyaring UMKM terverifikasi yang memiliki kapasitas produksi dan kategori relevan terhadap RFQ Anda.</small>
            </div>
          </div>

          <div id="aiMatchingContent">
            <div class="text-center py-5">
              <div class="spinner-border text-warning mb-2"></div>
              <p class="text-secondary small">Sedang menganalisis kandidat mitra UMKM terbaik...</p>
            </div>
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
      if (!auth.guard(['buyer'])) return;

      document.getElementById('sidebar-container').innerHTML = ui.renderDashboardSidebar('peluang-bisnis');
      document.getElementById('topbar-container').innerHTML = ui.renderDashboardTopbar('Kelola Pengadaan (RFQ)');

      const user = auth.getUser();
      let allRfqs = [];
      const modalRfq = new bootstrap.Modal(document.getElementById('modalRfq'));
      const modalPeminat = new bootstrap.Modal(document.getElementById('modalPeminat'));
      const modalAiMatching = new bootstrap.Modal(document.getElementById('modalAiMatching'));

      // Load Categories for select
      try {
        const catRes = await api.get('/publik/kategori-produk');
        const categories = catRes.data || [];
        const katSelect = document.getElementById('rfqKategori');
        katSelect.innerHTML = '<option value="">Pilih Kategori...</option>' + 
          categories.map(c => `<option value="${c.id}">${c.nama_kategori}</option>`).join('');
      } catch (err) {
        console.error('Error load categories:', err);
      }

      async function loadRfqs() {
        try {
          const res = await api.get('/buyer/peluang-bisnis');
          allRfqs = res.data || [];
          renderRfqTable();
        } catch (err) {
          ui.showAlert(err.message || 'Gagal memuat daftar RFQ', 'danger');
        }
      }

      function renderRfqTable() {
        const tbody = document.getElementById('rfqTableBody');
        if (!allRfqs.length) {
          tbody.innerHTML = `
            <tr>
              <td colspan="7" class="text-center py-5 text-muted">
                Anda belum mempublikasikan permintaan pengadaan (RFQ). Klik "Publikasikan RFQ Baru" di atas.
              </td>
            </tr>
          `;
          return;
        }

        tbody.innerHTML = allRfqs.map(p => {
          const peminatCount = p.peminat_count || (p.minat ? p.minat.length : 0);

          return `
            <tr>
              <td>
                <div class="fw-bold text-navy">${p.judul}</div>
                <small class="text-secondary d-block text-truncate" style="max-width: 260px;">${p.deskripsi ? p.deskripsi : '-'}</small>
              </td>
              <td><span class="badge bg-light text-dark border">${p.kategori?.nama_kategori || 'Semua Sektor'}</span></td>
              <td><span class="small fw-semibold">${p.target_volume || '-'}</span></td>
              <td><small class="text-secondary">${p.kapasitas || '-'}</small></td>
              <td>
                <div class="d-flex flex-column gap-1">
                  <button class="btn btn-sm btn-kadin-outline py-1 px-2 btn-view-respon" data-id="${p.id}" style="font-size: 0.78rem;">
                    <i class="bi bi-people-fill text-warning me-1"></i> ${peminatCount} Respon
                  </button>
                  <button class="btn btn-sm btn-warning text-dark py-1 px-2 btn-ai-match" data-id="${p.id}" style="font-size: 0.78rem;">
                    <i class="bi bi-stars me-1"></i> Rekomendasi AI
                  </button>
                </div>
              </td>
              <td>${ui.renderBadge(p.status)}</td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-secondary py-1 px-2 btn-close-rfq" data-id="${p.id}" data-status="${p.status}" style="font-size: 0.78rem;">
                  <i class="bi bi-power me-1"></i> ${p.status === 'terbuka' ? 'Tutup' : 'Buka'}
                </button>
              </td>
            </tr>
          `;
        }).join('');

        // Bind View Respon
        document.querySelectorAll('.btn-view-respon').forEach(btn => {
          btn.addEventListener('click', async () => {
            const id = btn.getAttribute('data-id');
            const item = allRfqs.find(p => p.id == id);
            if (!item) return;

            document.getElementById('viewRfqJudul').innerText = item.judul;
            document.getElementById('viewRfqSub').innerText = `Target Sektor: ${item.kategori?.nama_kategori || 'Semua'} | Volume: ${item.target_volume || '-'}`;
            
            modalPeminat.show();
            await loadPeminatList(item.id, item.judul);
          });
        });

        // Bind AI Matchmaking Button (Fase 3)
        document.querySelectorAll('.btn-ai-match').forEach(btn => {
          btn.addEventListener('click', async () => {
            const id = btn.getAttribute('data-id');
            const item = allRfqs.find(p => p.id == id);
            if (!item) return;

            document.getElementById('aiMatchingRfqJudul').innerText = `Kebutuhan: "${item.judul}"`;
            modalAiMatching.show();
            await loadAiRecommendations(item.id, item.judul);
          });
        });

        // Bind Toggle Close/Open RFQ
        document.querySelectorAll('.btn-close-rfq').forEach(btn => {
          btn.addEventListener('click', async () => {
            const id = btn.getAttribute('data-id');
            const currentStatus = btn.getAttribute('data-status');
            const newStatus = currentStatus === 'terbuka' ? 'ditutup' : 'terbuka';

            try {
              await api.post(`/buyer/peluang-bisnis/${id}/status`, { status: newStatus });
              ui.showAlert(`Status RFQ diubah menjadi ${newStatus}!`, 'success');
              await loadRfqs();
            } catch (err) {
              ui.showAlert(err.message || 'Gagal mengubah status', 'danger');
            }
          });
        });
      }

      async function loadAiRecommendations(peluangId, judul) {
        const container = document.getElementById('aiMatchingContent');
        container.innerHTML = `
          <div class="text-center py-5">
            <div class="spinner-border text-warning mb-2"></div>
            <p class="text-secondary small">AI sedang menyaring dan merangking kandidat UMKM terverifikasi...</p>
          </div>
        `;

        try {
          const res = await api.get(`/buyer/peluang-bisnis/${peluangId}/rekomendasi-umkm`);
          const recs = res.data || [];

          if (!recs.length) {
            container.innerHTML = `
              <div class="alert alert-light border text-center py-4">
                <i class="bi bi-info-circle text-muted fs-3 d-block mb-2"></i>
                <p class="mb-0 small text-secondary">Belum ada profil UMKM terverifikasi yang cocok dengan kriteria pengadaan ini saat ini.</p>
              </div>
            `;
            return;
          }

          container.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span class="small fw-semibold text-navy"><i class="bi bi-trophy-fill text-warning me-1"></i> Top ${recs.length} Rekomendasi Teratas</span>
              <span class="badge bg-light text-dark border">Sumber: ${res.meta?.source === 'ai' ? '🤖 AI Reasoning Model' : '📊 Sistem Kurasi Skor Lestari'}</span>
            </div>
            <div class="d-flex flex-column gap-3">
              ${recs.map((r, idx) => {
                const u = r.umkm || {};
                const cleanWa = (u.kontak_wa || '').replace(/[^0-9]/g, '');
                const buyerCompany = user?.buyer?.nama_perusahaan || user?.nama || 'Mitra Buyer';
                const waMessage = encodeURIComponent(`Halo ${u.nama_usaha}, kami dari ${buyerCompany} tertarik untuk bermitra terkait kebutuhan pengadaan "${judul}" di KADIN Digital Hub.`);
                const waLink = `https://wa.me/${cleanWa}?text=${waMessage}`;
                const matchScore = r.skor_kecocokan || 8;
                const matchPct = Math.min(100, Math.round(matchScore * 10));

                return `
                  <div class="card p-3 border rounded-3 shadow-xs bg-white">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                      <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-navy text-white rounded-pill px-2 py-1" style="font-size: 0.75rem;">Rank #${idx + 1}</span>
                        <h6 class="fw-bold text-navy mb-0">${u.nama_usaha || 'UMKM Binaan'}</h6>
                        <span class="badge bg-light text-dark border small">${u.kategori?.nama_kategori || 'Umum'}</span>
                      </div>
                      <div class="d-flex align-items-center gap-2">
                        ${ui.renderLestariBadge(u.skor_lestari_level || 'bronze', u.skor_lestari || 0)}
                      </div>
                    </div>

                    <div class="mb-2">
                      <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-secondary" style="font-size: 0.75rem;">Skor Kecocokan AI:</small>
                        <small class="fw-bold text-success" style="font-size: 0.75rem;">${matchScore} / 10 (${matchPct}%)</small>
                      </div>
                      <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: ${matchPct}%"></div>
                      </div>
                    </div>

                    <div class="p-2 rounded bg-light border-start border-3 border-warning mb-3">
                      <small class="text-dark d-block fw-semibold" style="font-size: 0.78rem;"><i class="bi bi-lightbulb-fill text-warning me-1"></i> Analisis Kecocokan:</small>
                      <small class="text-secondary" style="font-size: 0.76rem;">${r.alasan}</small>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                      <small class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-geo-alt me-1"></i>${u.alamat || 'Kota Yogyakarta'}</small>
                      <a href="${waLink}" target="_blank" class="btn btn-success btn-sm d-inline-flex align-items-center gap-1 py-1 px-3" style="font-size: 0.8rem;">
                        <i class="bi bi-whatsapp"></i> Hubungi Langsung
                      </a>
                    </div>
                  </div>
                `;
              }).join('')}
            </div>
          `;
        } catch (err) {
          container.innerHTML = `
            <div class="alert alert-danger py-3 small">
              Gagal memuat rekomendasi AI: ${err.message || 'Terjadi kesalahan sistem.'}
            </div>
          `;
        }
      }

      async function loadPeminatList(peluangId, judul) {
        const tbody = document.getElementById('peminatBuyerTableBody');
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm text-navy me-2"></div>Memuat respon UMKM...</td></tr>';

        try {
          const res = await api.get(`/buyer/peluang-bisnis/${peluangId}/minat`);
          const minatList = res.data || [];

          if (!minatList.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada UMKM yang menyatakan minat untuk pengadaan ini.</td></tr>';
            return;
          }

          tbody.innerHTML = minatList.map(m => {
            const umkm = m.umkm || {};
            const levelBadge = ui.renderLestariBadge(umkm.level_lestari || 'bronze', umkm.skor_lestari || 0);
            const praktiks = Array.isArray(umkm.praktik_ramah_lingkungan) ? umkm.praktik_ramah_lingkungan : [];
            const cleanWa = (umkm.kontak_wa || '').replace(/[^0-9]/g, '');
            const buyerCompanyName = user?.buyer?.nama_perusahaan || user?.nama || 'Mitra Buyer';
            const waMessage = encodeURIComponent(`Halo ${umkm.nama_usaha}, kami dari ${buyerCompanyName} berminat menindaklanjuti penawaran Anda terkait kebutuhan pengadaan "${judul}" di KADIN Digital Hub.`);
            const waLink = `https://wa.me/${cleanWa}?text=${waMessage}`;

            return `
              <tr>
                <td>
                  <div class="fw-bold text-navy">${umkm.nama_usaha || '-'}</div>
                  <small class="text-secondary"><i class="bi bi-person me-1"></i>${umkm.user?.nama || '-'}</small>
                </td>
                <td>${levelBadge}</td>
                <td>
                  <div class="d-flex flex-wrap gap-1">
                    ${praktiks.length ? praktiks.map(t => ui.renderTagPraktik(t)).join('') : '<span class="text-muted small">-</span>'}
                  </div>
                </td>
                <td style="max-width: 250px;">
                  <p class="small mb-0 text-dark">${m.catatan || '-'}</p>
                </td>
                <td><small class="text-secondary">${ui.formatDate(m.created_at)}</small></td>
                <td class="text-end">
                  <a href="${waLink}" target="_blank" class="btn btn-success btn-sm d-inline-flex align-items-center gap-1">
                    <i class="bi bi-whatsapp"></i> Hubungi WA
                  </a>
                </td>
              </tr>
            `;
          }).join('');

        } catch (err) {
          tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-danger">${err.message || 'Gagal memuat minat'}</td></tr>`;
        }
      }

      // Add RFQ Button
      document.getElementById('btnTambahRfq').addEventListener('click', () => {
        document.getElementById('formRfq').reset();
        modalRfq.show();
      });

      // Submit Form RFQ
      document.getElementById('formRfq').addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = document.getElementById('btnSubmitRfq');

        const payload = {
          judul: document.getElementById('rfqJudul').value.trim(),
          kategori_produk_id: parseInt(document.getElementById('rfqKategori').value),
          target_volume: document.getElementById('rfqTargetVolume').value.trim() || null,
          kapasitas: document.getElementById('rfqKapasitas').value.trim() || null,
          deskripsi: document.getElementById('rfqDeskripsi').value.trim(),
        };

        submitBtn.disabled = true;
        try {
          await api.post('/buyer/peluang-bisnis', payload);
          modalRfq.hide();
          ui.showAlert('Permintaan pengadaan (RFQ) berhasil dipublikasikan!', 'success');
          await loadRfqs();
        } catch (err) {
          ui.showAlert(err.message || 'Gagal membuat RFQ', 'danger');
        } finally {
          submitBtn.disabled = false;
        }
      });

      await loadRfqs();
    });
  </script>
</body>
</html>
