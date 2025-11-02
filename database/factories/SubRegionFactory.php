<?php

namespace Database\Factories;

use App\Models\SubRegion;
use App\Enums\SubRegionLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubRegion>
 */
class SubRegionFactory extends Factory
{
    protected $model = SubRegion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->city(),
            'level' => SubRegionLevel::VILLAGE,
        ];
    }

    /**
     * Create a village level SubRegion
     */
    public function village(): static
    {
        return $this->state([
            'level' => SubRegionLevel::VILLAGE,
        ]);
    }

    /**
     * Create a RW level SubRegion
     */
    public function rw(): static
    {
        return $this->state([
            'level' => SubRegionLevel::RW,
        ]);
    }

    /**
     * Create a RT level SubRegion
     */
    public function rt(): static
    {
        return $this->state([
            'level' => SubRegionLevel::RT,
        ]);
    }

    /**
     * Set parent for the SubRegion
     */
    public function withParent(SubRegion $parent): static
    {
        return $this->state([
            'parent_id' => $parent->id,
        ]);
    }
}
