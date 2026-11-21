<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    book: Object,
});

const form = useForm({
    kode_buku: props.book.kode_buku,
    judul: props.book.judul,
    pengarang: props.book.pengarang,
    penerbit: props.book.penerbit,
    tahun: props.book.tahun,
    stok: props.book.stok,
});

function submit() {
    form.put(route('books.update', props.book.id));
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Buku</h1>

            <div class="max-w-xl bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Buku</label>
                        <input v-model="form.kode_buku" type="text" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm">
                        <div v-if="form.errors.kode_buku" class="mt-1 text-sm text-red-600">{{ form.errors.kode_buku }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                        <input v-model="form.judul" type="text" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm">
                        <div v-if="form.errors.judul" class="mt-1 text-sm text-red-600">{{ form.errors.judul }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pengarang</label>
                        <input v-model="form.pengarang" type="text" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm">
                        <div v-if="form.errors.pengarang" class="mt-1 text-sm text-red-600">{{ form.errors.pengarang }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Penerbit</label>
                        <input v-model="form.penerbit" type="text" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm">
                        <div v-if="form.errors.penerbit" class="mt-1 text-sm text-red-600">{{ form.errors.penerbit }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                        <input v-model="form.tahun" type="text" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm">
                        <div v-if="form.errors.tahun" class="mt-1 text-sm text-red-600">{{ form.errors.tahun }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                        <input v-model="form.stok" type="number" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm">
                        <div v-if="form.errors.stok" class="mt-1 text-sm text-red-600">{{ form.errors.stok }}</div>
                    </div>

                    <div class="pt-2 flex items-center gap-3">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 bg-gray-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 disabled:opacity-50 transition"
                        >
                            Simpan Perubahan
                        </button>
                        <Link href="/books" class="text-sm text-gray-600 hover:text-gray-900">Batal</Link>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>