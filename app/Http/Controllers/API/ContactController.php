<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\CmnController;
use Illuminate\Http\Request;
use Validator;

class ContactController extends CmnController
{
    /**
     * request contact
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function PostContact(Request $request)
    {
        $validator = Validator::make($request->all(), 
            [
                'email'     => 'required|email|max:255',
                'phone'     => array(
                                'required',
                                'max:10',
                                'regex:/^(0?)(3[2-9]|5[6|8|9]|7[0|6-9]|8[0-6|8|9]|9[0-4|6-9])[0-9]{7}$/'
                            ),
                'content'   => 'required|max:10000',
            ],
            [
                'required'  => 'Giá trị đầu vào của :attribute không hợp lệ.',
                'max'       => 'Giá trị đầu vào của :attribute không hợp lệ.',
                'email'     => 'Giá trị đầu vào của :attribute không hợp lệ.',
                'regex'     => 'Giá trị đầu vào của :attribute không hợp lệ.'
            ], 
            [
                'email'     => 'Email',
                'phone'     => 'Số điện thoại',
                'content'   => 'Nội dung yêu cầu',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'result'    => false,
                'message'   => $validator->errors()->first()
            ], 200);
        }

        // Send Notification
        $sMsg = "<b>⏰ Contact</b>" .
                "\n" . 
                "\n" . 
                "Có một tin nhắn hỗ trợ" .
                "\n" . 
                "\n" . 
                "- Email: $request->email" .
                "\n" . 
                "- Số điện thoại: $request->phone" .
                "\n" . 
                "- Nội dung cần hỗ trợ: $request->content" .
                "\n" . 
                "- Thời gian: " . date('Y/m/d H:i') .
                "\n" . 
                "\n" . 
                "<i>Hãy liên hệ hỗ trợ nhé.</i>";
        $this->telegramSendMessage($sMsg);

        return response()->json([
            'result'    => false,
            'message'   => 'Đã gửi yêu cầu hỗ trợ.'
        ], 200);
    }
}
