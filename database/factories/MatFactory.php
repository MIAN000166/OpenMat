<?php

namespace Database\Factories;

use App\Models\Admin\Mat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin\Mat>
 */
class MatFactory extends Factory
{
    protected $model = Mat::class;
    /**
     *
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'region' => $this->faker->name(),
            'state' => $this->faker->name(),
            'location' => $this->faker->name(),
            'phone_number' => $this->faker->phoneNumber(),
            'website' => $this->faker->url(),
            'physical_address' => $this->faker->address(),
            'open_mat_time' => $this->faker->time(),
            'open_mat_day' => $this->faker->time(),
            'link_to_waiver' => $this->faker->url(),
            'other_info' => $this->faker->name(),
            'user_id' => 1,
        ];
    }
}
