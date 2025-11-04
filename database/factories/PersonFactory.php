<?php

namespace Database\Factories;

use App\Models\Person;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Person>
 */
class PersonFactory extends Factory
{
    protected $model = Person::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $birthDate = $this->faker->dateTimeBetween('-80 years', '-18 years');
        $isFemale = $this->faker->boolean();

        // Generate NIK based on birth date and gender
        $regionCode = '320101'; // Default region code
        $dateCode = $birthDate->format('dmy');
        if ($isFemale) {
            $dateCode = str_pad((int)$dateCode + 400000, 6, '0', STR_PAD_LEFT);
        }
        $sequenceCode = str_pad($this->faker->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);
        $nik = $regionCode . $dateCode . $sequenceCode;

        return [
            'nik' => $nik,
            'name' => $this->faker->name(),
            'kk_number' => $this->faker->numerify('################'),
            'birth_date' => $birthDate,
            'birth_place' => $this->faker->city(),
            'is_deceased' => false,
            'address' => $this->faker->address(),
            'sub_region' => $this->faker->numerify('###/###'),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the person is deceased.
     */
    public function deceased(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_deceased' => true,
        ]);
    }

    /**
     * Indicate that the person is male.
     */
    public function male(): static
    {
        return $this->state(function (array $attributes) {
            $birthDate = Carbon::parse($attributes['birth_date']);
            $regionCode = '320101';
            $dateCode = $birthDate->format('dmy');
            $sequenceCode = str_pad($this->faker->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);

            return [
                'nik' => $regionCode . $dateCode . $sequenceCode,
            ];
        });
    }

    /**
     * Indicate that the person is female.
     */
    public function female(): static
    {
        return $this->state(function (array $attributes) {
            $birthDate = Carbon::parse($attributes['birth_date']);
            $regionCode = '320101';
            $dateCode = str_pad((int)$birthDate->format('dmy') + 400000, 6, '0', STR_PAD_LEFT);
            $sequenceCode = str_pad($this->faker->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT);

            return [
                'nik' => $regionCode . $dateCode . $sequenceCode,
            ];
        });
    }
}
