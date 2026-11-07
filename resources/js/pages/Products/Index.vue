<script setup lang="ts">

    defineProps<{
        products: {
            data: {
                id: number;
                name: string;
                description: string;
                price: number;
                stock: number;
                category?: {
                    name: string;
                };
            }[];
        };
    }>();

</script>

<template>
    <div>
        <h1>Produtos</h1>
        <p>Página de produtos</p>
        <br>
    </div>

    <p v-if="!products.data.length">Nenhum produto encontrado.</p>

    <div class="grid grid-cols-2 gap-4">
        <div v-for="product in products.data" :key="product.id" class="border p-4 rounded">
            <h2>{{ product.name }}</h2>
            <p>{{ product.description }}</p>
            <p>Preço: {{ product.price }}</p>
            <p>Estoque: {{ product.stock }}</p>
            <p v-if="product.category">Categoria: {{ product.category.name }}</p>
        </div>
    </div>
</template>