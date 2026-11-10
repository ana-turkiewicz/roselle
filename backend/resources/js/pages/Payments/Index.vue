<script setup lang="ts">

    defineProps<{
        payments: {
            data: {
                id: number;
                payment_method: string;
                status: string;
                amount: number;
                transaction_id: string;
                paid_at: string | null;
                order_id: number;
                }[];
            };
        }>();

</script>

<template>
    <div>
        <h1>Pagamentos</h1>
        <p>Página de pagamentos</p>
        <br>
    </div>

    <p v-if="!payments.data.length">Nenhum pagamento encontrado.</p>

    <div class="grid grid-cols-2 gap-4">
        <div v-for="payment in payments.data" :key="payment.id" class="border p-4 rounded">
            <p>{{ payment.payment_method }}</p>
            <h2>{{ payment.status }}</h2>
            <p>{{ payment.amount }}</p>
            <p>{{ payment.transaction_id }}</p>
            <p>{{ payment.paid_at }}</p>
            <p>{{ payment.order_id }}</p>

        </div>
    </div>
</template>