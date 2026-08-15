<?php
namespace App\Middleware;

use Auth;
use Closure;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response\RedirectResponse;

class Authorization
{
    public function handle(ServerRequestInterface $request, Closure $next)
    {
        if (Auth::check()) {            
            // Call the next middleware/controller
            return $next($request);
        }

        return new RedirectResponse('/');
    }
}