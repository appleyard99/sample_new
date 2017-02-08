<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Authenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {//验证时否是游客
            if ($request->ajax()) {//若是游客且请求方式是ajax时抛出401的相应
                return response('Unauthorized.', 401);
            } else {
                //return redirect()->guest('auth/login');
                return redirect()->guest('login');//否则跳转到登陆界面
            }
        }

        return $next($request);
    }
}
