<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Car;

class CarFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Car::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'reference_url' => $this->faker->word(),
            'mark' => $this->faker->word(),
            'model' => $this->faker->word(),
            'year' => $this->faker->word(),
            'motor' => $this->faker->word(),
            'fuel_type' => $this->faker->word(),
            'gearbox' => $this->faker->word(),
            'color' => $this->faker->word(),
            'body_type' => $this->faker->word(),
            'mileage_in_km' => $this->faker->word(),
            'technical_inspection_date' => $this->faker->word(),
            'price' => $this->faker->numberBetween(-10000, 10000),
            'upload_date' => $this->faker->dateTime(),
            'specifications' => '{}',
        ];
    }
}
