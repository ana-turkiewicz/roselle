<script setup lang="ts">
import { Users2 } from '@lucide/vue';


    defineProps<{
        users: {
            data: {
                id: number;
                name: string;
                email: string;
                email_verified_at: string;
                password: string;
                remember_token: string;
                }[];
            };
        }>();

</script>

<template>
    <div>
        <h1>Usuários</h1>
        <p>Página de usuários</p>
        <br>
    </div>

    <p v-if="!users.data.length">Nenhum usuário encontrado.</p>

    <div class="grid grid-cols-2 gap-4">
        <div v-for="user in users.data" :key="user.id" class="border p-4 rounded">
            <h2>{{ user.name }}</h2>
            <p>{{ user.email }}</p>
            <p>{{ user.email_verified_at }}</p>
            <p>{{ user.password }}</p>
            <p>{{ user.remember_token }}</p>
        </div>
    </div>
</template>