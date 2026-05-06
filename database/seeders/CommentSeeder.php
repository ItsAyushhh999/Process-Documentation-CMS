<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    {
        $taskId  = 27; // change to test task ID
        $userIds = User::pluck('id')->toArray();
        $total   = 1000;
        $batch   = 500;

        $this->command->info("Seeding {$total} comments for task {$taskId}...");

        for ($i = 0; $i < $total / $batch; $i++) {
            $rows = [];

            for ($j = 0; $j < $batch; $j++) {
                $rows[] = [
                    'taskId'     => $taskId,
                    'comments'   => fake()->paragraph(),
                    'reply_id'   => 0,
                    'isPinned'   => '0',
                    'check'      => '0',
                    'createdBy'  => fake()->randomElement($userIds),
                    'updatedBy'  => fake()->randomElement($userIds),
                    'pinnedBy'   => 0,
                    'checkedBy'  => 0,
                    'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
                    'updated_at' => now(),
                ];
            }

            DB::table('task_comments')->insert($rows);
            $this->command->info("Inserted " . (($i + 1) * $batch) . " comments...");
        }

        $this->command->info('Done!');
    }
    }
}
