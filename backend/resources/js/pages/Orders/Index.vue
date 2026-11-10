<script setup>
    import { ref, onMounted } from 'vue';
    import { Link } from '@inertiajs/vue3';
    import axios from 'axios';

    const orders = ref([]);
    const loading = ref(true);
    const error = ref('');
    const processing = ref(null);

    const statuses = [
        { value: 'pending', label: 'Pendente' },
        { value: 'confirmed', label: 'Confirmado' },
        { value: 'processing', label: 'Em preparação' },
        { value: 'shipped', label: 'Enviado' },
        { value: 'delivered', label: 'Entregue' },
        { value: 'cancelled', label: 'Cancelado' },
        { value: 'refunded', label: 'Reembolsado' }
    ];

    function getStatusLabel(status) {
        return statuses.find(item => item.value === status)?.label ?? status;
    }

    function getStatusClass(status) {
        const classes = {
            pending: 'bg-yellow-100 text-yellow-800',
            confirmed: 'bg-blue-100 text-blue-800',
            processing: 'bg-purple-100 text-purple-800',
            shipped: 'bg-indigo-100 text-indigo-800',
            delivered: 'bg-green-100 text-green-800',
            cancelled: 'bg-red-100 text-red-800',
            refunded: 'bg-gray-200 text-gray-700'
        };

        return classes[status] ?? 'bg-gray-100 text-gray-700';
    }

    function formatPrice(price) {
        return Number(price).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
    }

    function formatDate(date) {
        if (!date) return 'Não informada';

        return new Date(date).toLocaleDateString('pt-BR');
    }

    async function getOrders() {
        try {
            const response = await axios.get('/api/orders');

            orders.value = response.data.data ?? response.data;
        } catch (err) {
            error.value = 'Não foi possível carregar os pedidos.';
            console.error(err);
        } finally {
            loading.value = false;
        }
    }

    async function updateStatus(order, status) {
        processing.value = order.id;

        try {
            await axios.patch(`/api/orders/${order.id}`, {
                status: status
            });

            order.status = status;
        } catch (err) {
            alert('Não foi possível atualizar o status do pedido.');
            console.error(err);
        } finally {
            processing.value = null;
        }
    }

    onMounted(getOrders);
</script>

<template>
    <div class="max-w-6xl mx-auto p-6">

        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Pedidos</h1>
        </div>

        <p v-if="loading">
            Carregando pedidos...
        </p>

        <p v-else-if="error" class="text-red-600">
            {{ error }}
        </p>

        <div
            v-else-if="orders.length === 0"
            class="border rounded p-6 text-center"
        >
            Nenhum pedido cadastrado.
        </div>

        <div v-else class="border rounded overflow-x-auto">

            <table class="w-full text-sm text-left">
                <thead class="bg-gray-100 text-black">
                    <tr>
                        <th class="p-4">ID</th>
                        <th class="p-4">Cliente</th>
                        <th class="p-4">Data</th>
                        <th class="p-4">Total</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="order in orders"
                        :key="order.id"
                        class="border-t"
                    >
                        <td class="p-4">
                            #{{ order.id }}
                        </td>

                        <td class="p-4">
                            <div class="font-medium">
                                {{ order.user_id?.name ?? 'Cliente não informado' }}
                            </div>

                            <div class="text-gray-500 text-xs">
                                {{ order.user_id?.email ?? '' }}
                            </div>
                        </td>

                        <td class="p-4 whitespace-nowrap">
                            {{ formatDate(order.created_at) }}
                        </td>

                        <td class="p-4 whitespace-nowrap">
                            {{ formatPrice(order.total) }}
                        </td>

                        <td class="p-4">
                            <div class="flex flex-col gap-2">

                                <span
                                    class="inline-block w-fit px-2 py-1 rounded text-xs"
                                    :class="getStatusClass(order.status)"
                                >
                                    {{ getStatusLabel(order.status) }}
                                </span>

                                <select
                                    :value="order.status"
                                    :disabled="processing === order.id"
                                    @change="updateStatus(order, $event.target.value)"
                                    class="border rounded p-2 bg-white text-black text-xs"
                                >
                                    <option
                                        v-for="status in statuses"
                                        :key="status.value"
                                        :value="status.value"
                                    >
                                        {{ status.label }}
                                    </option>
                                </select>

                                <span
                                    v-if="processing === order.id"
                                    class="text-gray-500 text-xs"
                                >
                                    Atualizando...
                                </span>
                            </div>
                        </td>

                        <td class="p-4">
                            <Link
                                :href="`/orders/${order.id}`"
                                class="border px-3 py-2 rounded inline-flex justify-center hover:bg-gray-100"
                            >
                                Detalhes
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>
</template>