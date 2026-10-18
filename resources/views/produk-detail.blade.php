<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Produk - KADIN Digital Hub</title>
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
      <div class="d-flex align-items-center gap-2 ms-auto">
        <a href="katalog.html" class="btn btn-sm btn-kadin-outline"><i class="bi bi-arrow-left"></i> Kembali ke Katalog</a>
      </div>
    </div>
  </nav>

  <!-- Content -->
  <main class="py-5" id="mainContainer">
    <div class="container">
      <div class="row justify-content-center" id="loadingState">
        <div class="col-12 text-center py-5">
          <div class="spinner-border text-navy" role="status"></div>
          <div class="text-secondary mt-2">Memuat detail produk...</div>
        </div>
      </div>

      <div class="row g-5 d-none" id="contentState">
        <!-- Foto Produk -->
        <div class="col-lg-5">
          <div class="card-clean p-2 bg-light text-center overflow-hidden" style="min-height: 380px; display: flex; align-items: center; justify-content: center;">
            <img id="produkImage" src="" alt="Foto Produk" class="img-fluid rounded" style="max-height: 400px; object-fit: contain;">
            <div id="noImagePlaceholder" class="text-muted d-none py-5">
              <i class="bi bi-image fs-1 d-block mb-2"></i>
              <span>Foto tidak tersedia</span>
            </div>
          </div>
        </div>

        <!-- Info Produk & Profil UMKM -->
        <div class="col-lg-7">
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-secondary text-white" id="produkKategori">-</span>
            <span class="badge-status status-verified"><i class="bi bi-patch-check-fill"></i> Terverifikasi KADIN</span>
          </div>

          <h1 class="h2 fw-bold text-navy mb-2" id="produkNama">-</h1>
          <div class="h3 fw-bold text-warning mb-4" id="produkHarga">-</div>

          <div class="card-clean p-3 mb-4 bg-light">
            <h6 class="fw-bold text-navy mb-2">Deskripsi Produk</h6>
            <p class="text-secondary mb-0" id="produkDeskripsi" style="white-space: pre-line;">-</p>
          </div>

          <!-- Card UMKM Pemilik -->
          <div class="card-clean p-4 border border-warning border-opacity-50 mb-4">
            <div class="d-flex align-items-start gap-3">
              <div class="rounded-circle bg-navy text-white d-flex align-items-center justify-content-center fs-3 fw-bold flex-shrink-0" style="width: 56px; height: 56px; background-color: var(--kadin-navy);" id="umkmAvatar">
                U
              </div>
              <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-1">
                  <h5 class="fw-bold text-navy mb-0" id="umkmNamaUsaha">-</h5>
                  <span class="badge-status status-verified" style="font-size: 0.7rem;"><i class="bi bi-check-circle-fill"></i> Verified</span>
                </div>
                <div class="small text-secondary mb-2" id="umkmKategori">-</div>
                <p class="small text-muted mb-2"><i class="bi bi-geo-alt-fill text-danger me-1"></i> <span id="umkmAlamat">-</span></p>
                <div class="d-flex flex-wrap gap-2 pt-2">
                  <a href="#" id="waButton" target="_blank" class="btn btn-success btn-sm px-3 d-inline-flex align-items-center gap-2">
                    <i class="bi bi-whatsapp"></i> Hubungi UMKM via WhatsApp
                  </a>
                  <a href="#" id="umkmDetailLink" class="btn btn-kadin-outline btn-sm px-3">
                    Lihat Profil & Produk Lainnya
                  </a>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </main>

  <footer class="public-footer mt-auto">
    <div class="container text-center small text-light text-opacity-50">
      &copy; 2026 KADIN Kota Yogyakarta. Hak Cipta Dilindungi.
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', async () => {
      const urlParams = new URLSearchParams(window.location.search);
      const produkId = urlParams.get('id');

      if (!produkId) {
        alert('Produk tidak ditemukan');
        window.location.href = 'katalog.html';
        return;
      }

      try {
        const res = await api.get(`/publik/produk/${produkId}`);
        const p = res.data;

        if (!p) {
          document.getElementById('loadingState').innerHTML = '<div class="col-12 text-center text-danger py-5">Produk tidak ditemukan atau belum disetujui.</div>';
          return;
        }

        document.getElementById('produkNama').innerText = p.nama_produk;
        document.getElementById('produkHarga').innerText = ui.formatRupiah(p.harga);
        document.getElementById('produkDeskripsi').innerText = p.deskripsi || 'Tidak ada deskripsi rinci.';
        document.getElementById('produkKategori').innerText = p.kategori?.nama_kategori || 'Umum';

        if (p.foto_produk) {
          const img = document.getElementById('produkImage');
          img.src = p.foto_url || p.foto_produk;
          img.classList.remove('d-none');
        } else {
          document.getElementById('produkImage').classList.add('d-none');
          document.getElementById('noImagePlaceholder').classList.remove('d-none');
        }

        // UMKM data
        if (p.umkm) {
          document.getElementById('umkmNamaUsaha').innerText = p.umkm.nama_usaha;
          document.getElementById('umkmKategori').innerText = `Sektor: ${p.umkm.kategori?.nama_kategori || '-'}`;
          document.getElementById('umkmAlamat').innerText = p.umkm.alamat || '-';
          document.getElementById('umkmAvatar').innerText = (p.umkm.nama_usaha || 'U').charAt(0).toUpperCase();

          document.getElementById('umkmDetailLink').href = `umkm-detail.html?id=${p.umkm.id}`;

          // Setup WhatsApp Message Link
          let phone = p.umkm.kontak_wa ? p.umkm.kontak_wa.replace(/[^0-9]/g, '') : '';
          if (phone.startsWith('0')) {
            phone = '62' + phone.substring(1);
          }
          const msg = encodeURIComponent(`Halo ${p.umkm.nama_usaha}, saya tertarik dengan produk "${p.nama_produk}" yang terdaftar di KADIN Digital Hub Yogyakarta.`);
          document.getElementById('waButton').href = `https://wa.me/${phone}?text=${msg}`;
        }

        document.getElementById('loadingState').classList.add('d-none');
        document.getElementById('contentState').classList.remove('d-none');

      } catch (err) {
        console.error('Error load detail produk:', err);
        document.getElementById('loadingState').innerHTML = '<div class="col-12 text-center text-danger py-5">Gagal memuat informasi produk. Pastikan backend aktif.</div>';
      }
    });
  </script>
</body>
</html>
