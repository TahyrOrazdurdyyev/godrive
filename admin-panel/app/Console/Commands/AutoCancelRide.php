<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AutoCancelRide extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ride:auto-cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executes the autoCancelRide.js file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $node_path = env('NODE_PATH','');

        if(!empty($node_path)){

            // Run the JS file using Node.js
            $command = $node_path.' '.storage_path('app/firebase/autoCancelRide.js');
        
            $output = shell_exec($command." /dev/null 2>&1");

            // Log the output
            \Log::info('AutoCancelRide Output: ' . $output);

            $this->info('Auto Cancel Ride process executed.');

        }else{

            // Log the output
            \Log::info('AutoCancelRide Output: Node path is not defined');
        }
    }
}
