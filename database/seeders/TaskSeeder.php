<?php

namespace Database\Seeders;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    public function run()
    {
        $tasks = [
            [
                'title'       => 'Initial project setup',
                'description' => 'Set up the project structure and install dependencies.',
                'project_id'  => 1,
                'priority'    => '1',
                'deadline'    => Carbon::now()->addDays(7),
                'status'      => 1,
                'createdBy'   => 1,
                'updatedBy'   => 1,
            ],
            [
                'title'       => 'Design database schema',
                'description' => 'Create the ERD and define all table structures.',
                'project_id'  => 1,
                'priority'    => '1',
                'deadline'    => Carbon::now()->addDays(14),
                'status'      => 2,
                'assignedAt'  => Carbon::now(),
                'assignedBy'  => 1,
                'createdBy'   => 1,
                'updatedBy'   => 2,
            ],
            [
                'title'       => 'Build REST API endpoints',
                'description' => 'Develop all required API routes and controllers.',
                'project_id'  => 1,
                'priority'    => '0',
                'deadline'    => Carbon::now()->addDays(21),
                'status'      => 3,
                'assignedAt'  => Carbon::now()->subDays(2),
                'assignedBy'  => 1,
                'createdBy'   => 2,
                'updatedBy'   => 2,
            ],
            [
                'title'       => 'Write unit tests',
                'description' => 'Cover all service and controller methods with tests.',
                'project_id'  => 1,
                'priority'    => '1',
                'deadline'    => Carbon::now()->addDays(30),
                'status'      => 1,
                'createdBy'   => 2,
                'updatedBy'   => 2,
            ],
            [
                'title'       => 'Deploy to staging',
                'description' => 'Deploy the application to the staging environment.',
                'project_id'  => 1,
                'priority'    => '0',
                'deadline'    => Carbon::now()->addDays(45),
                'status'      => 1,
                'createdBy'   => 1,
                'updatedBy'   => 1,
            ],
        ];

        foreach ($tasks as $taskData) {
            $created = Task::create($taskData);  

            DB::table('task_collaborators')->insert([
                'taskId'       => $created->id,
                'collaborator' => $taskData['createdBy'],
                'flag'         => '0',
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
            ]);
        }
    }
}