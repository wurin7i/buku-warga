<?php

use App\Core\Data\Person\CreatePersonData;
use App\Core\Data\Person\UpdatePersonData;
use App\Core\Contracts\PersonServiceInterface;
use App\Models\Person;
use App\Models\Area;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

describe('Person Complete Workflow Feature', function () {
    beforeEach(function () {
        $this->personService = app(PersonServiceInterface::class);
    });

    describe('Person Creation Workflow', function () {
        it('creates person with complete NIK processing workflow', function () {
            $createData = CreatePersonData::from([
                'nik' => '3201012301920001',
                'name' => 'Workflow Test User',
                'birth_date' => '1992-01-23',
                'birth_place' => 'Kota Yogyakarta'
            ]);

            // Test the complete creation workflow
            $person = $this->personService->create($createData);

            // Verify person creation
            expect($person->nik)->toBe('3201012301920001');
            expect($person->name)->toBe('Workflow Test User');
            expect($person->birthDate->format('Y-m-d'))->toBe('1992-01-23');
            expect($person->birthPlace)->toBe('Kota Yogyakarta');

            // Verify NIK processing results (auto-extracted)
            expect($person->getGender())->toBe(\WuriN7i\IdRefs\Enums\Gender::Male);

            // Verify database persistence
            $this->assertDatabaseHas('people', [
                'nik' => '3201012301920001',
                'name' => 'Workflow Test User',
                'birth_place' => 'Kota Yogyakarta'
            ]);
        });

        it('creates person with complete validation workflow', function () {
            $createData = CreatePersonData::from([
                'nik' => '3201012301920002',
                'name' => 'Complete User',
                'birth_date' => '1985-05-15',
                'birth_place' => 'Kulonprogo',
                'address' => 'Jl. Test No. 123',
                'notes' => 'Test user notes'
            ]);

            $person = $this->personService->create($createData);

            // Verify all fields are properly set
            expect($person->nik)->toBe('3201012301920002');
            expect($person->name)->toBe('Complete User');
            expect($person->birthPlace)->toBe('Kulonprogo');
            expect($person->address)->toBe('Jl. Test No. 123');
            expect($person->notes)->toBe('Test user notes');

            // Verify persistence
            $this->assertDatabaseHas('people', [
                'nik' => '3201012301920002',
                'name' => 'Complete User',
                'birth_place' => 'Kulonprogo',
                'address' => 'Jl. Test No. 123'
            ]);
        });

        it('handles duplicate NIK validation in workflow', function () {
            // Create first person
            $person1 = Person::factory()->create(['nik' => '3201012301920003']);

            // Attempt to create duplicate
            $createData = CreatePersonData::from([
                'nik' => '3201012301920003', // Same NIK
                'name' => 'Duplicate User',
                'birth_date' => '1990-01-01',
                'birth_place' => 'Bantul'
            ]);

            expect(fn() => $this->personService->create($createData))
                ->toThrow(\App\Core\Exceptions\DuplicateNIKException::class);
        });
    });

    describe('Person Update Workflow', function () {
        it('updates person with validation workflow', function () {
            // Create initial person
            $person = Person::factory()->create([
                'nik' => '3201012301920004',
                'name' => 'Original Name',
                'birth_place' => 'Original Place'
            ]);

            // Update through service
            $updateData = UpdatePersonData::from([
                'name' => 'Updated Name',
                'birth_place' => 'Updated Place',
                'address' => 'Updated Address 789'
            ]);

            $updatedPerson = $this->personService->update($person->id, $updateData);

            // Verify updates
            expect($updatedPerson->name)->toBe('Updated Name');
            expect($updatedPerson->birthPlace)->toBe('Updated Place');
            expect($updatedPerson->address)->toBe('Updated Address 789');
            expect($updatedPerson->nik)->toBe('3201012301920004'); // NIK should remain unchanged

            // Verify database
            $this->assertDatabaseHas('people', [
                'id' => $person->id,
                'name' => 'Updated Name',
                'birth_place' => 'Updated Place',
                'address' => 'Updated Address 789'
            ]);
        });

        it('handles partial updates correctly', function () {
            // Create person
            $person = Person::factory()->create([
                'nik' => '3201012301920008',
                'name' => 'Original Name',
                'birth_place' => 'Original Place',
                'address' => 'Original Address'
            ]);

            // Partial update (only some fields)
            $updateData = UpdatePersonData::from([
                'name' => 'Updated Name',
                'address' => 'New Address 456'
                // birth_place and notes not updated
            ]);

            $updatedPerson = $this->personService->update($person->id, $updateData);

            // Verify updated fields
            expect($updatedPerson->name)->toBe('Updated Name');
            expect($updatedPerson->address)->toBe('New Address 456');

            // Verify unchanged fields remain
            expect($updatedPerson->birthPlace)->toBe('Original Place');
            expect($updatedPerson->nik)->toBe('3201012301920008'); // NIK never changes
        });
    });

    describe('Search and Filter Workflow', function () {
        it('searches persons by various criteria', function () {
            // Create test persons
            Person::factory()->create([
                'nik' => '3201012301920005',
                'name' => 'Slamet Raharja',
                'address' => 'Slamet Address'
            ]);

            Person::factory()->create([
                'nik' => '3201012301920006',
                'name' => 'Sri Rahayu',
                'address' => 'Sri Address'
            ]);

            // Test search by name
            $johnResult = Person::where('name', 'like', '%Slamet%')->first();
            expect($johnResult)->not->toBeNull();
            expect($johnResult->name)->toBe('Slamet Raharja');

            // Test search by NIK
            $nikResult = Person::where('nik', '3201012301920006')->first();
            expect($nikResult)->not->toBeNull();
            expect($nikResult->name)->toBe('Sri Rahayu');

            // Test search by address
            $addressResult = Person::where('address', 'like', '%Sri%')->first();
            expect($addressResult)->not->toBeNull();
            expect($addressResult->nik)->toBe('3201012301920006');
        });

        it('filters persons by birth year from NIK', function () {
            // Create persons with different birth years
            Person::factory()->create([
                'nik' => '3201011234920001', // Born in 1992
                'name' => 'Person 1992'
            ]);

            Person::factory()->create([
                'nik' => '3201011234850001', // Born in 1985
                'name' => 'Person 1985'
            ]);

            // Test filtering by birth year (extracted from NIK)
            $person1992 = Person::where('nik', 'like', '%92%')->first();
            $person1985 = Person::where('nik', 'like', '%85%')->first();

            expect($person1992->name)->toBe('Person 1992');
            expect($person1985->name)->toBe('Person 1985');
        });
    });

    describe('Data Integrity Workflow', function () {
        it('maintains data integrity during operations', function () {
            // Create person
            $person = Person::factory()->create([
                'nik' => '3201012301920009',
                'name' => 'Integrity Test'
            ]);

            $personId = $person->id;

            // Verify creation
            $this->assertDatabaseHas('people', [
                'id' => $personId,
                'nik' => '3201012301920009'
            ]);

            // Delete person
            $person->delete();

            // Verify deletion
            $this->assertDatabaseMissing('people', ['id' => $personId]);
        });

        it('validates business rules in complete workflow', function () {
            // Test NIK format validation via service
            $invalidData = CreatePersonData::from([
                'nik' => '123', // Invalid format
                'name' => 'Invalid NIK User',
                'birth_date' => '1990-01-01',
                'birth_place' => 'Bantul'
            ]);

            expect(fn() => $this->personService->create($invalidData))
                ->toThrow(\App\Core\Exceptions\InvalidNIKFormatException::class);

            // Test business validation works
            expect($invalidData->nik)->toBe('123'); // Data creation works
            expect($invalidData->name)->toBe('Invalid NIK User');
        });
    });
});
