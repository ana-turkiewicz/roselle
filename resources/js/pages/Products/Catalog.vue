
<script setup>
    import { ref, computed, onMounted, watch } from 'vue';
    import axios from 'axios';

    const products = ref([]);
    const loading = ref(true);
    const error = ref('');
    const search = ref('');
    const selectedCategory = ref('');
    const sortBy = ref('name_asc');
    const currentPage = ref(1);
    const perPage = 8;

    const addingProductId = ref(null);
    const cartMessage = ref('');
    const cartError = ref('');

    async function getProducts() {
        try {
            const response = await axios.get('/api/products');
            const data = response.data.data ?? response.data;

            products.value = Array.isArray(data) ? data : [];
        } catch (err) {
            error.value = 'Não foi possível carregar os produtos.';
            console.error(err);
        } finally {
            loading.value = false;
        }
    }

    const activeProducts = computed(() => {
        return products.value.filter(product => product.is_active);
    });

    const categories = computed(() => {
        const categoryMap = new Map();

        activeProducts.value.forEach(product => {
            if (product.category) {
                categoryMap.set(
                    product.category.id,
                    product.category.name
                );
            }
        });

        return Array.from(categoryMap, ([id, name]) => ({
            id,
            name
        })).sort((a, b) => a.name.localeCompare(b.name));
    });

    const filteredProducts = computed(() => {
        let result = [...activeProducts.value];

        if (search.value.trim()) {
            const term = search.value.toLowerCase().trim();

            result = result.filter(product =>
                product.name.toLowerCase().includes(term)
            );
        }

        if (selectedCategory.value) {
            result = result.filter(product =>
                String(product.category?.id) === selectedCategory.value
            );
        }

        switch (sortBy.value) {
            case 'name_asc':
                result.sort((a, b) =>
                    a.name.localeCompare(b.name)
                );
                break;

            case 'price_asc':
                result.sort((a, b) =>
                    Number(a.price) - Number(b.price)
                );
                break;

            case 'price_desc':
                result.sort((a, b) =>
                    Number(b.price) - Number(a.price)
                );
                break;

            case 'newest':
                result.sort((a, b) =>
                    new Date(b.created_at) - new Date(a.created_at)
                );
                break;
        }

        return result;
    });

    const totalPages = computed(() => {
        return Math.ceil(filteredProducts.value.length / perPage);
    });

    const paginatedProducts = computed(() => {
        const start = (currentPage.value - 1) * perPage;

        return filteredProducts.value.slice(
            start,
            start + perPage
        );
    });

    watch([search, selectedCategory, sortBy], () => {
        currentPage.value = 1;
    });

    function formatPrice(price) {
        return Number(price).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
    }

    async function addToCart(product) {
        cartMessage.value = '';
        cartError.value = '';
        addingProductId.value = product.id;

        try {
            // Endpoint esperado para adicionar um produto ao carrinho.
            await axios.post('/cart/items', {
                product_id: product.id,
                quantity: 1
            });

            cartMessage.value = `${product.name} adicionado ao carrinho!`;
        } catch (err) {
            console.error(err);

            if (err.response?.status === 401) {
                cartError.value = 'Entre na sua conta para adicionar produtos ao carrinho.';
            } else {
                cartError.value =
                    err.response?.data?.message ??
                    'Não foi possível adicionar o produto ao carrinho.';
            }
        } finally {
            addingProductId.value = null;
        }
    }

    onMounted(getProducts);
</script>

<template>
    <div class="max-w-6xl mx-auto p-6">

        <!-- Cabeçalho -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold">
                Roselle
            </h1>

            <p class="text-gray-500 mt-1">
                Encontre seus produtos favoritos.
            </p>
        </div>

        <!-- Filtros -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

            <input
                v-model="search"
                type="text"
                placeholder="Buscar produto..."
                class="border rounded px-4 py-2 w-full focus:outline-none focus:ring-1 focus:ring-black"
            >

            <select
                v-model="selectedCategory"
                class="border rounded px-4 py-2 w-full bg-white"
            >
                <option value="">
                    Todas as categorias
                </option>

                <option
                    v-for="category in categories"
                    :key="category.id"
                    :value="String(category.id)"
                >
                    {{ category.name }}
                </option>
            </select>

            <select
                v-model="sortBy"
                class="border rounded px-4 py-2 w-full bg-white"
            >
                <option value="name_asc">
                    Nome: A-Z
                </option>

                <option value="price_asc">
                    Menor preço
                </option>

                <option value="price_desc">
                    Maior preço
                </option>

                <option value="newest">
                    Mais recentes
                </option>
            </select>

        </div>

        <!-- Mensagens -->
        <p
            v-if="cartMessage"
            class="bg-green-100 text-green-800 rounded p-3 mb-4"
        >
            {{ cartMessage }}
        </p>

        <p
            v-if="cartError"
            class="bg-red-100 text-red-800 rounded p-3 mb-4"
        >
            {{ cartError }}
        </p>

        <!-- Carregamento -->
        <p v-if="loading" class="text-gray-500">
            Carregando produtos...
        </p>

        <!-- Erro -->
        <p v-else-if="error" class="text-red-600">
            {{ error }}
        </p>

        <!-- Nenhum produto -->
        <div
            v-else-if="filteredProducts.length === 0"
            class="border rounded p-8 text-center"
        >
            <h2 class="font-semibold text-lg">
                Nenhum produto encontrado.
            </h2>

            <p class="text-gray-500 mt-1">
                Tente alterar sua busca ou categoria.
            </p>
        </div>

        <template v-else>

            <p class="text-sm text-gray-500 mb-4">
                {{ filteredProducts.length }}
                {{ filteredProducts.length === 1 ? 'produto encontrado' : 'produtos encontrados' }}
            </p>

            <!-- Produtos -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

                <div
                    v-for="product in paginatedProducts"
                    :key="product.id"
                    class="border rounded-lg p-3 flex flex-col gap-3"
                >

                    <!-- Imagem -->
                    <div class="h-52 bg-gray-100 rounded overflow-hidden flex items-center justify-center">

                        <img
                            v-if="product.image"
                            :src="product.image"
                            :alt="product.name"
                            class="w-full h-full object-cover"
                        >

                        <span
                            v-else
                            class="text-sm text-gray-400"
                        >
                            SEM IMAGEM
                        </span>

                    </div>

                    <!-- Informações -->
                    <div class="flex-1">

                        <p class="text-xs text-gray-500 mb-1">
                            {{ product.category?.name ?? 'Sem categoria' }}
                        </p>

                        <h2 class="font-semibold text-lg">
                            {{ product.name }}
                        </h2>

                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">
                            {{ product.description }}
                        </p>

                    </div>

                    <!-- Preço e estoque -->
                    <div>
                        <p class="text-xl font-bold">
                            {{ formatPrice(product.price) }}
                        </p>

                        <p
                            v-if="product.stock > 0"
                            class="text-xs text-green-700 mt-1"
                        >
                            Disponível
                        </p>

                        <p
                            v-else
                            class="text-xs text-red-600 mt-1"
                        >
                            Esgotado
                        </p>
                    </div>

                    <!-- Carrinho -->
                    <button
                        type="button"
                        @click="addToCart(product)"
                        :disabled="product.stock <= 0 || addingProductId === product.id"
                        class="w-full bg-black text-white rounded px-4 py-2 hover:bg-gray-800 disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed"
                    >
                        <span v-if="addingProductId === product.id">
                            Adicionando...
                        </span>

                        <span v-else-if="product.stock <= 0">
                            Indisponível
                        </span>

                        <span v-else>
                            Adicionar ao carrinho
                        </span>
                    </button>

                </div>

            </div>

            <!-- Paginação -->
            <div
                v-if="totalPages > 1"
                class="flex justify-center items-center gap-4 mt-8"
            >

                <button
                    type="button"
                    @click="currentPage--"
                    :disabled="currentPage === 1"
                    class="border rounded px-4 py-2 disabled:opacity-40"
                >
                    Anterior
                </button>

                <span class="text-sm text-gray-600">
                    Página {{ currentPage }} de {{ totalPages }}
                </span>

                <button
                    type="button"
                    @click="currentPage++"
                    :disabled="currentPage === totalPages"
                    class="border rounded px-4 py-2 disabled:opacity-40"
                >
                    Próxima
                </button>

            </div>

        </template>

    </div>
</template>
