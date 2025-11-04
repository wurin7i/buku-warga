<?php

namespace App\Core\Contracts;

use App\Core\Data\Person\CreatePersonData;
use App\Core\Data\Person\UpdatePersonData;
use App\Core\Data\Person\PersonData;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

interface PersonServiceInterface
{
    /**
     * Create new person from data
     */
    public function create(CreatePersonData $data): PersonData;

    /**
     * Create new person and return the model instance
     */
    public function createModel(CreatePersonData $data): \App\Models\Person;

    /**
     * Update existing person
     */
    public function update(int $id, UpdatePersonData $data): PersonData;

    /**
     * Delete person
     */
    public function delete(int $id): bool;

    /**
     * Find person by ID
     */
    public function findById(int $id): ?PersonData;

    /**
     * Find person by NIK
     */
    public function findByNIK(string $nik): ?PersonData;

    /**
     * Get paginated list of people
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Mark person as deceased
     */
    public function markAsDeceased(int $id, ?Carbon $deathDate = null): PersonData;

    /**
     * Move person to new property
     */
    public function moveToProperty(int $personId, int $propertyId, ?Carbon $moveDate = null): bool;

    /**
     * End current occupancy
     */
    public function moveOut(int $personId, ?Carbon $moveOutDate = null): bool;

    /**
     * Get people in specific sub-region
     */
    public function getBySubRegion(int $subRegionId): Collection;

    /**
     * Validate NIK format and uniqueness
     */
    public function validateNIK(string $nik, ?int $excludeId = null): array;
}
