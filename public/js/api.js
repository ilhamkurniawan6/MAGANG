/**
 * KADIN Digital Hub - API Client
 * Mengelola komunikasi HTTP ke Backend Laravel API dengan token Sanctum
 */

const API_BASE_URL = window.API_BASE_URL || '/api';

const api = {
  baseUrl: API_BASE_URL,

  /**
   * Helper request universal
   */
  async request(endpoint, options = {}) {
    const url = `${API_BASE_URL}${endpoint.startsWith('/') ? endpoint : '/' + endpoint}`;
    const token = localStorage.getItem('kadin_token');
    
    const headers = {
      'Accept': 'application/json',
      ...(options.headers || {})
    };

    if (token && !headers['Authorization']) {
      headers['Authorization'] = `Bearer ${token}`;
    }

    // Jika body adalah FormData, jangan set Content-Type agar browser otomatis mengisinya dengan boundary
    if (options.body && !(options.body instanceof FormData) && typeof options.body === 'object') {
      headers['Content-Type'] = 'application/json';
      options.body = JSON.stringify(options.body);
    }

    try {
      const response = await fetch(url, {
        ...options,
        headers
      });

      const contentType = response.headers.get('content-type');
      let data = null;
      if (contentType && contentType.includes('application/json')) {
        data = await response.json();
      } else {
        data = { message: await response.text() };
      }

      if (!response.ok) {
        if (response.status === 401) {
          // Token expired or invalid
          console.warn('Session expired or unauthorized');
          if (window.location.pathname.includes('/admin/') || 
              window.location.pathname.includes('/umkm/') || 
              window.location.pathname.includes('/buyer/')) {
            localStorage.removeItem('kadin_token');
            localStorage.removeItem('kadin_user');
            window.location.href = '/login.html';
          }
        }
        
        const error = new Error(data.message || `Request failed with status ${response.status}`);
        error.status = response.status;
        error.errors = data.errors || null;
        error.data = data;
        throw error;
      }

      return data;
    } catch (err) {
      console.error(`API Error [${options.method || 'GET'} ${endpoint}]:`, err);
      throw err;
    }
  },

  get(endpoint, headers = {}) {
    return this.request(endpoint, { method: 'GET', headers });
  },

  post(endpoint, body = {}, headers = {}) {
    return this.request(endpoint, { method: 'POST', body, headers });
  },

  put(endpoint, body = {}, headers = {}) {
    return this.request(endpoint, { method: 'PUT', body, headers });
  },

  delete(endpoint, headers = {}) {
    return this.request(endpoint, { method: 'DELETE', headers });
  }
};

window.api = api;
window.API = api;
