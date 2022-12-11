<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Auth;
use App\Facebook;
use App\FacebookSettings;
use App\User;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\CmnController;
use Illuminate\Http\Request;
use Validator;
use Exception;
use Telegram\Bot\Api;

class FacebookController extends CmnController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function GetSettingsFacebook(Request $request)
    {
        $settings = FacebookSettings::get();
        return response()->json(['result' => $settings], 200);
    }

    public function PostSettingsFacebook(Request $request)
    {
        try {
            $settings = new FacebookSettings;
            if ($request->type === 'amount') {
                if ($request->amount_min !== null || $request->amount_max !== null) {
                    if ($request->amount_min !== null && $request->amount_max !== null && (int)$request->amount_min >= (int)$request->amount_max) {
                        return response()->json(['message' => 'Giá trị đầu vào không hợp lệ.'], 200);
                    }
                    $amount = FacebookSettings::where('type', $request->type)->first();
                    if ($amount === null) {
                        $settings->amount_min = $request->amount_min;
                        $settings->amount_max = $request->amount_max;
                        $settings->type = $request->type;
                        $settings->save();
                    } else {
                        $updateAmount = FacebookSettings::find($amount->id);
                        if ($request->amount_min !== '') {
                            $updateAmount->amount_min = $request->amount_min;
                        }
                        if ($request->amount_max !== '') {
                            $updateAmount->amount_max = $request->amount_max;
                        }
                        $updateAmount->save();
                    }
                }
            }

            if ($request->type === 'minutes') {
                if ($request->minutes_min !== null || $request->minutes_max !== null) {
                    if ($request->minutes_min !== null && $request->minutes_max !== null && (int)$request->minutes_min >= (int)$request->minutes_max) {
                        return response()->json(['message' => 'Giá trị đầu vào không hợp lệ.'], 200);
                    }
                    $amount = FacebookSettings::where('type', $request->type)->first();
                    if ($amount === null) {
                        $settings->minutes_min = $request->minutes_min;
                        $settings->minutes_max = $request->minutes_max;
                        $settings->type = $request->type;
                        $settings->save();
                    } else {
                        $updateAmount = FacebookSettings::find($amount->id);
                        if ($request->minutes_min !== '') {
                            $updateAmount->minutes_min = $request->minutes_min;
                        }
                        if ($request->minutes_max !== '') {
                            $updateAmount->minutes_max = $request->minutes_max;
                        }
                        $updateAmount->save();
                    }
                }
            }

            if ($request->type === 'speed') {
                if ($request->speed_min !== null || $request->speed_max !== null) {
                    if ($request->speed_min !== null && $request->speed_max !== null && (int)$request->speed_min >= (int)$request->speed_max) {
                        return response()->json(['message' => 'Giá trị đầu vào không hợp lệ.'], 200);
                    }
                    $speed = FacebookSettings::where('type', $request->type)->first();
                    if ($speed === null) {
                        $settings->speed_min = $request->speed_min;
                        $settings->speed_max = $request->speed_max;
                        $settings->type = $request->type;
                        $settings->save();
                    } else {
                        $updateSpeed = FacebookSettings::find($speed->id);
                        if ($request->speed_min !== '') {
                            $updateSpeed->speed_min = $request->speed_min;
                        }
                        if ($request->speed_max !== '') {
                            $updateSpeed->speed_max = $request->speed_max;
                        }
                        $updateSpeed->save();
                    }
                }
            }

            if ($request->type === 'channel') {
                if (isset($request->action) === true && $request->action === 'delete') {
                    FacebookSettings::where('id', $request->id)->delete();
                    return response()->json(['message' => 'Đã xóa kênh ' . $request->channel_name . ' thành công.'], 200);
                } elseif ($request->channel_name !== null || $request->channel_price !== null || $request->channel_description !== null) {
                    $channel = FacebookSettings::where('type', $request->type)->where('channel_type', $request->channel_type)->where('channel_name', $request->channel_name)->first();
                    if ($channel === null) {
                        $settings->channel_type = $request->channel_type;
                        $settings->channel_name = $request->channel_name;
                        $settings->channel_price = $request->channel_price;
                        $settings->channel_description = $request->channel_description;
                        $settings->type = $request->type;
                        $settings->save();
                    } else {
                        return response()->json([
                            'message'   => 'Kênh ' . $request->channel_name . ' đã tồn tại.',
                            'delete'    => false
                        ], 200);
                    }
                }
            }
        } catch(Exception $e) {
            return response()->json(['message' => 'Đã có một lỗi xảy ra. Vui lòng thử lại.'], 200);
        }

        return response()->json(['message' => 'Cài đặt thành công.'], 200);
    }

    public function GetOrderShareFacebook(Request $request, $user_id)
    {
        $orderShare = Facebook::where('user_id', $user_id)->where('type', 'share')->orderBy('created_at', 'desc')->limit(10)->get();
        return response()->json(['result' => $orderShare], 200);
    }

    public function GetTotalOrderFacebook(Request $request)
    {
        $orders = Facebook::whereDate('created_at', '>=', today())->get();
        return response()->json(['result' => $orders], 200);
    }

    public function GetTotalOrderByUserFacebook(Request $request, $user_id)
    {
        $orders = Facebook::where('user_id', $user_id)->get();
        return response()->json(['result' => $orders], 200);
    }

    public function GetOrderCommentFacebook(Request $request, $user_id)
    {
        $orderComment = Facebook::where('user_id', $user_id)->where('type', 'comment')->orderBy('created_at', 'desc')->limit(10)->get();
        return response()->json(['result' => $orderComment], 200);
    }

    /**
     * buff share
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function BuffShare(Request $request)
    {
        try {
            $resultValidate = $this->ValidateBuff($request);
            if ($resultValidate !== true) {
                return $resultValidate;
            }

            $channel = FacebookSettings::where('type', 'channel')->where('id', $request->channel)->first();

            $sUserId = $request->user_id;
            $sTotalPayment = (int)$channel->channel_price * (int)$request->amount;

            $sAccountBalance = $this->accountBalance($sUserId, $sTotalPayment);
            if ($sAccountBalance === false) {
                return response()->json([
                    'result'    => false,
                    'message'   => 'Số dư tài khoản của bạn không đủ. Vui lòng nạp thêm Xu'
                ], 200);
            }

            $sServicePayment = $this->servicePayment($sUserId, $sTotalPayment);
            if ($sServicePayment === false) {
                return response()->json([
                    'result'    => false,
                    'message'   => 'Thất bại'
                ], 200);
            }

            $buffShare = new Facebook;
            $buffShare->user_id         = $request->user_id;
            $buffShare->uid             = $request->uid;
            $buffShare->type            = 'share';
            $buffShare->status          = 0;
            $buffShare->channel         = $channel->channel_name;
            $buffShare->amount          = $request->amount;
            $buffShare->total_payment   = (int)$channel->channel_price * (int)$request->amount;
            $buffShare->save();

            // Send Notification
            $user = User::find($request->user_id);
            $sUserName = is_null($user->name) === true ? $user->username : $user->name;

            $sMsg = "<b>🔥 Buff Share</b>" .
            "\n" . 
            "\n" . 
            "User $sUserName vừa tạo đơn tăng chia sẻ bài viết Facebook" .
            "\n" . 
            "\n" . 
            "- UID: $request->uid" .
            "\n" . 
            "- Số lượng: $request->amount lượt chia sẻ" .
            "\n" . 
            "- Kênh: Kênh $channel->channel_name giá $channel->channel_price Xu" .
            "\n" . 
            "- URL: <a href='" . url('buff/' . $buffShare->id . '/confirm') ."'>Xác nhận</a>" .
            "\n" . 
            "- Thời gian: " . date('Y/m/d H:i') .
            "\n" . 
            "\n" . 
            "<i>Hãy hoàn thành và xác nhận cho người dùng.</i>";
            $this->telegramSendMessage($sMsg);

        } catch(Exception $e) {
            return response()->json([
                'result'    => false,
                'message'   => 'Đã có một lỗi xảy ra. Vui lòng thử lại.'
            ], 200);
        }

        return response()->json([
            'result'    => true,
            'message'   => 'Thành công'
        ], 200);
    }

    /**
     * buff comment
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function BuffComment(Request $request)
    {
        try {
            $resultValidate = $this->ValidateBuff($request);
            if ($resultValidate !== true) {
                return $resultValidate;
            }

            $channel = FacebookSettings::where('type', 'channel')->where('id', $request->channel)->first();

            $sUserId = $request->user_id;
            $sTotalPayment = (int)$channel->channel_price * (int)$request->amount;

            $sAccountBalance = $this->accountBalance($sUserId, $sTotalPayment);
            if ($sAccountBalance === false) {
                return response()->json([
                    'result'    => false,
                    'message'   => 'Số dư tài khoản của bạn không đủ. Vui lòng nạp thêm Xu'
                ], 200);
            }

            $sServicePayment = $this->servicePayment($sUserId, $sTotalPayment);
            if ($sServicePayment === false) {
                return response()->json([
                    'result'    => false,
                    'message'   => 'Thất bại'
                ], 200);
            }

            $buffComment = new Facebook;
            $buffComment->user_id         = $request->user_id;
            $buffComment->uid             = $request->uid;
            $buffComment->type            = 'comment';
            $buffComment->status          = 0;
            $buffComment->channel         = $channel->channel_name;
            $buffComment->amount          = $request->amount;
            $buffComment->speed           = $request->speed;
            $buffComment->content         = $request->content;
            $buffComment->total_payment   = (int)$channel->channel_price * (int)$request->amount;
            $buffComment->save();

            // Send Notification
            $user = User::find($request->user_id);
            $sUserName = is_null($user->name) === true ? $user->username : $user->name;

            $sMsg = "<b>🔥 Buff Comment</b>" .
            "\n" . 
            "\n" . 
            "User $sUserName vừa tạo đơn tăng bình luận bài viết Facebook" .
            "\n" . 
            "\n" . 
            "- UID: $request->uid" .
            "\n" . 
            "- Kênh: Kênh $channel->channel_name giá $channel->channel_price Xu" .
            "\n" . 
            "- Số lượng: $request->amount lượt bình luận" .
            "\n" . 
            "- Tốc độ: $request->speed Giây/Lượt" .
            "\n" . 
            "- Nội dung: $request->content" .
            "\n" . 
            "- URL: <a href='" . url('buff/' . $buffComment->id . '/confirm') ."'>Xác nhận</a>" .
            "\n" . 
            "- Thời gian: " . date('Y/m/d H:i') .
            "\n" . 
            "\n" . 
            "<i>Hãy hoàn thành và xác nhận cho người dùng.</i>";
            $this->telegramSendMessage($sMsg);

        } catch(Exception $e) {
            return response()->json([
                'result'    => false,
                'message'   => 'Đã có một lỗi xảy ra. Vui lòng thử lại.'
            ], 200);
        }

        return response()->json([
            'result'    => true,
            'message'   => 'Thành công'
        ], 200);
    }

    /**
     * check isset and is empty
     */
    private function IsEmpty($sStr)
    {
        if ($sStr === null || $sStr === '') {
            return true;
        }
        return false;
    }

    /**
     * get List buff facebook
     */
    public function GetListBuffFacebook(Request $request, $user_id, $buff_type)
    {
        $aListBuff = Facebook::where('user_id', $user_id)->where('type', $buff_type)->orderBy('created_at', 'desc')->limit(10)->get();
        return response()->json(['result' => $aListBuff], 200);
    }

    /**
     * buff facebook
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function PostBuffFacebook(Request $request)
    {
        try {
            $aRules = array(
                'uid'       => 'required|max:255',
                'channel'   => 'required|max:255',
                'amount'    => 'required|max:10',
            );
            if ($request->type === 'like' || $request->type === 'view') {
                unset($aRules['channel']);
            } elseif ($request->type === 'live') {
                unset($aRules['channel']);
                $aRules['minutes'] = 'required';
            }

            $resultValidate = $this->ValidateBuff($request, $aRules);
            if ($resultValidate !== true) {
                return $resultValidate;
            }

            $oChannel = FacebookSettings::where('type', 'channel')->where('channel_type', $request->type)->where('id', $request->channel)->first();
            $sTotalPayment = (int)$oChannel->channel_price * (int)$request->amount * (int)$request->minutes;

            $sAccountBalance = $this->accountBalance($request->user_id, $sTotalPayment);
            if ($sAccountBalance === false) {
                return response()->json([
                    'result'    => false,
                    'message'   => 'Số dư tài khoản của bạn không đủ. Vui lòng nạp thêm Xu!'
                ], 200);
            }

            $sServicePayment = $this->servicePayment($request->user_id, $sTotalPayment);
            if ($sServicePayment === false) {
                return response()->json([
                    'result'    => false,
                    'message'   => 'Thất bại'
                ], 200);
            }

            $oBuff = new Facebook;
            $oBuff->user_id         = $request->user_id;
            $oBuff->uid             = $request->uid;
            $oBuff->type            = $request->type;
            $oBuff->status          = 0;
            if ($this->IsEmpty($request->minutes) === false) {
                $oBuff->minutes     = $request->minutes;
                $sMsgMinutes =  "\n" . 
                                "- Số phút: $oBuff->minutes";
            }
            $oBuff->channel         = $oChannel->channel_name;
            $oBuff->amount          = $request->amount;
            $oBuff->total_payment   = $sTotalPayment;
            $oBuff->save();

            $oUser = User::find($request->user_id);
            $sUserName = is_null($oUser->name) === true ? $oUser->username : $oUser->name;

            // Send Notification
            $sMsg = "<b>🔥 Buff Like</b>" .
            "\n" . 
            "\n" . 
            "User $sUserName vừa tạo đơn sử dụng dịch vụ tăng $oBuff->type cho post/video/... Facebook." .
            "\n" . 
            "\n" . 
            "- UID: $oBuff->uid" .
            "\n" . 
            "- Số lượng: $oBuff->amount" .
            $sMsgMinutes .
            "\n" . 
            "- Giá: " . number_format($oBuff->total_payment, 0, ',', '.') . "đ" .
            "\n" . 
            "- URL: <a href='" . url('buff/' . $oBuff->id . '/confirm') ."'>Xác nhận</a>" .
            "\n" . 
            "- Thời gian: " . date('Y/m/d H:i') .
            "\n" . 
            "\n" . 
            "<i>Hãy hoàn thành và xác nhận cho người dùng.</i>";
            $this->telegramSendMessage($sMsg);

        } catch(Exception $e) {
            return response()->json([
                'result'    => false,
                'message'   => 'Đã có một lỗi xảy ra. Vui lòng thử lại.',
                'error'     => $e->getMessage()
            ], 200);
        }

        return response()->json([
            'result'    => true,
            'message'   => 'Thành công'
        ], 200);
    }

    /**
     * Validate Buff
     */
    public function ValidateBuff(Request $request, $aRules = array())
    {
        $aRulesValidator = array(
            'uid'       => 'required|max:255',
            'channel'   => 'required|max:255',
            'amount'    => 'required|max:10',
        );
        if (count($aRules) > 0) {
            $aRulesValidator = $aRules;
        }

        $validator = Validator::make($request->all(), 
            $aRulesValidator,
            [
                'required'  => 'Giá trị đầu vào của :attribute không hợp lệ.',
                'max'       => 'Giá trị đầu vào của :attribute không hợp lệ.',
            ], 
            [
                'uid'       => 'link/uid bài viết',
                'channel'   => 'kênh',
                'amount'    => 'số lượng',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'result'    => false,
                'message'   => $validator->errors()->first()
            ], 200);
        }

        $patternVideo = '/^http(?:s?):\/\/(?:www\.|web\.|m\.)?facebook\.com\/([A-z0-9\.]+)\/videos(?:\/[0-9A-z].+)?|watch|story\/(\d+)(?:.+)?$/';
        $patternPost = '/^http(?:s?):\/\/(?:www\.|web\.|m\.)?facebook\.com\/?:post|activity|photo|media|questions|note|story|stories(?:\/[0-9A-z].+)?\/(\d+)(?:.+)?$/';
        if (preg_match($patternVideo, $request->uid) === 0 && preg_match($patternPost, $request->uid) === 0) {
            return response()->json([
                'result'    => false,
                'message'   => 'Giá trị đầu vào của link/uid bài viết không hợp lệ.'
            ], 200);
        }

        return true;
    }

    /**
     * Get Buff Confirm
     */
    public function GetBuffConfirm(Request $request, $id)
    {
        $facebook = Facebook::where('id', $id)->first();
        return response()->json([
            'result'    => $facebook,
        ], 200);
    }

    /**
     * Post Buff Confirm
     */
    public function PostBuffConfirm(Request $request, $id)
    {
        $facebook = Facebook::where('id', $id)->first();
        $facebook->status = '1';
        $facebook->save();
        return response()->json([
            'result'    => true,
            'message'   => 'Đã xác nhận hoàn thành.'
        ], 200);
    }
}
