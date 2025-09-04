<script setup lang="ts">
import {onMounted} from "vue";
import {capitalizeFirstLetter} from "@/helpers.ts";

defineProps({
    parts: {type: Object, required: true},
})

onMounted(() => {
    console.log(document.querySelector('#parts-table'));
})
</script>

<template>
    <table id="parts-table" class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
        <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
        <tr
            v-for="title in Object.keys(parts)"
            :key="title"
            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200"
        >
            <td class="align-top px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                {{
                    capitalizeFirstLetter(title)
                }} {{ !Array.isArray(parts[title]) ? 'Name' : '' }}
            </td>
            <td
                v-if="!Array.isArray(parts[title])"
                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200"
            >
                {{ parts[title] }}
            </td>
            <td
                v-else
                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200"
            >
                <span v-for="val in parts[title]">
                    - &nbsp; {{ val }} <br>
                </span>
            </td>
        </tr>
        </tbody>
    </table>
</template>

