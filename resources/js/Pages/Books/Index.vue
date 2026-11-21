<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';

const props = defineProps({
    books: Object,
    search: String,
});

const keyword = ref(props.search ?? '');
const hasilCari = ref(null);
const sedangMencari = ref(false);
const pesanError = ref('');

let timer = null;
let permintaan = null;

const daftarBuku = computed(() => hasilCari.value ?? props.books.data);

async function ambilHasil() {
    if (permintaan) {
        permintaan.abort();
    }
    permintaan = new AbortController();

    sedangMencari.value = true;
    pesanError.value = '';

    try {
        const respons = await axios.get(route('books.search'), {
            params: { q: keyword.value.trim() },
            signal: permintaan.signal,
        });
        hasilCari.value = respons.data;
        sedangMencari.value = false;
    } catch (error) {
        if (axios.isCancel(error)) {
            return;
        }
        sedangMencari.value = false;
        pesanError.value = 'Pencarian gagal. Muat ulang halaman, lalu coba lagi.';
    }
}

watch(keyword, (nilai) => {
    clearTimeout(timer);

    if (nilai.trim() === '') {
        if (permintaan) {
            permintaan.abort();
        }
        hasilCari.value = null;
        sedangMencari.value = false;
        pesanError.value = '';
        return;
    }

    timer = setTimeout(ambilHasil, 800);
});

function hapus(id) {
    if (confirm('Yakin ingin menghapus buku ini?')) {
        router.delete(route('books.destroy', id), {
            onSuccess: () => {
                if (keyword.value.trim() !== '') {
                    ambilHasil();
                }
            },
        });
    }
}

onBeforeUnmount(() => {
    clearTimeout(timer);
    if (permintaan) {
        permintaan.abort();
    }
});
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Data Buku</h1>

            <!-- Flash Message -->
            <div
                v-if="$page.props.flash.success"
                class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm"
            >
                {{ $page.props.flash.success }}
            </div>

            <!-- Card Container Utama -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <!-- Baris Tombol Tambah & Pencarian -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <Link
                        href="/books/create"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition"
                    >
                        + Tambah Buku
                    </Link>

                    <div class="flex items-center gap-3">
                        <span v-if="sedangMencari" class="text-sm text-gray-400">Mencari...</span>
                        <input
                            v-model="keyword"
                            type="text"
                            placeholder="Ketik judul, pengarang, atau penerbit..."
                            class="w-full sm:w-72 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm"
                        >
                    </div>
                </div>

                <!-- Pesan Error AJAX -->
                <div v-if="pesanError" class="mb-4 text-sm text-red-600">
                    {{ pesanError }}
                </div>

                <!-- Tabel Data Buku -->
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengarang</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="book in daftarBuku" :key="book.id" class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">
                                    {{ book.kode_buku }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ book.judul }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ book.pengarang }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ book.stok }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                    <Link
                                        :href="`/books/${book.id}/edit`"
                                        class="inline-block px-3 py-1 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-md font-medium transition"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        @click="hapus(book.id)"
                                        class="inline-block px-3 py-1 bg-red-50 text-red-600 hover:bg-red-100 rounded-md font-medium transition"
                                    >
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="daftarBuku.length === 0">
                                <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">
                                    Tidak ada buku yang ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginasi Estetik (Baru Ditambahkan) -->
                <div v-if="hasilCari === null && books.links" class="mt-5 flex flex-wrap items-center gap-1.5">
                    <template v-for="link in books.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            v-html="link.label"
                            class="px-3 py-1.5 text-sm rounded-md border transition"
                            :class="link.active
                                ? 'bg-gray-800 text-white border-gray-800 font-semibold'
                                : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'"
                        />
                        <span
                            v-else
                            v-html="link.label"
                            class="px-3 py-1.5 text-sm rounded-md border border-gray-100 text-gray-400 bg-gray-50"
                        />
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>