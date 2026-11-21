<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;

class DashboardController extends Controller
{
    /**
     * Menyiapkan angka ringkasan dan data grafik untuk halaman Dashboard.
     */
    public function index()
    {
        // Bagian 1: Angka untuk kartu ringkasan
        $totalBuku = Book::count();
        $totalStok = (int) Book::sum('stok');
        $totalAnggota = Member::count();

        // Peminjaman yang belum dikembalikan
        $sedangDipinjam = Loan::where('status', 'dipinjam')->count();

        // Peminjaman belum kembali yang jatuh temponya sebelum hari ini
        $terlambat = Loan::where('status', 'dipinjam')
            ->where('tanggal_kembali_rencana', '<', today()->toDateString())
            ->count();

        // Jumlah seluruh denda yang tercatat
        $totalDenda = (int) Loan::sum('denda');
        $peminjamanHariIni = Loan::where('tanggal_pinjam', today()->toDateString())->count();

        // Bagian 2: Data grafik peminjaman 6 bulan terakhir
        $namaBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                      'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $label = [];
        $jumlah = [];

        for ($i = 11; $i >= 0; $i--) {
            $awal = today()->startOfMonth()->subMonths($i);
            $akhir = $awal->copy()->endOfMonth();

            $label[] = $namaBulan[$awal->month - 1] . ' ' . $awal->year;

            $jumlah[] = Loan::whereBetween('tanggal_pinjam', [
                $awal->toDateString(),
                $akhir->toDateString(),
            ])->count();
        }

        // Bagian 3: Kirim data ke Dashboard.vue
        return inertia('Dashboard', [
            'statistik' => [
                'total_buku' => $totalBuku,
                'total_stok' => $totalStok,
                'total_anggota' => $totalAnggota,
                'sedang_dipinjam' => $sedangDipinjam,
                'terlambat' => $terlambat,
                'total_denda' => $totalDenda,
                'statistik' => [
    'total_buku' => $totalBuku,
    'total_stok' => $totalStok,
    'total_anggota' => $totalAnggota,
    'sedang_dipinjam' => $sedangDipinjam,
    'terlambat' => $terlambat,
    'total_denda' => $totalDenda,
    'peminjaman_hari_ini' => $peminjamanHariIni,
],
            ],
            'grafik' => [
                'label' => $label,
                'jumlah' => $jumlah,
            ],
        ]);
    }
}