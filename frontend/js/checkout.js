import { $, api, esc, init, money, show } from './app.js';

await init({ login: true });

async function loadAddresses(selected) {
  const { data } = await api('/addresses');

  $('#address').innerHTML = data.map((a) => `<option value="${a.id}" ${a.id == selected ? 'selected' : ''}>${esc(a.recipient_name)} - ${esc(a.street)}, ${esc(a.number)} - ${esc(a.city)}/${esc(a.state)}</option>`).join('')
    || '<option value="">Nenhum endereço cadastrado</option>';
}

const cart = await api('/cart');

$('#summary').innerHTML = `
  <ul>${cart.items.map((i) => `<li>${i.quantity} x ${esc(i.name)} - ${money(i.price)} cada = ${money(i.subtotal)}</li>`).join('')}</ul>
  <p>Subtotal: ${money(cart.subtotal)}<br>Frete: ${money(cart.shipping)}</p>
  <p class="price">Total: ${money(cart.total)}</p>`;

if (!cart.items.length) {
  $('#summary').innerHTML = '<p>Seu carrinho está vazio. <a href="index.html">Ver catálogo</a></p>';
  $('#confirm').disabled = true;
}

$('#new-address').onsubmit = async (e) => {
  e.preventDefault();

  try {
    const address = await api('/addresses', { method: 'POST', body: Object.fromEntries(new FormData(e.target)) });
    e.target.reset();
    await loadAddresses(address.id);
    show('Endereço salvo.', 'ok');
  } catch (err) {
    show(err.message);
  }
};

$('#confirm').onclick = async () => {
  try {
    const order = await api('/orders', { method: 'POST', body: { address_id: Number($('#address').value) } });
    location.href = 'order.html?id=' + order.id;
  } catch (err) {
    show(err.message);
  }
};

loadAddresses();