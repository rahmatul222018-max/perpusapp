<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';

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

// Validasi realtime NIS/NIM
const statusNis = ref('');
let timerNis = null;
let permintaanNis = null;

async function periksaNis() {
    if (permintaanNis) {
        permintaanNis.abort();
    }
    permintaanNis = new AbortController();
    statusNis.value = 'memeriksa';

    try {
        const respons = await axios.get(route('members.cek-nis'), {
            params: {
                nis_nim: form.nis_nim.trim(),
            },
            signal: permintaanNis.signal,
        });
        statusNis.value = respons.data.tersedia ? 'tersedia' : 'terpakai';
    } catch (error) {
        if (axios.isCancel(error)) {
            return;
        }
        statusNis.value = 'gagal';
    }
}

watch(() => form.nis_nim, (nilai) => {
    clearTimeout(timerNis);

    if (!nilai || nilai.trim() === '') {
        if (permintaanNis) {
            permintaanNis.abort();
        }
        statusNis.value = '';
        return;
    }

    timerNis = setTimeout(periksaNis, 500);
});

onBeforeUnmount(() => {
    clearTimeout(timerNis);
    if (permintaanNis) {
        permintaanNis.abort();
    }
});

function submit() {
    form.post(route('members.store'));
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Anggota</h1>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                            <input
                                v-model="form.nama"
                                type="text"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                            >
                            <div v-if="form.errors.nama" class="text-red-600 text-sm mt-1">{{ form.errors.nama }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIS/NIM</label>
                            <input
                                v-model="form.nis_nim"
                                type="text"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                            >

                            <!-- Pesan hasil pemeriksaan realtime -->
                            <div v-if="statusNis === 'memeriksa'" class="text-gray-500 text-sm mt-1">
                                Memeriksa NIS/NIM...
                            </div>
                            <div v-else-if="statusNis === 'tersedia'" class="text-green-700 text-sm mt-1">
                                NIS/NIM ini tersedia.
                            </div>
                            <div v-else-if="statusNis === 'terpakai'" class="text-red-600 text-sm mt-1">
                                NIS/NIM ini sudah terdaftar. Gunakan nomor lain.
                            </div>
                            <div v-else-if="statusNis === 'gagal'" class="text-yellow-700 text-sm mt-1">
                                Pemeriksaan otomatis gagal. Anda tetap dapat menyimpan; server akan memeriksa ulang.
                            </div>

                            <div v-if="form.errors.nis_nim" class="text-red-600 text-sm mt-1">{{ form.errors.nis_nim }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                            <input
                                v-model="form.no_hp"
                                type="text"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                            >
                            <div v-if="form.errors.no_hp" class="text-red-600 text-sm mt-1">{{ form.errors.no_hp }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Foto (JPG/PNG, maksimal 2 MB)</label>
                            <input type="file" accept="image/png, image/jpeg" @change="pilihFoto" class="text-sm text-gray-600">
                            <div v-if="form.errors.foto" class="text-red-600 text-sm mt-1">{{ form.errors.foto }}</div>
                            <img v-if="pratinjau" :src="pratinjau" alt="Pratinjau foto" class="mt-2 h-32 w-32 object-cover rounded-md border">
                        </div>

                        <div class="pt-2">
                            <button
                                type="submit"
                                :disabled="form.processing || statusNis === 'terpakai'"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                            >
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>