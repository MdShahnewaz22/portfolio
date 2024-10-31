<script setup>
import { ref, onMounted } from "vue";
import BackendLayout from "@/Layouts/BackendLayout.vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AlertMessage from "@/Components/AlertMessage.vue";
import { displayResponse, displayWarning } from "@/responseMessage.js";

const props = defineProps(["education", "id"]);

const form = useForm({
    institution_name: props.education?.institution_name ?? "",
    year_to_year: props.education?.year_to_year ?? "",
    certificate_name: props.education?.certificate_name ?? "",
    group: props.education?.group ?? "",

    imagePreview: props.education?.image ?? "",
    filePreview: props.education?.file ?? "",
    _method: props.education?.id ? "put" : "post",
});

const handleimageChange = (event) => {
    const file = event.target.files[0];
    form.image = file;

    // Display image preview
    const reader = new FileReader();
    reader.onload = (e) => {
        form.imagePreview = e.target.result;
    };
    reader.readAsDataURL(file);
};

const handlefileChange = (event) => {
    const file = event.target.files[0];
    form.file = file;
};

const submit = () => {
    const routeName = props.id
        ? route("backend.education.update", props.id)
        : route("backend.education.store");
    form.transform((data) => ({
        ...data,
        remember: "",
        isDirty: false,
    })).post(routeName, {
        onSuccess: (response) => {
            if (!props.id) form.reset();
            displayResponse(response);
        },
        onError: (errorObject) => {
            displayWarning(errorObject);
        },
    });
};
</script>

<template>
    <BackendLayout>
        <div
            class="w-full mt-3 transition duration-1000 ease-in-out transform bg-white border border-gray-700 rounded-md shadow-lg shadow-gray-800/50 dark:bg-slate-900"
        >
            <div
                class="flex items-center justify-between w-full text-gray-700 bg-gray-100 rounded-md shadow-md dark:bg-gray-800 dark:text-gray-200 shadow-gray-800/50"
            >
                <div>
                    <h1 class="p-4 text-xl font-bold dark:text-white">
                        {{ $page.props.pageTitle }}
                    </h1>
                </div>
                <div class="p-4 py-2"></div>
            </div>

            <form @submit.prevent="submit" class="p-4">
                <AlertMessage />
                <div
                    class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4"
                >
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel
                            for="institution_name"
                            value="Institution name"
                        />
                        <input
                            id="institution_name"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.institution_name"
                            type="text"
                            placeholder="Institution name"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.institution_name"
                        />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="year_to_year" value="Year to year" />
                        <input
                            id="year_to_year"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.year_to_year"
                            type="text"
                            placeholder="Year to year"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.year_to_year"
                        />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel
                            for="certificate_name"
                            value="Certificate name"
                        />
                        <input
                            id="certificate_name"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.certificate_name"
                            type="text"
                            placeholder="Certificate name"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.certificate_name"
                        />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel
                            for="group"
                            value="Group"
                        />
                        <input
                            id="group"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.group"
                            type="text"
                            placeholder="Group"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.group"
                        />
                    </div>
                </div>
                <div class="flex items-center justify-end mt-4">
                    <PrimaryButton
                        type="submit"
                        class="ms-4"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        {{ props.id ?? false ? "Update" : "Create" }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </BackendLayout>
</template>
