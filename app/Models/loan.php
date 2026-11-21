<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    // Tarif denda perpustakaan: Rp1.000 per hari keterlambatan
    public const DENDA_PER_HARI = 2000;

    protected $fillable = [
        'book_id',
        'member_id',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_kembali',
        'status',
        'denda',
    ];

    // Mendaftarkan atribut virtual agar ikut terkirim ke Vue
    protected $appends = ['hari_terlambat', 'perkiraan_denda'];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Atribut virtual: menghitung jumlah hari keterlambatan.
     */
    protected function hariTerlambat(): Attribute
    {
        return Attribute::make(get: function () {
            $jatuhTempo = Carbon::parse($this->tanggal_kembali_rencana)->startOfDay();

            $acuan = $this->tanggal_kembali
                ? Carbon::parse($this->tanggal_kembali)->startOfDay()
                : today();

            if ($acuan->lessThanOrEqualTo($jatuhTempo)) {
                return 0;
            }

            return (int) $jatuhTempo->diffInDays($acuan);
        });
    }

    /**
     * Atribut virtual: menghitung perkiraan denda (hari terlambat x Rp1.000).
     */
    protected function perkiraanDenda(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->hari_terlambat * self::DENDA_PER_HARI
        );
    }
}