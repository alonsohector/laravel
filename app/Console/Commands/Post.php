<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Post extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Incfile:POST {--U|URL_POST=Incfile_defined}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a simple POST request to external URL.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
