import { $, api, esc, init, money, qs, show, status } from './app.js';

const { user } = await init({ login: true });

function render(o) {
  const a = o.address;
  const pending = o.status === 'pending';

  $('#order').innerHTML = `
    <h1>Pedido #${o.id}</h1>
    <p>Status: <span class="tag">${status(o.status)}</span>
      ${o.paid_at ? `<br>Pago em ${new Date(o.paid_at).toLocaleString('pt-BR')}` : ''}</p>
    <div class="card">
      <h2>Itens</h2>
      <div class="table-wrap"><table>
        <tr><th>Produto</th><th>Qtd</th><th>Preço</th><th>Subtotal</th></tr>
        ${o.items.map((i) => `<tr><td>${esc(i.name)}</td><td>${i.quantity}</td><td>${money(i.unit_price)}</td><td>${money(i.subtotal)}</td></tr>`).join('')}
      </table></div>
      <p>Frete: ${money(o.shipping)}</p>
      <p class="price">Total: ${money(o.total)}</p>
    </div>
    <div class="card">
      <h2>Entrega</h2>
      <p>${esc(a.recipient_name)}<br>${esc(a.street)}, ${esc(a.number)} ${esc(a.complement)}<br>${esc(a.neighborhood)} - ${esc(a.city)}/${esc(a.state)} - ${esc(a.zip_code)}</p>
    </div>
    ${pending ? `
      <button id="cancel" class="secondary">Cancelar pedido</button>` : ''}
    <p><a href="orders.html">Voltar aos pedidos</a></p>`;

  $('#cancel')?.addEventListener('click', () => act(`/orders/${o.id}/cancel`, 'Pedido cancelado e estoque devolvido.'));
}

async function act(path, message) {
  try {
    render(await api(path, { method: 'POST' }));
    show(message, 'ok');
  } catch (err) {
    show(err.message);
  }
}

try {
  render(await api('/orders/' + qs('id')));
} catch (err) {
  show(err.message);
}