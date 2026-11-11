import { $, api, esc, init, money } from './app.js';

await init();

const form = $('#filters');
let page = 1;

const categories = await api('/categories');
form.category_id.innerHTML += categories.data.map((c) => `<option value="${c.id}">${esc(c.name)}</option>`).join('');

async function load() {
  const params = new URLSearchParams(new FormData(form));
  params.set('page', page);

  const { data, meta } = await api('/products?' + params);

  $('#list').innerHTML = data.map((p) => `
    <a class="card" href="product.html?id=${p.id}">
      ${p.image ? `<img src="${p.image}" alt="">` : ''}
      <h3>${esc(p.name)}</h3>
      <div class="price">${money(p.price)}</div>
      ${p.stock === 0 ? '<span class="tag">Indisponível</span>' : ''}
    </a>`).join('') || '<p>Nenhum produto encontrado para essa busca.</p>';

  $('#pager').innerHTML = `
    <button class="secondary" id="prev" ${page <= 1 ? 'disabled' : ''}>Anterior</button>
    <span>Página ${meta.current_page} de ${meta.last_page}</span>
    <button class="secondary" id="next" ${page >= meta.last_page ? 'disabled' : ''}>Próxima</button>`;

  $('#prev').onclick = () => { page--; load(); };
  $('#next').onclick = () => { page++; load(); };
}

form.onsubmit = (e) => {
  e.preventDefault();
  page = 1;
  load();
};

load();