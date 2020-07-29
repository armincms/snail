<?php

namespace Armincms\Snail\Console;

use Illuminate\Console\Command;
use Armincms\Snail\Snail;

class UserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snail:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        Snail::createUser($this);

        $this->info('User created successfully.');
    }
}
