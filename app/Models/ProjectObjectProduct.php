<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectObjectProduct extends Model
{
    protected $table = 'project_object_products';

    protected $fillable = ['project_object_id', 'bitrix_product_id', 'product_name', 'quantity'];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    public function projectObject()
    {
        return $this->belongsTo(ProjectObject::class, 'project_object_id');
    }
}
