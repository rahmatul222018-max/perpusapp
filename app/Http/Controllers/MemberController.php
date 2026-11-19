<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // baris baru: untuk menghapus berkas foto

class MemberController extends Controller
{
    public function index(Request $request)
{
    // Mengambil data anggota; jika ada kata kunci, filter berdasarkan nama ATAU NIS/NIM
   $members = Member::when($request->search, function ($query, $search) {
    $query->where('nama', 'like', "%{$search}%")
          ->orWhere('nis_nim', 'like', "%{$search}%")
          ->orWhere('no_hp', 'like', "%{$search}%");
})->latest()->paginate(10)->withQueryString();

    return inertia('Members/Index', [
        'members' => $members,
        'search' => $request->search,
    ]);
}

    public function create()
{
    return inertia('Members/Create');
}

public function store(Request $request)
{
    // 1. Validasi data teks dan berkas foto
   $validated = $request->validate([
    'nama' => 'required|max:255',
    'nis_nim' => 'required|max:50|unique:members',
    'no_hp' => 'nullable|digits_between:8,15',
    'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
]);

    // 2. Jika ada berkas foto yang diunggah, simpan ke storage/app/public/members
    // lalu catat path-nya (contoh: members/abc123.jpg) untuk disimpan di database
    if ($request->hasFile('foto')) {
        $validated['foto'] = $request->file('foto')->store('members', 'public');
    }

    // 3. Simpan sebagai baris baru di tabel members
    Member::create($validated);

    // 4. Kembali ke daftar anggota, sertakan flash message
    return redirect()->route('members.index')->with('success', 'Anggota berhasil ditambahkan');
}

    public function show(Member $member)
    {
        //
    }

   public function edit(Member $member)
{
    // $member sudah otomatis berisi data anggota yang akan diedit (Route Model Binding)
    return inertia('Members/Edit', [
        'member' => $member,
    ]);
}

public function update(Request $request, Member $member)
{
    // Aturan sama seperti store(), dengan pengecualian unique agar NIS/NIM milik sendiri tidak dianggap duplikat
   $validated = $request->validate([
    'nama' => 'required|max:255',
    'nis_nim' => 'required|max:50|unique:members,nis_nim,' . $member->id,
    'no_hp' => 'nullable|digits_between:8,15',
    'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
]);

    if ($request->hasFile('foto')) {
        // Ada foto baru: hapus foto lama (jika ada) agar tidak menumpuk di storage
        if ($member->foto) {
            Storage::disk('public')->delete($member->foto);
        }

        // Simpan foto baru, catat path-nya
        $validated['foto'] = $request->file('foto')->store('members', 'public');
    } else {
        // Tidak ada foto baru: buang kunci 'foto' agar foto lama TIDAK tertimpa kosong
        unset($validated['foto']);
    }

    $member->update($validated);

    return redirect()->route('members.index')->with('success', 'Anggota berhasil diperbarui');
}

    public function destroy(Member $member)
{
    // Hapus berkas foto dari storage (jika ada), baru hapus barisnya dari database
    if ($member->foto) {
        Storage::disk('public')->delete($member->foto);
    }

    $member->delete();

    return redirect()->route('members.index')->with('success', 'Anggota berhasil dihapus');
}

    /**
     * Endpoint JSON untuk live search anggota (dipanggil oleh Axios).
     */
    public function search(Request $request)
    {
        $kataKunci = $request->query('q', '');

        $members = Member::where(function ($query) use ($kataKunci) {
            $query->where('nama', 'like', "%{$kataKunci}%")
                  ->orWhere('nis_nim', 'like', "%{$kataKunci}%");
        })
        ->latest()
        ->limit(10)
        ->get(['id', 'nama', 'nis_nim', 'no_hp', 'foto']);

        return response()->json($members);
    }

    /**
     * Endpoint JSON untuk validasi realtime: apakah NIS/NIM masih tersedia?
     */
    public function cekNis(Request $request)
    {
        $nisNim = $request->query('nis_nim', '');
        $abaikanId = $request->query('abaikan');

        $sudahDipakai = Member::where('nis_nim', $nisNim)
            ->when($abaikanId, function ($query, $id) {
                $query->where('id', '!=', $id);
            })
            ->exists();

        return response()->json([
            'tersedia' => ! $sudahDipakai,
        ]);
    }
}