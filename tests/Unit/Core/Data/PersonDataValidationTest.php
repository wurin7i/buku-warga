<?php

use App\Core\Data\Person\CreatePersonData;
use App\Core\Data\Person\UpdatePersonData;

uses(Tests\TestCase::class);

describe('CreatePersonData', function () {
    describe('functionality', function () {
        it('accepts valid data', function () {
            $data = CreatePersonData::from([
                'nik' => '3201012301920001',
                'name' => 'Slamet Raharja',
                'birth_place' => 'Bantul',
                'address' => 'Jl. Test No. 123'
            ]);

            expect($data->nik)->toBe('3201012301920001');
            expect($data->name)->toBe('Slamet Raharja');
            expect($data->birthPlace)->toBe('Bantul');
            expect($data->address)->toBe('Jl. Test No. 123');
        });
    });

    describe('NIK validation method', function () {
        it('validates NIK using helper', function () {
            $data = CreatePersonData::from([
                'nik' => '3201012301920001',
                'name' => 'Slamet Raharja'
            ]);

            // Should not throw exception for valid NIK
            $data->validateNIK();
            expect(true)->toBeTrue(); // If no exception thrown, this passes
        });

        it('validates NIK with valid format', function () {
            $data = CreatePersonData::from([
                'nik' => '1234567890123456', // Valid format
                'name' => 'Slamet Raharja'
            ]);

            // Should not throw exception for valid format
            $data->validateNIK();
            expect(true)->toBeTrue(); // If no exception thrown, this passes
        });
    });

    describe('effective birth date', function () {
        it('uses provided birth_date if available', function () {
            $birthDate = '1990-05-15';
            $data = CreatePersonData::from([
                'nik' => '3201012301920001',
                'name' => 'Slamet Raharja',
                'birth_date' => $birthDate
            ]);

            $effectiveDate = $data->getEffectiveBirthDate();
            expect($effectiveDate->format('Y-m-d'))->toBe($birthDate);
        });

        it('extracts birth_date from NIK if not provided', function () {
            $data = CreatePersonData::from([
                'nik' => '3201012301920001', // 23 Jan 1992
                'name' => 'Slamet Raharja'
            ]);

            $effectiveDate = $data->getEffectiveBirthDate();
            expect($effectiveDate->format('Y-m-d'))->toBe('1992-01-23');
        });
    });

    describe('NIK info', function () {
        it('returns NIKData from helper', function () {
            $data = CreatePersonData::from([
                'nik' => '3201012301920001',
                'name' => 'Slamet Raharja'
            ]);

            $nikInfo = $data->getNIKInfo();
            expect($nikInfo)->toBeInstanceOf(\App\Core\Data\NIK\NIKData::class);
            expect($nikInfo->nik)->toBe('3201012301920001');
            expect($nikInfo->isValid)->toBeTrue();
        });
    });
});

describe('UpdatePersonData', function () {
    describe('functionality', function () {
        it('accepts partial update data', function () {
            $data = UpdatePersonData::from([
                'name' => 'Updated Name'
            ]);

            expect($data->name)->toBe('Updated Name');
            expect($data->birthPlace)->toBeNull();
        });

        it('accepts full update data', function () {
            $data = UpdatePersonData::from([
                'name' => 'Updated Name',
                'birth_place' => 'Updated Place',
                'address' => 'Updated Address',
                'notes' => 'Updated Notes'
            ]);

            expect($data->name)->toBe('Updated Name');
            expect($data->birthPlace)->toBe('Updated Place');
            expect($data->address)->toBe('Updated Address');
            expect($data->notes)->toBe('Updated Notes');
        });
    });

    describe('non-null values', function () {
        it('returns only non-null values for update', function () {
            $data = UpdatePersonData::from([
                'name' => 'Updated Name',
                'birth_place' => null,
                'address' => 'Updated Address'
            ]);

            $nonNullValues = $data->getNonNullValues();

            expect($nonNullValues)->toHaveKey('name');
            expect($nonNullValues)->toHaveKey('address');
            expect($nonNullValues)->not->toHaveKey('birth_place');
            expect($nonNullValues['name'])->toBe('Updated Name');
            expect($nonNullValues['address'])->toBe('Updated Address');
        });
    });
});
