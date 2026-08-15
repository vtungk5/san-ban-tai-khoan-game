<?php

namespace App\Controllers\Client;

use App\Models\Film;
use GuzzleHttp\Client;
use Laminas\Diactoros\ServerRequest;
use MiladRahimi\PhpRouter\View\View;
use Auth;
use Laminas\Diactoros\Response\RedirectResponse;

class PAGES
{
    public function index(View $view)
    {
        return $view->make('client/index');
    }

    public function payment(View $view)
    {
        return $view->make('client/payment');
    }

    public function profile(View $view)
    {
        return $view->make('client/info/profile');
    }

    public function security(View $view)
    {
        return $view->make('client/info/security');
    }

    public function history(View $view)
    {
        return $view->make('client/info/history');
    }
    
    public function logout(View $view)
    {
        $Auth = new Auth();
        $Auth->logout();
        return new RedirectResponse('/login');
    }
    
    public function search(View $view,ServerRequest $request)
    {
        
        $keywords = "Tất cả";

        if (isset($request->getQueryParams()['q'])){
            $keywords = $request->getQueryParams()['q'];
        }
        
        $data = [
            "keywords"=>$keywords
        ];
        return $view->make('client/search',$data);

    }

    public function login(View $view)
    {
        return $view->make('client/auth/login');
    }

    public function register(View $view)
    {
        return $view->make('client/auth/register');
    }
    
}
