<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title'         => 'Project Title',
            'description'   => 'It is very Important Task',
            'project_id'    => '1',
            'priority'      => '0',
            'deadline'      => now(),
            'status'        => '7',
            'assignedAt'    => null,
            'assignedBy'    => '0',
            'completedAt'   => null,
            'completedBy'   => '0',
            'taskEndedAt'   => null,
            'taskEndedBy'   => '0',
            'created_by'    => '1',
            'updated_by'    => '1',
        ];
    }
}
