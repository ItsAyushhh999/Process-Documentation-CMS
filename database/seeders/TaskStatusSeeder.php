<?php

namespace Database\Seeders;

use App\Models\TaskStatus;
use Illuminate\Database\Seeder;

class TaskStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $insertData = [
            [
              'name' => 'Assigned',
              'value' => '0',
              'createdBy' => 1,
              'updatedBy' => 1,
            ],
            [
              'name' => 'In Progress',
              'value' => '1',
              'createdBy' => 1,
              'updatedBy' => 1,
            ],
            [
              'name' => 'Assigned for Review',
              'value' => '2',
              'createdBy' => 1,
              'updatedBy' => 1,
            ],
            [
              'name' => 'Reviewing',
              'value' => '3',
              'createdBy' => 1,
              'updatedBy' => 1,
            ],
            [
              'name' => 'Reviewed',
              'value' => '4',
              'createdBy' => 1,
              'updatedBy' => 1,
            ],
            [
              'name' => 'Completed',
              'value' => '5',
              'createdBy' => 1,
              'updatedBy' => 1,
            ],
            [
              'name' => 'Closed',
              'value' => '6',
              'createdBy' => 1,
              'updatedBy' => 1,
            ],
            [
              'name' => 'Created',
              'value' => '7',
              'createdBy' => 1,
              'updatedBy' => 1,
            ],
            [
              'name' => 'Staging - Ready to upload',
              'value' => '8',
              'createdBy' => 1,
              'updatedBy' => 1,
            ],
            [
              'name' => 'Staging - Uploaded',
              'value' => '9',
              'createdBy' => 1,
              'updatedBy' => 1,
            ],
            [
              'name' => 'Live - Ready to upload',
              'value' => '10',
              'createdBy' => 1,
              'updatedBy' => 1,
            ],
            [
              'name' => 'Live - Uploaded',
              'value' => '11',
              'createdBy' => 1,
              'updatedBy' => 1,
            ],
        ];

        TaskStatus::insert($insertData);

    }
}
