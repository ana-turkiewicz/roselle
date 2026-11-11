import { $, api, esc, money, show, status } from './app.js';

const filter = $('#order-status');
let page = 1;

const NEXT = { paid: ['shipped', 'Marcar como enviado'], shipped: ['delivered', 'Marcar como entregue'] };

async function load() {
  const { data, meta } = await api(`/admin/orders?page=${page}&status=${filter.value}`);

  $('#admin-orders').innerHTML = data.length ? `
    <table>
      <tr><th>Pedido</th><th>Cliente</th><th>Total</th><th>Status</th><th></th></tr>
      ${data.map((o) => `
        <tr>
          <td><a href="order.html?id=${o.id}">#${o.id}</a></td>
          <td>${esc(o.user.name)}</td>
          <td>${money(o.total)}</td>
          <td>${status(o.status)}</td>
          <td>${NEXT[o.status] ? `<button class="secondary" data-order="${o.id}" data-next="${NEXT[o.status][0]}">${NEXT[o.status][1]}</button>` : ''}</td>
        </tr>`).join('')}
    </table>` : '<p>Nenhum pedido encontrado.</p>';

  $('#orders-pager').innerHTML = meta.last_page > 1 ? `
    <button class="secondary" id="orders-prev" ${page <= 1 ? 'disabled' : ''}>Anterior</button>
    <span>Página ${meta.current_page} de ${meta.last_page}</span>
    <button class="secondary" id="orders-next" ${page >= meta.last_page ? 'disabled' : ''}>Próxima</button>` : '';

  $('#orders-prev')?.addEventListener('click', () => { page--; load(); });
  $('#orders-next')?.addEventListener('click', () => { page++; load(); });

  document.querySelectorAll('[data-order]').forEach((button) => {
    button.onclick = async () => {
      try {
        await api(`/admin/orders/${button.dataset.order}/status`, { method: 'PATCH', body: { status: button.dataset.next } });
        show('Status atualizado.', 'ok');
        load();
      } catch (err) {
        show(err.message);
      }
    };
  });
}

filter.onchange = () => {
  page = 1;
  load();
};

load();