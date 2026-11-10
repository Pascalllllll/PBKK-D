<?php

namespace App\Models;

/**
 * Slug dibuat otomatis dari kolom nama dan dijamin unik.
 * "Kaos Polos" -> kaos-polos, jika sudah ada -> kaos-polos-2, dst.
 */
trait PunyaSlug
{
    protected function isiSlug(array $data): array
    {
        if (! isset($data['data']['nama'])) {
            return $data;
        }

        $id   = isset($data['id']) ? (int) (is_array($data['id']) ? $data['id'][0] : $data['id']) : 0;
        $base = url_title($data['data']['nama'], '-', true) ?: 'item';
        $slug = $base;

        for ($i = 2; $this->builder()->where('slug', $slug)->where('id !=', $id)->countAllResults() > 0; $i++) {
            $slug = "{$base}-{$i}";
        }

        $data['data']['slug'] = $slug;

        return $data;
    }
}
