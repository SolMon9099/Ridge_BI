<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\NotificationGroup;
use App\Models\NotificationMsg;

class MailController extends Controller
{
    public function basic_email()
    {
        $data = ['name' => 'Virat Gandhi'];

        Mail::send(['text' => 'mail'], $data, function ($message) {
            $message->to('maximovic9099@gmail.com', 'Tutorials Point')->subject('BBBb');
            $message->from('pmomo9099@gmail.com', 'Momo');
        });
        echo 'Basic Email Sent. Check your inbox.';
    }

    public function html_email()
    {
        $data = ['name' => 'Virat Gandhi'];
        Mail::send('mail', $data, function ($message) {
            $message->to('maximovic9099@gmail.com', 'Tutorials Point')->subject('Laravel HTML Testing Mail');
            $message->from('pmomo9099@gmail.com', 'Virat Gandhi');
        });
        echo 'HTML Email Sent. Check your inbox.';
    }

    public function attachment_email()
    {
        $data = ['name' => 'Virat Gandhi'];
        Mail::send('mail', $data, function ($message) {
            $message->to('abc@gmail.com', 'Tutorials Point')->subject('Laravel Testing Mail with Attachment');
            $message->attach('C:\laravel-master\laravel\public\uploads\image.png');
            $message->attach('C:\laravel-master\laravel\public\uploads\test.txt');
            $message->from('xyz@gmail.com', 'Virat Gandhi');
        });
        echo 'Email Sent with attachment. Check your inbox.';
    }

    public function sendInavasionMail(Request $request)
    {
        $camera_serial = $request['serial_no'];
        $detect_type = $request['detect_type'];
        $type_title = '侵入';
        $type_url = 'danger';
        if ($detect_type == 'vc') {
            $type_title = '車両侵入';
            $type_url = 'vc';
        }
        if ($detect_type == 'pit') {
            $type_title = 'ピット入場';
            $type_url = 'pit';
        }
        $host = request()->getSchemeAndHttpHost();
        $camera_record = Camera::query()->where('serial_no', $camera_serial)->get()->first();
        if ($camera_record != null && $camera_record->contract_no != null) {
            $contract_no = $camera_record->contract_no;
            $group_record = NotificationGroup::query()
                ->where('contract_no', $contract_no)
                ->where('name', '侵入検知')
                ->get()->first();
            if ($group_record != null) {
                $mails = $group_record->emails;
                if (!empty($mails)) {
                    $mails = explode(',', $mails);
                    $mail_data = NotificationMsg::query()->where('title', 'Like', '%侵入もしくは入場%')
                        ->where('contract_no', $contract_no)
                        ->get()->first();
                    if ($mail_data != null) {
                        $subject = $mail_data->title;
                        $subject = str_replace('〇〇(侵入もしくは入場)', $type_title, $subject);
                        $content = $mail_data->content;
                        $content = str_replace('〇〇', $camera_serial, $content);
                        $content = str_replace('(侵入もしくは入場)', $type_title, $content);
                        $content = str_replace('@URL@', '<a href="'.$host.'/admin/'.$type_url.'/list">'.$host.'/admin/'.$type_url.'/list </a>', $content);
                        $mail_content = ['content' => nl2br($content)];
                        Mail::send('mail', $mail_content, function ($message) use ($mails, $subject) {
                            foreach ($mails as $mail) {
                                $message->to($mail, '顧客')->subject($subject);
                            }
                            echo 'mail send success';
                        });
                    }
                }
            }
        }
    }
}
