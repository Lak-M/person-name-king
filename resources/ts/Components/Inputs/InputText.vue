<script setup lang="ts">
import InputText from 'primevue/inputtext';

defineProps({
    name: {type: String, required: false, default: null},
    errors: {type: Object, default: null},
})

</script>

<template>
    <InputText
        size="large"
        :class="{
        '!border !border-[var(--p-sky-500)]': !errors?.[name],
        '!border !border-[var(--p-red-500)]': errors?.[name]
        }"
        type="search"
    />
</template>
