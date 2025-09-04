<script setup lang="ts">
import {ref} from "vue";

defineProps({
    text: {type: [String, Array<string>], required: true,},
})

const isCopied = ref(false);

function copy(text: string|string[]) {
    copyToClipboard(text);
    isCopied.value = true;
    setTimeout(() => {
        isCopied.value = false;
    }, 2000);

}

async function copyToClipboard(text: string|string[]) {
    if (Array.isArray(text)) {
        text = text.join(', ');
    }
    try {
        await navigator.clipboard.writeText(text);
    } catch (err) {
        console.error("Failed to copy:", err);
    }
}
</script>

<template>
    <div class="relative">
        <i class="pi pi-clipboard cursor-pointer !text-xs text-[var(--p-sky-500)]" @click="copy(text)"></i>
        <span v-if="isCopied" class="absolute min-w-24 text-xs -top-2 lg:right-0 lg:left-6 -right-4 italic">copied ✔️</span>
    </div>
</template>

