<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition()
    {
        $branch = ['main', 'production', 'stagging', 'development'];

        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'url' => $this->faker->url,
            'created_by' => 1,
            'updated_by' => 1,
            'sub_projects' => 0,
            'development_pipeline' => $this->faker->word,
            'staging_pipeline' => $this->faker->word,
            'production_Pipeline' => $this->faker->word,
            // 'branch' => $branch[array_rand($branch)],
        ];
    }
}
