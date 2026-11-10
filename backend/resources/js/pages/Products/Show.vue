<script setup>
    import { ref, onMounted } from 'vue';
    import { Link } from '@inertiajs/vue3';
    import axios from 'axios';

    const props = defineProps({
        id: [Number, String]
    });

    const product = ref(null);
    const loading = ref(true);

    async function getProduct() {
        try {
            const response = await axios.get(`/api/products/${props.id}`);

            product.value = response.data;
        } catch (err) {
            alert('Não foi possível carregar o produto.');
            console.error(err);
        } finally {
            loading.value = false;
        }
    }

    onMounted(getProduct);
</script>

<template>
    <div class="max-w-2xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Detalhes do produto</h1>

        <p v-if="loading">Carregando produto...</p>

        <div v-else-if="product" class="border rounded p-4 space-y-4">

            <div>
                <label>ID: {{ product.id }}</label>
            </div>

            <div class="w-full text-black h-56 bg-gray-200 rounded overflow-hidden flex items-center justify-center">
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

            <div>
                <strong>Nome:</strong>
                {{ product.name }}
            </div>

            <div>
                <strong>Descrição:</strong>
                {{ product.description || 'Sem descrição' }}
            </div>

            <div>
                <strong>Preço:</strong>
                R$ {{ product.price }}
            </div>

            <div>
                <strong>Estoque:</strong>
                {{ product.stock }}
            </div>

            <div>
                <strong>Categoria:</strong>
                {{ product.category?.name || 'Não informada' }}
            </div>

            <div>
                <strong>Status:</strong>

                <span
                    class="ml-2 px-3 py-1 rounded text-sm"
                    :class="product.is_active
                        ? 'bg-green-100 text-green-700'
                        : 'bg-gray-200 text-gray-700'"
                >
                    {{ product.is_active ? 'Ativo' : 'Desativado' }}
                </span>
            </div>

            <div class="flex flex-col gap-2 pt-2">
                <Link
                    :href="`/products/${product.id}/edit`"
                    class="bg-black text-white px-4 py-2 rounded w-80 flex items-center justify-center"
                >
                    Editar
                </Link>

                <Link href="/products" class="border px-4 py-2 rounded w-80 flex items-center justify-center">
                    Voltar
                </Link>
            </div>
        </div>
    </div>
</template>