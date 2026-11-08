```vue
<script setup>
    import { reactive, ref } from 'vue';
    import { Link, router } from '@inertiajs/vue3';
    import axios from 'axios';

    const form = reactive({
        name: '',
        description: '',
        slug: ''
    });

    const errors = ref({});
    const processing = ref(false);

    async function submit() {
        processing.value = true;
        errors.value = {};

        try {
            await axios.post('/api/categories', form);
            router.visit('/categories');
        } catch (err) {
            if (err.response?.status === 422) {
                errors.value = err.response.data.errors;
            } else {
                alert('Erro ao cadastrar categoria.');
                console.error(err);
            }
        } finally {
            processing.value = false;
        }
    }
</script>

<template>
    <div class="max-w-2xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Cadastrar categoria</h1>

        <form
            @submit.prevent="submit"
            class="space-y-4 border rounded p-4 flex flex-col gap-3"
        >
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
                <label>Slug:</label>

                <input
                    v-model="form.slug"
                    type="text"
                    class="border rounded w-full p-2"
                >

                <p class="text-red-600">{{ errors.slug?.[0] }}</p>
            </div>

            <div>
                <label>Descrição:</label>

                <textarea
                    v-model="form.description"
                    class="border rounded w-full p-2"
                ></textarea>

                <p class="text-red-600">{{ errors.description?.[0] }}</p>
            </div>

            <div class="flex gap-2">
                <button
                    type="submit"
                    :disabled="processing"
                    class="bg-black text-white px-4 py-2 rounded w-60 flex items-center justify-center"
                >
                    {{ processing ? 'Salvando...' : 'Cadastrar' }}
                </button>

                <Link
                    href="/categories"
                    class="border px-4 py-2 rounded w-60 flex items-center justify-center"
                >
                    Voltar
                </Link>
            </div>
        </form>
    </div>
</template>
```