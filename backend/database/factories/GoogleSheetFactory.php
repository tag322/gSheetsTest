<?php

namespace Database\Factories;

use App\Models\GoogleSheet;

use Illuminate\Database\Eloquent\Factories\Factory;

class GoogleSheetFactory extends Factory
{

    protected $model = GoogleSheet::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'status' => rand(0,1) ? 'Allowed' : 'Prohibited',
            'commentary' => $this->faker->sentence(),
        ];

    }
}
