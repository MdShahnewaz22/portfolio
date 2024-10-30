<script setup>
import { ref, onMounted } from "vue";
import BackendLayout from "@/Layouts/BackendLayout.vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AlertMessage from "@/Components/AlertMessage.vue";
import { displayResponse, displayWarning } from "@/responseMessage.js";

const props = defineProps(["about", "id"]);

const form = useForm({
    phone: props.about?.phone ?? "",
    gmail: props.about?.gmail ?? "",
    github: props.about?.github ?? "",
    skype: props.about?.skype ?? "",
    language: props.about?.language ?? "",
    years_experience: props.about?.years_experience ?? "",
    handled_project: props.about?.handled_project ?? "",
    open_source: props.about?.open_source ?? "",
    awards: props.about?.awards ?? "",
    description: props.about?.description ?? "",

    imagePreview: props.about?.image ?? "",
    filePreview: props.about?.file ?? "",
    _method: props.about?.id ? "put" : "post",
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
        ? route("backend.about.update", props.id)
        : route("backend.about.store");
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
                        <InputLabel for="phone" value="Phone" />
                        <input
                            id="phone"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.phone"
                            type="text"
                            placeholder="Phone"
                        />
                        <InputError class="mt-2" :message="form.errors.phone" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="gmail" value="Gmail" />
                        <input
                            id="gmail"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.gmail"
                            type="text"
                            placeholder="Gmail"
                        />
                        <InputError class="mt-2" :message="form.errors.gmail" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="github" value="Github" />
                        <input
                            id="github"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.github"
                            type="text"
                            placeholder="Github"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.github"
                        />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="skype" value="Skype" />
                        <input
                            id="skype"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.skype"
                            type="text"
                            placeholder="Skype"
                        />
                        <InputError class="mt-2" :message="form.errors.skype" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="language" value="Language" />
                        <input
                            id="language"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.language"
                            type="text"
                            placeholder="Language"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.language"
                        />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel
                            for="years_experience"
                            value="Years experience"
                        />
                        <input
                            id="years_experience"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.years_experience"
                            type="text"
                            placeholder="Years experience"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.years_experience"
                        />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel
                            for="handled_project"
                            value="Handled project"
                        />
                        <input
                            id="handled_project"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.handled_project"
                            type="text"
                            placeholder="Handled project"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.handled_project"
                        />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="open_source" value="Open source" />
                        <input
                            id="open_source"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.open_source"
                            type="text"
                            placeholder="Open source"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.open_source"
                        />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="awards" value="Awards" />
                        <input
                            id="awards"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.awards"
                            type="text"
                            placeholder="Awards"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.awards"
                        />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="description" value="Description" />
                        <textarea name="Description"  id="description"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.description"
                            type="text"
                            placeholder="Description">Description</textarea>

                        <InputError
                            class="mt-2"
                            :message="form.errors.description"
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
