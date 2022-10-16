<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\CmnController;
use App\Payment;
use App\SystemSettings;
use App\User;
use Illuminate\Http\Request;

class PaymentController extends CmnController
{
    public function index(Request $request, $user_id)
    {
        $payment = Payment::where('user_id', $user_id)->orderBy('created_at', 'desc')->limit(10)->get();
        return response()->json([
            'result' => $payment
        ], 200);
    }

    /**
     * Get Buy currency
     */
    public function GetBuyCurrency(Request $request, $code)
    {
        $payment = Payment::where('code', $code)->first();
        $systemSettings = null;
        if ($payment !== null) {
            $systemSettings = SystemSettings::where('payment_type', $payment->payment_type)->first();
        }
        return response()->json([
            'result'    => $payment,
            'settings'  => $systemSettings
        ], 200);
    }

    /**
     * Post Create Order Buy currency
     */
    public function PostBuyCurrency(Request $request)
    {
        $payment = new Payment;
        $code = rand(100000000, 999999999);
        $payment->user_id       = $request->user_id;
        $payment->code          = $code;
        $payment->amount_coin   = $request->currency;
        $payment->price         = $request->currency;
        $payment->payment_type  = $request->payment_type;
        $payment->save();

        return response()->json([
            'message'   => '',
            'result'    => $code
        ], 200);
    }

    /**
     * Post Delete Buy currency
     */
    public function PostDeleteBuyCurrency(Request $request, $id)
    {
        Payment::where('id', $id)->delete();
        return response()->json(['message' => 'Huỷ lệnh thành công.'], 200);
    }

    /**
     * Post Confirm Buy currency
     */
    public function PostConfirmBuyCurrency(Request $request, $id)
    {
        $payment = Payment::find($id);
        $payment->status = '1';
        $payment->save();

        $user = User::find($payment->user_id);
        $sUserName = is_null($user->name) === true ? $user->username : $user->name;
        $sPaymentType = $payment->payment_type === 'atm' ? 'Chuyển khoản ngân hàng' : 'Momo';

        // Send Notification
        $sMsg = "<b>💰 Currency Payment</b>" .
                "\n" . 
                "\n" . 
                "User $sUserName vừa thực hiện nạp Xu và đang chờ mở khoá" .
                "\n" . 
                "\n" . 
                "- Nội dung chuyển khoản: $payment->code" .
                "\n" . 
                "- Số lượng: $payment->amount_coin Xu" .
                "\n" . 
                "- Phương thức thanh toán: $sPaymentType" .
                "\n" . 
                "- URL: <a href='" . url('payment/' . $payment->code . '/confirm') ."'>Xác nhận</a>" .
                "\n" . 
                "- Thời gian: " . date('Y/m/d H:i') .
                "\n" . 
                "\n" . 
                "<i>Hãy xác nhận và mở khoá cho người dùng.</i>";
        $this->telegramSendMessage($sMsg);

        return response()->json(['message' => 'Bạn đã xác nhận chuyển tiền. Vui lòng chờ hệ thống xác nhận.'], 200);
    }

    /**
     * Post Confirm Sell currency
     */
    public function PostConfirmSellCurrency(Request $request, $id)
    {
        $payment = Payment::find($id);

        if ($payment !== null) {
            $user = User::find($payment->user_id);
            if ($user !== null) {
                $user->coin = (int) $user->coin + (int) $payment->amount_coin;
                $user->save();

                $payment->status = '2';
                $payment->save();

                return response()->json(['message' => 'Bạn đã xác nhận đã nhận tiền.'], 200);
            }
        }

        return response()->json(['message' => 'Xác nhận không thành công. Vui lòng thử lại.'], 200);
    }

    /**
     * Post Confirm Sell currency Fail
     */
    public function PostConfirmSellCurrencyFail(Request $request, $id)
    {
        $payment = Payment::find($id);
        $payment->status = '3';
        $payment->save();
        return response()->json(['message' => 'Bạn đã xác nhận chưa nhận tiền.'], 200);
    }

    public function PostAddOrSubCurrency(Request $request)
    {
        $user = User::find($request->user_id);
        if ($user !== null) {
            if ($request->action_option == 'add') {
                $user->coin = (int) $user->coin + (int) $request->currency;
            } else {
                $user->coin = (int) $user->coin - (int) $request->currency;
                if ($user->coin <= 0) {
                    $user->coin = 0;
                }
            }
            $user->save();

            return response()->json(['result' => $user->coin], 200);
        }
        return response()->json(['result' => false], 200);
    }
}
