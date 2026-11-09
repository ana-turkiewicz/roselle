<script setup>
    import { ref, onMounted } from 'vue';
    import { Link } from '@inertiajs/vue3';
    import axios from 'axios';

    const props = defineProps({
        id: [Number, String]
    });

    const order = ref(null);
    const loading = ref(true);

    const statuses = {
        pending: 'Pendente',
        confirmed: 'Confirmado',
        processing: 'Em preparação',
        shipped: 'Enviado',
        delivered: 'Entregue',
        cancelled: 'Cancelado',
        refunded: 'Reembolsado'
    };

    function formatPrice(price) {
        return Number(price).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
    }

    function formatDate(date) {
        if (!date) return 'Não informada';

        return new Date(date).toLocaleString('pt-BR');
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

    async function getOrder() {
        try {
            const response = await axios.get(`/api/orders/${props.id}`);

            order.value = response.data.data ?? response.data;
        } catch (err) {
            alert('Não foi possível carregar o pedido.');
            console.error(err);
        } finally {
            loading.value = false;
        }
    }

    onMounted(getOrder);
</script>

<template>
    <div class="max-w-2xl mx-auto p-6">

        <h1 class="text-2xl font-bold mb-6">
            Detalhes do pedido
        </h1>

        <p v-if="loading">
            Carregando pedido...
        </p>

        <div v-else-if="order" class="border rounded p-4 space-y-5">

            <div>
                <label>ID: {{ order.id }}</label>
            </div>

            <div>
                <strong>Cliente:</strong>

                <p>{{ order.user_id?.name ?? 'Não informado' }}</p>
                <p class="text-sm text-gray-500">
                    {{ order.user_id?.email ?? '' }}
                </p>
            </div>

            <div>
                <strong>Endereço de entrega:</strong>

                <div v-if="order.address_id" class="mt-1 text-sm space-y-1">
                    <p>{{ order.address_id.recipient_name }}</p>
                    <p>
                        {{ order.address_id.street }},
                        {{ order.address_id.number }}
                    </p>
                    <p v-if="order.address_id.complement">
                        {{ order.address_id.complement }}
                    </p>
                    <p>
                        {{ order.address_id.neighborhood }} -
                        {{ order.address_id.city }}/{{ order.address_id.state }}
                    </p>
                    <p>CEP: {{ order.address_id.zip_code }}</p>
                </div>

                <p v-else class="text-gray-500">
                    Endereço não informado.
                </p>
            </div>

            <div>
                <strong>Itens do pedido:</strong>

                <div
                    v-if="order.items?.length"
                    class="mt-3 space-y-3"
                >
                    <div
                        v-for="item in order.items"
                        :key="item.id"
                        class="border rounded p-3 flex justify-between items-center gap-3"
                    >
                        <div>
                            <p class="font-medium">
                                {{ item.product?.name ?? 'Produto indisponível' }}
                            </p>

                            <p class="text-sm text-gray-500">
                                Quantidade: {{ item.quantity }}
                            </p>

                            <p class="text-sm text-gray-500">
                                Unitário: {{ formatPrice(item.unit_price) }}
                            </p>
                        </div>

                        <strong class="whitespace-nowrap">
                            {{ formatPrice(item.quantity * item.unit_price) }}
                        </strong>
                    </div>
                </div>

                <p v-else class="text-gray-500 mt-1">
                    Nenhum item disponível para exibição.
                </p>
            </div>

            <div>
                <strong>Total:</strong>

                <p class="text-lg font-semibold mt-1">
                    {{ formatPrice(order.total) }}
                </p>
            </div>

            <div>
                <strong>Observações:</strong>

                <p class="mt-1">
                    {{ order.notes || 'Nenhuma observação.' }}
                </p>
            </div>

            <div>
                <strong>Status:</strong>

                <span
                    class="ml-2 inline-block px-3 py-1 rounded text-sm"
                    :class="getStatusClass(order.status)"
                >
                    {{ statuses[order.status] ?? order.status }}
                </span>
            </div>

            <div>
                <strong>Data do pedido:</strong>

                <p class="mt-1">
                    {{ formatDate(order.created_at) }}
                </p>
            </div>

            <div>
                <strong>Última atualização:</strong>

                <p class="mt-1">
                    {{ formatDate(order.updated_at) }}
                </p>
            </div>

            <div class="flex flex-col gap-2 pt-2">

                <Link
                    :href="`/orders/${order.id}/edit`"
                    class="bg-black text-white px-4 py-2 rounded w-80 flex items-center justify-center hover:bg-gray-800"
                >
                    Editar
                </Link>

                <Link
                    href="/orders"
                    class="border px-4 py-2 rounded w-80 flex items-center justify-center hover:bg-gray-100"
                >
                    Voltar
                </Link>

            </div>
        </div>
    </div>
</template>