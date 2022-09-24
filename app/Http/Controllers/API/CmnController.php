<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\SystemSettings;
use App\User;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramResponseException;
use Exception;

class CmnController extends Controller
{
    public function telegramSendMessage($sMsg)
    {
        $oTelegramBot = SystemSettings::whereNotNull('bot_token')->get();
        if ($oTelegramBot === null) {
            return response()->json([
                'message'   => 'Bạn chưa setting thông báo',
                'result'    => ''
            ], 200);
        }

        $sBotToken = null;
        foreach ($oTelegramBot as $aTelegramBot) {
            if ($sBotToken !== $aTelegramBot['bot_token']) {
                $sBotToken = $aTelegramBot['bot_token'];

                try {
                    $oTelegram = new Api($sBotToken);

                    $aBot = $oTelegram->getMe();
                    if ($aBot['is_bot'] === false) {
                        $sBotToken = null;
                        continue;
                    }
                } catch(TelegramResponseException $e) {
                    $sBotToken = null;
                    continue;
                }
            }

            $oResponse = $oTelegram->sendMessage([
                'chat_id'       => $aTelegramBot['chat_id'], 
                'text'          => $sMsg,
                'parse_mode'    => 'HTML'
            ]);
        }

        return response()->json(['result' => 'Gửi thông báo thành công'], 200);
    }

    public function accountBalance($sUserId, $sTotalPayment)
    {
        try {
            $user = User::where('id', $sUserId)->first();
            return (int) $user->coin - (int) $sTotalPayment > 0;
        } catch(Exception $e) {
            return false;
        }
    }

    public function servicePayment($sUserId, $sTotalPayment)
    {
        try {
            $user = User::where('id', $sUserId)->first();
            $user->coin = (int) $user->coin - (int) $sTotalPayment;
            $user->save();
            return true;
        } catch(Exception $e) {
            return false;
        }
    }
}
