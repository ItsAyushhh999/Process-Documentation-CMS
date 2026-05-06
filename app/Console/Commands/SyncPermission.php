<?php

namespace App\Console\Commands;

use App\Models\Permission;
use Illuminate\Console\Command;

class SyncPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:sync-permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $permissions = Permission::$defaultPermissions;

        $data = [];
        foreach ($permissions as $permission) {
            $data[] = [
                'name' => $permission,
            ];
        }

        Permission::insert($data);

        $this->info('Permission synced!.');

        return Command::SUCCESS;
    }
}
