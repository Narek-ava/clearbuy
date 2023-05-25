<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class ZapierJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;
    public int $id;
    public string $model;
    public string $jsonResource;
    public string $webHookURL;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param integer $id
     * @param string $model
     * @param string $jsonResource
     * @param string $webHookURL
     * @return void
     */ 
    public function __construct(User $user, int $id, string $model, string $jsonResource, string $webHookURL)
    {
        $this->user = $user;
        $this->id = $id;
        $this->model = $model;
        $this->jsonResource = $jsonResource;
        $this->webHookURL = $webHookURL;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Http::post($this->webHookURL, (new $this->jsonResource($this->model::find($this->id), $this->user))->resolve());
    }
}
