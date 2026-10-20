<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';

// 'book' diterima dari BookController@edit (Langkah 2), berisi data lama buku ini
const props = defineProps({
    book: Object,
});

// Berbeda dari Create.vue: nilai awal form diisi dari props.book, bukan string/angka kosong
const form = useForm({
    kode_buku: props.book.kode_buku,
    judul: props.book.judul,
    pengarang: props.book.pengarang,
    penerbit: props.book.penerbit,
    tahun: props.book.tahun,
    stok: props.book.stok,
});

function submit() {
    // form.put() (bukan form.post() seperti pada Create.vue) karena ini operasi Update
    // route('books.update', props.book.id) merujuk ke method update() pada Langkah 2
    form.put(route('books.update', props.book.id));
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Buku</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Kode Buku</label>
                            <input v-model="form.kode_buku" type="text" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                            <span v-if="form.errors.kode_buku" class="text-sm text-red-600">{{ form.errors.kode_buku }}</span>
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700">Judul</label>
                            <input v-model="form.judul" type="text" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700">Pengarang</label>
                            <input v-model="form.pengarang" type="text" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700">Penerbit</label>
                            <input v-model="form.penerbit" type="text" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700">Tahun</label>
                            <input v-model="form.tahun" type="text" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700">Stok</label>
                            <input v-model="form.stok" type="number" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full">
                        </div>

                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>