<script setup>
    import { reactive, ref } from 'vue';
    import { Link, router } from '@inertiajs/vue3';
    import axios from 'axios';

    const form = reactive({
        name: '',
        description: '',
        price: '',
        stock: 0,
        category_id: ''
    });

    const categories = ref([]);
    const image = ref(null);
    const preview = ref(null);
    const errors = ref({});
    const processing = ref(false);

    async function getCategories() {
        try {
            const response = await axios.get('/api/categories');
            categories.value = response.data;
        } catch (err) {
            console.error('Erro ao carregar categorias:', err);
        }
    }

    function selectImage(event) {
        image.value = event.target.files[0] ?? null;
        preview.value = image.value ? URL.createObjectURL(image.value) : null;
    }

    async function submit() {
        processing.value = true;
        errors.value = {};

        const data = new FormData();

        Object.entries(form).forEach(([key, value]) => {
            data.append(key, value);
        });

        if (image.value) {
            data.append('image', image.value);
        }

        try {
            await axios.post('/api/products', data);
            router.visit('/products');
        } catch (err) {
            if (err.response?.status === 422) {
                errors.value = err.response.data.errors;
            } else {
                alert('Erro ao cadastrar produto.');
                console.error(err);
            }
        } finally {
            processing.value = false;
        }
    }

    getCategories();
</script>

<template>
    <div class="max-w-2xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Cadastrar produto</h1>

        <form @submit.prevent="submit" class="space-y-4 border rounded p-4 flex flex-col gap-3">
            <div>
                <label>Nome:</label>
                <input v-model="form.name" type="text" class="border rounded w-full p-2">
                <p class="text-red-600">{{ errors.name?.[0] }}</p>
            </div>

            <div>
                <label>Descrição:</label>
                <textarea v-model="form.description" class="border rounded w-full p-2"></textarea>
                <p class="text-red-600">{{ errors.description?.[0] }}</p>
            </div>

            <div>
                <label>Preço:</label>
                <input v-model="form.price" type="number" step="0.01" class="border rounded w-full p-2">
                <p class="text-red-600">{{ errors.price?.[0] }}</p>
            </div>

            <div>
                <label>Estoque:</label>
                <input v-model.number="form.stock" type="number" class="border rounded w-full p-2">
                <p class="text-red-600">{{ errors.stock?.[0] }}</p>
            </div>

            <div>
                <label>Categoria:</label>

                <select
                    v-model="form.category_id"
                    class="border rounded w-full p-2 bg-white text-black"
                >
                    <option value="" disabled class="bg-white text-black">
                        Selecione uma categoria
                    </option>

                    <option
                        v-for="category in categories"
                        :key="category.id"
                        :value="category.id"
                        class="bg-white text-black"
                    >
                        {{ category.name }}
                    </option>
                </select>

                <p class="text-red-600">{{ errors.category_id?.[0] }}</p>
            </div>

            <div>
                <label class="block mb-2">Imagem do produto:</label>

                <label
                    class="inline-block bg-black text-white px-4 py-2 rounded cursor-pointer hover:bg-gray-800"
                >
                    Escolher imagem

                    <input
                        type="file"
                        accept="image/*"
                        @change="selectImage"
                        class="hidden"
                    >
                </label>

                <img
                    v-if="preview"
                    :src="preview"
                    alt="Prévia da imagem"
                    class="mt-3 w-40 h-40 object-cover rounded"
                >

                <p v-else class="text-gray-500 text-sm mt-2">
                    Nenhuma imagem selecionada.
                </p>

                <p class="text-red-600">{{ errors.image?.[0] }}</p>
            </div>

            <div class="flex gap-2">
                <button type="submit" :disabled="processing" class="bg-black text-white px-4 py-2 rounded w-60 flex items-center justify-center">
                    {{ processing ? 'Salvando...' : 'Cadastrar' }}
                </button>

                <Link href="/products" class="border px-4 py-2 rounded w-60 flex items-center justify-center">
                    Voltar
                </Link>
            </div>
        </form>
    </div>
</template>