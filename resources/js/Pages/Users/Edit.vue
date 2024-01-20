<template>
    <div>
        <Head :title="`${form.name || ''} ${form.username}`" />
        <div class="flex justify-start mb-8 max-w-3xl">
            <h1 class="text-3xl font-bold">
                <Link class="text-indigo-400 hover:text-indigo-600" href="/users">Users</Link>
                <span class="text-indigo-400 font-medium">/</span>
                {{ form.name || '' }} {{ form.username }}
            </h1>
            <img v-if="user.image" class="block ml-4 w-8 h-8 rounded-full" :src="user.image" />
        </div>
        <trashed-message v-if="user.deleted" class="mb-6" >
            This user has been deleted.
        </trashed-message>
        <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
            <form @submit.prevent="update">
                <div class="flex flex-wrap -mb-8 -mr-6 p-8">
                    <text-input v-model="form.name" :error="form.errors.name" class="pb-8 pr-6 w-full lg:w-1/2" label="Name" />
                    <text-input v-model="form.username" :error="form.errors.username" class="pb-8 pr-6 w-full lg:w-1/2" label="Username" />
                    <text-input v-model="form.email" :error="form.errors.email" class="pb-8 pr-6 w-full lg:w-1/2" label="Email" />
                    <text-input v-model="form.password" :error="form.errors.password" class="pb-8 pr-6 w-full lg:w-1/2" type="password" autocomplete="new-password" label="Password" />
                    <text-input v-model="form.bio" :error="form.errors.bio" class="pb-8 pr-6 w-full lg:w-1/2" type="text" label="Bio" />
                    <file-input v-model="form.image" :error="form.errors.image" class="pb-8 pr-6 w-full lg:w-1/2" type="file" accept="image/*" label="Photo" />
                </div>
                <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
                    <button v-if="!user.deleted" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete User</button>
                    <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Update User</loading-button>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
import { Head, Link } from '@inertiajs/vue3';
import Layout from '@/Shared/Layout.vue';
import TextInput from '@/Shared/TextInput.vue';
import FileInput from '@/Shared/FileInput.vue';
import LoadingButton from '@/Shared/LoadingButton.vue';
import TrashedMessage from '@/Shared/TrashedMessage.vue';

export default {
    components: {
        FileInput,
        Head,
        Link,
        LoadingButton,
        TextInput,
        TrashedMessage,
    },
    layout: Layout,
    props: {
        user: Object,
    },
    remember: 'form',
    data() {
        return {
            form: this.$inertia.form({
                _method: 'put',
                name: this.user.name,
                username: this.user.username,
                email: this.user.email,
                password: '',
                image: null,
                bio: this.user.bio,
            }),
        }
    },
    methods: {
        update() {
            this.form.put(`/users/${this.user.id}`, {
                onSuccess: () => this.form.reset('password', 'image'),
            })
        },
        destroy() {
            if (confirm('Are you sure you want to delete this user?')) {
                this.$inertia.delete(`/users/${this.user.id}`);
                this.user.deleted=true;
            }
        }
    }
}
</script>