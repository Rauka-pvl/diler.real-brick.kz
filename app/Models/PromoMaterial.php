<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PromoMaterial extends Model
{
    protected $fillable = ['name', 'file_path', 'file_name', 'file_size', 'mime_type'];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['Б', 'КБ', 'МБ', 'ГБ'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /** Удалить файл из хранилища при удалении записи */
    protected static function booted(): void
    {
        static::deleting(function (PromoMaterial $material) {
            if (Storage::disk('local')->exists($material->file_path)) {
                Storage::disk('local')->delete($material->file_path);
            }
        });
    }
}
