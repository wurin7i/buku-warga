<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\SubRegion;
use App\Models\Cluster;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => $this->faker->numerify('Rumah ##'),
            'sub_region_id' => SubRegion::factory(),
            'cluster_id' => null,
            'owner_id' => null,
        ];
    }

    /**
     * Indicate that the property has an owner.
     */
    public function withOwner(): static
    {
        return $this->state(fn(array $attributes) => [
            'owner_id' => \App\Models\Person::factory(),
        ]);
    }

    /**
     * Indicate that the property belongs to a cluster.
     */
    public function withCluster(): static
    {
        return $this->state(fn(array $attributes) => [
            'cluster_id' => Cluster::factory(),
        ]);
    }
}
