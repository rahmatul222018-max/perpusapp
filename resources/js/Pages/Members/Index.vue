<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    members: Object,
    search: String,
});

const keyword = ref(props.search);

function cari() {
    router.get('/members', { search: keyword.value });
}

function hapus(id) {
    if (confirm('Yakin ingin menghapus anggota ini?')) {
        router.delete(route('members.destroy', id));
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Data Anggota
            </h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                    
                    <!-- Flash Message -->
                    <div
                        v-if="$page.props.flash.success"
                        class="mb-4 rounded-md bg-green-50 p-4 text-sm font-medium text-green-700 border border-green-200"
                    >
                        {{ $page.props.flash.success }}
                    </div>

                    <!-- Baris Tombol Tambah & Pencarian -->
                    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                        <Link
                            href="/members/create"
                            class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2.5 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-gray-700"
                        >
                            + Tambah Anggota
                        </Link>

                        <input
                            v-model="keyword"
                            @keyup.enter="cari"
                            type="text"
                            placeholder="Cari nama, NIS/NIM, atau No. HP..."
                            class="w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:w-80"
                        >
                    </div>

                    <!-- Tabel Data Anggota -->
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Foto
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        NIS/NIM
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Nama
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                        No. HP
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="member in members.data" :key="member.id" class="hover:bg-gray-50">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <img
                                            v-if="member.foto"
                                            :src="`/storage/${member.foto}`"
                                            alt="Foto anggota"
                                            class="h-12 w-12 rounded-md object-cover border border-gray-200"
                                        >
                                        <span
                                            v-else
                                            class="inline-flex h-12 w-12 items-center justify-center rounded-md bg-gray-100 text-xs text-gray-400"
                                        >
                                            -
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ member.nis_nim }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ member.nama }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                        {{ member.no_hp || '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center text-sm font-medium">
                                        <div class="flex items-center justify-center gap-2">
                                            <Link
                                                :href="`/members/${member.id}/edit`"
                                                class="rounded bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-100"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="hapus(member.id)"
                                                class="rounded bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-100"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="members.data.length === 0">
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                        Belum ada data anggota.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginasi -->
                    <div class="mt-6 flex flex-wrap items-center gap-1">
                        <template v-for="link in members.links" :key="link.label">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                v-html="link.label"
                                class="rounded-md border px-3 py-1.5 text-sm transition"
                                :class="link.active
                                    ? 'border-gray-800 bg-gray-800 text-white'
                                    : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'"
                            />
                            <span
                                v-else
                                v-html="link.label"
                                class="rounded-md border border-gray-200 bg-gray-50 px-3 py-1.5 text-sm text-gray-400"
                            />
                        </template>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>