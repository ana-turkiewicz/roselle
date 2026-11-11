import { $, api, init, money, show, status } from './app.js';

await init({ login: true });

let page = 1;

async function load() {
  try {
    const { data, meta } = await api('/orders?page=' + page);

    $('#orders').innerHTML = data.length ? `
      <table>
        <tr><th>Pedido</th><th>Data</th><th>Total</th><th>Status</th><th></th></tr>
        ${data.map((o) => `
          <tr>
            <td>#${o.id}</td>
            <td>${new Date(o.created_at).toLocaleDateString('pt-BR')}</td>
            <td>${money(o.total)}</td>
            <td>${status(o.status)}</td>
            <td><a href="order.html?id=${o.id}">Ver detalhe</a></td>
          </tr>`).join('')}
      </table>` : '<p>Você ainda não fez pedidos. <a href="index.html">Ver catálogo</a></p>';

    $('#pager').innerHTML = meta.last_page > 1 ? `
      <button class="secondary" id="prev" ${page <= 1 ? 'disabled' : ''}>Anterior</button>
      <span>Página ${meta.current_page} de ${meta.last_page}</span>
      <button class="secondary" id="next" ${page >= meta.last_page ? 'disabled' : ''}>Próxima</button>` : '';

    $('#prev')?.addEventListener('click', () => { page--; load(); });
    $('#next')?.addEventListener('click', () => { page++; load(); });
  } catch (err) {
    show(err.message);
  }
}

load();