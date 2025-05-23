<template>
    <div>
        <Head title="Create User" />
        <h1 class="mb-8 text-3xl font-bold">
            <Link class="text-indigo-400 hover:text-indigo-600" href="/users"
                >Users</Link
            >
            <span class="text-indigo-400 font-medium">/</span> Create
        </h1>
        <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
            <form @submit.prevent="store">
                <div class="flex flex-wrap -mb-8 -mr-6 p-8">
                    <text-input
                        v-model="form.first_name"
                        :error="form.errors.first_name"
                        class="pb-8 pr-6 w-full lg:w-1/2"
                        label="First name"
                    />
                    <text-input
                        v-model="form.last_name"
                        :error="form.errors.last_name"
                        class="pb-8 pr-6 w-full lg:w-1/2"
                        label="Last name"
                    />
                    <text-input
                        v-model="form.username"
                        :error="form.errors.username"
                        class="pb-8 pr-6 w-full lg:w-1/2"
                        label="Username"
                    />
                    <text-input
                        v-model="form.email"
                        :error="form.errors.email"
                        class="pb-8 pr-6 w-full lg:w-1/2"
                        label="Email"
                    />
                    <text-input
                        v-model="form.password"
                        :error="form.errors.password"
                        class="pb-8 pr-6 w-full lg:w-1/2"
                        type="password"
                        autocomplete="new-password"
                        label="Password"
                    />
                    <select-input v-model="form.owner" :error="form.errors.owner"
                    class="pb-8 pr-6 w-full lg:w-1/2" label="Owner" >
                        <option :value="true">Yes</option>
                        <option :value="false">No</option>
                    </select-input>
                    <file-input v-model="form.image" :error="form.errors.image" class="pb-8 pr-6 w-full lg:w-1/2" type="file" accept="image/*" label="Photo" />
                    <text-input
                        v-model="form.bio"
                        :error="form.errors.bio"
                        class="pb-8 pr-6 w-full lg:w-1/2"
                        type="text"
                        label="Bio"
                    />
                </div>
                <div
                    class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100"
                >
                    <loading-button
                        :loading="form.processing"
                        class="btn-indigo"
                        type="submit"
                        >Create User</loading-button
                    >
                </div>
            </form>
        </div>
    </div>
</template>
<script>
import { Head, Link } from "@inertiajs/vue3";
import Layout from "@/Shared/Layout.vue";
import TextInput from "@/Shared/TextInput.vue";
import SelectInput from "@/Shared/SelectInput.vue";
import FileInput from "@/Shared/FileInput.vue";
import LoadingButton from "@/Shared/LoadingButton.vue";

export default {
    components: {
        Head,
        Link,
        LoadingButton,
        TextInput,
        SelectInput,
        FileInput
    },
    layout: Layout,
    remember: "form",
    data() {
        return {
            form: this.$inertia.form({
                name: "",
                first_name: "",
                last_name: "",
                email: "",
                username: "",
                password: "",
                owner: false,
                image: null,
                bio: "",
            }),
        };
    },
    methods: {
        store() {
            this.form.post("/users");
        },
    },
};
</script>
