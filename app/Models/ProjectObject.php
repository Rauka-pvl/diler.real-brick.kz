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
    public const INTERMEDIARY_ARCHITECT = 'architect';
    public const INTERMEDIARY_DESIGNER = 'designer';

    public const MODERATION_DRAFT = 'draft';

    public const MODERATION_PENDING = 'pending_moderation';

    public const MODERATION_REJECTED = 'rejected';

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
        'map_lat',
        'map_lng',
        'name',
        'architect_org',
        'architect_phone',
        'architect_contact',
        'architect_email',
        'investor_contact',
        'investor_phone',
        'intermediary_type',
        'intermediary_name',
        'intermediary_contact',
        'intermediary_position',
        'intermediary_percent',
        'competing_materials',
        'stage',
        'moderation_status',
        'duplicate_of_project_object_id',
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

    public function duplicateOf()
    {
        return $this->belongsTo(self::class, 'duplicate_of_project_object_id');
    }

    public function isPublishedObject(): bool
    {
        return $this->moderation_status === null;
    }

    public function isModerationDraft(): bool
    {
        return $this->moderation_status === self::MODERATION_DRAFT;
    }

    public function isModerationPending(): bool
    {
        return $this->moderation_status === self::MODERATION_PENDING;
    }

    public function isModerationRejected(): bool
    {
        return $this->moderation_status === self::MODERATION_REJECTED;
    }

    public function moderationStatusLabel(): ?string
    {
        return match ($this->moderation_status) {
            self::MODERATION_DRAFT => 'Черновик',
            self::MODERATION_PENDING => 'На модерации',
            self::MODERATION_REJECTED => 'Заявка отклонена',
            default => null,
        };
    }

    public function formatAddressLine(): string
    {
        $parts = array_filter([
            $this->address_country,
            $this->address_locality,
            $this->address_street,
            $this->address_house,
        ]);
        $line = implode(', ', $parts);
        if ($this->address_cadastral) {
            $line .= ($line !== '' ? '; ' : '') . 'кад. ' . $this->address_cadastral;
        }

        return $line !== '' ? $line : '—';
    }

    public static function stageOptions(): array
    {
        return [
            self::STAGE_NEGOTIATIONS => 'Переговоры',
            self::STAGE_CONTRACT_SIGNED => 'Подпись договора',
            self::STAGE_COMPLETED => 'Завершено',
        ];
    }

    public static function intermediaryTypeOptions(): array
    {
        return [
            self::INTERMEDIARY_ARCHITECT => 'Архитектор',
            self::INTERMEDIARY_DESIGNER => 'Дизайнер',
        ];
    }

    public function getStageLabelAttribute(): string
    {
        return self::stageOptions()[$this->stage] ?? $this->stage;
    }

    public function getIntermediaryTypeLabelAttribute(): ?string
    {
        if (! $this->intermediary_type) {
            return null;
        }
        return self::intermediaryTypeOptions()[$this->intermediary_type] ?? $this->intermediary_type;
    }
}
