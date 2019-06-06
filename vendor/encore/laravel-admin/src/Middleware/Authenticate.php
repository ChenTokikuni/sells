<?php

namespace Encore\Admin\Middleware;

use Closure;
use Encore\Admin\Admin;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;	//sql語法判斷


class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $redirectTo = admin_base_path(config('admin.auth.redirect_to', 'auth/login'));

        if (Auth::guard('admin')->guest() && !$this->shouldPassThrough($request)) {
			
            return redirect()->guest($redirectTo);
        }
		
		if(!$this->shouldPassThrough($request)){
			$default_num = 0;
			$data_session = DB::table('admin_users')
							->select('session_id')
							->get()->toArray();
			$user_session = session()->getId();
			foreach($data_session as $v){
				$session_data[$default_num] = $v->session_id;
				$default_num = $default_num+1;
			}
			if(!in_array($user_session,$session_data)){
				
				Auth::guard('admin')->logout();
				$request->session()->invalidate();

				return redirect(config('admin.route.prefix'));
			}
		}
		
		 
		
        return $next($request);
    }

    /**
     * Determine if the request has a URI that should pass through verification.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function shouldPassThrough($request)
    {
        $excepts = config('admin.auth.excepts', [
            'auth/login',
            'auth/logout',
        ]);

        return collect($excepts)
            ->map('admin_base_path')
            ->contains(function ($except) use ($request) {
                if ($except !== '/') {
                    $except = trim($except, '/');
                }

                return $request->is($except);
            });
    }
	
}
