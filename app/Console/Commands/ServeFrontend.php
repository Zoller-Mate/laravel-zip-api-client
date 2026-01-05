<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ServeFrontend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'serve:frontend {--port=8001 : The port to serve the application on}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the Laravel development server on port 8001 (frontend default)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $port = $this->option('port');
        
        $this->info("Starting Laravel development server on http://127.0.0.1:{$port}");
        $this->info('Press Ctrl+C to stop the server');
        $this->newLine();

        $process = new Process([PHP_BINARY, 'artisan', 'serve', '--port=' . $port]);
        $process->setTimeout(null);
        $process->setTty(Process::isTtySupported());
        
        return $process->run(function ($type, $buffer) {
            echo $buffer;
        });
    }
}
