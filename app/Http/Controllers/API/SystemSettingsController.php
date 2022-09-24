<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\SystemSettings;
use Illuminate\Http\Request;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramResponseException;
use Exception;

class SystemSettingsController extends Controller
{
    public function index()
    {
        $systemSettings = SystemSettings::get();
        return response()->json(['result' => $systemSettings], 200);
    }

    /**
     * telegram Bot settings
     */
    public function PostTelegramBotSettings(Request $request)
    {
        try {
            $telegram = new Api($request->bot_token);

            $bot = $telegram->getMe();
            if ($bot['is_bot'] === false) {
                return response()->json([
                    'message' => 'Telegram Bot token không hợp lệ.'
                ], 200);
            }

            $response = $telegram->getUpdates();
            
            if (count($response) === 0) {
                return response()->json([
                    'message' => 'Vui lòng thêm bot này vào nhóm hoặc kênh Telegram và thăng cấp với tư cách là quản trị viên.'
                ], 200);
            }

            $arrResult = array();
            foreach ($response as $result) {
                $result = json_decode($result);
                if (property_exists($result, 'my_chat_member') === true) {
                    $arr = [
                        'chat_id'    => $result->my_chat_member->chat->id,
                        'chat_type'  => $result->my_chat_member->chat->type
                    ];
                    if (property_exists($result->my_chat_member->chat, 'title') === true) {
                        $arr['chat_title'] = $result->my_chat_member->chat->title;
                    }
                } elseif (property_exists($result, 'channel_post') === true) {
                    $arr = [
                        'chat_id'    => $result->channel_post->chat->id,
                        'chat_type'  => $result->channel_post->chat->type
                    ];
                    if (property_exists($result->channel_post->chat, 'title') === true) {
                        $arr['chat_title'] = $result->channel_post->chat->title;
                    }
                } elseif (property_exists($result, 'message') === true) {
                    $arr = [
                        'chat_id'    => $result->message->chat->id,
                        'chat_type'  => $result->message->chat->type
                    ];
                    if (property_exists($result->message->chat, 'title') === true) {
                        $arr['chat_title'] = $result->message->chat->title;
                    }
                }
                $arr['bot_name'] = $bot['first_name'];
                $arrResult[] = $arr;
            }

            $result = array();
            $uniqueId = array();
            foreach ($arrResult as $arr) {
                if (count($uniqueId) !== 0 && in_array($arr['chat_id'], $uniqueId) === true) {
                    continue;
                }
                $uniqueId[] = $arr['chat_id'];
                $result[] = $arr;
            }

            foreach ($result as $key => $arr) {
                if ($arr['chat_type'] === "channel" || $arr['chat_type'] === "supergroup") {
                    $chatId = SystemSettings::where('chat_id', $arr['chat_id'])->first();
                    if ($chatId !== null) {
                        continue;
                    }

                    $systemSettings = new SystemSettings;
                    $systemSettings->bot_token  = $request->bot_token;
                    $systemSettings->bot_name   = $bot['first_name'];
                    $systemSettings->chat_id    = $arr['chat_id'];
                    $systemSettings->chat_type  = $arr['chat_type'];
                    if (array_key_exists('chat_title', $arr)) {
                        $systemSettings->chat_title = $arr['chat_title'];
                    }
                    $systemSettings->save();
                } else {
                    unset($result[$key]);
                }
            }

            return response()->json([
                'message'   => 'Cài đặt thành công.',
                'result'    => SystemSettings::get()
            ], 200);

        } catch(TelegramResponseException $e) {
            return response()->json([
                'message' => 'Đã có một lỗi xảy ra. Vui lòng thử lại.'
            ], 200);
        }
    }

    public function PostDeleteTelegramBotSettings(Request $request, $id)
    {
        SystemSettings::where('id', $id)->delete();
        return response()->json(['message' => 'Xóa thành công.'], 200);
    }

    public function PostPaymentSettings(Request $request)
    {
        $systemSettings = new SystemSettings;
        $systemSettings->payment_type   = $request->payment_type;
        $systemSettings->account_name   = $request->account_name;
        $systemSettings->bank_code      = $request->bank_code;
        if ($request->payment_type === 'atm') {
            $systemSettings->bank_name      = $request->bank_name;
            $systemSettings->bank_branch    = $request->bank_branch;
        }
        $systemSettings->save();

        return response()->json([
            'message'   => 'Cài đặt thành công.',
            'result'    => SystemSettings::get()
        ], 200);
    }

    public function PostDeletePaymentSettings(Request $request, $id)
    {
        SystemSettings::where('id', $id)->delete();
        return response()->json(['message' => 'Xóa thành công.'], 200);
    }
}
