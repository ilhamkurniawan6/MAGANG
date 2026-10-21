<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Usaha - UMKM KADIN Hub</title>
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

        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h1 class="h4 fw-bold text-navy mb-1">Pengaturan Profil Usaha</h1>
            <p class="text-secondary small mb-0">Informasi ini akan ditampilkan di kartu kontak katalog dan halaman detail UMKM.</p>
          </div>
          <span id="badgeStatusHeader"></span>
        </div>

        <div class="row">
          <div class="col-lg-8">
            <div class="card-clean p-4 p-md-5 bg-white">
              <form id="profilForm">
                <div class="row g-3 mb-3">
                  <div class="col-md-6">
                    <label class="form-label small fw-semibold text-secondary">Nama Usaha / Merk Dagang <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="namaUsaha" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label small fw-semibold text-secondary">Sektor / Kategori Usaha <span class="text-danger">*</span></label>
                    <select class="form-select" id="kategoriId" required></select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label small fw-semibold text-secondary">Nomor WhatsApp Bisnis <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control" id="kontakWa" required placeholder="081234567890">
                    <small class="text-muted" style="font-size: 0.72rem;">Nomor tujuan saat pembeli klik hubungi via WhatsApp.</small>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label small fw-semibold text-secondary">Email Resmi Usaha</label>
                    <input type="email" class="form-control" id="emailUsaha" placeholder="kontak@usaha.com">
                  </div>
                  <div class="col-12">
                    <label class="form-label small fw-semibold text-secondary">Alamat Gerai / Workshop <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="alamat" rows="2" required></textarea>
                  </div>
                  <div class="col-12">
                    <label class="form-label small fw-semibold text-secondary">Deskripsi Singkat Usaha</label>
                    <textarea class="form-control" id="deskripsi" rows="4" placeholder="Jelaskan mengenai spesialisasi produk Anda..."></textarea>
                  </div>
                </div>

                <button type="submit" id="saveBtn" class="btn btn-kadin-primary px-4 py-2">
                  <i class="bi bi-floppy me-1"></i> Simpan Perubahan Profil
                </button>
              </form>
            </div>
          </div>

          <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="card-clean p-4 bg-light">
              <h6 class="fw-bold text-navy mb-2"><i class="bi bi-info-circle-fill text-warning me-1"></i> Ketentuan Verifikasi</h6>
              <p class="small text-secondary mb-2">
                Pastikan nomor WhatsApp dan alamat workshop selalu akurat. Data yang valid mempermudah kurator KADIN memverifikasi usaha Anda dan mempercepat proses transaksi pembeli.
              </p>
              <hr>
              <div class="small text-muted">
                <strong>Catatan:</strong> Perubahan data profil tidak menghapus status verifikasi yang sudah Anda dapatkan.
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

      document.getElementById('sidebar-container').innerHTML = ui.renderDashboardSidebar('profil');
      document.getElementById('topbar-container').innerHTML = ui.renderDashboardTopbar('Profil Usaha');

      // Load Kategori
      try {
        const katRes = await api.get('/publik/kategori-umkm');
        const select = document.getElementById('kategoriId');
        if (katRes.data) {
          katRes.data.forEach(k => {
            const opt = document.createElement('option');
            opt.value = k.id;
            opt.textContent = k.nama_kategori;
            select.appendChild(opt);
          });
        }
      } catch (e) {
        console.warn('Error load kategori umkm:', e);
      }

      // Load UMKM Profile
      try {
        const res = await api.get('/umkm/profil');
        const u = res.data;
        if (u) {
          document.getElementById('namaUsaha').value = u.nama_usaha || '';
          document.getElementById('kategoriId').value = u.kategori_id || '';
          document.getElementById('kontakWa').value = u.kontak_wa || '';
          document.getElementById('emailUsaha').value = u.email_usaha || '';
          document.getElementById('alamat').value = u.alamat || '';
          document.getElementById('deskripsi').value = u.deskripsi || '';
          document.getElementById('badgeStatusHeader').innerHTML = ui.renderBadge(u.status_verifikasi);
        }
      } catch (err) {
        console.error('Error load profil:', err);
        ui.showAlert('Gagal memuat profil UMKM.', 'danger');
      }

      document.getElementById('profilForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('saveBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';

        const payload = {
          nama_usaha: document.getElementById('namaUsaha').value,
          kategori_id: document.getElementById('kategoriId').value,
          kontak_wa: document.getElementById('kontakWa').value,
          email_usaha: document.getElementById('emailUsaha').value || null,
          alamat: document.getElementById('alamat').value,
          deskripsi: document.getElementById('deskripsi').value || null
        };

        try {
          await api.post('/umkm/profil', payload);
          ui.showAlert('Profil usaha berhasil diperbarui!', 'success');
        } catch (err) {
          console.error('Error update profil:', err);
          ui.showAlert(err.message || 'Gagal menyimpan profil.', 'danger');
        } finally {
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-floppy me-1"></i> Simpan Perubahan Profil';
        }
      });
    });
  </script>
</body>
</html>
