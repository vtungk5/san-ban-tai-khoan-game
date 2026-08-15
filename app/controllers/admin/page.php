<?php

namespace App\Controllers\Admin;

use App\Models\Film;
use GuzzleHttp\Client;
use Laminas\Diactoros\ServerRequest;
use MiladRahimi\PhpRouter\View\View;
use Auth;
use Laminas\Diactoros\Response\RedirectResponse;
use App\Models\User;
use App\Models\Service;
use App\Models\Account;

class PAGES
{
    public function index(View $view)
    {
        return $view->make('admin/index');
    }

    public function setting(View $view)
    {
        return $view->make('admin/setting');
    }

    public function product(View $view)
    {
        return $view->make('admin/product/list');
    }

    public function add_product(View $view)
    {
        return $view->make('admin/product/add');
    }

    public function account(View $view)
    {
        return $view->make('admin/account/list');
    }

    public function add_account(View $view)
    {
        return $view->make('admin/account/add');
    }

    public function edit_account(View $view,$id)
    {
        $data = Account::where(["id" => $id])->first();

        if (!$data) {
            return new RedirectResponse("/admin/account/list");
        }

        return $view->make('admin/account/edit', ['get' => $data]);
    }

    public function service(View $view)
    {
        return $view->make('admin/service/list');
    }

    public function add_service(View $view)
    {
        return $view->make('admin/service/add');
    }

    public function edit_service(View $view,$id)
    {
        $data = Service::where(["id" => $id])->first();

        if (!$data) {
            return new RedirectResponse("/admin/service/list");
        }

        return $view->make('admin/service/edit', ['get' => $data]);
    }

    public function users(View $view, $type, $status = "all")
    {
        $validTypes = ["member", "admin"];
        $validStatuses = ["active", "lock"];

        if (!in_array($type, $validTypes)) {
            $type = "member";
        }

        if (in_array($status, $validStatuses)) {
            $list = User::where(["level" => $type, "status" => $status])->list();
        } else {
            $list = User::where(["level" => $type])->list();
        }


        return $view->make("admin/users/list", ["list" => $list]);
    }

    public function edit_users(View $view, $type, $uid)
    {
        $user = User::where(["uid" => $uid])->first();
        $validTypes = ["member", "admin"];

        if (!in_array($type, $validTypes)) {
            $type = "member";
        }

        if (!$user) {
            return new RedirectResponse("/admin/users/$type");
        }

        return $view->make('admin/users/edit', ['get' => $user]);
    }
}
