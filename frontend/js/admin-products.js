import { $, api, esc, init, money, show } from './app.js';

await init({ admin: true });

const form = $('#product-form');
let page = 1;
let products = [];
let editing = null;

const categories = (await api('/categories')).data;
form.category_id.innerHTML = categories.map((c) => `<option value="${c.id}">${esc(c.name)}</option>`).join('');

function reset() {
  editing = null;
  form.reset();
  $('#product-title').textContent = 'Novo produto';
}

async function load() {
  const { data, meta } = await api('/admin/products?page=' + page);
  products = data;

  $('#products').innerHTML = `
    <table>
      <tr><th></th><th>Nome</th><th>Categoria</th><th>Preço</th><th>Estoque</th><th>Status</th><th></th></tr>
      ${data.map((p) => `
        <tr>
          <td>${p.image ? `<img src="${p.image}" alt="">` : ''}</td>
          <td>${esc(p.name)}</td>
          <td>${esc(p.category.name)}</td>
          <td>${money(p.price)}</td>
          <td>${p.stock}</td>
          <td>${p.is_active ? 'Ativo' : 'Inativo'}</td>
          <td>
            <button class="secondary" data-edit="${p.id}">Editar</button>
            <button class="secondary" data-delete="${p.id}">Excluir</button>
          </td>
        </tr>`).join('')}
    </table>`;

  $('#products-pager').innerHTML = `
    <button class="secondary" id="products-prev" ${page <= 1 ? 'disabled' : ''}>Anterior</button>
    <span>Página ${meta.current_page} de ${meta.last_page}</span>
    <button class="secondary" id="products-next" ${page >= meta.last_page ? 'disabled' : ''}>Próxima</button>`;

  $('#products-prev').onclick = () => { page--; load(); };
  $('#products-next').onclick = () => { page++; load(); };

  document.querySelectorAll('[data-edit]').forEach((button) => {
    button.onclick = () => {
      editing = products.find((p) => p.id == button.dataset.edit);
      form.name.value = editing.name;
      form.price.value = editing.price;
      form.stock.value = editing.stock;
      form.category_id.value = editing.category.id;
      form.is_active.value = editing.is_active ? '1' : '0';
      form.description.value = editing.description ?? '';
      $('#product-title').textContent = 'Editar produto';
      form.scrollIntoView();
    };
  });

  document.querySelectorAll('[data-delete]').forEach((button) => {
    button.onclick = async () => {
      if (!confirm('Excluir produto? Ele será desativado e os pedidos antigos continuam intactos.')) return;

      try {
        await api('/admin/products/' + button.dataset.delete, { method: 'DELETE' });
        show('Produto excluído.', 'ok');
        load();
      } catch (err) {
        show(err.message);
      }
    };
  });
}

form.onsubmit = async (e) => {
  e.preventDefault();

  const data = new FormData(form);
  if (!data.get('image').size) data.delete('image');
  if (editing) data.append('_method', 'PUT');

  try {
    await api(editing ? '/admin/products/' + editing.id : '/admin/products', { method: 'POST', form: data });
    reset();
    show('Produto salvo.', 'ok');
    load();
  } catch (err) {
    show(err.message);
  }
};

$('#product-cancel').onclick = reset;

load();