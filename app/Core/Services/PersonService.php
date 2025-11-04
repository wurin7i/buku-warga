<?php

namespace App\Core\Services;

use App\Core\Contracts\PersonServiceInterface;
use App\Core\Data\Person\CreatePersonData;
use App\Core\Data\Person\UpdatePersonData;
use App\Core\Data\Person\PersonData;
use App\Core\Helpers\NIKHelper;
use App\Core\Exceptions\PersonNotFoundException;
use App\Core\Exceptions\DuplicateNIKException;
use App\Core\Exceptions\InvalidNIKFormatException;
use App\Models\Person;
use App\Models\Property;
use App\Models\Occupant;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PersonService implements PersonServiceInterface
{
    public function create(CreatePersonData $data): PersonData
    {
        $person = $this->createModel($data);
        return PersonData::fromModel($person);
    }

    public function createModel(CreatePersonData $data): Person
    {
        // Validate NIK using helper
        $data->validateNIK();

        // Check for duplicate
        if (Person::where('nik', $data->nik)->exists()) {
            throw new DuplicateNIKException($data->nik);
        }

        return DB::transaction(function () use ($data) {
            // Create person
            $person = Person::create([
                'nik' => $data->nik,
                'name' => $data->name,
                'kk_number' => $data->kkNumber,
                'birth_date' => $data->getEffectiveBirthDate(),
                'birth_place' => $data->birthPlace,
                'is_deceased' => false,
                'address' => $data->address,
                'sub_region' => $data->subRegion,
                'notes' => $data->notes,
            ]);

            // Create occupancy if property specified
            if ($data->propertyId) {
                $property = Property::findOrFail($data->propertyId);

                Occupant::create([
                    'person_id' => $person->id,
                    'building_id' => $property->id,
                    'is_resident' => $data->isResident ?? true,
                    'moved_in_date' => $data->movedInDate ?? now(),
                ]);

                // Refresh to load relationships
                $person->refresh();
            }

            return $person;
        });
    }

    public function update(int $id, UpdatePersonData $data): PersonData
    {
        $person = Person::find($id);

        if (!$person) {
            throw new PersonNotFoundException("Person with ID $id");
        }

        $person->update($data->getNonNullValues());
        $person->refresh();

        return PersonData::fromModel($person);
    }

    public function delete(int $id): bool
    {
        $person = Person::find($id);

        if (!$person) {
            throw new PersonNotFoundException("Person with ID $id");
        }

        return $person->delete();
    }

    public function findById(int $id): ?PersonData
    {
        $person = Person::with(['occupy.building.subRegion'])->find($id);

        return $person ? PersonData::fromModel($person) : null;
    }

    public function findByNIK(string $nik): ?PersonData
    {
        $person = Person::with(['occupy.building.subRegion'])
            ->where('nik', $nik)
            ->first();

        return $person ? PersonData::fromModel($person) : null;
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Person::with(['occupy.building.subRegion']);

        // Apply filters
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('nik', 'like', "%$search%");
            });
        }

        if (isset($filters['is_deceased'])) {
            $query->where('is_deceased', $filters['is_deceased']);
        }

        if (!empty($filters['sub_region_id'])) {
            $query->whereHas('occupy.building', function ($q) use ($filters) {
                $q->where('sub_region_id', $filters['sub_region_id']);
            });
        }

        if (!empty($filters['has_occupancy'])) {
            if ($filters['has_occupancy']) {
                $query->whereHas('occupy');
            } else {
                $query->whereDoesntHave('occupy');
            }
        }

        return $query->paginate($perPage);
    }

    public function markAsDeceased(int $id, ?Carbon $deathDate = null): PersonData
    {
        $person = Person::find($id);

        if (!$person) {
            throw new PersonNotFoundException("Person with ID $id");
        }

        return DB::transaction(function () use ($person, $deathDate) {
            // Mark as deceased
            $person->update([
                'is_deceased' => true,
                'notes' => ($person->notes ? $person->notes . "\n" : '') .
                    "Meninggal pada: " . ($deathDate ?? now())->format('d/m/Y')
            ]);

            // End current occupancy
            if ($person->occupy && !$person->occupy->moved_out_date) {
                $person->occupy->update([
                    'moved_out_date' => $deathDate ?? now()
                ]);
            }

            $person->refresh();
            return PersonData::fromModel($person);
        });
    }

    public function moveToProperty(int $personId, int $propertyId, ?Carbon $moveDate = null): bool
    {
        $person = Person::findOrFail($personId);
        $property = Property::findOrFail($propertyId);
        $moveDate = $moveDate ?? now();

        return DB::transaction(function () use ($person, $property, $moveDate) {
            // End current occupancy
            if ($person->occupy && !$person->occupy->moved_out_date) {
                $person->occupy->update([
                    'moved_out_date' => $moveDate
                ]);
            }

            // Create new occupancy
            Occupant::create([
                'person_id' => $person->id,
                'building_id' => $property->id,
                'is_resident' => true,
                'moved_in_date' => $moveDate,
            ]);

            return true;
        });
    }

    public function moveOut(int $personId, ?Carbon $moveOutDate = null): bool
    {
        $person = Person::findOrFail($personId);
        $moveOutDate = $moveOutDate ?? now();

        if (!$person->occupy || $person->occupy->moved_out_date) {
            return false; // No current occupancy
        }

        $person->occupy->update([
            'moved_out_date' => $moveOutDate
        ]);

        return true;
    }

    public function getBySubRegion(int $subRegionId): Collection
    {
        return Person::with(['occupy.building.subRegion'])
            ->whereHas('occupy.building', function ($query) use ($subRegionId) {
                $query->where('sub_region_id', $subRegionId);
            })
            ->get();
    }

    public function validateNIK(string $nik, ?int $excludeId = null): array
    {
        $errors = [];

        // Format validation using helper
        if (!NIKHelper::isValid($nik)) {
            $errors[] = 'NIK must be exactly 16 digits';
        }

        // Uniqueness validation
        $query = Person::where('nik', $nik);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            $errors[] = 'NIK already exists';
        }

        // Date validation using helper
        if (NIKHelper::isValid($nik) && !NIKHelper::extractBirthDate($nik)) {
            $errors[] = 'Invalid date in NIK';
        }

        $isFormatValid = NIKHelper::isValid($nik);
        $isUnique = !Person::where('nik', $nik)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->exists();

        return [
            'valid' => $isFormatValid,
            'unique' => $isUnique,
            'errors' => $errors
        ];
    }
}
