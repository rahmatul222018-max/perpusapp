<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

defineProps({
    members: Array,
    books: Array,
});

const form = useForm({
    member_id: '',
    book_id: '',
});

function submit() {
    form.post(route('loans.store'));
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Pinjam Buku</h1>

            <div class="max-w-xl bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Anggota</label>
                        <select
                            v-model="form.member_id"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm"
                        >
                            <option value="" disabled>-- Pilih anggota --</option>
                            <option v-for="member in members" :key="member.id" :value="member.id">
                                {{ member.nama }} ({{ member.nis_nim }})
                            </option>
                        </select>
                        <div v-if="form.errors.member_id" class="mt-1 text-sm text-red-600">
                            {{ form.errors.member_id }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Buku</label>
                        <select
                            v-model="form.book_id"
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm text-sm"
                        >
                            <option value="" disabled>-- Pilih buku --</option>
                            <option v-for="book in books" :key="book.id" :value="book.id">
                                {{ book.judul }} (stok: {{ book.stok }})
                            </option>
                        </select>
                        <div v-if="form.errors.book_id" class="mt-1 text-sm text-red-600">
                            {{ form.errors.book_id }}
                        </div>
                        <div v-if="books.length === 0" class="mt-1 text-sm text-amber-700">
                            Belum ada buku dengan stok tersedia.
                        </div>
                    </div>
                    <div class="p-3 rounded-lg bg-gray-50 border border-gray-200 text-xs text-gray-600">
    Tanggal pinjam adalah hari ini. Jatuh tempo otomatis 14 hari setelah tanggal pinjam.
</div>
                    <div class="pt-2 flex items-center gap-3">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 bg-gray-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 disabled:opacity-50 transition"
                        >
                            Simpan Peminjaman
                        </button>
                        <Link href="/loans" class="text-sm text-gray-600 hover:text-gray-900">Batal</Link>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>