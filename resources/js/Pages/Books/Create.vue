<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';

// useForm mengelola nilai input dan pesan validasi
const form = useForm({
  kode_buku: '',
  judul: '',
  pengarang: '',
  penerbit: '',
  tahun: '',
  stok: 0,
});

function submit() {
  form.post(route('books.store'));
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md mt-6">
      <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Buku Baru</h1>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Kode Buku</label>
          <input v-model="form.kode_buku" type="text" class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
          <span v-if="form.errors.kode_buku" class="text-red-500 text-sm mt-1 block">{{ form.errors.kode_buku }}</span>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Judul Buku</label>
          <input v-model="form.judul" type="text" class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
          <span v-if="form.errors.judul" class="text-red-500 text-sm mt-1 block">{{ form.errors.judul }}</span>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Pengarang</label>
          <input v-model="form.pengarang" type="text" class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
          <span v-if="form.errors.pengarang" class="text-red-500 text-sm mt-1 block">{{ form.errors.pengarang }}</span>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Penerbit</label>
          <input v-model="form.penerbit" type="text" class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Terbit</label>
          <input v-model="form.tahun" type="text" class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
          <input v-model="form.stok" type="number" class="w-full border border-gray-300 rounded-md p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <div class="pt-4">
          <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md shadow transition">
            Simpan Buku
          </button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>