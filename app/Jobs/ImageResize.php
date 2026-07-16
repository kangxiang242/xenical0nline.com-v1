<?php

namespace App\Jobs;

use App\Services\ImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class ImageResize implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $path;

    protected $sizes = [];

    /**
     * Create a new job instance.
     * @param $path
     * @param array $sizes
     */
    public function __construct($path,$sizes)
    {
        $this->path = $path;
        $this->sizes = (array)$sizes;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $image = app(ImageService::class);
        foreach($this->sizes as $size){
            $image->resize($this->path,$size);
        }
    }
}
