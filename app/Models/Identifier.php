<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

/**
 * @property \Spatie\SchemalessAttributes\SchemalessAttributes $data
 */
class Identifier extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'identifier_value',
        'issue_date',
        'expiry_date',
    ];

    protected $casts = [
        'data' => SchemalessAttributes::class,
    ];

    public function scopeWithData(): Builder
    {
        return $this->data->modelScope();
    }
}
