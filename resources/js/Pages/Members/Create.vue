<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    nama: '',
    nis_nim: '',
    no_hp: '',
    foto: null,
});

const pratinjau = ref(null);

function pilihFoto(event) {
    const berkas = event.target.files[0];
    form.foto = berkas ?? null;
    pratinjau.value = berkas ? URL.createObjectURL(berkas) : null;
}

function submit() {
    form.post(route('members.store'));
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Tambah Anggota
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama</label>
                            <input
                                v-model="form.nama"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                            <div v-if="form.errors.nama" class="mt-1 text-sm text-red-600">{{ form.errors.nama }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">NIS/NIM</label>
                            <input
                                v-model="form.nis_nim"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                            <div v-if="form.errors.nis_nim" class="mt-1 text-sm text-red-600">{{ form.errors.nis_nim }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">No. HP</label>
                            <input
                                v-model="form.no_hp"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            >
                            <div v-if="form.errors.no_hp" class="mt-1 text-sm text-red-600">{{ form.errors.no_hp }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Foto (JPG/PNG, maksimal 2 MB)</label>
                            <input
                                type="file"
                                accept="image/png, image/jpeg"
                                @change="pilihFoto"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-gray-800 file:px-4 file:py-2 file:text-xs file:font-semibold file:uppercase file:text-white hover:file:bg-gray-700"
                            >
                            <div v-if="form.errors.foto" class="mt-1 text-sm text-red-600">{{ form.errors.foto }}</div>

                            <img
                                v-if="pratinjau"
                                :src="pratinjau"
                                alt="Pratinjau foto"
                                class="mt-3 h-32 w-32 rounded-md border border-gray-200 object-cover"
                            >
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700 disabled:opacity-50"
                            >
                                Simpan
                            </button>
                            <Link
                                href="/members"
                                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 transition hover:bg-gray-50"
                            >
                                Batal
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>