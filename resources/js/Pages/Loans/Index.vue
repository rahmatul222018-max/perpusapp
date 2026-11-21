<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';

defineProps({
    loans: Object,
});

const memproses = ref(false);

function formatRupiah(angka) {
    return 'Rp' + Number(angka).toLocaleString('id-ID');
}

function kembalikan(loan) {
    if (!confirm('Proses pengembalian buku "' + loan.book.judul + '"?')) {
        return;
    }

    router.patch(route('loans.kembalikan', loan.id), {}, {
        preserveScroll: true,
        onStart: () => { memproses.value = true; },
        onFinish: () => { memproses.value = false; },
    });
}
</script>

<template>
    <AuthenticatedLayout>
        <div class="p-6">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Data Peminjaman</h1>

            <!-- Flash Message Sukses -->
            <div
                v-if="$page.props.flash.success"
                class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm"
            >
                {{ $page.props.flash.success }}
            </div>

            <!-- Pesan Error Pengembalian -->
            <div
                v-if="$page.props.errors.loan"
                class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm"
            >
                {{ $page.props.errors.loan }}
            </div>

            <!-- Card Container Utama -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <!-- Tombol Pinjam Buku -->
                <div class="mb-6">
                    <Link
                        href="/loans/create"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition"
                    >
                        + Pinjam Buku
                    </Link>
                </div>

                <!-- Tabel Data Peminjaman -->
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Anggota</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode Buku</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Buku</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Pinjam</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jatuh Tempo</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Kembali</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Denda</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="loan in loans.data" :key="loan.id" class="hover:bg-gray-50 transition">
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">
                                    {{ loan.member.nama }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                    {{ loan.book.kode_buku }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ loan.book.judul }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ loan.tanggal_pinjam }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ loan.tanggal_kembali_rencana }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ loan.tanggal_kembali || '-' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center text-sm">
                                    <span
                                        class="inline-block px-3 py-1 rounded-full text-xs font-semibold capitalize"
                                        :class="loan.status === 'dipinjam'
                                            ? 'bg-amber-50 text-amber-700 border border-amber-200'
                                            : 'bg-green-50 text-green-700 border border-green-200'"
                                    >
                                        {{ loan.status }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    <!-- Kasus 1: Sudah dikembalikan -->
                                    <span v-if="loan.status === 'dikembalikan'" class="font-medium text-gray-700">
                                        {{ formatRupiah(loan.denda) }}
                                    </span>
                                    <!-- Kasus 2: Masih dipinjam & terlambat -->
                                    <span v-else-if="loan.hari_terlambat > 0" class="font-medium text-red-600">
                                        Terlambat {{ loan.hari_terlambat }} hari ({{ formatRupiah(loan.perkiraan_denda) }})
                                    </span>
                                    <!-- Kasus 3: Masih dipinjam & belum terlambat -->
                                    <span v-else class="text-gray-400">-</span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <button
                                        v-if="loan.status === 'dipinjam'"
                                        type="button"
                                        :disabled="memproses"
                                        @click="kembalikan(loan)"
                                        class="px-3 py-1 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-md font-medium disabled:opacity-50 transition"
                                    >
                                        Kembalikan
                                    </button>
                                    <span v-else class="text-gray-400">-</span>
                                </td>
                            </tr>
                            <tr v-if="loans.data.length === 0">
                                <td colspan="9" class="px-6 py-6 text-center text-sm text-gray-500">
                                    Belum ada data peminjaman.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginasi Estetik -->
                <div v-if="loans.links" class="mt-5 flex flex-wrap items-center gap-1.5">
                    <template v-for="link in loans.links" :key="link.label">
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