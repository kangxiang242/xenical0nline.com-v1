<?php

namespace App\Jobs;

use App\Services\CacheService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClearRemoteRedis implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $keys;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($keys)
    {
        $this->keys = $keys;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        app(CacheService::class)->clearRemoteKey($this->keys);
    }
}
