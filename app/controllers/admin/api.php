<?php

namespace App\Controllers\Admin;

use App\Models\User;
use GuzzleHttp\Client;
use Laminas\Diactoros\ServerRequest;
use stdClass;
use Hash;
use Auth;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Account;

class API
{
    public function header()
    {
        header("Content-Type: application/json; charset=UTF-8");
    }

    public function Update_Setting(ServerRequest $request)
    {
        $this->header();
        $body = objectToArray($request->getParsedBody());

        $title = isset($body->title) ? str($body->title) : null;
        $keywords = isset($body->keywords) ? str($body->keywords) : null;
        $description = isset($body->description) ? str($body->description) : null;
        $partner_id = isset($body->partner_id) ? $body->partner_id : null;
        $partner_key = isset($body->partner_key) ? $body->partner_key : null;
        $signature = isset($body->signature) ? $body->signature : null;

        if (empty($title)):
            $data['status'] = "warning";
            $data['message'] = "Tiêu đề không được bỏ trống";
        elseif (empty($keywords)):
            $data['status'] = "warning";
            $data['message'] = "Từ khóa không được bỏ trống";
        elseif (empty($description)):
            $data['status'] = "warning";
            $data['message'] = "Mô tả không được bỏ trống";
        else:
            $save = new stdClass;
            $save->title = $title;
            $save->keywords = $keywords;
            $save->description = $description;
            $save->partner_id = $partner_id;
            $save->partner_key = $partner_key;
            $save->signature = $signature;

            if (Setting::set($save)->update()):
                $data['status'] = "success";
                $data['message'] = "Cập nhật cài đặt thành công";
            else:
                $data['status'] = "error";
                $data['message'] = "Cập nhật cài đặt thất bại";
            endif;

        endif;

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }


    public function Toggle_Users_Status($uid)
    {
        $this->header();

        $user = User::where(["uid" => $uid])->first();

        if (!$user):
            $data['status'] = "error";
            $data['message'] = "Người dùng không tồn tại";
        elseif (User::where(["uid" => $uid])->set(['status' => ($user->status === 'active') ? 'lock' : 'active'])->update()):
            $data['status'] = "success";
            $data['message'] = "Trạng thái người dùng đã được cập nhật thành công";
        else:
            $data['status'] = "error";
            $data['message'] = "Không thể cập nhật trạng thái người dùng";
        endif;


        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function Update_Users(ServerRequest $request, $uid)
    {
        $this->header();

        $body = objectToArray($request->getParsedBody());

        $fullname = isset($body->fullname) ? str($body->fullname) : null;
        $money = isset($body->money) ? (float)$body->money : null;
        $email = isset($body->email) ? str($body->email) : null;
        $level = isset($body->level) ? str($body->level) : null;

        $user = User::where(["uid" => $uid])->first();

        if (!$user):
            $data['status'] = "error";
            $data['message'] = "Người dùng không tồn tại";
        elseif (empty($fullname)):
            $data['status'] = "warning";
            $data['message'] = "Họ và tên không được bỏ trống";
        elseif ($money == ""):
            $data['status'] = "warning";
            $data['message'] = "Số dư không hợp lệ";
        elseif (empty($email)):
            $data['status'] = "warning";
            $data['message'] = "Địa chỉ email không được bỏ trống";
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)):
            $data['status'] = "error";
            $data['message'] = "Địa chỉ email không hợp lệ";
        else:

            $find = User::where(["email" => $email])->first();

            $check = true;

            if (!empty($find)):
                if ($uid != $find->uid):
                    $check = false;
                endif;
            endif;

            $save = new stdClass;
            $save->fullname = $fullname;
            $save->money = $money;
            $save->email = $email;
            $save->level = $level;

            if (!$check):

                $data['status'] = "error";
                $data['message'] = "Email đã tồn tại";

            elseif (User::where(["uid" => $uid])->set($save)->update()):

                $data['status'] = "success";
                $data['message'] = "Cập nhật thông tin người dùng thành công";

            else:

                $data['status'] = "error";
                $data['message'] = "Cập nhật thông tin người dùng thất bại";

            endif;

        endif;

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function Delete_Users($uid)
    {
        $this->header();

        $user = User::where(["uid" => $uid])->first();

        if (!$user) {
            $data['status'] = "error";
            $data['message'] = "Người dùng không tồn tại";
        } else {
            if (User::where(["uid" => $uid])->delete()) {
                $data['status'] = "success";
                $data['message'] = "Xóa người dùng thành công";
                $data['redirect'] = "?ok";
            } else {
                $data['status'] = "error";
                $data['message'] = "Không thể xóa người dùng";
            }
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function Add_Service(ServerRequest $request)
    {
        $this->header();

        $body = objectToArray($request->getParsedBody());

        $logo = isset($_FILES['logo']) ? $_FILES['logo'] : null;
        $name = isset($body->name) ? $body->name : null;
        $path = isset($body->path) ? $body->path : null;
        $type = isset($body->type) ? $body->type : null;

        $check = Service::where(["path" => $path])->first();

        if ($check):
            $data['status'] = "error";
            $data['message'] = "Đường dẫn đã tồn tại";
        elseif (empty($logo) || !$logo):
            $data['status'] = "warning";
            $data['message'] = "Logo không được bỏ trống";
        elseif (empty($name)):
            $data['status'] = "warning";
            $data['message'] = "Tên dịch vụ không được bỏ trống";

        elseif (empty($path)):
            $data['status'] = "warning";
            $data['message'] = "Đường dẫn không được bỏ trống";
        elseif (!preg_match('/^[a-zA-Z0-9\-\/_]+$/', $path)):
            $data['status'] = "error";
            $data['message'] = "Đường dẫn không hợp lệ. Chỉ được chứa chữ cái, số, dấu gạch ngang (-), gạch dưới (_) và dấu gạch chéo (/).";
        elseif (empty($type)):
            $data['status'] = "warning";
            $data['message'] = "Thể loại không được bỏ trống";
        else:

            $result = upload_images($logo, "service");

            if (!$result):

                $data['status'] = "error";
                $data['message'] = "Ảnh không hợp lệ";

            else:

                $save = new stdClass;
                $save->logo = $result;
                $save->name = $name;
                $save->path = $path;

                if (Service::set($save)->insert()):

                    $data['status'] = "success";
                    $data['message'] = "Thêm dịch vụ thành công";

                else:

                    $data['status'] = "error";
                    $data['message'] = "Thêm dịch vụ thất bại";

                endif;

            endif;
        endif;

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function Edit_Service(ServerRequest $request, $id)
    {
        $this->header();

        $body = objectToArray($request->getParsedBody());
        $logo = isset($_FILES['logo']) ? $_FILES['logo'] : null;
        $name = isset($body->name) ? $body->name : null;
        $path = isset($body->path) ? $body->path : null;
        $type = isset($body->type) ? $body->type : null;

        $service = Service::where(['id' => $id])->first();

        if (!$service):
            $data['status'] = "error";
            $data['message'] = "Dịch vụ không tồn tại";
        elseif (empty($name)):
            $data['status'] = "warning";
            $data['message'] = "Tên dịch vụ không được bỏ trống";
        elseif (empty($path)):
            $data['status'] = "warning";
            $data['message'] = "Đường dẫn không được bỏ trống";
        elseif (!preg_match('/^[a-zA-Z0-9\-\/_]+$/', $path)):
            $data['status'] = "error";
            $data['message'] = "Đường dẫn không hợp lệ. Chỉ được chứa chữ cái, số, dấu gạch ngang (-), gạch dưới (_) và dấu gạch chéo (/).";
        elseif (empty($type)):
            $data['status'] = "warning";
            $data['message'] = "Thể loại không được bỏ trống";
        else:

            $find = Service::where(["path" => $path])->first();

            $check = true;

            if (!empty($find)):
                if ($id != $find->id):
                    $check = false;
                endif;
            endif;

            if (!$check):

                $data['status'] = "error";
                $data['message'] = "Đường dẫn đã tồn tại";

            else:

                $save = new stdClass;
                $save->name = $name;
                $save->path = $path;
                $save->type = $type;

                $sm = true;

                if (!empty($logo['name'])):
                    $result = upload_images($logo, "service");

                    if ($result):
                        $oldLogoPath = $_SERVER['DOCUMENT_ROOT'] . "/public" . $service->logo;

                        if (file_exists($oldLogoPath)):
                            unlink($oldLogoPath);
                        endif;

                        $save->logo = $result;
                    else:
                        $sm = false;
                    endif;
                endif;

                if (!$sm):

                    $data['status'] = "error";
                    $data['message'] = "Ảnh không hợp lệ" . $logo['name'];

                elseif (Service::where(['id' => $id])->set($save)->update()):
                    $data['status'] = "success";
                    $data['message'] = "Cập nhật dịch vụ thành công";
                else:
                    $data['status'] = "error";
                    $data['message'] = "Cập nhật dịch vụ thất bại";
                endif;

            endif;

        endif;

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function Delete_Service($id)
    {
        $this->header();

        $find = Service::where(["id" => $id])->first();

        if (!$find) {
            $data['status'] = "error";
            $data['message'] = "Dịch vụ không tồn tại";
        } else {
            if (Service::where(["id" => $id])->delete()) {
                $data['status'] = "success";
                $data['message'] = "Xóa dịch vụ thành công";
                $data['redirect'] = "?ok";
            } else {
                $data['status'] = "error";
                $data['message'] = "Không thể xóa dịch vụ";
            }
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function Add_Account(ServerRequest $request)
    {
        $this->header();

        $body = objectToArray($request->getParsedBody());

        $name = isset($body->name) ? $body->name : null;

        $check = Account::where(["name" => $name])->first();

        if ($check):

            $data['status'] = "error";
            $data['message'] = "Tên loại tài khoản đã tồn tại";

        elseif (empty($name)):

            $data['status'] = "warning";
            $data['message'] = "Tên loại tài khoản không được bỏ trống";

        else:

            $save = new stdClass;
            $save->name = $name;

            if (Account::set($save)->insert()):

                $data['status'] = "success";
                $data['message'] = "Thêm loại tài khoản thành công";

            else:

                $data['status'] = "error";
                $data['message'] = "Thêm loại tài khoản thất bại";

            endif;

        endif;

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function Edit_Account(ServerRequest $request, $id)
    {
        $this->header();

        $body = objectToArray($request->getParsedBody());
        $name = isset($body->name) ? $body->name : null;

        $Account = Account::where(['id' => $id])->first();

        if (!$Account):
            $data['status'] = "error";
            $data['message'] = "loại tài khoản không tồn tại";
        elseif (empty($name)):
            $data['status'] = "warning";
            $data['message'] = "Tên loại tài khoản không được bỏ trống";
        else:
            $check = true;

            if (!empty($find)):
                $find = Account::where(["name" => $name])->first();
                if ($id != $find->id):
                    $check = false;
                endif;
            endif;

            if (!$check):

                $data['status'] = "error";
                $data['message'] = "Tên loại tài khoản đã tồn tại";

            else:

                $save = new stdClass;
                $save->name = $name;

                if (Account::where(['id' => $id])->set($save)->update()):
                    $data['status'] = "success";
                    $data['message'] = "Cập nhật loại tài khoản thành công";
                else:
                    $data['status'] = "error";
                    $data['message'] = "Cập nhật loại tài khoản thất bại";
                endif;

            endif;

        endif;

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function Delete_Account($id)
    {
        $this->header();

        $find = Account::where(["id" => $id])->first();

        if (!$find) {
            $data['status'] = "error";
            $data['message'] = "loại tài khoản không tồn tại";
        } else {
            if (Account::where(["id" => $id])->delete()) {
                $data['status'] = "success";
                $data['message'] = "Xóa loại tài khoản thành công";
                $data['redirect'] = "?ok";
            } else {
                $data['status'] = "error";
                $data['message'] = "Không thể xóa loại tài khoản";
            }
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
