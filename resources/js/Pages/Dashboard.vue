<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BarChart from '@/Components/BarChart.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    statistik: Object,
    grafik: Object,
});

function formatRupiah(angka) {
    return 'Rp' + Number(angka).toLocaleString('id-ID');
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h1>

            <!-- 4 Kartu Ringkasan -->
           <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-5">
                <!-- KARTU 1: Judul Buku -->
                <div class="rounded-xl border border-gray-100 border-l-4 border-l-blue-500 bg-white p-6 shadow-sm">
                    <div class="text-sm font-medium text-gray-500">Judul Buku</div>
                    <div class="mt-2 text-3xl font-bold text-gray-800">
                        {{ statistik.total_buku }}
                    </div>
                    <div class="mt-1 text-xs text-gray-500">
                        Total stok: {{ statistik.total_stok }} eksemplar
                    </div>
                </div>

                <!-- KARTU 2: Anggota -->
                <div class="rounded-xl border border-gray-100 border-l-4 border-l-green-500 bg-white p-6 shadow-sm">
                    <div class="text-sm font-medium text-gray-500">Anggota</div>
                    <div class="mt-2 text-3xl font-bold text-gray-800">
                        {{ statistik.total_anggota }}
                    </div>
                    <div class="mt-1 text-xs text-gray-500">
                        Terdaftar di perpustakaan
                    </div>
                </div>

                <!-- KARTU 3: Sedang Dipinjam -->
                <div class="rounded-xl border border-gray-100 border-l-4 border-l-amber-500 bg-white p-6 shadow-sm">
                    <div class="text-sm font-medium text-gray-500">Sedang Dipinjam</div>
                    <div class="mt-2 text-3xl font-bold text-gray-800">
                        {{ statistik.sedang_dipinjam }}
                    </div>
                    <div
                        class="mt-1 text-xs font-medium"
                        :class="statistik.terlambat > 0 ? 'text-red-600' : 'text-gray-500'"
                    >
                        {{ statistik.terlambat }} terlambat
                    </div>
                </div>

                <!-- KARTU 4: Total Denda -->
                <div class="rounded-xl border border-gray-100 border-l-4 border-l-red-500 bg-white p-6 shadow-sm">
                    <div class="text-sm font-medium text-gray-500">Total Denda</div>
                    <div class="mt-2 text-3xl font-bold text-gray-800">
                        {{ formatRupiah(statistik.total_denda) }}
                    </div>
                    <div class="mt-1 text-xs text-gray-500">
                        Dari buku yang sudah dikembalikan
                    </div>
                </div>
            </div>

            <!-- KARTU 5: Peminjaman Hari Ini -->
<div class="rounded-xl border border-gray-100 border-l-4 border-l-indigo-500 bg-white p-6 shadow-sm">
    <div class="text-sm font-medium text-gray-500">Peminjaman Hari Ini</div>
    <div class="mt-2 text-3xl font-bold text-gray-800">
        {{ statistik.peminjaman_hari_ini }}
    </div>
    <div class="mt-1 text-xs text-gray-500">
        Transaksi masuk hari ini
    </div>
</div>

            <!-- Kotak Grafik Batang -->
            <div class="mt-6 rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-base font-bold text-gray-800">
    Peminjaman 12 Bulan Terakhir
</h3>

                <BarChart
                    :label="grafik.label"
                    :jumlah="grafik.jumlah"
                    judul="Jumlah peminjaman"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>