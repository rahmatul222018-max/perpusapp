<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    books: Object,
    search: String,
});

// State untuk input pencarian
const keyword = ref(props.search);

// Fungsi pencarian saat menekan Enter
function cari() {
    router.get('/books', { search: keyword.value }, { preserveState: true });
}

// Fungsi konfirmasi hapus data
function hapus(id) {
    if (confirm('Yakin ingin menghapus buku ini?')) {
        router.delete(route('books.destroy', id));
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Buku</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Flash Message Sukses -->
                <div v-if="$page.props.flash.success" class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg shadow">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Flash Message Error (Tambahan Tugas Mandiri 1) -->
                <div v-if="$page.props.flash.error" class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg shadow">
                    {{ $page.props.flash.error }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
                    
                    <!-- Bagian Tombol Tambah & Kotak Pencarian -->
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                        <Link href="/books/create" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            + Tambah Buku
                        </Link>

                        <input 
                            v-model="keyword"
                            @keyup.enter="cari"
                            type="text"
                            placeholder="Cari kode, judul, atau pengarang..."
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full sm:w-72"
                        >
                    </div>

                    <!-- Tabel Data Buku -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengarang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="book in books.data" :key="book.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ book.kode_buku }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ book.judul }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ book.pengarang }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ book.stok }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                        <Link :href="`/books/${book.id}/edit`" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded">Edit</Link>
                                        <button @click="hapus(book.id)" class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded">Hapus</button>
                                    </td>
                                </tr>
                                <tr v-if="books.data.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data buku ditemukan.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>