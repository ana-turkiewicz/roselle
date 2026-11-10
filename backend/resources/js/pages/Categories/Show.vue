```vue
<script setup>
    import { ref, onMounted } from 'vue';
    import { Link } from '@inertiajs/vue3';
    import axios from 'axios';

    const props = defineProps({
        id: [Number, String]
    });

    const category = ref(null);
    const loading = ref(true);

    async function getCategory() {
        try {
            const response = await axios.get(`/api/categories/${props.id}`);

            category.value = response.data;
        } catch (err) {
            alert('Não foi possível carregar a categoria.');
            console.error(err);
        } finally {
            loading.value = false;
        }
    }

    onMounted(getCategory);
</script>

<template>
    <div class="max-w-2xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Detalhes da categoria</h1>

        <p v-if="loading">Carregando categoria...</p>

        <div v-else-if="category" class="border rounded p-4 space-y-4">

            <div>
                <label>ID: {{ category.id }}</label>
            </div>

            <div>
                <strong>Nome:</strong>
                {{ category.name }}
            </div>

            <div>
                <strong>Slug:</strong>
                {{ category.slug }}
            </div>

            <div>
                <strong>Descrição:</strong>
                {{ category.description || 'Sem descrição' }}
            </div>

            <div class="flex flex-col gap-2 pt-2">
                <Link
                    :href="`/categories/${category.id}/edit`"
                    class="bg-black text-white px-4 py-2 rounded w-80 flex items-center justify-center"
                >
                    Editar
                </Link>

                <Link
                    href="/categories"
                    class="border px-4 py-2 rounded w-80 flex items-center justify-center"
                >
                    Voltar
                </Link>
            </div>
        </div>
    </div>
</template>
```