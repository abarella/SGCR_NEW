<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check users in database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all(['name', 'email']);
        
        if ($users->count() > 0) {
            $this->info('Users found:');
            foreach ($users as $user) {
                $this->line("- {$user->name} ({$user->email})");
            }
        } else {
            $this->error('No users found in database');
        }
    }
} 