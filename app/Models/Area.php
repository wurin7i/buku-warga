<?php

namespace App\Models;

use App\Enums\AreaAttributes\Level;
use App\Enums\AreaAttributes\Type;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $code
 * @property string $label
 * @property Type $type
 * @property Level $level
 * @property-read Area $parent
 * @property-read \Illuminate\Database\Eloquent\Collection $children
 * @method static Builder applyLevel(Level $level);
 */
class Area extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'label', 'type'];

    protected $casts = [
        'type' => Type::class,
        'level' => Level::class,
    ];

    protected static function booted(): void
    {
        static::creating(fn (Area $model) => $model->handleCreating());
    }

    public function scopeApplyLevel(Builder $builder, Level $level): void
    {
        $builder->where($this->qualifyColumn('level'), $level);
    }

    public function handleCreating(): void
    {
        if (!$this->type) {
            $this->type = Type::Administrative;
        }

        if ($this->type->is(Type::Administrative)) {
            if ($this->parent) {
                $this->level = $this->parent->level->getLowerLevel();
            } else {
                $this->level = Level::ResidentCommunity;
            }
        }
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }
}
