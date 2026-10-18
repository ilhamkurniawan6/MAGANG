<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Produk - UMKM KADIN Hub</title>
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
            <h1 class="h4 fw-bold text-navy mb-1">Katalog Produk Usaha</h1>
            <p class="text-secondary small mb-0">Kelola daftar produk Anda. Setiap penambahan atau pembaruan akan melewati proses kurasi admin.</p>
          </div>
          <button type="button" class="btn btn-kadin-gold btn-sm px-3 d-flex align-items-center gap-1" onclick="bukaModalProduk()">
            <i class="bi bi-plus-circle"></i> Tambah Produk Baru
          </button>
        </div>

        <!-- Table Produk -->
        <div class="card-clean p-0 overflow-hidden bg-white">
          <div class="table-responsive">
            <table class="table-clean">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Foto & Nama Produk</th>
                  <th>Kategori</th>
                  <th>Harga (Rp)</th>
                  <th>Status Moderasi</th>
                  <th>Terakhir Update</th>
                  <th class="text-end">Aksi</th>
                </tr>
              </thead>
              <tbody id="produkTableBody">
                <tr><td colspan="7" class="text-center py-5"><div class="spinner-border text-navy"></div></td></tr>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- Modal Form Tambah/Edit Produk -->
  <div class="modal fade" id="produkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="produkForm">
          <div class="modal-header">
            <h5 class="modal-title fw-bold text-navy" id="modalTitle">Tambah Produk Baru</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="editProdukId">
            
            <div class="alert alert-info py-2 px-3 small mb-3">
              <i class="bi bi-info-circle-fill me-1"></i> Produk yang disimpan akan berstatus <strong>Menunggu Moderasi</strong> sampai disetujui oleh admin.
            </div>

            <div class="row g-3">
              <div class="col-md-8">
                <label class="form-label small fw-semibold text-secondary">Nama Produk <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="namaProduk" required placeholder="Contoh: Kemeja Batik Tulis Sutra">
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold text-secondary">Kategori Produk <span class="text-danger">*</span></label>
                <select class="form-select" id="kategoriProdukId" required></select>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold text-secondary">Harga Produk (Rp) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="hargaProduk" required min="0" step="1000" placeholder="Contoh: 150000">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold text-secondary">Foto Produk</label>
                <input type="file" class="form-control" id="fotoProduk" accept="image/jpeg,image/png,image/webp">
                <small class="text-muted" style="font-size: 0.72rem;">Format JPG/PNG/WebP maks. 2MB</small>
              </div>
              <div class="col-12">
                <label class="form-label small fw-semibold text-secondary">Deskripsi Rinci Produk <span class="text-danger">*</span></label>
                <textarea class="form-control" id="deskripsiProduk" rows="4" required placeholder="Jelaskan bahan, ukuran, spesifikasi, dan keunggulan produk..."></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
            <button type="submit" id="saveProdukBtn" class="btn btn-kadin-primary btn-sm px-4">
              Simpan Produk
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    let modalInstance = null;
    let kategoriList = [];

    document.addEventListener('DOMContentLoaded', async () => {
      if (!auth.guard(['umkm'])) return;

      document.getElementById('sidebar-container').innerHTML = ui.renderDashboardSidebar('produk');
      document.getElementById('topbar-container').innerHTML = ui.renderDashboardTopbar('Kelola Produk');
      modalInstance = new bootstrap.Modal(document.getElementById('produkModal'));

      // Load Kategori Produk
      try {
        const katRes = await api.get('/publik/kategori-produk');
        kategoriList = katRes.data || [];
        const select = document.getElementById('kategoriProdukId');
        select.innerHTML = '<option value="">Pilih Kategori...</option>' + 
          kategoriList.map(k => `<option value="${k.id}">${k.nama_kategori}</option>`).join('');
      } catch (e) {
        console.warn('Error load kategori produk:', e);
      }

      loadProdukList();

      // Form Submit
      document.getElementById('produkForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('editProdukId').value;
        const btn = document.getElementById('saveProdukBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';

        const formData = new FormData();
        formData.append('nama_produk', document.getElementById('namaProduk').value);
        formData.append('kategori_produk_id', document.getElementById('kategoriProdukId').value);
        formData.append('harga', document.getElementById('hargaProduk').value);
        formData.append('deskripsi', document.getElementById('deskripsiProduk').value);

        const fileInput = document.getElementById('fotoProduk');
        if (fileInput.files.length > 0) {
          formData.append('foto_produk', fileInput.files[0]);
        }

        try {
          if (id) {
            // Update
            formData.append('_method', 'PUT');
            await api.post(`/umkm/produk/${id}`, formData);
            ui.showAlert('Produk berhasil diperbarui dan diajukan untuk moderasi.', 'success');
          } else {
            // Create
            await api.post('/umkm/produk', formData);
            ui.showAlert('Produk berhasil ditambahkan dan diajukan untuk moderasi!', 'success');
          }

          modalInstance.hide();
          loadProdukList();
        } catch (err) {
          console.error('Error simpan produk:', err);
          let msg = err.message || 'Gagal menyimpan produk.';
          if (err.errors) {
            msg = Object.values(err.errors).flat().join('<br>');
          }
          alert(msg);
        } finally {
          btn.disabled = false;
          btn.innerHTML = 'Simpan Produk';
        }
      });
    });

    async function loadProdukList() {
      const tbody = document.getElementById('produkTableBody');
      tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5"><div class="spinner-border text-navy"></div></td></tr>';

      try {
        const res = await api.get('/umkm/produk');
        const list = res.data || [];

        if (list.length === 0) {
          tbody.innerHTML = `
            <tr>
              <td colspan="7" class="text-center py-5 text-muted">
                <i class="bi bi-box-seam fs-1 d-block mb-2 text-secondary opacity-50"></i>
                Belum ada produk yang didaftarkan. Klik tombol <strong>Tambah Produk Baru</strong> di atas.
              </td>
            </tr>
          `;
          return;
        }

        tbody.innerHTML = list.map((p, i) => `
          <tr>
            <td class="fw-semibold text-muted">${i + 1}</td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <div class="rounded bg-light d-flex align-items-center justify-content-center overflow-hidden flex-shrink-0" style="width: 48px; height: 48px;">
                  ${p.foto_produk ? 
                    `<img src="${p.foto_url || p.foto_produk}" class="w-100 h-100 object-fit-cover">` : 
                    `<i class="bi bi-image text-muted"></i>`
                  }
                </div>
                <div>
                  <div class="fw-bold text-navy">${p.nama_produk}</div>
                  <small class="text-muted text-truncate d-block" style="max-width: 200px;">${p.deskripsi || ''}</small>
                </div>
              </div>
            </td>
            <td><span class="badge bg-light text-dark border">${p.kategori?.nama_kategori || '-'}</span></td>
            <td><span class="fw-bold text-navy">${ui.formatRupiah(p.harga)}</span></td>
            <td>${ui.renderBadge(p.status)}</td>
            <td><small class="text-muted">${ui.formatDate(p.updated_at || p.created_at)}</small></td>
            <td class="text-end">
              <div class="btn-group">
                <button class="btn btn-sm btn-outline-secondary py-1 px-2" onclick="editProduk(${p.id})">
                  <i class="bi bi-pencil-square"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger py-1 px-2" onclick="hapusProduk(${p.id})">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        `).join('');

      } catch (err) {
        console.error('Error load produk UMKM:', err);
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-danger">Gagal memuat katalog produk.</td></tr>';
      }
    }

    function bukaModalProduk() {
      document.getElementById('editProdukId').value = '';
      document.getElementById('modalTitle').innerText = 'Tambah Produk Baru';
      document.getElementById('produkForm').reset();
      modalInstance.show();
    }

    async function editProduk(id) {
      try {
        const res = await api.get(`/umkm/produk/${id}`);
        const p = res.data;

        document.getElementById('editProdukId').value = p.id;
        document.getElementById('modalTitle').innerText = 'Edit Produk';
        document.getElementById('namaProduk').value = p.nama_produk;
        document.getElementById('kategoriProdukId').value = p.kategori_produk_id;
        document.getElementById('hargaProduk').value = Math.floor(p.harga);
        document.getElementById('deskripsiProduk').value = p.deskripsi || '';
        document.getElementById('fotoProduk').value = '';

        modalInstance.show();
      } catch (err) {
        console.error('Error load detail produk:', err);
        alert('Gagal mengambil data produk.');
      }
    }

    async function hapusProduk(id) {
      if (!confirm('Apakah Anda yakin ingin menghapus produk ini?')) return;

      try {
        await api.delete(`/umkm/produk/${id}`);
        ui.showAlert('Produk berhasil dihapus.', 'success');
        loadProdukList();
      } catch (err) {
        console.error('Error delete produk:', err);
        alert(err.message || 'Gagal menghapus produk.');
      }
    }
  </script>
</body>
</html>
