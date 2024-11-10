<script setup>
import { ref, onMounted } from "vue";
import BackendLayout from "@/Layouts/BackendLayout.vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import AlertMessage from "@/Components/AlertMessage.vue";
import { displayResponse, displayWarning } from "@/responseMessage.js";

const props = defineProps(["featuredproject", "id"]);

const form = useForm({
    project_name: props.featuredproject?.project_name ?? "",
    live_link: props.featuredproject?.live_link ?? "",
    image: props.featuredproject?.image ?? [],
    imagePreview: props.featuredproject?.image ?? [],
    _method: props.featuredproject?.id ? "put" : "post",
});


const handleImageChange = event => {
    const files = Array.from(event.target.files);
    form.image = files;

    // Display image preview
    form.imagePreview = [];
    const renderPromises =files.map((file)=>{
        return new Promise((resolve)=>{
            const reader=new FileReader();
            reader.onload=(e)=>{
                resolve(e.target.result);
            };
            reader.readAsDataURL(file);
        });
    });
    Promise.all(renderPromises).then((previews)=>{
        form.imagePreview=previews
    });
};


const submit = () => {
    const routeName = props.id
        ? route("backend.featuredproject.update", props.id)
        : route("backend.featuredproject.store");
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
                        <InputLabel for="project_name" value="Project name" />
                        <input
                            id="project_name"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.project_name"
                            type="text"
                            placeholder="Project name"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.project_name"
                        />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="live_link" value="Live link" />
                        <input
                            id="live_link"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.live_link"
                            type="text"
                            placeholder="Live link"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.live_link"
                        />
                    </div>


                    <div class="col-span-1 md:col-span-4">
                        <InputLabel for="image" value="Image" />
                        <div class="flex flex-wrap mt-2">
                            <div v-for="(preview, index) in form.imagePreview" :key="index" class="relative max-w-xs mt-2 mr-2">
                                <img :src="preview" alt="Photo Preview" class="object-cover w-16 h-16 rounded-md" />
                            </div>
                        </div>
                        <input id="image" type="file" accept="image/*" multiple
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:borhandleImageChangeder-slate-600"
                            @change="handleImageChange" />
                        <InputError class="mt-2" :message="form.errors.image" />
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
