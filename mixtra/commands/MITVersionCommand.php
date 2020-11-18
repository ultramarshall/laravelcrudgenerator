<?php 
namespace mixtra\commands;

use App;
use Illuminate\Console\Command;

class MITVersionCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'mixtra:version';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'MIXTRA Version Command';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Version : 1.4.2");
    }
}
