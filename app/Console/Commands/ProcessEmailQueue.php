<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ProcessEmailQueue extends Command
{
    protected $signature = 'email:process
                            {--max-jobs=0 : Maximum number of jobs to process (0 = unlimited)}
                            {--timeout=60 : Timeout for each job}
                            {--daemon : Run as daemon (continuous background processing)}
                            {--sleep=3 : Sleep time between jobs when running as daemon}';

    protected $description = 'Process email queue in the background';

    public function handle()
    {
        $maxJobs = (int) $this->option('max-jobs');
        $timeout = (int) $this->option('timeout');
        $daemon = $this->option('daemon');
        $sleep = (int) $this->option('sleep');

        if ($daemon || $maxJobs === 0) {
            $this->runDaemonMode($timeout, $sleep);
        } else {
            $this->runBatchMode($maxJobs, $timeout);
        }
    }

    private function runDaemonMode($timeout, $sleep)
    {
        $this->info('Starting email queue daemon in the background...');

        $exitCode = Artisan::call('queue:work', [
            '--queue' => 'default',
            '--timeout' => $timeout,
            '--tries' => 3,
            '--sleep' => $sleep,
            '--daemon' => true,
            '--verbose' => true,
        ]);

        if ($exitCode === 0) {
            $this->info('Email queue daemon stopped gracefully.');
        } else {
            $this->error('Email queue daemon encountered errors.');
        }

        return $exitCode === 0 ? Command::SUCCESS : Command::FAILURE;
    }

    private function runBatchMode($maxJobs, $timeout)
    {
        $this->info("📧 Starting email queue batch processing (max: {$maxJobs} jobs, timeout: {$timeout}s)...");

        $exitCode = Artisan::call('queue:work', [
            '--queue' => 'default',
            '--timeout' => $timeout,
            '--tries' => 3,
            '--max-jobs' => $maxJobs,
            '--stop-when-empty' => true,
            '--verbose' => true,
        ]);

        if ($exitCode === 0) {
            $this->info('✓ Email queue batch processing completed successfully.');
        } else {
            $this->error('✗ Email queue batch processing encountered errors.');
        }

        return $exitCode === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}
