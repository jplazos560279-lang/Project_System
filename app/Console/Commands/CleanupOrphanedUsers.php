<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CleanupOrphanedUsers extends Command
{
    protected $signature = 'users:cleanup-orphaned';
    protected $description = 'Delete users that have no linked employee record (except admin)';

    public function handle(): int
    {
        // Find users without a linked employee, excluding admin
        $orphanedUsers = User::where('role', '!=', 'admin')
            ->whereDoesntHave('employee')
            ->get();

        if ($orphanedUsers->isEmpty()) {
            $this->info('No orphaned users found.');
            return 0;
        }

        $this->info("Found {$orphanedUsers->count()} orphaned user(s) to delete:");

        foreach ($orphanedUsers as $user) {
            $this->line(" - {$user->name} ({$user->email})");
            $user->delete();
        }

        $this->info('Cleanup complete.');
        return 0;
    }
}
