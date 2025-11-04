<?php

namespace Database\Factories;

use App\Models\Area;
use App\Enums\AreaType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Area>
 */
class AreaFactory extends Factory
{
    protected $model = Area::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->city(),
            'type' => AreaType::SubRegion,
        ];
    }

    /**
     * Create a village (kalurahan)
     */
    public function village(): static
    {
        return $this->state(fn() => [
            'name' => $this->faker->city() . ' Village',
            'type' => AreaType::SubRegion,
            'parent_id' => null,
        ]);
    }

    /**
     * Create an RW (padukuhan)
     */
    public function rw(): static
    {
        return $this->state(fn() => [
            'name' => 'RW ' . $this->faker->numberBetween(1, 20),
            'type' => AreaType::Cluster,
        ]);
    }

    /**
     * Create an RT (rukun tetangga)
     */
    public function rt(): static
    {
        return $this->state(fn() => [
            'name' => 'RT ' . $this->faker->numberBetween(1, 10),
            'type' => AreaType::Cluster,
        ]);
    }
}
