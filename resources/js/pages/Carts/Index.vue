<script setup lang="ts">

    defineProps<{
        carts: {
            data: {
                id: number;
                status: string;
                user_id: number;
                }[];
            };
        }>();

</script>

<template>
    <div>
        <h1>Carrinhos</h1>
        <p>Página de carrinhos</p>
        <br>
    </div>

    <p v-if="!carts.data.length">Nenhum carrinho encontrado.</p>

    <div class="grid grid-cols-2 gap-4">
        <div v-for="cart in carts.data" :key="cart.id" class="border p-4 rounded">
            <h2>{{ cart.status }}</h2>
            <p>{{ cart.user_id }}</p>
        </div>
    </div>
</template>