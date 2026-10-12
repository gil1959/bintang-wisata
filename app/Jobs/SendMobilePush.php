<?php
namespace App\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue,SerializesModels};
class SendMobilePush implements ShouldQueue
{
    use Dispatchable,InteractsWithQueue,Queueable,SerializesModels;
    public $tries=3;
    public $backoff=60;
    public function __construct(public int $userId,public array $payload){}
    public function handle(){app(\App\Services\Mobile\FirebasePush::class)->send($this->userId,$this->payload);}
}
