import { $, api, esc, init, money, show } from './app.js';

const { user } = await init();

function render(cart) {
  if (!cart.items.length) {
    $('#cart').innerHTML = '<p>Seu carrinho está vazio. <a href="index.html">Ver catálogo</a></p>';
    return;
  }

  const canCheckout = cart.items.every((i) => i.available);

  $('#cart').innerHTML = `
    <div class="table-wrap"><table>
      <tr><th></th><th>Produto</th><th>Preço</th><th>Qtd</th><th>Subtotal</th><th></th></tr>
      ${cart.items.map((i) => `
        <tr>
          <td>${i.image ? `<img src="${i.image}" alt="">` : ''}</td>
          <td>${esc(i.name)} ${i.available ? '' : '<span class="tag">Indisponível</span>'}</td>
          <td>${money(i.price)}</td>
          <td><input type="number" min="0" value="${i.quantity}" data-id="${i.id}" style="width:70px"></td>
          <td>${money(i.subtotal)}</td>
          <td><button class="secondary" data-remove="${i.id}">Remover</button></td>
        </tr>`).join('')}
    </table></div>
    <p>Subtotal: ${money(cart.subtotal)}<br>Frete: ${money(cart.shipping)} <span class="muted">(grátis a partir de R$ 200,00)</span></p>
    <p class="price">Total: ${money(cart.total)}</p>
    ${canCheckout ? '' : '<p class="msg warn">Remova os itens indisponíveis para finalizar a compra.</p>'}
    <button id="checkout" ${canCheckout ? '' : 'disabled'}>Finalizar compra</button>`;

  document.querySelectorAll('input[data-id]').forEach((input) => {
    input.onchange = () => change(`/cart/items/${input.dataset.id}`, { method: 'PATCH', body: { quantity: Number(input.value) } });
  });

  document.querySelectorAll('[data-remove]').forEach((button) => {
    button.onclick = () => change(`/cart/items/${button.dataset.remove}`, { method: 'DELETE' });
  });

  $('#checkout').onclick = () => {
    location.href = user ? 'checkout.html' : 'login.html?next=checkout.html';
  };
}

async function change(path, options) {
  try {
    render(await api(path, options));
    await init();
    show('');
  } catch (err) {
    show(err.message);
    render(await api('/cart'));
  }
}

render(await api('/cart'));