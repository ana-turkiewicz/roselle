<script setup lang="ts">

    defineProps<{
        orders: {
            data: {
                id: number;
                status: string;
                total: number;
                notes: string | null;
                user_id: number;
                address_id: number;
                }[];
            };
        }>();

</script>

<template>
    <div>
        <h1>Pedidos</h1>
        <p>Página de pedidos</p>
        <br>
    </div>

    <p v-if="!orders.data.length">Nenhum pedido encontrado.</p>

    <div class="grid grid-cols-2 gap-4">
        <div v-for="order in orders.data" :key="order.id" class="border p-4 rounded">
            <h2>{{ order.status }}</h2>
            <p>{{ order.total }}</p>
            <p>{{ order.notes }}</p>
            <p>{{ order.user_id }}</p>
            <p>{{ order.address_id }}</p>

        </div>
    </div>
</template>