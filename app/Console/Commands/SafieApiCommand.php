<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Service\SafieApiService;

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
        $safie_service = new SafieApiService();
        $safie_service->generateRefreshToken();

        return 0;
    }
}