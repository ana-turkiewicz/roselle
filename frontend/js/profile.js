import { $, api, esc, init, show } from './app.js';

const { user } = await init({ login: true });

const userForm = $('#user');
const addressForm = $('#address');
let addresses = [];
let editing = null;

userForm.name.value = user.name;
userForm.email.value = user.email;

userForm.onsubmit = async (e) => {
  e.preventDefault();

  try {
    await api('/user', { method: 'PUT', body: Object.fromEntries(new FormData(userForm)) });
    await init({ login: true });
    show('Dados atualizados.', 'ok');
  } catch (err) {
    show(err.message);
  }
};

async function load() {
  addresses = (await api('/addresses')).data;

  $('#addresses').innerHTML = addresses.map((a) => `
    <div class="card">
      ${esc(a.recipient_name)}<br>${esc(a.street)}, ${esc(a.number)} ${esc(a.complement)} - ${esc(a.neighborhood)}<br>${esc(a.city)}/${esc(a.state)} - ${esc(a.zip_code)}<br>
      <button class="secondary" data-edit="${a.id}">Editar</button>
      <button class="secondary" data-delete="${a.id}">Excluir</button>
    </div>`).join('') || '<p>Nenhum endereço cadastrado.</p>';

  document.querySelectorAll('[data-edit]').forEach((button) => {
    button.onclick = () => {
      editing = addresses.find((a) => a.id == button.dataset.edit);
      Object.keys(Object.fromEntries(new FormData(addressForm))).forEach((key) => { addressForm[key].value = editing[key] ?? ''; });
      $('#address-title').textContent = 'Editar endereço';
    };
  });

  document.querySelectorAll('[data-delete]').forEach((button) => {
    button.onclick = async () => {
      try {
        await api('/addresses/' + button.dataset.delete, { method: 'DELETE' });
        show('');
        load();
      } catch (err) {
        show(err.message);
      }
    };
  });
}

addressForm.onsubmit = async (e) => {
  e.preventDefault();

  try {
    await api(editing ? '/addresses/' + editing.id : '/addresses', {
      method: editing ? 'PUT' : 'POST',
      body: Object.fromEntries(new FormData(addressForm)),
    });
    editing = null;
    addressForm.reset();
    $('#address-title').textContent = 'Novo endereço';
    show('Endereço salvo.', 'ok');
    load();
  } catch (err) {
    show(err.message);
  }
};

load();