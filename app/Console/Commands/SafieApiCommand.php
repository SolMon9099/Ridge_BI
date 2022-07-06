<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Service\SafieApiService;
use Illuminate\Support\Facades\Log;

class SafieApiCommand extends Command
{
    protected $signature = 'safie:refresh_access_token';

    protected $description = 'Safie APIのリフレッシュトークンを再発行する';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Log::info('Refresh Token****************');
        $safie_service = new SafieApiService();
        $safie_service->generateRefreshToken();

        return 0;
    }
}
