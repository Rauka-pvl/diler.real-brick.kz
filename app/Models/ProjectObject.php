<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectObject extends Model
{
    use HasFactory;

    public const STAGE_NEGOTIATIONS = 'negotiations';
    public const STAGE_CONTRACT_SIGNED = 'contract_signed';
    public const STAGE_COMPLETED = 'completed';

    protected $table = 'project_objects';

    protected $fillable = [
        'dealer_id',
        'client_id',
        'manager_name',
        'manager_position',
        'manager_phone',
        'manager_email',
        'contact_name',
        'contact_phone',
        'contact_email',
        'address_country',
        'address_locality',
        'address_street',
        'address_house',
        'address_cadastral',
        'name',
        'architect_org',
        'architect_phone',
        'architect_contact',
        'architect_email',
        'investor_contact',
        'investor_phone',
        'competing_materials',
        'stage',
        'planned_delivery_date',
        'title_page_path',
        'visualization_path',
    ];

    protected function casts(): array
    {
        return [
            'planned_delivery_date' => 'date',
        ];
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function objectProducts()
    {
        return $this->hasMany(ProjectObjectProduct::class, 'project_object_id');
    }

    public static function stageOptions(): array
    {
        return [
            self::STAGE_NEGOTIATIONS => 'Переговоры',
            self::STAGE_CONTRACT_SIGNED => 'Подпись договора',
            self::STAGE_COMPLETED => 'Завершено',
        ];
    }

    public function getStageLabelAttribute(): string
    {
        return self::stageOptions()[$this->stage] ?? $this->stage;
    }
}
