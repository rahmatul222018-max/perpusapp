<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

// BAGIAN INI WAJIB ADA: Menerima data $books dari BookController
defineProps({
  books: Object,
});
</script>

<template>
  <AuthenticatedLayout>
    <div class="max-w-6xl mx-auto p-6 bg-white rounded-lg shadow-md mt-6">
      
      <!-- Header Halaman & Tombol Tambah Buku -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Data Buku</h1>
        <Link 
          href="/books/create" 
          class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md shadow transition flex items-center gap-1"
        >
          <span class="text-lg">+</span> Tambah Buku
        </Link>
      </div>

      <!-- Tabel Data Buku -->
      <div class="overflow-x-auto border border-gray-200 rounded-lg">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-100 border-b border-gray-200 text-gray-700 font-semibold text-sm">
              <th class="p-3">Kode</th>
              <th class="p-3">Judul</th>
              <th class="p-3">Pengarang</th>
              <th class="p-3">Penerbit</th>
              <th class="p-3 text-center">Tahun</th>
              <th class="p-3 text-center">Stok</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 text-gray-600 text-sm">
            
            <!-- Looping data buku -->
            <tr v-for="book in books.data" :key="book.id" class="hover:bg-gray-50 transition">
              <td class="p-3 font-medium text-gray-900">{{ book.kode_buku }}</td>
              <td class="p-3">{{ book.judul }}</td>
              <td class="p-3">{{ book.pengarang }}</td>
              <td class="p-3">{{ book.penerbit || '-' }}</td>
              <td class="p-3 text-center">{{ book.tahun || '-' }}</td>
              <td class="p-3 text-center">
                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                  {{ book.stok }}
                </span>
              </td>
            </tr>
            
            <!-- Pesan jika data masih kosong -->
            <tr v-if="!books.data || books.data.length === 0">
              <td colspan="6" class="p-4 text-center text-gray-500 italic">
                Belum ada data buku.
              </td>
            </tr>

          </tbody>
        </table>
      </div>

    </div>
  </AuthenticatedLayout>
</template>