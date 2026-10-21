<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>WhatsApp Bot AI Simulator - KADIN Digital Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  @vite(['resources/css/kadin.css', 'resources/js/app.js'])
  <style>
    .wa-chat-container {
      background: #e5ddd5;
      background-image: radial-gradient(#d1c7bc 1px, transparent 1px);
      background-size: 16px 16px;
      border-radius: 12px;
      height: 500px;
      overflow-y: auto;
      padding: 1.25rem;
      display: flex;
      flex-direction: column;
      gap: 0.85rem;
      scroll-behavior: smooth;
    }
    .chat-bubble {
      max-width: 80%;
      padding: 0.65rem 0.95rem;
      border-radius: 8px;
      font-size: 0.9rem;
      line-height: 1.45;
      position: relative;
      word-wrap: break-word;
      white-space: pre-wrap;
      animation: fadeInBubble 0.2s ease-in-out;
    }
    @keyframes fadeInBubble {
      from { opacity: 0; transform: translateY(4px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .chat-bubble.bot {
      background: #ffffff;
      align-self: flex-start;
      border-top-left-radius: 0;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      color: #111b21;
    }
    .chat-bubble.user {
      background: #d9fdd3;
      align-self: flex-end;
      border-top-right-radius: 0;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      color: #111b21;
    }
    .chat-time {
      font-size: 0.7rem;
      color: #667781;
      text-align: right;
      margin-top: 4px;
    }
    .chat-image-preview {
      max-width: 220px;
      max-height: 180px;
      object-fit: cover;
      border-radius: 6px;
      margin-bottom: 6px;
      border: 1px solid rgba(0,0,0,0.1);
    }
    .typing-dots {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 4px 8px;
    }
    .typing-dots span {
      width: 6px;
      height: 6px;
      background-color: #888;
      border-radius: 50%;
      animation: typingAnimation 1.2s infinite ease-in-out;
    }
    .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
    .typing-dots span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typingAnimation {
      0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
      40% { transform: scale(1); opacity: 1; }
    }
    .quick-pill {
      cursor: pointer;
      transition: all 0.15s ease-in-out;
    }
    .quick-pill:hover {
      background-color: #f0f2f5 !important;
      border-color: #075e54 !important;
      color: #075e54 !important;
    }
  </style>
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
            <h1 class="h4 fw-bold text-navy mb-1"><i class="bi bi-whatsapp text-success me-2"></i>Simulator WhatsApp Bot Tambah Produk (AI/NLP)</h1>
            <p class="text-secondary small mb-0">Uji coba simulasi payload webhook Wablas & FSM Bot AI tanpa membutuhkan nomor WhatsApp fisik.</p>
          </div>
          <div class="d-flex gap-2">
            <a href="moderasi-produk.html" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1">
              <i class="bi bi-box-seam-fill"></i> Cek Moderasi Produk &rarr;
            </a>
          </div>
        </div>

        <div class="row g-4">
          <!-- Kolom Konfigurasi Pengirim & Quick Guide -->
          <div class="col-lg-4">
            <div class="card-clean p-4 h-100 shadow-xs bg-white">
              <h5 class="fw-bold text-navy mb-3" style="font-size: 1rem;"><i class="bi bi-sliders me-2 text-warning"></i> Skenario Simulasi</h5>
              
              <div class="mb-3">
                <label class="form-label small fw-semibold">Pilih UMKM Binaan</label>
                <select class="form-select form-select-sm" id="selectUmkm">
                  <option value="">Memuat data UMKM...</option>
                </select>
                <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Nomor harus cocok dengan UMKM yang terdaftar di database.</small>
              </div>

              <div class="mb-3">
                <label class="form-label small fw-semibold">Nomor WhatsApp Pengirim</label>
                <input type="text" class="form-control form-control-sm" id="inputNomorWa" placeholder="6281234567890">
              </div>

              <hr class="my-3">

              <h6 class="fw-bold text-navy mb-2" style="font-size: 0.88rem;"><i class="bi bi-diagram-3-fill text-primary me-1"></i> Alur FSM Bot AI:</h6>
              <div class="small text-secondary ps-2 mb-3" style="line-height: 1.5; font-size: 0.82rem;">
                <div class="mb-2"><span class="badge bg-secondary me-1">1</span> <strong>Kirim Foto:</strong> Klik tombol kamera / foto contoh.</div>
                <div class="mb-2"><span class="badge bg-secondary me-1">2</span> <strong>Kirim Detail:</strong> Tulis nama produk dan harga bebas (AI mengekstrak entitas).</div>
                <div class="mb-2"><span class="badge bg-secondary me-1">3</span> <strong>Konfirmasi:</strong> Balas <code>OK</code> untuk simpan ke antrean kurasi, atau <code>ULANG</code>.</div>
                <div><span class="badge bg-secondary me-1">4</span> <strong>Batal:</strong> Ketik <code>BATAL</code> kapan saja untuk reset.</div>
              </div>

              <div class="p-3 bg-light rounded-3 border mb-3">
                <small class="text-navy fw-bold d-block mb-1"><i class="bi bi-shield-check text-success me-1"></i> Keamanan Kurasi:</small>
                <small class="text-secondary d-block" style="font-size: 0.76rem;">Produk yang berhasil diinput via bot selalu berstatus <code>menunggu_moderasi</code> sebelum tayang di katalog publik.</small>
              </div>

              <button class="btn btn-sm btn-outline-danger w-100" id="btnResetChat">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Simulasi Chat
              </button>
            </div>
          </div>

          <!-- Kolom WhatsApp Mockup Chat -->
          <div class="col-lg-8">
            <div class="card-clean p-0 overflow-hidden shadow-sm border bg-white">
              <!-- Header Mockup WhatsApp -->
              <div class="p-3 d-flex justify-content-between align-items-center" style="background-color: #075e54; color: #fff;">
                <div class="d-flex align-items-center gap-3">
                  <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 42px; height: 42px;">
                    <i class="bi bi-robot fs-5 text-success"></i>
                  </div>
                  <div>
                    <div class="fw-bold lh-1" style="font-size: 1rem;">KADIN Hub WhatsApp Bot (AI)</div>
                    <small class="text-light text-opacity-75" style="font-size: 0.75rem;">
                      <span class="spinner-grow spinner-grow-sm text-warning" style="width: 8px; height: 8px;" role="status"></span>
                      Online | Status Sesi: <strong id="badgeSessionStep" class="badge bg-warning text-dark text-uppercase px-2">idle</strong>
                    </small>
                  </div>
                </div>
                <div>
                  <span class="badge bg-white bg-opacity-25 text-white small px-2 py-1"><i class="bi bi-patch-check-fill me-1 text-warning"></i>Verified Bot</span>
                </div>
              </div>

              <!-- Chat Area Messages -->
              <div class="wa-chat-container" id="chatArea">
                <div class="chat-bubble bot">
                  Halo! Selamat datang di Simulator WhatsApp Bot KADIN Hub 🌿
Silakan pilih UMKM di panel samping lalu kirim foto produk untuk memulai simulasi penambahan produk.
                  <div class="chat-time">Bot System</div>
                </div>
              </div>

              <!-- Input Area Mockup -->
              <div class="p-3 bg-light border-top">
                <!-- Quick Action Buttons -->
                <div class="d-flex gap-2 mb-2 flex-wrap align-items-center">
                  <span class="small text-muted fw-semibold me-1" style="font-size: 0.76rem;">Shortcut Demo:</span>
                  <button type="button" class="btn btn-xs btn-outline-success border bg-white py-1 px-2 rounded-pill quick-pill" onclick="kirimContohFoto('https://images.unsplash.com/photo-1599658880436-c61792e70672?w=500')">
                    <i class="bi bi-camera me-1"></i> [1] Kirim Foto Kerupuk
                  </button>
                  <button type="button" class="btn btn-xs btn-outline-secondary border bg-white py-1 px-2 rounded-pill quick-pill" onclick="isiContoh('Kerupuk Udang Gurih Khas Jogja seharga 15.000')">
                    [2] "Kerupuk Udang 15.000"
                  </button>
                  <button type="button" class="btn btn-xs btn-outline-secondary border bg-white py-1 px-2 rounded-pill quick-pill" onclick="isiContoh('Batik Tulis Motif Parang Rp 350.000')">
                    "Batik Parang 350rb"
                  </button>
                  <button type="button" class="btn btn-xs btn-outline-primary border bg-white py-1 px-2 rounded-pill quick-pill fw-bold" onclick="isiContoh('OK')">
                    [3] "OK"
                  </button>
                  <button type="button" class="btn btn-xs btn-outline-danger border bg-white py-1 px-2 rounded-pill quick-pill" onclick="isiContoh('BATAL')">
                    "BATAL"
                  </button>
                </div>

                <div class="row g-2 align-items-center">
                  <div class="col-auto">
                    <label class="btn btn-outline-secondary btn-sm mb-0 position-relative" title="Unggah Foto dari Komputer">
                      <i class="bi bi-paperclip fs-6"></i>
                      <input type="file" id="fileFotoInput" accept="image/*" class="d-none">
                    </label>
                  </div>
                  <div class="col">
                    <input type="text" class="form-control form-control-sm" id="inputPesanText" placeholder="Ketik pesan WhatsApp atau klik shortcut di atas...">
                  </div>
                  <div class="col-auto">
                    <button class="btn btn-success btn-sm px-3 d-flex align-items-center gap-1" id="btnKirimPesan">
                      <i class="bi bi-send-fill"></i> <span>Kirim</span>
                    </button>
                  </div>
                </div>
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
      if (!auth.guard(['admin'])) return;

      document.getElementById('sidebar-container').innerHTML = ui.renderDashboardSidebar('wa-bot-simulator');
      document.getElementById('topbar-container').innerHTML = ui.renderDashboardTopbar('WA Bot Simulator');

      const selectUmkm = document.getElementById('selectUmkm');
      const inputNomor = document.getElementById('inputNomorWa');
      const chatArea = document.getElementById('chatArea');
      const inputPesan = document.getElementById('inputPesanText');
      const fileInput = document.getElementById('fileFotoInput');
      const btnKirim = document.getElementById('btnKirimPesan');
      const stepBadge = document.getElementById('badgeSessionStep');

      // Load UMKMs
      try {
        const res = await api.get('/admin/umkm');
        const list = res.data || [];
        if (list.length > 0) {
          selectUmkm.innerHTML = '<option value="">-- Pilih UMKM untuk Simulasi --</option>' + 
            list.map(u => `<option value="${u.kontak_wa || '081234567890'}">${u.nama_usaha} (${u.kontak_wa || 'No WA'})</option>`).join('');

          selectUmkm.selectedIndex = 1;
          inputNomor.value = selectUmkm.value;
        } else {
          selectUmkm.innerHTML = '<option value="081234567890">UMKM Binaan (081234567890)</option>';
          inputNomor.value = '081234567890';
        }
      } catch (err) {
        selectUmkm.innerHTML = '<option value="081234567890">UMKM Binaan (081234567890)</option>';
        inputNomor.value = '081234567890';
      }

      selectUmkm.addEventListener('change', () => {
        if (selectUmkm.value) {
          inputNomor.value = selectUmkm.value;
        }
      });

      function appendBubble(sender, text, imageSrc = null) {
        const bubble = document.createElement('div');
        bubble.className = `chat-bubble ${sender}`;
        const timeNow = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        let imgHtml = '';
        if (imageSrc) {
          imgHtml = `<img src="${imageSrc}" class="chat-image-preview d-block" alt="Foto Produk">`;
        }

        bubble.innerHTML = `${imgHtml}<div>${text}</div><div class="chat-time">${timeNow}</div>`;
        chatArea.appendChild(bubble);
        chatArea.scrollTop = chatArea.scrollHeight;
        return bubble;
      }

      function showTypingIndicator() {
        const bubble = document.createElement('div');
        bubble.className = 'chat-bubble bot typing-indicator-bubble';
        bubble.id = 'botTypingBubble';
        bubble.innerHTML = '<div class="typing-dots"><span></span><span></span><span></span></div>';
        chatArea.appendChild(bubble);
        chatArea.scrollTop = chatArea.scrollHeight;
      }

      function hideTypingIndicator() {
        const typing = document.getElementById('botTypingBubble');
        if (typing) {
          typing.remove();
        }
      }

      async function sendWebhookPayload(formData) {
        showTypingIndicator();
        btnKirim.disabled = true;

        try {
          const endpointUrl = `${api.baseUrl || '/api'}/wablas/webhook`;
          const response = await fetch(endpointUrl, {
            method: 'POST',
            body: formData,
            headers: {
              'Accept': 'application/json',
            }
          });

          const data = await response.json();
          hideTypingIndicator();

          if (data.session_step) {
            stepBadge.innerText = data.session_step;
          }

          if (data.reply) {
            appendBubble('bot', data.reply);
          } else if (data.message) {
            appendBubble('bot', data.message);
          }
        } catch (err) {
          hideTypingIndicator();
          console.error('Webhook request error:', err);
          appendBubble('bot', '⚠️ Gagal terhubung ke webhook endpoint backend: ' + err.message);
        } finally {
          btnKirim.disabled = false;
        }
      }

      // Handle Kirim Pesan Teks
      btnKirim.addEventListener('click', async () => {
        const text = inputPesan.value.trim();
        const nomor = inputNomor.value.trim();
        if (!text || !nomor) {
          ui.toast('Pilih UMKM atau isi nomor pengirim terlebih dahulu.', 'warning');
          return;
        }

        appendBubble('user', text);
        inputPesan.value = '';

        const fd = new FormData();
        fd.append('phone', nomor);
        fd.append('message', text);

        await sendWebhookPayload(fd);
      });

      inputPesan.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
          btnKirim.click();
        }
      });

      // Handle Kirim Foto dari File Input
      fileInput.addEventListener('change', async () => {
        if (!fileInput.files.length) return;
        const file = fileInput.files[0];
        const nomor = inputNomor.value.trim();
        if (!nomor) {
          ui.toast('Masukkan nomor WA pengirim terlebih dahulu.', 'warning');
          return;
        }

        const reader = new FileReader();
        reader.onload = async (e) => {
          appendBubble('user', '📷 [Mengirim Foto Produk]', e.target.result);

          const fd = new FormData();
          fd.append('phone', nomor);
          fd.append('file', file);
          fd.append('type', 'image');

          await sendWebhookPayload(fd);
        };
        reader.readAsDataURL(file);
        fileInput.value = '';
      });

      // Handle Kirim Foto dari URL Contoh
      window.kirimContohFoto = async (url) => {
        const nomor = inputNomor.value.trim();
        if (!nomor) {
          ui.toast('Pilih UMKM pengirim terlebih dahulu.', 'warning');
          return;
        }

        appendBubble('user', '📷 [Mengirim Foto Produk]', url);

        const fd = new FormData();
        fd.append('phone', nomor);
        fd.append('url', url);
        fd.append('type', 'image');

        await sendWebhookPayload(fd);
      };

      document.getElementById('btnResetChat').addEventListener('click', async () => {
        const nomor = inputNomor.value.trim();
        if (nomor) {
          const fd = new FormData();
          fd.append('phone', nomor);
          fd.append('message', 'BATAL');
          await fetch(`${api.baseUrl || '/api'}/wablas/webhook`, {
            method: 'POST',
            body: fd,
            headers: { 'Accept': 'application/json' }
          }).catch(() => {});
        }

        chatArea.innerHTML = `
          <div class="chat-bubble bot">
            Sesi simulasi di-reset. Kirim foto produk untuk memulai alur pendaftaran produk baru via WhatsApp Bot.
            <div class="chat-time">Bot System</div>
          </div>
        `;
        stepBadge.innerText = 'idle';
      });

      window.isiContoh = (text) => {
        inputPesan.value = text;
        inputPesan.focus();
      };
    });
  </script>
</body>
</html>
