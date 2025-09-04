<script setup type="ts">
import Dialog from 'primevue/dialog';


import {capitalizeFirstLetter} from "@/helpers.ts";
import CopyToClipboard from "@/Components/CopyToClipboard.vue";
import DataTable from "@/Components/DataTable.vue";

import {jsPDF} from 'jspdf'
import {autoTable} from 'jspdf-autotable'
import {ref} from "vue";

defineProps({
    parts: {type: Object, required: true},
})

const showTable = ref(false)

function download() {
    const doc = new jsPDF()

    autoTable(doc, {
        html: '#parts-table',
    })
    doc.save('Formatted Name.pdf')
}

function print() {
        const doc = new jsPDF()

        autoTable(doc, {
            html: '#parts-table',
        });

    const pdfBlob = doc.output("blob");
    const url = URL.createObjectURL(pdfBlob);

    const iframe = document.createElement("iframe");
    iframe.style.display = "none";
    iframe.src = url;

    iframe.onload = function () {
        iframe.contentWindow?.focus();
        iframe.contentWindow?.print();
    };

    document.body.appendChild(iframe);
}

</script>

<template>
    <div class="space-y-4">
        <Dialog v-model:visible="showTable" modal :dismissableMask="true" header="Formatted Name"
                class="w-[90%] lg:w-2/3">
            <DataTable :parts="parts"/>
        </Dialog>
        <DataTable :parts="parts" class="hidden"/>
        <div class="flex justify-start gap-x-2 mb-6">
            <i @click="showTable = !showTable" class="pi pi-search-plus cursor-pointer text-[var(--p-sky-500)]"></i>
            <i @click="print" class="pi pi-print cursor-pointer text-[var(--p-sky-500)]"/>
            <i @click="download" class="pi pi-download cursor-pointer text-[var(--p-sky-500)]"/>
        </div>
        <ul class="space-y-6 divide-gray-300 divide-y-1 divide-solid">
            <li v-for="(val, part) in parts" class="flex flex-col md:flex-row justify-between">
                <div class="flex flex-col md:flex-row">
                    <div class="font-semibold w-42">{{
                            capitalizeFirstLetter(part)
                        }} {{ !Array.isArray(val) ? 'Name' : '' }}
                    </div>

                    <span v-if="Array.isArray(val)" class="flex flex-col">
                    <span v-for="val1 in val">- &nbsp; {{ val1 }}</span>
                </span>

                    <span v-else>{{ val }}</span>
                </div>
                <CopyToClipboard :text="val" class="self-end md:self-start"/>
            </li>
        </ul>
    </div>
</template>
