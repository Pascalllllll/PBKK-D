<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Pengaturan toko (kunci-nilai). Semua baris dibaca sekali per request lalu disimpan di properti,
 * karena ongkir dan rekening dipakai di banyak view sekaligus.
 */
class PengaturanModel extends Model
{
    /** Nilai bawaan bila kunci belum pernah disimpan. */
    public const BAWAAN = [
        'ongkir'        => '0',
        'gratis_ongkir' => '0',   // 0 = tidak ada gratis ongkir
        'rekening'      => '',    // satu rekening per baris: Bank | Nomor | Atas nama
        'instruksi'     => '',
        'email'         => '',
        'whatsapp'      => '',
    ];

    protected $table         = 'pengaturan';
    protected $primaryKey    = 'kunci';
    protected $returnType    = 'array';
    protected $allowedFields = ['kunci', 'nilai'];
    protected $useTimestamps = true;
    protected $createdField  = '';
    protected $useAutoIncrement = false;

    private ?array $cache = null;

    public function semua(): array
    {
        return $this->cache ??= array_column($this->findAll(), 'nilai', 'kunci') + self::BAWAAN;
    }

    public function nilai(string $kunci): string
    {
        return (string) ($this->semua()[$kunci] ?? '');
    }

    public function simpan(array $data): void
    {
        foreach (array_intersect_key($data, self::BAWAAN) as $kunci => $nilai) {
            $this->upsert(['kunci' => $kunci, 'nilai' => (string) $nilai]);
        }
        $this->cache = null;
    }

    /** @return list<array{bank: string, nomor: string, nama: string}> */
    public function rekening(): array
    {
        $hasil = [];
        foreach (preg_split('/\R/', $this->nilai('rekening')) as $baris) {
            $bagian = array_map('trim', explode('|', $baris));
            if (count($bagian) === 3 && $bagian[0] !== '' && $bagian[1] !== '') {
                $hasil[] = ['bank' => $bagian[0], 'nomor' => $bagian[1], 'nama' => $bagian[2]];
            }
        }

        return $hasil;
    }

    /** Checkout baru dibuka setelah admin mengisi minimal satu rekening tujuan. */
    public function checkoutSiap(): bool
    {
        return $this->rekening() !== [];
    }

    public function ongkir(int $subtotal): int
    {
        $gratis = (int) $this->nilai('gratis_ongkir');

        return $gratis > 0 && $subtotal >= $gratis ? 0 : (int) $this->nilai('ongkir');
    }
}
