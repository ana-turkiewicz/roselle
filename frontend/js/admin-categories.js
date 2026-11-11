import { $, api, esc, show } from './app.js';

const form = $('#category-form');
let categories = [];
let editing = null;

function reset() {
  editing = null;
  form.reset();
  $('#category-title').textContent = 'Nova categoria';
}

async function load() {
  categories = (await api('/categories')).data;
  document.dispatchEvent(new CustomEvent('categories:changed', { detail: categories }));

  $('#categories').innerHTML = `
    <table>
      <tr><th>Nome</th><th>Descrição</th><th></th></tr>
      ${categories.map((c) => `
        <tr>
          <td>${esc(c.name)}</td>
          <td>${esc(c.description)}</td>
          <td>
            <button class="secondary" data-edit-category="${c.id}">Editar</button>
            <button class="secondary" data-delete-category="${c.id}">Excluir</button>
          </td>
        </tr>`).join('')}
    </table>`;

  document.querySelectorAll('[data-edit-category]').forEach((button) => {
    button.onclick = () => {
      editing = categories.find((c) => c.id == button.dataset.editCategory);
      form.name.value = editing.name;
      form.description.value = editing.description ?? '';
      $('#category-title').textContent = 'Editar categoria';
    };
  });

  document.querySelectorAll('[data-delete-category]').forEach((button) => {
    button.onclick = async () => {
      if (!confirm('Excluir categoria?')) return;

      try {
        await api('/admin/categories/' + button.dataset.deleteCategory, { method: 'DELETE' });
        show('Categoria excluída.', 'ok');
        load();
      } catch (err) {
        show(err.message);
      }
    };
  });
}

form.onsubmit = async (e) => {
  e.preventDefault();

  try {
    await api(editing ? '/admin/categories/' + editing.id : '/admin/categories', {
      method: editing ? 'PUT' : 'POST',
      body: Object.fromEntries(new FormData(form)),
    });
    reset();
    show('Categoria salva.', 'ok');
    load();
  } catch (err) {
    show(err.message);
  }
};

$('#category-cancel').onclick = reset;

load();