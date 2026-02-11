<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'company',
        'bin',
        'contact_person_name',
        'contact_person_phone',
        'email',
        'city',
        'legal_address',
        'requisites',
        'instagram',
        'notes',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function projectObjects()
    {
        return $this->hasMany(ProjectObject::class, 'dealer_id');
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
