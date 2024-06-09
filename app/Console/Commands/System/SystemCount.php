<?php

namespace App\Console\Commands\System;

use App\Services\SystemCounterService;
use Illuminate\Console\Command;

class SystemCount extends Command
{
    public SystemCounterService $systemCounterService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:system-count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set cache product system count. Run at 12h PM every days';

    public function __construct(SystemCounterService $systemCounterService)
    {
        parent::__construct();
        $this->systemCounterService = $systemCounterService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->systemCounterService->forgetCache();
        return $this->systemCounterService->index();
    }
}
