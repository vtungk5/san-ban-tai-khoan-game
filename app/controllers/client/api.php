<?php

namespace App\Controllers\Client;

use App\Models\User;
use GuzzleHttp\Client;
use Laminas\Diactoros\ServerRequest;
use stdClass;
use Hash;
use Auth;

class API
{
    public function header()
    {
        header("Content-Type: application/json; charset=UTF-8");
    }

    public function register(ServerRequest $request)
    {
        $this->header();
        $body = objectToArray($request->getParsedBody());
        $fullname = isset($body->fullname) ? str($body->fullname) : null;
        $username = isset($body->username) ? str($body->username) : null;
        $email = isset($body->email) ? str($body->email) : null;
        $password = isset($body->password) ? $body->password : null;
        $rePassword = isset($body->re_password) ? $body->re_password : null;

        $data = [];

        if (empty($fullname)):
            $data['status'] = "warning";
            $data['message'] = "Họ và tên không được bỏ trống";
        elseif (empty($username)):
            $data['status'] = "warning";
            $data['message'] = "Tên đăng nhập không được bỏ trống";
            
        elseif (!empty(User::where(["username" => $username])->count())):
            $data['status'] = "error";
            $data['message'] = "Tên đăng nhập đã tồn tại";
        elseif (empty($email)):
            $data['status'] = "warning";
            $data['message'] = "Email không được bỏ trống";
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)):
            $data['status'] = "error";
            $data['message'] = "Email không hợp lệ";
        elseif (!empty(User::where(["email" => $email])->count())):
            $data['status'] = "error";
            $data['message'] = "Email đã được sử dụng";
        elseif (empty($password)):
            $data['status'] = "warning";
            $data['message'] = "Mật khẩu không được bỏ trống";
        elseif (strlen($password) < 6):
            $data['status'] = "error";
            $data['message'] = "Mật khẩu phải có ít nhất 6 ký tự";
        elseif (empty($rePassword)):
            $data['status'] = "warning";
            $data['message'] = "Vui lòng nhập lại mật khẩu";
        elseif ($password !== $rePassword):
            $data['status'] = "error";
            $data['message'] = "Mật khẩu nhập lại không khớp";
        else:

            $save = new stdClass;
            $save->fullname = $fullname;
            $save->username = $username;
            $save->email = $email;
            $save->password = Hash::make($password);
            $save->level = "member";
            $save->money = 0;
            $save->status = "active";

            if (Auth::save($save)):

                $data['status'] = "success";
                $data['redirect'] = "/";
                $data['message'] = "Tạo tài khoản thành công";

            else :

                $data['status'] = "error";
                $data['message'] = "Tạo tài khoản thất bại";

            endif;

        endif;

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function login(ServerRequest $request)
    {
        $this->header();
        $body = objectToArray($request->getParsedBody());
        $account = isset($body->account) ? str($body->account) : null;
        $password = isset($body->password) ? $body->password : null;

        $check = false;

        if (!empty($account)):
            if (!empty(User::where(["uid" => $account])->count())):
                $check = "uid";
            elseif (!empty(User::where(["username" => $account])->count())):
                $check = "username";
            elseif (!empty(User::where(["email" => $account])->count())):
                $check = "email";
            endif;
        endif;

        if (empty($account)) :

            $data['status'] = "warning";
            $data['message'] = "Tài khoản không được bỏ trống";

        elseif (!$check) :

            $data['status'] = "error";
            $data['message'] = "Tài khoản không chính xác";

        elseif (empty($password)) :

            $data['status'] = "warning";
            $data['message'] = "Mật khẩu không được bỏ trống";

        else:

            $users = User::where(["$check" => $account])->first();

            if (!Hash::check($password, $users->password)):

                $data['status'] = "error";
                $data['message'] = "Mật khẩu tài khoản không chính xác";

            else:

                Auth::login($users);

                $data['status'] = "success";
                $data['redirect'] = "/";
                $data['message'] = "Đăng nhập tài khoản thành công";

            endif;

        endif;

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function changeinfo(ServerRequest $request){

        $this->header();
        $body = objectToArray($request->getParsedBody());
        $fullname = isset($body->fullname) ? str($body->fullname) : null;
        $email = isset($body->email) ? str($body->email) : null;


        if (empty($fullname)):
            $data['status'] = "warning";
            $data['message'] = "Họ và tên không được bỏ trống";
        
        elseif (empty($email)):
            $data['status'] = "warning";
            $data['message'] = "Email không được bỏ trống";
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)):
            $data['status'] = "error";
            $data['message'] = "Email không hợp lệ";
        else:
            $check = true;

            if (!empty(User::where(["email" => $email])->count())):
                if($email != Auth::user()->email):
                    $check = false;
                endif;
            endif;

            $save = new stdClass;
            $save->fullname = $fullname;
            $save->email = $email;

            if(!$check):
                $data['status'] = "error";
                $data['message'] = "Email đã tồn tại";
            elseif(User::where(["uid"=>Auth::user()->uid])->set($save)->update()):
                $data['status'] = "success";
                $data['message'] = "Cập nhập thông tin tài khoản thành công";
            else:
                $data['status'] = "error";
                $data['message'] = "Cập nhập thông tin tài khoản thất bại";           
            endif;

        endif;

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    }

    public function changepassword(ServerRequest $request)
    {
        $this->header();
        $body = objectToArray($request->getParsedBody());
    
        $oldPassword = isset($body->old_password) ? $body->old_password : null;
        $newPassword = isset($body->new_password) ? $body->new_password : null;
        $confirmPassword = isset($body->confirm_password) ? $body->confirm_password : null;
    
        if (empty($oldPassword)):
            $data['status'] = "warning";
            $data['message'] = "Mật khẩu cũ không được bỏ trống";
        elseif (empty($newPassword)):
            $data['status'] = "warning";
            $data['message'] = "Mật khẩu mới không được bỏ trống";
        elseif (empty($confirmPassword)):
            $data['status'] = "warning";
            $data['message'] = "Xác nhận mật khẩu mới không được bỏ trống";
        elseif ($newPassword !== $confirmPassword):
            $data['status'] = "error";
            $data['message'] = "Mật khẩu mới và xác nhận mật khẩu không khớp";
        else:
            $user = Auth::user();
    
            if (!Hash::check($oldPassword, $user->password)):
                $data['status'] = "error";
                $data['message'] = "Mật khẩu cũ không chính xác";
            else:
                $save = new stdClass;
                $save->password = Hash::make($newPassword);
    
                if (User::where(["uid"=>Auth::user()->uid])->set($save)->update()):
                    $data['status'] = "success";
                    $data['message'] = "Đổi mật khẩu thành công";
                else:
                    $data['status'] = "error";
                    $data['message'] = "Đổi mật khẩu thất bại";
                endif;
            endif;
        endif;
    
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    
}
