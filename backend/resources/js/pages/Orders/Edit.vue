<script setup>
    import { reactive, ref, onMounted } from 'vue';
    import { Link, router } from '@inertiajs/vue3';
    import axios from 'axios';

    const props = defineProps({
        id: [Number, String]
    });

    const form = reactive({
        status: 'pending',
        notes: ''
    });

    const order = ref(null);
    const errors = ref({});
    const loading = ref(true);
    const processing = ref(false);

    const statuses = [
        { value: 'pending', label: 'Pendente' },
        { value: 'confirmed', label: 'Confirmado' },
        { value: 'processing', label: 'Em preparação' },
        { value: 'shipped', label: 'Enviado' },
        { value: 'delivered', label: 'Entregue' },
        { value: 'cancelled', label: 'Cancelado' },
        { value: 'refunded', label: 'Reembolsado' }
    ];

    function formatPrice(price) {
        return Number(price).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
    }

    async function getOrder() {
        try {
            const response = await axios.get(`/api/orders/${props.id}`);

            order.value = response.data.data ?? response.data;

            form.status = order.value.status;
            form.notes = order.value.notes ?? '';
        } catch (err) {
            alert('Não foi possível carregar o pedido.');
            console.error(err);
        } finally {
            loading.value = false;
        }
    }

    async function submit() {
        processing.value = true;
        errors.value = {};

        try {
            await axios.patch(`/api/orders/${props.id}`, {
                status: form.status,
                notes: form.notes || null
            });

            router.visit('/orders');
        } catch (err) {
            if (err.response?.status === 422) {
                errors.value = err.response.data.errors;
            } else {
                alert('Erro ao atualizar pedido.');
                console.error(err);
            }
        } finally {
            processing.value = false;
        }
    }

    onMounted(getOrder);
</script>

<template>
    <div class="max-w-2xl mx-auto p-6">

        <h1 class="text-2xl font-bold mb-6">
            Editar pedido
        </h1>

        <p v-if="loading">
            Carregando pedido...
        </p>

        <form
            v-else-if="order"
            @submit.prevent="submit"
            class="space-y-4 border rounded p-4 flex flex-col gap-3"
        >

            <div>
                <label>ID: {{ order.id }}</label>
            </div>

            <div>
                <label>Cliente: {{ order.user_id?.name ?? 'Não informado' }}</label>
            </div>

            <div>
                <label>Total: {{ formatPrice(order.total) }}</label>
            </div>

            <div>
                <label class="block mb-2">Status:</label>

                <select
                    v-model="form.status"
                    class="border rounded w-full p-2 bg-white text-black"
                >
                    <option
                        v-for="status in statuses"
                        :key="status.value"
                        :value="status.value"
                    >
                        {{ status.label }}
                    </option>
                </select>

                <p class="text-red-600">
                    {{ errors.status?.[0] }}
                </p>
            </div>

            <div>
                <label>Observações:</label>

                <textarea
                    v-model="form.notes"
                    class="border rounded w-full p-2"
                ></textarea>

                <p class="text-red-600">
                    {{ errors.notes?.[0] }}
                </p>
            </div>

            <div class="flex gap-2">

                <button
                    type="submit"
                    :disabled="processing"
                    class="bg-black text-white px-4 py-2 rounded w-60 flex items-center justify-center hover:bg-gray-800"
                >
                    {{ processing ? 'Salvando...' : 'Salvar alterações' }}
                </button>

                <Link
                    href="/orders"
                    class="border px-4 py-2 rounded w-60 flex items-center justify-center hover:bg-gray-100"
                >
                    Voltar
                </Link>

            </div>
        </form>
    </div>
</template>