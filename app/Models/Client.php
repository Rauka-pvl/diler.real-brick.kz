<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    public const TYPE_INDIVIDUAL = 'individual';
    public const TYPE_LEGAL = 'legal';
    public const TYPE_IP = 'ip';

    protected $fillable = [
        'dealer_id',
        'name',
        'type',
        'requisites',
        'address',
        'city',
        'phone',
        'email',
        'instagram',
        'contact_person_name',
        'contact_person_position',
        'contact_person_phone',
    ];

    public function isIndividual(): bool
    {
        return $this->type === self::TYPE_INDIVIDUAL;
    }

    public function isLegal(): bool
    {
        return $this->type === self::TYPE_LEGAL;
    }

    public function isIp(): bool
    {
        return $this->type === self::TYPE_IP;
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_LEGAL => 'Юр. лицо',
            self::TYPE_IP => 'ИП',
            default => 'Физ. лицо',
        };
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    public function projectObjects()
    {
        return $this->hasMany(ProjectObject::class, 'client_id');
    }
}
