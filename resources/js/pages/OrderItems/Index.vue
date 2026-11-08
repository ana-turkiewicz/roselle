<script setup lang="ts">

    defineProps<{
        orderItems: {
            data: {
                id: number;
                quantity: number;
                unit_price: number;
                order_id: number;
                product_id: number;
                }[];
            };
        }>();

</script>

<template>
    <div>
        <h1>Itens do Pedido</h1>
        <p>Página de itens de pedidos</p>
        <br>
    </div>

    <p v-if="!orderItems.data.length">Nenhum item de pedido encontrado.</p>

    <div class="grid grid-cols-2 gap-4">
        <div v-for="order in orderItems.data" :key="order.id" class="border p-4 rounded">
            <h2>{{ order.quantity }}</h2>
            <p>{{ order.unit_price }}</p>
            <p>{{ order.order_id }}</p>
            <p>{{ order.product_id }}</p>

        </div>
    </div>
</template>