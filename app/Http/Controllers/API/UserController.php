<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Validator;
use Exception;
use App\ResetPassword;
use Mail;
use App\Mail\mailResetPassword;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cookie;
use GuzzleHttp\Cookie\CookieJar;

class UserController extends Controller
{
    public function login(Request $request) { 
        $validator = Validator::make($request->all(), 
            [
                'username'  => 'alpha_dash|max:255',
                'email'     => 'required|max:255',
                'password'  => 'required',
            ],
            [
                'required'  => ':attribute không được để trống.',
                'alpha_dash' => ':attribute không được chứa dấu cách, dấu chấm, ký tự đặc biệt.',
            ], 
            [
                'username'  => 'Username',
                'email'     => 'Email',
                'password'  => 'Password',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 404);
        }

        if(User::where('email', $request->email)->exists()) {
            $user = User::where('email', $request->email)->where('deleted_at', NULL)->first();
            if($user->exists()){
                if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                    $token = Auth::user()->createToken('login')->accessToken;
                    
                    if($user->image_profile != null) {
                        $imagePath = 'public/images/users/'.$user->id.'/image_profile'.'/';
                        if(Storage::disk('local')->exists($imagePath.$user->image_profile)) {
                            $img = base64_encode(Storage::disk('local')->get($imagePath.$user->image_profile));
                            $image_profile = 'data:image/'.pathinfo($user->image_profile)['extension'].';base64,'.$img;
                        } else {
                            $image_profile = '';
                        }
                    } else {
                        $image_profile = '';
                    }

                    return response()->json([
                        'message' => 'Successful!',
                        'user' => collect($user)->merge(['image_profile' => $image_profile]),
                        'access_token' => $token
                    ], 200);
                } else {
                    return response()->json(['message' => 'Mật khẩu không đúng.'], 404);
                }
            } else {
                return response()->json(['message' => 'Tài khoản đã bị xóa.'], 404);
            }
        } else {
            return response()->json(['message' => 'Email không tồn tại.'], 404);
        }
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), 
            [
                'username'  => 'required|alpha_dash|unique:users|max:255',
                'email'     => 'required|unique:users|max:255',
                'password'  => 'required',
            ],
            [
                'required'  => ':attribute không được để trống.',
                'alpha_dash' => ':attribute không được chứa dấu cách, dấu chấm, ký tự đặc biệt.',
                'unique'    => ':attribute đã tồn tại.',
            ], 
            [
                'username'  => 'Username',
                'email'     => 'Email',
                'password'  => 'Password',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 404);
        }

        $user = new User;
        $user->username = trim($request->username);
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json(['message' => 'Successful'], 200);
    }

    public function logout(Request $request) {
        if(Auth::user()) {
            $token = $request->user()->token();
            $token->revoke();
            return response()->json(['message' => 'Đăng xuất thành công.'], 200);
        } else {
            return response()->json(['message' => 'Chưa đăng nhập.'], 200);
        }
    }

    public function profile(Request $request, $username) {
        $user = User::whereNull('deleted_at')->where('username', $username)->first();
        if($user != null) {
            return response()->json([
                'user' => $user
            ], 200);
        }
        return response()->json(['message' => 'Not Found.'], 404);
    }
    public function profileUpdate(Request $request, $id) {
        $request = $request->data;

        $user = User::find($id);
        if($request['old_password'] != '') {
            if(!Hash::check($request['old_password'], $user->password)) {
                return response()->json(['message' => 'Old password is incorrect.'], 404);
            }
        }

        $validator = Validator::make($request, 
            [
                'username'  => 'required|alpha_dash|unique:users,username,'.$id.',id,deleted_at,NULL|max:100',
                'email'     => 'required|unique:users,email,'.$id.',id,deleted_at,NULL|max:100',
                'password'  => 'nullable|confirmed|min:6'
            ],
            [
                'required'  => ':attribute không được để trống.',
                'alpha_dash' => ':attribute không được chứa dấu cách, dấu chấm, ký tự đặc biệt.',
                'unique'    => ':attribute đã tồn tại.',
                'confirmed' => ':attribute xác nhận không khớp. Hãy nhập lại.'
            ],
            [
                'username'  => 'Username',
                'email'     => 'Email',
                'password'  => 'Password',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 404);
        }

        $user->name = $request['name'];
        $user->username = trim($request['username']);
        $user->email = $request['email'];
        $user->address = $request['address'];
        $user->phone_number = $request['phone_number'];
        if($request['password'] != '') {
            $user->password = bcrypt($request['password']);
        };
        
        $user->save();
        return response()->json($user, 200);
    }

    public function user() {
        return response()->json(auth()->user());  
    }

    //Reset Password, check validate mail and send email
    public function resetPassword(Request $request) {
        $validator = Validator::make($request->all(), [
        	'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Email không hợp lệ.'], 404);
        }
        $user = User::where('email', $request->email)->first();
        if($user) {
            $password = Str::random(8);
            $user->password = bcrypt($password);
            $user->save();
            Mail::to($request->email)->send(new mailResetPassword($user->username, $password));
            return response()->json([
                'message' => 'success'
            ], 200);
        }
        return response()->json(['message' => 'Email không tồn tại.'], 404);
    }
}