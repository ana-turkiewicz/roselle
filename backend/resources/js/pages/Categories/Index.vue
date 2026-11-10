```vue
<script setup>
    import { ref, onMounted } from 'vue';
    import { Link } from '@inertiajs/vue3';
    import axios from 'axios';

    const categories = ref([]);
    const loading = ref(true);
    const error = ref('');

    async function getCategories() {
        try {
            const response = await axios.get('/api/categories');

            categories.value = response.data;
        } catch (err) {
            error.value = 'Não foi possível carregar as categorias.';
            console.error(err);
        } finally {
            loading.value = false;
        }
    }

    onMounted(getCategories);
</script>

<template>
    <div class="max-w-6xl mx-auto p-6">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Categorias</h1>

            <Link
                href="/categories/create"
                class="bg-black text-white px-4 py-2 rounded"
            >
                Nova categoria
            </Link>
        </div>

        <p v-if="loading">
            Carregando categorias...
        </p>

        <p v-else-if="error">
            {{ error }}
        </p>

        <div
            v-else-if="categories.length === 0"
            class="border rounded p-6 text-center"
        >
            Nenhuma categoria cadastrada.
        </div>

        <div
            v-else
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"
        >
            <div
                v-for="category in categories"
                :key="category.id"
                class="border rounded p-4 flex flex-col gap-3"
            >
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">
                        ID: {{ category.id }}
                    </span>
                </div>

                <div>
                    <h2 class="text-lg font-semibold">
                        {{ category.name }}
                    </h2>

                    <p class="text-sm text-gray-600">
                        {{ category.description || 'Sem descrição' }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-2 mt-auto pt-2">
                    <Link
                        :href="`/categories/${category.id}`"
                        class="border px-3 py-1 rounded text-sm w-full flex items-center justify-center"
                    >
                        Detalhes
                    </Link>

                    <Link
                        :href="`/categories/${category.id}/edit`"
                        class="border px-3 py-1 rounded text-sm w-full flex items-center justify-center"
                    >
                        Editar
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
```