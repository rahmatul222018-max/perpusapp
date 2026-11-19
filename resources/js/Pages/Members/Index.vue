<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';

const props = defineProps({
    members: Object,
    search: String,
});

const keyword = ref(props.search ?? '');
const hasilCari = ref(null);
const sedangMencari = ref(false);
const pesanError = ref('');

let timer = null;
let permintaan = null;

const daftarAnggota = computed(() => hasilCari.value ?? props.members.data);

async function ambilHasil() {
    if (permintaan) {
        permintaan.abort();
    }
    permintaan = new AbortController();

    sedangMencari.value = true;
    pesanError.value = '';

    try {
        const respons = await axios.get(route('members.search'), {
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

    timer = setTimeout(ambilHasil, 300);
});

function hapus(id) {
    if (confirm('Yakin ingin menghapus anggota ini?')) {
        router.delete(route('members.destroy', id), {
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
        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h1 class="text-2xl font-bold text-gray-800 mb-6">Data Anggota</h1>

                <div
                    v-if="$page.props.flash.success"
                    class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg"
                >
                    {{ $page.props.flash.success }}
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
                        <Link
                            href="/members/create"
                            class="inline-flex items-center px-4 py-2.5 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition"
                        >
                            + TAMBAH ANGGOTA
                        </Link>

                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <span v-if="sedangMencari" class="text-sm text-gray-500 animate-pulse">
                                Mencari...
                            </span>
                            <input
                                v-model="keyword"
                                type="text"
                                placeholder="Ketik nama atau NIS/NIM..."
                                class="w-full sm:w-72 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                            >
                        </div>
                    </div>

                    <div v-if="pesanError" class="mb-4 p-3 bg-red-100 text-red-700 rounded-md text-sm">
                        {{ pesanError }}
                    </div>

                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIS/NIM</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. HP</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="member in daftarAnggota" :key="member.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        <img
                                            v-if="member.foto"
                                            :src="`/storage/${member.foto}`"
                                            alt="Foto anggota"
                                            class="h-12 w-12 rounded-full object-cover"
                                        >
                                        <span v-else class="text-gray-400">-</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ member.nis_nim }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ member.nama }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ member.no_hp }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                        <Link
                                            :href="`/members/${member.id}/edit`"
                                            class="inline-block px-3 py-1 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded text-xs font-semibold transition"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            @click="hapus(member.id)"
                                            class="inline-block px-3 py-1 bg-red-50 text-red-600 hover:bg-red-100 rounded text-xs font-semibold transition"
                                        >
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="daftarAnggota.length === 0">
                                    <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">
                                        {{ keyword.trim() !== '' ? 'Tidak ada anggota yang ditemukan.' : 'Belum ada data anggota.' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginasi hanya untuk daftar bawaan -->
                    <div v-if="hasilCari === null && members.links" class="mt-4 flex flex-wrap gap-2">
                        <template v-for="link in members.links" :key="link.label">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                v-html="link.label"
                                class="px-3 py-1 border rounded text-sm hover:bg-gray-100"
                                :class="{ 'bg-gray-800 text-white hover:bg-gray-700': link.active }"
                            />
                            <span v-else v-html="link.label" class="px-3 py-1 border rounded text-sm text-gray-400" />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>