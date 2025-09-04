<script setup lang="ts">
import {onMounted, ref} from "vue";
import {usePage, useForm} from '@inertiajs/vue3'

import SelectButton from 'primevue/selectbutton';
import PrimeButton from 'primevue/button';
import Select from 'primevue/select';

import Mode from '@/Enums/Mode';
import Country from '@/Enums/Country';
import Ethnicity from '@/Enums/Ethnicity';
import InputText from '@/Components/Inputs/InputText.vue';
import DataList from "@/Components/DataList.vue";
import {capitalizeWords} from "@/helpers.ts";

import BgImg from '@/assets/img/backgrounds/white-charm.jpg';


const props = defineProps({errors: {type: Object, default: {} }});
const page = usePage()

const mode = ref(Mode.Full);
const modeOptions = [Mode.Full, Mode.Parts];

const dataObtainedMode = ref<Mode|null>(null)

let countryOptions: { name: string; value: string|null }[] = [];

const form = useForm({
    full_name: '',
    first_name: '',
    middle_name: '',
    last_name: '',
    prefix_name: '',
    suffix_name: '',
    country: null,
})

onMounted(() => {
    setCountyOptions()
})

function setCountyOptions() {
    for (const val of Object.values(Country)) {
        countryOptions.push({
            name: capitalizeWords(val.toString()),
            value: val
        });
    }

    for (const val of Object.values(Ethnicity)) {
        countryOptions.push({
            name: capitalizeWords(val.toString()),
            value: val
        });
    }

    countryOptions.sort((a, b) =>
        a.name.localeCompare(b.name, undefined, {numeric: true, sensitivity: "base"})
    );

    countryOptions.unshift({'name': 'Default', 'value': null});
}

function submit() {
    let route = '/full-name/format'

    if (mode.value === Mode.Parts) {
        route = '/name-parts/format'
    }

    dataObtainedMode.value = mode.value;

    form.post(route, {preserveUrl: true,})
}

</script>

<template>
    <!-- Header -->
    <main
        :style="`background-image: url(${BgImg})`"
        class="min-h-screen flex flex-col justify-center items-center p-2 overflow-x-hidden pb-12"
    >
        <!-- Mode Switch -->
        <div
            class="w-full flex self-start justify-between gap-x-2 absolute inset-0"
        >
            <div class="flex items-center gap-x-2">
                <SelectButton v-model="mode" :options="modeOptions" class="shadow" :allowEmpty="false"/>
                <span class="font-bold">Mode</span>
            </div>
            <a href="https://github.com/Lak-M/person-name-king">
                <span class="inline-flex items-center gap-2 shadow p-2">
                    <i class="pi pi-github !text-xl"/>
                    <span>GitHub</span>
                </span>
            </a>
        </div>

        <TransitionGroup name="mode">
            <div v-if="page.props.name" class="mt-16"></div>
            <!-- Full Mode -->
            <div
                class="w-full flex flex-col justify-center items-center lg:max-w-2/3 xl:max-w-1/2"
                v-if="mode === Mode.Full"
                key="full-mode"
            >
                <div
                    class="flex flex-col flex-wrap md:flex-row w-full"
                >
                    <InputText @keyup.enter="submit" v-model="form.full_name" :errors="props.errors" class="md:grow-[2]"
                               placeholder="Full Name" name="full_name"/>
                    <Select
                        v-model="form.country"
                        :options="countryOptions"
                        optionValue="value"
                        optionLabel="name"
                        placeholder="Western"
                        class="!border !border-[var(--p-sky-500)]"
                    />
                    <PrimeButton label="Format" @click="submit" :loading="form.processing" class="mt-2 md:mt-0"/>
                </div>
                <div v-if="page.props?.name && dataObtainedMode === Mode.Full" class="mt-8 w-full px-4 lg:px-0">
                    <DataList :parts="page.props.name"/>
                </div>
            </div>

            <!-- Parts Mode -->
            <div
                v-if="mode === Mode.Parts"
                class="w-full flex flex-col justify-center items-center lg:max-w-2/3 xl:max-w-1/2"
                key="parts-mode-container"
            >
                <div
                    class="grid grid-cols-1 md:grid grid-rows-4 md:grid-cols-2 w-full justify-center"
                >
                    <InputText @keyup.enter="submit" v-model="form.first_name" :errors="props.errors"
                               placeholder="First Name" name="first_name"/>
                    <InputText @keyup.enter="submit" v-model="form.middle_name" :errors="props.errors"
                               placeholder="Middle Name" name="middle_name"/>
                    <InputText @keyup.enter="submit" v-model="form.last_name" :errors="props.errors"
                               placeholder="Last Name" name="last_name"/>
                    <InputText @keyup.enter="submit" v-model="form.prefix_name" :errors="props.errors"
                               placeholder="Prefix Name" name="prefix_name"/>
                    <InputText @keyup.enter="submit" v-model="form.suffix_name" :errors="props.errors"
                               placeholder="Suffix Name" name="suffix_name"/>
                    <Select
                        v-model="form.country"
                        :options="countryOptions"
                        optionValue="value"
                        optionLabel="name"
                        placeholder="Western"
                        class="!border !border-[var(--p-sky-500)]"
                    />
                    <div v-if="Object.keys(props.errors).length > 0" class="md:col-span-2 mt-1 text-center">
                        <span v-for="error in props.errors" class="flex flex-col text-[var(--p-red-500)] gap-2 mt-2"
                              key="errors">{{ error }}</span>
                    </div>
                    <PrimeButton label="Format" @click="submit" :loading="form.processing" class="md:col-span-2 mt-2"/>
                </div>
                <div v-if="page.props?.name && dataObtainedMode === Mode.Parts" class="mt-8 w-full px-4 lg:px-0">
                    <DataList :parts="page.props.name"/>
                </div>
            </div>

            <div v-if="Object.keys(props.errors).length > 0 && mode === Mode.Full" key="errors-container">
                <span v-for="error in props.errors"
                      class="flex flex-col text-[var(--p-red-500)] gap-2 mt-2">{{ error }}</span>
            </div>
        </TransitionGroup>
    </main>
</template>

<style scoped>
.mode-move,
.mode-enter-active,
.mode-leave-active {
    transition: all 0.5s ease;
}

.mode-enter-from,
.mode-leave-to {
    opacity: 0;
    transform: translateX(60px);
}

.mode-leave-active {
    position: absolute;
}
</style>
