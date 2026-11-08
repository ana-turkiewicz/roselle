<script setup lang="ts">

    type Category = {
        id: number;
        name: string;
        description: string;
        slug: string;
    }

    defineProps<{
        categories: {
            data: Category[]
        };
    }>();

</script>

<template>
    <div>
        <h1>Categorias</h1>
        <p>Página de categorias</p>
        <br>
    </div>

    <p v-if="!categories.data.length">Nenhuma categoria encontrada.</p>

    <div class="grid grid-cols-2 gap-4">
        <div v-for="category in categories.data" :key="category.id" class="border p-4 rounded">
            <h2>{{ category.name }}</h2>
            <p>{{ category.description }}</p>
            <p>{{ category.slug }}</p>
        </div>
    </div>
</template>