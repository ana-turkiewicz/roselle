<script setup lang="ts">

    defineProps<{
        addresses: {
            data: {
                id: number;
                recipient_name: string;
                zip_code: string;
                street: string;
                number: string;
                complement: string;
                neighborhood: string;
                city: string;
                state: string;
                user?: {
                    name: string;
                };
                }[];
            };
        }>();

</script>

<template>
    <div>
        <h1>Endereços</h1>
        <p>Página de endereços</p>
        <br>
    </div>

    <p v-if="!addresses.data.length">Nenhum endereço encontrado.</p>

    <div class="grid grid-cols-2 gap-4">
        <div v-for="address in addresses.data" :key="address.id" class="border p-4 rounded">
            <h2>{{ address.recipient_name }}</h2>
            <p>{{ address.zip_code }}</p>
            <p>{{ address.street }}</p>
            <p>{{ address.number }}</p>
            <p>{{ address.complement }}</p>
            <p>{{ address.neighborhood }}</p>
            <p>{{ address.city }}</p>
            <p>{{ address.state }}</p>
            <p v-if="address.user">Usuário: {{ address.user.name }}</p>
        </div>
    </div>
</template>