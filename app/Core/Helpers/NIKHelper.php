<?php

namespace App\Core\Helpers;

use App\Core\Data\NIK\NIKData;
use Carbon\Carbon;
use App\Core\Exceptions\InvalidNIKFormatException;
use WuriN7i\IdRefs\Enums\Gender;

class NIKHelper
{
    /**
     * Validate NIK format
     */
    public static function isValid(string $nik): bool
    {
        return preg_match('/^\d{16}$/', $nik) === 1;
    }

    /**
     * Validate NIK and throw exception if invalid
     */
    public static function validate(string $nik): void
    {
        if (!self::isValid($nik)) {
            throw new InvalidNIKFormatException($nik);
        }
    }

    /**
     * Extract region code from NIK (6 digits pertama)
     */
    public static function getRegionCode(string $nik): ?string
    {
        if (!self::isValid($nik)) {
            return null;
        }

        return substr($nik, 0, 6);
    }

    /**
     * Extract birth date from NIK
     */
    public static function extractBirthDate(string $nik): ?Carbon
    {
        if (!self::isValid($nik)) {
            return null;
        }

        $dateNumber = substr($nik, 6, 6);

        // Adjust for female (subtract 40 from date)
        if (intval($dateNumber) > 400000) {
            $dateNumber = str_pad(intval($dateNumber) - 400000, 6, '0', STR_PAD_LEFT);
        }

        try {
            $birthDate = Carbon::createFromFormat('dmy', $dateNumber);

            // If birth date is in future, subtract 100 years
            if ($birthDate->isFuture()) {
                $birthDate->subYears(100);
            }

            return $birthDate;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Extract gender from NIK
     */
    public static function extractGender(string $nik): ?Gender
    {
        if (!self::isValid($nik)) {
            return null;
        }

        $dateNumber = substr($nik, 6, 6);
        return intval($dateNumber) > 400000 ? Gender::Female : Gender::Male;
    }

    /**
     * Check if NIK belongs to female
     */
    public static function isFemale(string $nik): bool
    {
        return self::extractGender($nik) === Gender::Female;
    }

    /**
     * Check if NIK belongs to male
     */
    public static function isMale(string $nik): bool
    {
        return self::extractGender($nik) === Gender::Male;
    }

    /**
     * Get age from NIK
     */
    public static function calculateAge(string $nik): ?int
    {
        $birthDate = self::extractBirthDate($nik);

        return $birthDate ? $birthDate->age : null;
    }

    /**
     * Format NIK for display (add dashes for readability)
     */
    public static function format(string $nik): string
    {
        if (!self::isValid($nik)) {
            return $nik;
        }

        return substr($nik, 0, 6) . '-' . substr($nik, 6, 6) . '-' . substr($nik, 12, 4);
    }

    /**
     * Extract all NIK information as DTO
     */
    public static function extractAll(string $nik): NIKData
    {
        return NIKData::fromNIK($nik);
    }

    /**
     * Mask NIK for privacy (show only first 6 and last 4 digits)
     */
    public static function mask(string $nik): string
    {
        if (!self::isValid($nik)) {
            return $nik;
        }

        return substr($nik, 0, 6) . '******' . substr($nik, -4);
    }

    /**
     * Compare two NIKs for potential duplicates or similarity
     */
    public static function isSimilar(string $nik1, string $nik2): bool
    {
        if (!self::isValid($nik1) || !self::isValid($nik2)) {
            return false;
        }

        // Same region and birth date indicates potential duplicate
        return substr($nik1, 0, 12) === substr($nik2, 0, 12);
    }
}
