/**
 * KADIN Digital Hub - Auth Helper
 * Mengelola sesi autentikasi, pengecekan role & navigasi dashboard
 */

const auth = {
  getToken() {
    return localStorage.getItem('kadin_token');
  },

  getUser() {
    const userStr = localStorage.getItem('kadin_user');
    try {
      if (!userStr) return null;
      const u = JSON.parse(userStr);
      if (u && !u.name && u.nama) {
        u.name = u.nama;
      }
      return u;
    } catch (e) {
      return null;
    }
  },

  isAuthenticated() {
    return !!this.getToken() && !!this.getUser();
  },

  requireAuth(allowedRoles = []) {
    if (!this.guard(allowedRoles)) return null;
    return this.getUser();
  },

  setSession(token, user) {
    if (user && !user.name && user.nama) {
      user.name = user.nama;
    }
    localStorage.setItem('kadin_token', token);
    localStorage.setItem('kadin_user', JSON.stringify(user));
  },

  clearSession() {
    localStorage.removeItem('kadin_token');
    localStorage.removeItem('kadin_user');
  },

  /**
   * Login universal dengan deteksi role otomatis
   */
  async login(email, password) {
    const res = await api.post('/login', { email, password });
    if (res.token && res.user) {
      this.setSession(res.token, res.user);
      return res;
    }
    throw new Error(res.message || 'Login gagal');
  },

  /**
   * Logout dan redirect ke login page
   */
  async logout() {
    try {
      await api.post('/logout');
    } catch (e) {
      console.warn('Logout API error:', e);
    } finally {
      this.clearSession();
      window.location.href = this.getRelativePath('login.html');
    }
  },

  /**
   * Registrasi mandiri UMKM
   */
  async registerUmkm(payload) {
    return await api.post('/register-umkm', payload);
  },

  /**
   * Dapatkan URL Dashboard sesuai role user
   */
  getDashboardUrl(role) {
    const r = (role || (this.getUser()?.role?.nama_role || '')).toLowerCase();
    if (r === 'admin') return this.getRelativePath('admin/dashboard.html');
    if (r === 'umkm') return this.getRelativePath('umkm/dashboard.html');
    if (r === 'buyer') return this.getRelativePath('buyer/dashboard.html');
    return this.getRelativePath('index.html');
  },

  /**
   * Helper untuk resolusi relative path yang fleksibel
   */
  getRelativePath(targetFile) {
    const path = window.location.pathname;
    if (path.includes('/admin/') || path.includes('/umkm/') || path.includes('/buyer/')) {
      return '../' + targetFile;
    }
    return targetFile;
  },

  /**
   * Guard halaman berdasarkan role yang diizinkan
   */
  guard(allowedRoles = []) {
    if (!this.isAuthenticated()) {
      window.location.href = this.getRelativePath('login.html');
      return false;
    }

    const user = this.getUser();
    const roleName = (user.role?.nama_role || '').toLowerCase();

    if (allowedRoles.length > 0 && !allowedRoles.map(r => r.toLowerCase()).includes(roleName)) {
      window.ui?.toast('Akses tidak diizinkan untuk peran ini.', 'danger');
      setTimeout(() => { window.location.href = this.getDashboardUrl(roleName); }, 1500);
      return false;
    }

    return true;
  }
};

window.auth = auth;
window.Auth = auth;
