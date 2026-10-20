<?php 
 
namespace App\Http\Controllers; 
 
use App\Models\Book; 
use Illuminate\Http\Request; 
 
class BookController extends Controller 
{ 
    public function index(Request $request) 
    { 
        // Mengambil data buku dengan fitur pencarian (berdasarkan judul atau pengarang) dan paginasi
        $books = Book::when($request->search, function ($query, $search) {
            $query->where('judul', 'like', "%{$search}%")
                  ->orWhere('pengarang', 'like', "%{$search}%");
        })->latest()->paginate(10)->withQueryString();
 
        // Mengirim data $books dan parameter search ke halaman Vue bernama Books/Index 
        return inertia('Books/Index', [ 
            'books' => $books, 
            'search' => $request->search,
        ]); 
    }
 
    public function create() 
    { 
        // Menampilkan halaman form tambah buku (Books/Create.vue)
        return inertia('Books/Create'); 
    } 
 
    public function store(Request $request) 
    { 
        // 1. Validasi data yang dikirim dari form
        $validated = $request->validate([ 
            'kode_buku'  => 'required|unique:books',   // wajib diisi, tidak boleh sama dengan kode_buku lain 
            'judul'      => 'required|max:255', 
            'pengarang'  => 'required|max:255', 
            'penerbit'   => 'nullable|max:255',        // boleh kosong 
            'tahun'      => 'nullable|digits:4',       // jika diisi, harus 4 digit angka 
            'stok'       => 'required|integer|min:0', 
        ]); 
 
        // 2. Simpan sebagai baris baru di tabel books 
        Book::create($validated); 
 
        // 3. Arahkan kembali ke halaman daftar buku dengan pesan sukses 
        return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan'); 
    } 

    public function show(Book $book) 
    { 
        // Dibiarkan kosong karena tidak dipakai pada studi kasus ini
    } 
 
    public function edit(Book $book) 
    { 
        // $book sudah otomatis berisi data buku yang akan diedit (hasil Route Model Binding) 
        return inertia('Books/Edit', [ 
            'book' => $book, 
        ]); 
    } 
 
    public function update(Request $request, Book $book) 
    { 
        // Validasi update dengan mengecualikan ID buku itu sendiri agar tidak error unique
        $validated = $request->validate([ 
            'kode_buku'  => 'required|unique:books,kode_buku,' . $book->id,  // kecualikan id buku ini 
            'judul'      => 'required|max:255', 
            'pengarang'  => 'required|max:255', 
            'penerbit'   => 'nullable|max:255', 
            'tahun'      => 'nullable|digits:4', 
            'stok'       => 'required|integer|min:0', 
        ]); 
 
        // Mengganti data lama pada $book dengan data baru yang sudah divalidasi 
        $book->update($validated); 
 
        // Kembali ke daftar buku dengan flash message sukses 
        return redirect()->route('books.index')->with('success', 'Buku berhasil diperbarui'); 
    }
 
    public function destroy(Book $book) 
    { 
        // Menghapus baris data buku secara permanen dari tabel books 
        $book->delete(); 

        // Kembali ke daftar buku dengan flash message sukses 
        return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus'); 
    }
}