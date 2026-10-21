<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pendaftaran Mandiri UMKM - KADIN Digital Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  @vite(['resources/css/kadin.css', 'resources/js/app.js'])
</head>
<body class="bg-light d-flex flex-column min-vh-100">

  <!-- Navbar Simple -->
  <nav class="navbar public-navbar">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="index.html">
        <i class="bi bi-shield-shaded text-warning fs-3"></i>
        <div>
          <div class="fw-bold text-navy lh-1" style="font-size: 1.15rem; color: var(--kadin-navy);">KADIN DIGITAL HUB</div>
          <small class="text-secondary" style="font-size: 0.7rem; letter-spacing: 0.05em;">KOTA YOGYAKARTA</small>
        </div>
      </a>
      <a href="index.html" class="btn btn-sm btn-kadin-outline"><i class="bi bi-arrow-left"></i> Kembali ke Beranda</a>
    </div>
  </nav>

  <main class="container my-5">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        
        <div class="card-clean p-4 p-md-5 bg-white">
          <div class="border-bottom pb-3 mb-4">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-light text-navy fw-semibold small mb-2">
              <i class="bi bi-building-add text-warning"></i> Onboarding Tahap 1
            </div>
            <h1 class="h3 fw-bold text-navy mb-1">Registrasi Mandiri UMKM Binaan</h1>
            <p class="text-secondary small mb-0">Daftarkan usaha Anda untuk diverifikasi oleh KADIN Kota Yogyakarta dan tampil di etalase resmi.</p>
          </div>

          <div id="alert-container"></div>

          <form id="registerForm">
            <h5 class="fw-bold text-navy mb-3 fs-6"><i class="bi bi-person-fill text-warning me-1"></i> Data Akun Penanggung Jawab</h5>
            
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label for="namaPemilik" class="form-label small fw-semibold text-secondary">Nama Lengkap Pemilik / PIC <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="namaPemilik" placeholder="Contoh: Budi Santoso" required>
              </div>
              <div class="col-md-6">
                <label for="email" class="form-label small fw-semibold text-secondary">Email Akun (Untuk Login) <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" placeholder="nama@email.com" required>
              </div>
              <div class="col-md-6">
                <label for="password" class="form-label small fw-semibold text-secondary">Kata Sandi (Min. 8 Karakter) <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="password" placeholder="••••••••" required minlength="8">
              </div>
              <div class="col-md-6">
                <label for="noWa" class="form-label small fw-semibold text-secondary">No. WhatsApp Pemilik <span class="text-danger">*</span></label>
                <input type="tel" class="form-control" id="noWa" placeholder="081234567890" required>
              </div>
            </div>

            <h5 class="fw-bold text-navy mb-3 fs-6"><i class="bi bi-shop text-warning me-1"></i> Informasi Legalitas Usaha</h5>
            
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label for="namaUsaha" class="form-label small fw-semibold text-secondary">Nama Usaha / Merk Dagang <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="namaUsaha" placeholder="Contoh: Batik Sekar Jogja" required>
              </div>
              <div class="col-md-6">
                <label for="kategoriId" class="form-label small fw-semibold text-secondary">Sektor / Kategori Usaha <span class="text-danger">*</span></label>
                <select class="form-select" id="kategoriId" required>
                  <option value="">Pilih Kategori Usaha...</option>
                </select>
              </div>
              <div class="col-12">
                <label for="alamat" class="form-label small fw-semibold text-secondary">Alamat Lengkap Workshop / Gerai <span class="text-danger">*</span></label>
                <textarea class="form-control" id="alamat" rows="2" placeholder="Jl. Malioboro No. XX, Danurejan, Kota Yogyakarta" required></textarea>
              </div>
              <div class="col-md-6">
                <label for="kontakWaUsaha" class="form-label small fw-semibold text-secondary">Nomor WhatsApp Resmi Bisnis <span class="text-danger">*</span></label>
                <input type="tel" class="form-control" id="kontakWaUsaha" placeholder="081234567890" required>
                <small class="text-muted" style="font-size: 0.72rem;">Nomor ini yang akan dihubungi oleh calon pembeli di etalase publik.</small>
              </div>
              <div class="col-md-6">
                <label for="emailUsaha" class="form-label small fw-semibold text-secondary">Email Resmi Usaha (Opsional)</label>
                <input type="email" class="form-control" id="emailUsaha" placeholder="kontak@batiksekar.id">
              </div>
              <div class="col-12">
                <label for="deskripsi" class="form-label small fw-semibold text-secondary">Deskripsi Ringkas Profil Usaha</label>
                <textarea class="form-control" id="deskripsi" rows="3" placeholder="Ceritakan produk unggulan, sejarah singkat, atau kapasitas produksi usaha Anda..."></textarea>
              </div>
            </div>

            <div class="alert alert-info py-2 px-3 small d-flex align-items-center mb-4">
              <i class="bi bi-info-circle-fill me-2 fs-5 text-primary"></i>
              <span>Setelah mendaftar, status UMKM Anda akan berstatus <strong>Pending</strong> sampai ditinjau & disetujui oleh tim verifikator KADIN.</span>
            </div>

            <button type="submit" id="submitBtn" class="btn btn-kadin-primary w-100 py-2 fw-bold">
              Kirim Pendaftaran UMKM <i class="bi bi-arrow-right ms-1"></i>
            </button>
          </form>

          <div class="text-center mt-4 pt-3 border-top">
            <span class="small text-secondary">Sudah memiliki akun?</span>
            <a href="login.html" class="small fw-bold text-navy ms-1 text-decoration-none hover-gold">Masuk di sini</a>
          </div>

        </div>

      </div>
    </div>
  </main>

  <footer class="text-center py-3 text-secondary small">
    &copy; 2026 KADIN Kota Yogyakarta
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', async () => {
      // Load Kategori UMKM
      try {
        const res = await api.get('/publik/kategori-umkm');
        const select = document.getElementById('kategoriId');
        if (res.data) {
          res.data.forEach(k => {
            const opt = document.createElement('option');
            opt.value = k.id;
            opt.textContent = k.nama_kategori;
            select.appendChild(opt);
          });
        }
      } catch (err) {
        console.warn('Kategori UMKM load error:', err);
      }

      document.getElementById('registerForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mendaftarkan...';

        const payload = {
          nama: document.getElementById('namaPemilik').value,
          email: document.getElementById('email').value,
          password: document.getElementById('password').value,
          no_wa: document.getElementById('noWa').value,
          nama_usaha: document.getElementById('namaUsaha').value,
          kategori_id: document.getElementById('kategoriId').value,
          alamat: document.getElementById('alamat').value,
          kontak_wa: document.getElementById('kontakWaUsaha').value,
          email_usaha: document.getElementById('emailUsaha').value || null,
          deskripsi: document.getElementById('deskripsi').value || null
        };

        try {
          const res = await auth.registerUmkm(payload);
          ui.showAlert('Pendaftaran UMKM berhasil! Anda dapat langsung login untuk memantau status verifikasi.', 'success');
          
          setTimeout(() => {
            window.location.href = 'login.html';
          }, 2000);

        } catch (err) {
          console.error('Registration error:', err);
          let msg = err.message || 'Gagal melakukan pendaftaran.';
          if (err.errors) {
            msg = Object.values(err.errors).flat().join('<br>');
          }
          ui.showAlert(msg, 'danger');
          btn.disabled = false;
          btn.innerHTML = 'Kirim Pendaftaran UMKM <i class="bi bi-arrow-right ms-1"></i>';
        }
      });
    });
  </script>
</body>
</html>
