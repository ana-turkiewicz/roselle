import { api, init, qs, show } from './app.js';

await init();

function send(id, path) {
  document.getElementById(id).onsubmit = async (e) => {
    e.preventDefault();

    try {
      const body = Object.fromEntries(new FormData(e.target));
      body.cart_session = localStorage.getItem('cart_session');

      const res = await api(path, { method: 'POST', body });

      localStorage.removeItem('cart_session');

      if (res.cart_adjusted) {
        sessionStorage.setItem('notice', 'Algumas quantidades do carrinho foram ajustadas ao estoque disponível.');
      }

      const next = qs('next');
      location.href = next && /^[\w.-]+\.html(\?.*)?$/.test(next) ? next : 'index.html';
    } catch (err) {
      show(err.message);
    }
  };
}

send('login', '/auth/login');
send('register', '/auth/register');
