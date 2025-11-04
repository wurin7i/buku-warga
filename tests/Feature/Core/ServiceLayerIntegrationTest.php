<?php

use App\Core\Contracts\PersonServiceInterface;
use App\Core\Services\PersonService;
use App\Core\Data\Person\CreatePersonData;
use App\Core\Data\Person\UpdatePersonData;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(Tests\TestCase::class, RefreshDatabase::class);

describe('Service Layer Integration', function () {
    beforeEach(function () {
        $this->personService = app(PersonServiceInterface::class);
    });

    describe('Dependency Injection Integration', function () {
        it('resolves PersonService through container', function () {
            $service = app(PersonServiceInterface::class);

            expect($service)->toBeInstanceOf(PersonService::class);
            expect($service)->toBeInstanceOf(PersonServiceInterface::class);
        });

        it('creates new instances correctly', function () {
            $service1 = app(PersonServiceInterface::class);
            $service2 = app(PersonServiceInterface::class);

            // Services should be the same type but may be different instances
            expect($service1)->toBeInstanceOf(PersonServiceInterface::class);
            expect($service2)->toBeInstanceOf(PersonServiceInterface::class);
            expect($service1::class)->toBe($service2::class);
        });
    });

    describe('Service-Model Integration', function () {
        it('service creates models correctly', function () {
            $createData = CreatePersonData::from([
                'nik' => '3201012301920001',
                'name' => 'Service Model Test',
                'birth_date' => '1992-01-23',
                'birth_place' => 'Bantul'
            ]);

            $person = $this->personService->createModel($createData);

            // Test model instance
            expect($person)->toBeInstanceOf(Person::class);
            expect($person->exists)->toBeTrue();

            // Test attributes
            expect($person->nik)->toBe('3201012301920001');
            expect($person->name)->toBe('Service Model Test');

            // Test database persistence
            $this->assertDatabaseHas('people', [
                'id' => $person->id,
                'nik' => '3201012301920001'
            ]);
        });

        it('service updates models correctly', function () {
            // Create via service
            $createData = CreatePersonData::from([
                'nik' => '3201012301920002',
                'name' => 'Original Service Name',
                'birth_date' => '1992-01-23',
                'birth_place' => 'Bantul'
            ]);

            $person = $this->personService->create($createData);

            // Update via service
            $updateData = UpdatePersonData::from([
                'name' => 'Updated Service Name',
                'address' => 'Service Address 123'
            ]);

            $updatedPerson = $this->personService->update($person->id, $updateData);

            // Test update results
            expect($updatedPerson->name)->toBe('Updated Service Name');
            expect($updatedPerson->address)->toBe('Service Address 123');
            expect($updatedPerson->nik)->toBe('3201012301920002'); // Should not change

            // Test database state
            $this->assertDatabaseHas('people', [
                'id' => $person->id,
                'name' => 'Updated Service Name',
                'address' => 'Service Address 123'
            ]);
        });
    });

    describe('Service-DTO Integration', function () {
        it('handles DTO validation in service layer', function () {
            // Test invalid NIK through service - actually this may not throw since our validation is in service layer
            $invalidData = CreatePersonData::from([
                'nik' => 'invalid-nik',
                'name' => 'Test User',
                'birth_date' => '1992-01-23',
                'birth_place' => 'Bantul'
            ]);

            // The validation happens in service layer, throws custom exception
            expect(fn() => $this->personService->create($invalidData))
                ->toThrow(\App\Core\Exceptions\InvalidNIKFormatException::class);
        });

        it('processes DTO transformations correctly', function () {
            $createData = CreatePersonData::from([
                'nik' => '3201012301920003',
                'name' => 'DTO Transform Test',
                'birth_date' => '1992-01-23',
                'birth_place' => 'Bantul'
            ]);

            $person = $this->personService->create($createData);

            // Verify DTO data is properly transformed to model
            expect($person->nik)->toBe($createData->nik);
            expect($person->name)->toBe($createData->name);
            expect($person->birthPlace)->toBe($createData->birthPlace);
            expect($person->birthDate->format('Y-m-d'))->toBe($createData->birthDate->format('Y-m-d'));
        });
    });

    describe('Service-Helper Integration', function () {
        it('integrates with NIKHelper for validation', function () {
            // Test with valid NIK
            $validData = CreatePersonData::from([
                'nik' => '3201012301920004',
                'name' => 'Valid NIK User',
                'birth_date' => '1992-01-23',
                'birth_place' => 'Bantul'
            ]);

            $person = $this->personService->create($validData);
            expect($person)->toBeInstanceOf(\App\Core\Data\Person\PersonData::class);

            // Test duplicate NIK validation
            expect(fn() => $this->personService->create($validData))
                ->toThrow(\App\Core\Exceptions\DuplicateNIKException::class);
        });

        it('uses NIKHelper for gender extraction', function () {
            // Male NIK (day <= 31)
            $maleData = CreatePersonData::from([
                'nik' => '3201012301920005', // Day: 23 (male)
                'name' => 'Male User',
                'birth_date' => '1992-01-23',
                'birth_place' => 'Bantul'
            ]);

            $malePerson = $this->personService->create($maleData);
            // Gender is extracted from NIK via helper method
            expect($malePerson->getGender())->toBe(\WuriN7i\IdRefs\Enums\Gender::Male);

            // Female NIK (day > 31, so day - 40)
            $femaleData = CreatePersonData::from([
                'nik' => '3201016301920006', // Day: 63 -> 63-40=23 (female)
                'name' => 'Female User',
                'birth_date' => '1992-01-23',
                'birth_place' => 'Bantul'
            ]);

            $femalePerson = $this->personService->create($femaleData);
            expect($femalePerson->getGender())->toBe(\WuriN7i\IdRefs\Enums\Gender::Female);
        });
    });

    describe('Error Handling Integration', function () {
        it('handles service layer exceptions properly', function () {
            // Test non-existent person update
            $updateData = UpdatePersonData::from([
                'name' => 'Non-existent Update'
            ]);

            expect(fn() => $this->personService->update(99999, $updateData))
                ->toThrow(\App\Core\Exceptions\PersonNotFoundException::class);
        });

        it('validates business rules across layers', function () {
            // Create initial person
            Person::factory()->create(['nik' => '3201012301920007']);

            // Try to create duplicate via service
            $duplicateData = CreatePersonData::from([
                'nik' => '3201012301920007',
                'name' => 'Duplicate User',
                'birth_date' => '1992-01-23',
                'birth_place' => 'Bantul'
            ]);

            expect(fn() => $this->personService->create($duplicateData))
                ->toThrow(\App\Core\Exceptions\DuplicateNIKException::class);
        });
    });

    describe('Transaction Integration', function () {
        it('handles database transactions properly', function () {
            $initialCount = Person::count();

            // Create person through service
            $createData = CreatePersonData::from([
                'nik' => '3201012301920008',
                'name' => 'Transaction Test',
                'birth_date' => '1992-01-23',
                'birth_place' => 'Bantul'
            ]);

            $person = $this->personService->create($createData);

            // Verify transaction committed
            expect(Person::count())->toBe($initialCount + 1);

            // Verify in fresh database query
            $freshPerson = Person::find($person->id);
            expect($freshPerson)->not->toBeNull();
            expect($freshPerson->nik)->toBe('3201012301920008');
        });
    });
});
