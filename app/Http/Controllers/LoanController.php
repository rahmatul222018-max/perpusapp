<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    /**
     * Menampilkan daftar peminjaman.
     */
    public function index()
    {
        $loans = Loan::with(['member', 'book'])
            ->orderByDesc('id')
            ->paginate(10);

        return inertia('Loans/Index', [
            'loans' => $loans,
        ]);
    }

    /**
     * Menampilkan form peminjaman.
     */
    public function create()
    {
        $members = Member::orderBy('nama')->get(['id', 'nama', 'nis_nim']);

        $books = Book::where('stok', '>', 0)
            ->orderBy('judul')
            ->get(['id', 'judul', 'stok']);

        return inertia('Loans/Create', [
            'members' => $members,
            'books' => $books,
        ]);
    }

    /**
     * Menyimpan transaksi peminjaman.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'book_id' => 'required|exists:books,id',
        ], [
            'member_id.required' => 'Anggota wajib dipilih.',
            'member_id.exists' => 'Anggota yang dipilih tidak ditemukan.',
            'book_id.required' => 'Buku wajib dipilih.',
            'book_id.exists' => 'Buku yang dipilih tidak ditemukan.',
        ]);

        // 2. Periksa stok di server
        $book = Book::findOrFail($validated['book_id']);
        if ($book->stok < 1) {
            return back()->withErrors([
                'book_id' => 'Stok buku ini sudah habis. Pilih buku lain.',
            ]);
        }

        // 3. Simpan peminjaman dan kurangi stok sebagai satu kesatuan
        DB::transaction(function () use ($validated, $book) {
            Loan::create([
                'member_id' => $validated['member_id'],
                'book_id' => $validated['book_id'],
                'tanggal_pinjam' => now()->toDateString(),
                'tanggal_kembali_rencana' => now()->addDays(14)->toDateString(),
                'status' => 'dipinjam',
            ]);

            $book->decrement('stok');
        });

        // 4. Kembali ke daftar peminjaman dengan pesan sukses
        return redirect()->route('loans.index')
            ->with('success', 'Peminjaman berhasil disimpan. Stok buku berkurang 1.');
    }
    /**
     * Memproses pengembalian buku dan menghitung denda.
     */
    public function kembalikan(Loan $loan)
    {
        // 1. Tolak jika buku ini sudah dikembalikan sebelumnya
        if ($loan->status === 'dikembalikan') {
            return back()->withErrors([
                'loan' => 'Buku ini sudah dikembalikan sebelumnya.',
            ]);
        }

        // 2. Hitung denda sebelum status dan tanggal_kembali diubah
        $hariTerlambat = $loan->hari_terlambat;
        $denda = $loan->perkiraan_denda;

        // 3. Simpan perubahan peminjaman dan tambah stok buku sebagai satu kesatuan
        DB::transaction(function () use ($loan, $denda) {
            $loan->update([
                'status' => 'dikembalikan',
                'tanggal_kembali' => today()->toDateString(),
                'denda' => $denda,
            ]);

            $loan->book->increment('stok');
        });

        // 4. Susun pesan sukses
        $pesan = 'Buku berhasil dikembalikan. Stok buku bertambah 1.';
        if ($denda > 0) {
            $pesan .= ' Terlambat ' . $hariTerlambat . ' hari, denda Rp'
                . number_format($denda, 0, ',', '.') . '.';
        }

        // 5. Kembali ke halaman daftar peminjaman dengan pesan sukses
        return back()->with('success', $pesan);
    }
}