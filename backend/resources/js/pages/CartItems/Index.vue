<script setup lang="ts">

    defineProps<{
        cartItems: {
            data: {
                id: number;
                quantity: number;
                cart_id: number;
                product_id: number;
                }[];
            };
        }>();

</script>

<template>
    <div>
        <h1>Itens de Carrinho</h1>
        <p>Página de itens de carrinho</p>
        <br>
    </div>

    <p v-if="!cartItems.data.length">Nenhum item de carrinho encontrado.</p>

    <div class="grid grid-cols-2 gap-4">
        <div v-for="cartItem in cartItems.data" :key="cartItem.id" class="border p-4 rounded">
            <h2>{{ cartItem.quantity }}</h2>
            <p>{{ cartItem.cart_id }}</p>
            <p>{{ cartItem.product_id }}</p>
        </div>
    </div>
</template>