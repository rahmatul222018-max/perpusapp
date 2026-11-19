<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';

const form = useForm({
    kode_buku: '',
    judul: '',
    pengarang: '',
    penerbit: '',
    tahun: '',
    stok: '',
});

// Validasi realtime Kode Buku (Tugas Mandiri No. 3)
const statusKode = ref('');
let timerKode = null;
let permintaanKode = null;

async function periksaKode() {
    if (permintaanKode) {
        permintaanKode.abort();
    }
    permintaanKode = new AbortController();
    statusKode.value = 'memeriksa';

    try {
        const respons = await axios.get(route('books.cek-kode'), {
            params: {
                kode_buku: form.kode_buku.trim(),
            },
            signal: permintaanKode.signal,
        });
        statusKode.value = respons.data.tersedia ? 'tersedia' : 'terpakai';
    } catch (error) {
        if (axios.isCancel(error)) {
            return;
        }
        statusKode.value = 'gagal';
    }
}

watch(() => form.kode_buku, (nilai) => {
    clearTimeout(timerKode);

    if (!nilai || nilai.trim() === '') {
        if (permintaanKode) {
            permintaanKode.abort();
        }
        statusKode.value = '';
        return;
    }

    timerKode = setTimeout(periksaKode, 500);
});

onBeforeUnmount(() => {
    clearTimeout(timerKode);
    if (permintaanKode) {
        permintaanKode.abort();
    }
});

function submit() {
    form.post(route('books.store'));
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-8">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Buku</h1>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Buku</label>
                            <input
                                v-model="form.kode_buku"
                                type="text"
                                placeholder="Contoh: BK-109"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                            >

                            <!-- Pesan hasil pemeriksaan realtime kode buku -->
                            <div v-if="statusKode === 'memeriksa'" class="text-gray-500 text-sm mt-1">
                                Memeriksa kode buku...
                            </div>
                            <div v-else-if="statusKode === 'tersedia'" class="text-green-700 text-sm mt-1">
                                Kode buku ini tersedia.
                            </div>
                            <div v-else-if="statusKode === 'terpakai'" class="text-red-600 text-sm mt-1">
                                Kode buku ini sudah terdaftar. Gunakan kode lain.
                            </div>
                            <div v-else-if="statusKode === 'gagal'" class="text-yellow-700 text-sm mt-1">
                                Pemeriksaan otomatis gagal. Anda tetap dapat menyimpan; server akan memeriksa ulang.
                            </div>

                            <div v-if="form.errors.kode_buku" class="text-red-600 text-sm mt-1">
                                {{ form.errors.kode_buku }}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                            <input
                                v-model="form.judul"
                                type="text"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                            >
                            <div v-if="form.errors.judul" class="text-red-600 text-sm mt-1">{{ form.errors.judul }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pengarang</label>
                            <input
                                v-model="form.pengarang"
                                type="text"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                            >
                            <div v-if="form.errors.pengarang" class="text-red-600 text-sm mt-1">{{ form.errors.pengarang }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Penerbit</label>
                            <input
                                v-model="form.penerbit"
                                type="text"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                            >
                            <div v-if="form.errors.penerbit" class="text-red-600 text-sm mt-1">{{ form.errors.penerbit }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                            <input
                                v-model="form.tahun"
                                type="number"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                            >
                            <div v-if="form.errors.tahun" class="text-red-600 text-sm mt-1">{{ form.errors.tahun }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                            <input
                                v-model="form.stok"
                                type="number"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                            >
                            <div v-if="form.errors.stok" class="text-red-600 text-sm mt-1">{{ form.errors.stok }}</div>
                        </div>

                        <div class="pt-2 flex items-center gap-3">
                            <button
                                type="submit"
                                :disabled="form.processing || statusKode === 'terpakai'"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                            >
                                Simpan
                            </button>
                            <Link
                                href="/books"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition"
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