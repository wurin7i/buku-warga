<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Collection;

class Administration extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $table = 'tenants';

    protected $fillable = [
        'id',
        'name',                    // Nama unit administrasi (RT 01 Jadan)
        'managed_area_id',         // Area yang dikelola (SubRegion ID)
        'access_scope',            // 'own' | 'children' | 'descendants'
        'settings',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'managed_area_id',
            'access_scope',
            'settings',
            'is_active',
        ];
    }

    /**
     * Area yang dikelola oleh unit administrasi ini
     */
    public function managedArea(): BelongsTo
    {
        return $this->belongsTo(SubRegion::class, 'managed_area_id');
    }

    /**
     * Get all areas dalam scope administrasi
     */
    public function getAccessibleAreas(): Collection
    {
        if (!$this->managedArea) {
            return collect();
        }

        return match ($this->access_scope) {
            'own' => collect([$this->managedArea]),
            'children' => collect([$this->managedArea])->merge($this->managedArea->children),
            'descendants' => $this->getAreaDescendants($this->managedArea),
            default => collect([$this->managedArea])
        };
    }

    /**
     * Get all people yang sedang dikelola administrasi ini
     * Berdasarkan tempat tinggal (occupancy) dalam area scope
     */
    public function getManagedPeople(): Collection
    {
        $accessibleAreaIds = $this->getAccessibleAreas()->pluck('id')->toArray();

        return Person::whereHas('occupy.building.subRegion', function ($query) use ($accessibleAreaIds) {
            $query->whereIn('id', $accessibleAreaIds);
        })->get();
    }

    /**
     * Check apakah person sedang dikelola oleh administrasi ini
     */
    public function isManagingPerson(Person $person): bool
    {
        if (!$person->occupy || !$person->occupy->building) {
            return false;
        }

        $personAreaId = $person->occupy->building->sub_region_id;
        $accessibleAreaIds = $this->getAccessibleAreas()->pluck('id')->toArray();

        return in_array($personAreaId, $accessibleAreaIds);
    }

    /**
     * Get properties dalam scope administrasi
     */
    public function getManagedProperties(): Collection
    {
        $accessibleAreaIds = $this->getAccessibleAreas()->pluck('id')->toArray();

        return Property::whereIn('sub_region_id', $accessibleAreaIds)->get();
    }

    private function getAreaDescendants(SubRegion $area): Collection
    {
        $descendants = collect([$area]);

        foreach ($area->children as $child) {
            $descendants = $descendants->merge($this->getAreaDescendants($child));
        }

        return $descendants;
    }

    /**
     * Check if can access specific area
     */
    public function canAccessArea(int|SubRegion $area): bool
    {
        $areaId = $area instanceof SubRegion ? $area->id : $area;

        return $this->getAccessibleAreas()
            ->pluck('id')
            ->contains($areaId);
    }

    /**
     * Get accessible area IDs untuk query optimization
     */
    public function getAccessibleAreaIds(): array
    {
        return $this->getAccessibleAreas()->pluck('id')->toArray();
    }
}
