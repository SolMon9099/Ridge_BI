<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
        });
    }

    public function render($request, Throwable $e)
    {
        $response = parent::render($request, $e);

        if (is_object($e)) {
            // エラーコードの取得
            $status_code = $response->getStatusCode();

            // 404, 500, 502の時のみSlackに通知
            // if ($status_code == 404 || $status_code == 502 || $status_code == 500) {
            if ($status_code == 404) {
                $url = URL::current();
                Log::info('----- 404エラー発生------'.$url);
                $split_urls = explode('/edit/', $url);
                $redirect_url = $split_urls[0];
                if (count($split_urls) == 1) {
                    $split_urls = explode('/edit_drawing/', $split_urls[0]);
                    $redirect_url = $split_urls[0].'/mapping';
                }
                if (count($split_urls) == 1) {
                    $split_urls = explode('/mapping_detail/', $split_urls[0]);
                    $redirect_url = $split_urls[0].'/mapping';
                }
                if (count($split_urls) > 1) {
                    $request->session()->flash('error', '選択したデータは別の方が変更中だったので最新の状態に更新しました。');

                    return redirect($redirect_url);
                } else {
                    return redirect()->route('admin.error', ['error_code' => $status_code]);
                }
            }

            // if ($status_code == 502 || $status_code == 500) {
            //     $message = "エラーが発生しました。\nエラーコード：".$status_code;
            //     SlackService::send($message);
            // }

            if ($e instanceof \Illuminate\Session\TokenMismatchException) {
                return redirect()->route('admin.login');
            }
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
                return redirect()->route('admin.login');
            }
        }

        return $response;
    }
}
