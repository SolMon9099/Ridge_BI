<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Service\SafieApiService;
use Illuminate\Support\Facades\Log;
use App\Models\Admin;

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
        $contracts_data = Admin::query()->where('is_main_admin', 1)->get()->all();
        if (count($contracts_data) > 0) {
            foreach ($contracts_data as $item) {
                $safie_service = new SafieApiService($item->contract_no);
                $safie_service->generateRefreshToken();
            }
        } else {
            $safie_service = new SafieApiService();
            $safie_service->generateRefreshToken();
        }

        return 0;
    }
}
