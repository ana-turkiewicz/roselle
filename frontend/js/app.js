export const $ = (selector) => document.querySelector(selector);
export const qs = (key) => new URLSearchParams(location.search).get(key);
export const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
export const money = (v) => Number(v).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

const STATUS = { pending: 'Aguardando pagamento', paid: 'Pago', shipped: 'Enviado', delivered: 'Entregue', cancelled: 'Cancelado' };
export const status = (s) => STATUS[s] || s;

export async function api(path, { method = 'GET', body, form } = {}) {
  const cookie = () => document.cookie.match(/XSRF-TOKEN=([^;]+)/);

  if (!cookie()) await fetch('/sanctum/csrf-cookie');

  const headers = { Accept: 'application/json' };
  const session = localStorage.getItem('cart_session');
  if (session) headers['X-Cart-Session'] = session;
  if (cookie()) headers['X-XSRF-TOKEN'] = decodeURIComponent(cookie()[1]);
  if (body) headers['Content-Type'] = 'application/json';

  const res = await fetch('/api' + path, { method, headers, body: form || (body && JSON.stringify(body)) });
  if (res.status === 204) return null;

  const data = await res.json().catch(() => ({}));
  if (!res.ok) {
    const first = data.errors && Object.values(data.errors)[0][0];
    throw { status: res.status, message: first || data.message || 'Erro inesperado.' };
  }
  if (data.session) localStorage.setItem('cart_session', data.session);

  return data;
}

export function show(message, type = '') {
  const box = $('#msg');
  box.className = 'msg ' + type;
  box.textContent = message;
  box.hidden = !message;
}

export async function init({ login = false, admin = false } = {}) {
  const user = await api('/user').catch(() => null);

  if ((login || admin) && !user) {
    location.href = 'login.html?next=' + encodeURIComponent(location.pathname.split('/').pop() + location.search);
    return null;
  }

  if (admin && user.role !== 'admin') {
    document.body.innerHTML = '<main><h1>403</h1><p>Acesso restrito a administradores.</p></main>';
    return null;
  }

  const cart = await api('/cart');
  const page = location.pathname.split('/').pop() || 'index.html';
  const links = [['index.html', 'Catálogo'], ['cart.html', `Carrinho (${cart.count})`]];

  if (user) links.push(['orders.html', 'Meus pedidos'], ['profile.html', 'Perfil']);
  if (user?.role === 'admin') links.push(['admin.html', 'Painel']);

  $('header').innerHTML = '<nav><a class="brand" href="index.html">Roselle</a>'
    + links.map(([href, text]) => `<a href="${href}" class="${href === page ? 'active' : ''}">${text}</a>`).join('')
    + (user ? `<a href="#" id="logout">Sair (${esc(user.name)})</a>` : `<a href="login.html" class="${page === 'login.html' ? 'active' : ''}">Entrar</a>`)
    + '</nav>';

  $('#logout')?.addEventListener('click', async (e) => {
    e.preventDefault();
    await api('/auth/logout', { method: 'POST' });
    location.href = 'index.html';
  });

  const notice = sessionStorage.getItem('notice');
  if (notice && $('#msg')) {
    show(notice, 'warn');
    sessionStorage.removeItem('notice');
  }

  return { user, cart };
}