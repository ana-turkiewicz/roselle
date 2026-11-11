import { $, api, esc, init, money, qs, show } from './app.js';

await init();

try {
  const p = await api('/products/' + qs('id'));
  const available = p.stock > 0;

  $('#product').innerHTML = `
    ${p.image ? `<img src="${p.image}" alt="">` : '<div></div>'}
    <div>
      <h1>${esc(p.name)}</h1>
      <p class="muted">${esc(p.category.name)}</p>
      <p>${esc(p.description)}</p>
      <p class="price">${money(p.price)}</p>
      <p>${available ? `${p.stock} em estoque` : '<span class="tag">Indisponível</span>'}</p>
      <form id="add">
        <label>Quantidade</label>
        <input type="number" name="quantity" value="1" min="1" max="${p.stock}" ${available ? '' : 'disabled'}>
        <p><button ${available ? '' : 'disabled'}>Adicionar ao carrinho</button></p>
      </form>
    </div>`;

  $('#add').onsubmit = async (e) => {
    e.preventDefault();

    try {
      await api('/cart/items', { method: 'POST', body: { product_id: p.id, quantity: Number(e.target.quantity.value) } });
      await init();
      show('Produto adicionado ao carrinho.', 'ok');
    } catch (err) {
      show(err.message);
    }
  };
} catch (err) {
  show(err.status === 404 ? 'Produto não encontrado.' : err.message);
}