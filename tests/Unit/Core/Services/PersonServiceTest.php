<?php

use App\Core\Services\PersonService;
use App\Core\Data\Person\CreatePersonData;
use App\Core\Data\Person\UpdatePersonData;
use App\Core\Data\Person\PersonData;
use App\Core\Exceptions\DuplicateNIKException;
use App\Core\Exceptions\PersonNotFoundException;
use App\Models\Person;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

describe('PersonService', function () {
    beforeEach(function () {
        $this->personService = app(\App\Core\Contracts\PersonServiceInterface::class);
    });

    describe('create()', function () {
        it('creates a new person successfully', function () {
            $createData = CreatePersonData::from([
                'nik' => '3201012301920001',
                'name' => 'Slamet Raharja',
                'birth_date' => '1992-01-23',
                'birth_place' => 'Bantul'
            ]);

            $result = $this->personService->create($createData);

            expect($result)->toBeInstanceOf(PersonData::class);
            expect($result->nik)->toBe('3201012301920001');
            expect($result->name)->toBe('Slamet Raharja');

            // Verify in database
            $this->assertDatabaseHas('people', [
                'nik' => '3201012301920001',
                'name' => 'Slamet Raharja'
            ]);
        });

        it('throws exception for duplicate NIK', function () {
            // Create first person
            Person::create([
                'nik' => '3201012301920001',
                'name' => 'Existing Person'
            ]);

            $createData = CreatePersonData::from([
                'nik' => '3201012301920001',
                'name' => 'Slamet Raharja'
            ]);

            expect(fn() => $this->personService->create($createData))
                ->toThrow(DuplicateNIKException::class);
        });

        it('creates person with occupancy when property specified', function () {
            // Skip this test for now - need to check occupancy implementation
        })->skip();
    });

    describe('createModel()', function () {
        it('returns Person model instance', function () {
            $createData = CreatePersonData::from([
                'nik' => '3201012301920001',
                'name' => 'Slamet Raharja'
            ]);

            $result = $this->personService->createModel($createData);

            expect($result)->toBeInstanceOf(Person::class);
            expect($result->nik)->toBe('3201012301920001');
            expect($result->name)->toBe('Slamet Raharja');
            expect($result->exists)->toBeTrue();
        });
    });

    describe('findById()', function () {
        it('returns PersonData for existing person', function () {
            $person = Person::factory()->create([
                'nik' => '3201012301920001',
                'name' => 'Slamet Raharja',
                'birth_date' => '1992-01-23'
            ]);

            $result = $this->personService->findById($person->id);

            expect($result)->toBeInstanceOf(PersonData::class);
            expect($result->id)->toBe($person->id);
            expect($result->nik)->toBe('3201012301920001');
        });

        it('returns null for non-existing person', function () {
            $result = $this->personService->findById(999);
            expect($result)->toBeNull();
        });
    });

    describe('findByNIK()', function () {
        it('returns PersonData for existing NIK', function () {
            $person = Person::factory()->create([
                'nik' => '3201012301920001',
                'name' => 'Slamet Raharja',
                'birth_date' => '1992-01-23'
            ]);

            $result = $this->personService->findByNIK('3201012301920001');

            expect($result)->toBeInstanceOf(PersonData::class);
            expect($result->id)->toBe($person->id);
            expect($result->nik)->toBe('3201012301920001');
        });

        it('returns null for non-existing NIK', function () {
            $result = $this->personService->findByNIK('9999999999999999');
            expect($result)->toBeNull();
        });
    });

    describe('update()', function () {
        it('updates existing person successfully', function () {
            $person = Person::factory()->create([
                'nik' => '3201012301920001',
                'name' => 'Original Name',
                'birth_date' => '1992-01-23'
            ]);

            $updateData = UpdatePersonData::from([
                'name' => 'Updated Name',
                'birth_place' => 'Updated Place'
            ]);

            $result = $this->personService->update($person->id, $updateData);

            expect($result)->toBeInstanceOf(PersonData::class);
            expect($result->name)->toBe('Updated Name');

            $this->assertDatabaseHas('people', [
                'id' => $person->id,
                'name' => 'Updated Name',
                'birth_place' => 'Updated Place'
            ]);
        });

        it('throws exception for non-existing person', function () {
            $updateData = UpdatePersonData::from([
                'name' => 'Updated Name'
            ]);

            expect(fn() => $this->personService->update(999, $updateData))
                ->toThrow(PersonNotFoundException::class);
        });
    });

    describe('delete()', function () {
        it('deletes existing person successfully', function () {
            $person = Person::factory()->create([
                'nik' => '3201012301920001',
                'birth_date' => '1992-01-23'
            ]);

            $result = $this->personService->delete($person->id);

            expect($result)->toBeTrue();
            $this->assertDatabaseMissing('people', [
                'id' => $person->id
            ]);
        });

        it('throws exception for non-existing person', function () {
            expect(fn() => $this->personService->delete(999))
                ->toThrow(PersonNotFoundException::class);
        });
    });

    describe('validateNIK()', function () {
        it('returns valid result for unique NIK', function () {
            $result = $this->personService->validateNIK('3201012301920001');

            expect($result)->toBeArray();
            expect($result['valid'])->toBeTrue();
            expect($result['unique'])->toBeTrue();
        });

        it('returns invalid result for duplicate NIK', function () {
            Person::factory()->create([
                'nik' => '3201012301920001',
                'birth_date' => '1992-01-23'
            ]);

            $result = $this->personService->validateNIK('3201012301920001');

            expect($result['valid'])->toBeTrue();
            expect($result['unique'])->toBeFalse();
        });

        it('excludes specified ID from uniqueness check', function () {
            $person = Person::factory()->create([
                'nik' => '3201012301920001',
                'birth_date' => '1992-01-23'
            ]);

            $result = $this->personService->validateNIK('3201012301920001', $person->id);

            expect($result['valid'])->toBeTrue();
            expect($result['unique'])->toBeTrue();
        });
    });
});
