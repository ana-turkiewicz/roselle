
<script setup>
    import { reactive, ref, onMounted } from 'vue';
    import { Link, router } from '@inertiajs/vue3';
    import axios from 'axios';

    const props = defineProps({
        id: [Number, String]
    });

    const form = reactive({
        name: '',
        description: '',
        price: '',
        stock: 0,
        category_id: '',
        is_active: 1
    });

    const categories = ref([]);
    const currentImage = ref(null);
    const newImage = ref(null);
    const preview = ref(null);

    const errors = ref({});
    const loading = ref(true);
    const processing = ref(false);

    async function getProduct() {
        try {
            const response = await axios.get(`/api/products/${props.id}`);
            const product = response.data;

            form.name = product.name;
            form.description = product.description ?? '';
            form.price = product.price;
            form.stock = product.stock;
            form.category_id = product.category_id ?? product.category?.id ?? '';
            form.is_active = Number(product.is_active);

            currentImage.value = product.image ? `${product.image}` : null;
        } catch (err) {
            alert('Não foi possível carregar o produto.');
            console.error(err);
        } finally {
            loading.value = false;
        }
    }

    async function getCategories() {
        try {
            const response = await axios.get('/api/categories');
            categories.value = response.data;
        } catch (err) {
            console.error('Erro ao carregar categorias:', err);
        }
    }

    function selectImage(event) {
        newImage.value = event.target.files[0] ?? null;

        preview.value = newImage.value
            ? URL.createObjectURL(newImage.value)
            : null;
    }

    async function submit() {
        processing.value = true;
        errors.value = {};

        const data = new FormData();

        Object.entries(form).forEach(([key, value]) => {
            data.append(key, value);
        });

        data.append('_method', 'PUT');

        if (newImage.value) {
            data.append('image', newImage.value);
        }

        try {
            await axios.post(`/api/products/${props.id}`, data);

            router.visit('/products');
        } catch (err) {
            if (err.response?.status === 422) {
                errors.value = err.response.data.errors;
            } else {
                alert('Erro ao atualizar produto.');
                console.error(err);
            }
        } finally {
            processing.value = false;
        }
    }

    onMounted(() => {
        getProduct();
        getCategories();
    });
</script>

<template>
    <div class="max-w-2xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Editar produto</h1>

        <p v-if="loading">Carregando produto...</p>

        <form
            v-else
            @submit.prevent="submit"
            class="space-y-4 border rounded p-4 flex flex-col gap-3"
        >
            <div>
                <label>ID: {{props.id}}</label>
            </div>

            <div>
                <label>Nome:</label>
                <input
                    v-model="form.name"
                    type="text"
                    class="border rounded w-full p-2"
                >
                <p class="text-red-600">{{ errors.name?.[0] }}</p>
            </div>

            <div>
                <label>Descrição:</label>
                <textarea
                    v-model="form.description"
                    class="border rounded w-full p-2"
                ></textarea>
                <p class="text-red-600">{{ errors.description?.[0] }}</p>
            </div>

            <div>
                <label>Preço:</label>
                <input
                    v-model="form.price"
                    type="number"
                    step="0.01"
                    class="border rounded w-full p-2"
                >
                <p class="text-red-600">{{ errors.price?.[0] }}</p>
            </div>

            <div>
                <label>Estoque:</label>
                <input
                    v-model.number="form.stock"
                    type="number"
                    class="border rounded w-full p-2"
                >
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
                <label class="block mb-2">Status:</label>

                <div class="flex gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            v-model="form.is_active"
                            type="radio"
                            :value="1"
                            name="is_active"
                        >
                        Ativo
                    </label>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input
                            v-model="form.is_active"
                            type="radio"
                            :value="0"
                            name="is_active"
                        >
                        Inativo
                    </label>
                </div>

                <p class="text-red-600">{{ errors.is_active?.[0] }}</p>
            </div>

            <div>
                <label class="block mb-2">Imagem do produto:</label>

                <div class="w-40 h-40 bg-gray-200 text-black rounded overflow-hidden flex items-center justify-center mb-3">
                    <img
                        v-if="preview || currentImage"
                        :src="preview || currentImage"
                        alt="Imagem"
                        class="w-full h-full object-cover"
                    >

                    <span v-else class="font-semibold text-sm">
                        SEM IMAGEM
                    </span>
                </div>

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

                <p class="text-red-600">{{ errors.image?.[0] }}</p>
            </div>

            <div class="flex gap-2">
                <button
                    type="submit"
                    :disabled="processing"
                    class="bg-black text-white px-4 py-2 rounded w-60 flex items-center justify-center"
                >
                    {{ processing ? 'Salvando...' : 'Salvar alterações' }}
                </button>

                <Link
                    href="/products"
                    class="border px-4 py-2 rounded w-60 flex items-center justify-center"
                >
                    Voltar
                </Link>
            </div>
        </form>
    </div>
</template>
