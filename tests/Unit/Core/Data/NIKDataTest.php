<?php

use App\Core\Helpers\NIKHelper;
use App\Core\Data\NIK\NIKData;

uses(Tests\TestCase::class);

describe('NIKData DTO Integration', function () {
    it('returns snake_case array when converted', function () {
        $nik = '3201012301920001';
        $nikData = NIKHelper::extractAll($nik);
        $array = $nikData->toArray();

        expect($array)->toHaveKeys([
            'nik',
            'is_valid',
            'region_code',
            'birth_date',
            'gender',
            'age'
        ]);
        expect($array['is_valid'])->toBeTrue();
        expect($array['region_code'])->toBe('320101');
    });

    it('can be created from array with snake_case keys', function () {
        $data = [
            'nik' => '3201012301920001',
            'is_valid' => true,
            'region_code' => '320101',
            'birth_date' => now(),
            'gender' => 'male',
            'age' => 30
        ];

        $nikData = NIKData::from($data);

        expect($nikData->nik)->toBe('3201012301920001');
        expect($nikData->isValid)->toBeTrue();
        expect($nikData->regionCode)->toBe('320101');
    });
});
