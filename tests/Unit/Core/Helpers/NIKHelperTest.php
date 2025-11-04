<?php

use App\Core\Helpers\NIKHelper;
use App\Core\Data\NIK\NIKData;
use App\Core\Exceptions\InvalidNIKFormatException;
use Carbon\Carbon;
use WuriN7i\IdRefs\Enums\Gender;

describe('NIKHelper', function () {
    describe('isValid()', function () {
        it('returns true for valid NIK', function () {
            $validNik = '3201012301920001';
            expect(NIKHelper::isValid($validNik))->toBeTrue();
        });

        it('returns false for invalid NIK formats', function () {
            $invalidNiks = [
                '123456789',          // Too short
                '32010123019200011',  // Too long
                '320101230192000a',   // Contains letter
                '',                   // Empty
                '3201-0123-019200-01' // Contains dashes
            ];

            foreach ($invalidNiks as $nik) {
                expect(NIKHelper::isValid($nik))->toBeFalse("NIK {$nik} should be invalid");
            }
        });
    });

    describe('validate()', function () {
        it('throws exception for invalid NIK', function () {
            expect(fn() => NIKHelper::validate('invalid-nik'))
                ->toThrow(InvalidNIKFormatException::class);
        });

        it('does not throw exception for valid NIK', function () {
            expect(fn() => NIKHelper::validate('3201012301920001'))
                ->not->toThrow(InvalidNIKFormatException::class);
        });
    });

    describe('getRegionCode()', function () {
        it('extracts region code from valid NIK', function () {
            $nik = '3201012301920001';
            $regionCode = NIKHelper::getRegionCode($nik);
            expect($regionCode)->toBe('320101');
        });

        it('returns null for invalid NIK', function () {
            $invalidNik = '123';
            $regionCode = NIKHelper::getRegionCode($invalidNik);
            expect($regionCode)->toBeNull();
        });
    });

    describe('extractBirthDate()', function () {
        it('extracts birth date for male correctly', function () {
            $nik = '3201012301920001'; // 23 Jan 1992, Male
            $birthDate = NIKHelper::extractBirthDate($nik);

            expect($birthDate)->toBeInstanceOf(Carbon::class);
            expect($birthDate->format('Y-m-d'))->toBe('1992-01-23');
        });

        it('extracts birth date for female correctly', function () {
            $nik = '3201016301920001'; // 23 Jan 1992, Female (63 = 23 + 40)
            $birthDate = NIKHelper::extractBirthDate($nik);

            expect($birthDate)->toBeInstanceOf(Carbon::class);
            expect($birthDate->format('Y-m-d'))->toBe('1992-01-23');
        });

        it('adjusts century for future dates', function () {
            $nik = '3201012301300001'; // 23 Jan 2030 -> should become 1930
            $birthDate = NIKHelper::extractBirthDate($nik);

            expect($birthDate)->toBeInstanceOf(Carbon::class);
            expect($birthDate->format('Y-m-d'))->toBe('1930-01-23');
        });

        it('returns null for invalid NIK', function () {
            $invalidNik = '123';
            expect(NIKHelper::extractBirthDate($invalidNik))->toBeNull();
        });
    });

    describe('extractGender()', function () {
        it('extracts male gender correctly', function () {
            $nik = '3201012301920001'; // Male (date: 23)
            $gender = NIKHelper::extractGender($nik);

            expect($gender)->toBeInstanceOf(Gender::class);
            expect($gender)->toBe(Gender::Male);
        });

        it('extracts female gender correctly', function () {
            $nik = '3201016301920001'; // Female (date: 63 = 23 + 40)
            $gender = NIKHelper::extractGender($nik);

            expect($gender)->toBeInstanceOf(Gender::class);
            expect($gender)->toBe(Gender::Female);
        });

        it('returns null for invalid NIK', function () {
            $invalidNik = '123';
            expect(NIKHelper::extractGender($invalidNik))->toBeNull();
        });
    });

    describe('isFemale() and isMale()', function () {
        it('correctly identifies female NIK', function () {
            $femaleNik = '3201016301920001';
            $maleNik = '3201012301920001';

            expect(NIKHelper::isFemale($femaleNik))->toBeTrue();
            expect(NIKHelper::isFemale($maleNik))->toBeFalse();
        });

        it('correctly identifies male NIK', function () {
            $maleNik = '3201012301920001';
            $femaleNik = '3201016301920001';

            expect(NIKHelper::isMale($maleNik))->toBeTrue();
            expect(NIKHelper::isMale($femaleNik))->toBeFalse();
        });
    });

    describe('calculateAge()', function () {
        it('calculates correct age from NIK', function () {
            $nik = '3201012301920001'; // 23 Jan 1992
            $age = NIKHelper::calculateAge($nik);

            $expectedAge = Carbon::createFromFormat('Y-m-d', '1992-01-23')->age;
            expect($age)->toBe($expectedAge);
        });

        it('returns null for invalid NIK', function () {
            $invalidNik = '123';
            expect(NIKHelper::calculateAge($invalidNik))->toBeNull();
        });
    });

    describe('format()', function () {
        it('formats NIK with dashes', function () {
            $nik = '3201012301920001';
            $formatted = NIKHelper::format($nik);
            expect($formatted)->toBe('320101-230192-0001');
        });

        it('returns original string for invalid NIK', function () {
            $invalidNik = '123';
            expect(NIKHelper::format($invalidNik))->toBe($invalidNik);
        });
    });

    describe('mask()', function () {
        it('masks NIK for privacy', function () {
            $nik = '3201012301920001';
            $masked = NIKHelper::mask($nik);
            expect($masked)->toBe('320101******0001');
        });

        it('returns original string for invalid NIK', function () {
            $invalidNik = '123';
            expect(NIKHelper::mask($invalidNik))->toBe($invalidNik);
        });
    });

    describe('isSimilar()', function () {
        it('identifies similar NIKs with same region and birth date', function () {
            $nik1 = '3201012301920001';
            $nik2 = '3201012301920002'; // Same region and birth date, different sequence

            expect(NIKHelper::isSimilar($nik1, $nik2))->toBeTrue();
        });

        it('identifies different NIKs with different birth dates', function () {
            $nik1 = '3201012301920001';
            $nik3 = '3201012401920001'; // Different birth date

            expect(NIKHelper::isSimilar($nik1, $nik3))->toBeFalse();
        });

        it('returns false for invalid NIKs', function () {
            $validNik = '3201012301920001';
            $invalidNik = '123';

            expect(NIKHelper::isSimilar($validNik, $invalidNik))->toBeFalse();
            expect(NIKHelper::isSimilar($invalidNik, $validNik))->toBeFalse();
        });
    });

    describe('extractAll()', function () {
        it('returns complete NIKData for valid NIK', function () {
            $nik = '3201012301920001';
            $nikData = NIKHelper::extractAll($nik);

            expect($nikData)->toBeInstanceOf(NIKData::class);
            expect($nikData->nik)->toBe($nik);
            expect($nikData->isValid)->toBeTrue();
            expect($nikData->regionCode)->toBe('320101');
            expect($nikData->gender)->toBe(Gender::Male);
            expect($nikData->birthDate->format('Y-m-d'))->toBe('1992-01-23');
            expect($nikData->age)->toBeInt();
        });

        it('returns NIKData with null values for invalid NIK', function () {
            $invalidNik = '123';
            $nikData = NIKHelper::extractAll($invalidNik);

            expect($nikData)->toBeInstanceOf(NIKData::class);
            expect($nikData->nik)->toBe($invalidNik);
            expect($nikData->isValid)->toBeFalse();
            expect($nikData->regionCode)->toBeNull();
            expect($nikData->gender)->toBeNull();
            expect($nikData->birthDate)->toBeNull();
            expect($nikData->age)->toBeNull();
        });
    });
});
