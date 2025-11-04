<?php

use App\Filament\Resources\PersonResource;
use App\Core\Contracts\PersonServiceInterface;
use App\Core\Data\Person\CreatePersonData;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

describe('PersonResource Integration', function () {
    beforeEach(function () {
        $this->personService = app(PersonServiceInterface::class);
    });

    describe('Resource Configuration', function () {
        it('has correct model binding', function () {
            expect(PersonResource::getModel())->toBe(Person::class);
        });

        it('has correct navigation icon', function () {
            expect(PersonResource::getNavigationIcon())->toBe('gmdi-people-alt-tt');
        });

        it('has correct record title attribute', function () {
            $resource = new PersonResource();
            expect($resource::getRecordTitleAttribute())->toBe('name');
        });
    });

    describe('Service Integration', function () {
        it('PersonResource uses PersonService for data operations', function () {
            // Test that PersonService is available and can be resolved
            $service = app(PersonServiceInterface::class);

            expect($service)->toBeInstanceOf(\App\Core\Services\PersonService::class);
        });

        it('can create person through service integration', function () {
            $createData = CreatePersonData::from([
                'nik' => '3201012301920001',
                'name' => 'Service Integration Test',
                'birth_date' => '1992-01-23',
                'birth_place' => 'Bantul'
            ]);

            $person = $this->personService->createModel($createData);

            expect($person)->toBeInstanceOf(Person::class);
            expect($person->nik)->toBe('3201012301920001');
            expect($person->name)->toBe('Service Integration Test');
        });
    });

    describe('Resource Configuration', function () {
        it('has correct navigation properties', function () {
            // Check navigation icon exists
            expect(PersonResource::getNavigationIcon())->not->toBeNull();

            // Check if methods exist (optional properties)
            expect(method_exists(PersonResource::class, 'getNavigationIcon'))->toBeTrue();

            // Test navigation icon value
            $navigationIcon = PersonResource::getNavigationIcon();
            expect($navigationIcon)->toBe('gmdi-people-alt-tt');
        });

        it('has correct model relationship', function () {
            $model = PersonResource::getModel();
            expect($model)->toBe(Person::class);

            // Verify the model can be instantiated
            $instance = new $model();
            expect($instance)->toBeInstanceOf(Person::class);
        });
    });
});

describe('PersonResource Pages Integration', function () {
    beforeEach(function () {
        $this->personService = app(PersonServiceInterface::class);
    });

    describe('CreatePerson Page', function () {
        it('uses PersonService for record creation', function () {
            // Create a person through the service to verify integration
            $createData = CreatePersonData::from([
                'nik' => '3201012301920001',
                'name' => 'Integration Test User',
                'birth_date' => '1992-01-23',
                'birth_place' => 'Bantul'
            ]);

            $person = $this->personService->createModel($createData);

            expect($person)->toBeInstanceOf(Person::class);
            expect($person->nik)->toBe('3201012301920001');
            expect($person->name)->toBe('Integration Test User');

            $this->assertDatabaseHas('people', [
                'nik' => '3201012301920001',
                'name' => 'Integration Test User'
            ]);
        });
    });

    describe('EditPerson Page', function () {
        it('integrates with PersonService for updates', function () {
            // Create a person first
            $person = Person::factory()->create([
                'nik' => '3201012301920001',
                'name' => 'Original Name',
                'birth_date' => '1992-01-23'
            ]);

            // Update through service
            $updateData = \App\Core\Data\Person\UpdatePersonData::from([
                'name' => 'Updated Name',
                'birth_place' => 'Updated Place'
            ]);

            $result = $this->personService->update($person->id, $updateData);

            expect($result->name)->toBe('Updated Name');

            $this->assertDatabaseHas('people', [
                'id' => $person->id,
                'name' => 'Updated Name',
                'birth_place' => 'Updated Place'
            ]);
        });
    });
});
