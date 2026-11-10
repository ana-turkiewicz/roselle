<script setup>
    import { ref, onMounted } from 'vue';
    import { Link } from '@inertiajs/vue3';
    import axios from 'axios';

    const products = ref([]);
    const loading = ref(true);
    const error = ref('');

    async function getProducts() {
        try {
            const response = await axios.get('/api/products');

            products.value = response.data;
        } catch (err) {
            error.value = 'Não foi possível carregar os produtos.';
            console.error(err);
        } finally {
            loading.value = false;
        }
    }

    onMounted(getProducts);
</script>

<template>
    <div class="max-w-6xl mx-auto p-6">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Produtos</h1>

            <Link
                href="/products/create"
                class="bg-black text-white px-4 py-2 rounded"
            >
                Novo produto
            </Link>
        </div>

        <p v-if="loading">
            Carregando produtos...
        </p>

        <p v-else-if="error">
            {{ error }}
        </p>

        <div
            v-else-if="products.length === 0"
            class="border rounded p-6 text-center"
        >
            Nenhum produto cadastrado.
        </div>

        <div
            v-else
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"
        >
            <div
                v-for="product in products"
                :key="product.id"
                class="border rounded p-4 flex flex-col gap-3"
            >
                <div class="w-full text-black h-44 bg-gray-200 rounded overflow-hidden flex items-center justify-center">
                    <img
                        v-if="product.image"
                        :src="product.image"
                        :alt="product.name"
                        class="w-full h-full object-cover"
                    >

                    <span v-else class="font-semibold text-sm">
                        SEM IMAGEM
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">
                        ID: {{ product.id }}
                    </span>

                    <span
                        v-if="product.is_active"
                        class="text-sm text-green-700 bg-green-100 px-2 py-1 rounded"
                    >
                        Ativo
                    </span>

                    <span
                        v-else
                        class="text-sm text-red-700 bg-red-100 px-2 py-1 rounded"
                    >
                        Inativo
                    </span>
                </div>

                <div>
                    <h2 class="text-lg font-semibold">{{ product.name }}</h2>
                    <p class="text-sm text-gray-600">
                        {{ product.category?.name ?? 'Sem categoria' }}
                    </p>
                </div>

                <div class="text-sm space-y-1">
                    <p>Preço: R$ {{ Number(product.price).toFixed(2) }}</p>
                    <p>Estoque: {{ product.stock }}</p>
                </div>

                <div class="flex flex-wrap gap-2 mt-auto pt-2">
                    <Link :href="`/products/${product.id}`"
                        class="border px-3 py-1 rounded text-sm w-100 flex items-center justify-center">
                        Detalhes
                    </Link>

                    <Link :href="`/products/${product.id}/edit`"
                        class="border px-3 py-1 rounded text-sm w-100 flex items-center justify-center">
                        Editar
                    </Link>
                </div>
            </div>           
        </div>
    </div>
</template>
