import { $, api, esc, money, show, status } from "./app.js";

const filter = $("#order-status");
let page = 1;

const STATUSES = ["pending", "paid", "shipped", "delivered", "cancelled"];

const statusSelect = (o) => `
  <select data-order="${o.id}" data-current="${o.status}" ${o.status === "delivered" ? "disabled" : ""}>
    ${STATUSES.map((s) => `<option value="${s}" ${s === o.status ? "selected" : ""}>${status(s)}</option>`).join("")}
  </select>`;

async function load() {
  const { data, meta } = await api(
    `/admin/orders?page=${page}&status=${filter.value}`,
  );

  $("#admin-orders").innerHTML = data.length
    ? `
    <table>
      <tr><th>Pedido</th><th>Cliente</th><th>Total</th><th>Status</th></tr>
      ${data
        .map(
          (o) => `
        <tr>
          <td><a href="order.html?id=${o.id}">#${o.id}</a></td>
          <td>${esc(o.user.name)}</td>
          <td>${money(o.total)}</td>
          <td>${statusSelect(o)}</td>
        </tr>`,
        )
        .join("")}
    </table>`
    : "<p>Nenhum pedido encontrado.</p>";

  $("#orders-pager").innerHTML =
    meta.last_page > 1
      ? `
    <button class="secondary" id="orders-prev" ${page <= 1 ? "disabled" : ""}>Anterior</button>
    <span>Página ${meta.current_page} de ${meta.last_page}</span>
    <button class="secondary" id="orders-next" ${page >= meta.last_page ? "disabled" : ""}>Próxima</button>`
      : "";

  $("#orders-prev")?.addEventListener("click", () => {
    page--;
    load();
  });
  $("#orders-next")?.addEventListener("click", () => {
    page++;
    load();
  });

  document.querySelectorAll("select[data-order]").forEach((select) => {
    select.onchange = async () => {
      const id = select.dataset.order;
      const next = select.value;

      if (
        next === "cancelled" &&
        !confirm(`Cancelar o pedido #${id}? O estoque será devolvido.`)
      ) {
        select.value = select.dataset.current;
        return;
      }

      try {
        await api(`/admin/orders/${id}/status`, {
          method: "PATCH",
          body: { status: next },
        });
        show("Status atualizado.", "ok");
      } catch (err) {
        show(err.message);
      }
      load();
    };
  });
}

filter.onchange = () => {
  page = 1;
  load();
};

load();