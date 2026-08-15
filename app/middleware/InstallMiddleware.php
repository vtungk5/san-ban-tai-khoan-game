<?php
namespace App\Middleware;

use App\Models\User;
use Closure;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ServerRequestInterface;

class InstallMiddleware
{
    public function handle(ServerRequestInterface $request, Closure $next)
    {

        if (empty(User::num())):
            return $next($request);
        endif;

        return new RedirectResponse('/');
    }
}
