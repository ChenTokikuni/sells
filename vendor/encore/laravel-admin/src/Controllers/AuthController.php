<?php

namespace Encore\Admin\Controllers;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\DB;	//for get user status 


class AuthController extends Controller
{
    /**
     * @var string
     */
    protected $loginView = 'admin::login';

    /**
     * Show the login page.
     *
     * @return \Illuminate\Contracts\View\Factory|Redirect|\Illuminate\View\View
     */
    public function getLogin()
    {
        if ($this->guard()->check()) {
            return redirect($this->redirectPath());
        }

        return view($this->loginView);
    }

    /**
     * Handle a login request.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function postLogin(Request $request)
    {
        $this->loginValidator($request->all())->validate();

        $credentials = $request->only([$this->username(), 'password']);
        $remember = $request->get('remember', false);
		
		/*transform ip format to database ip*/
		$user_ip = $request->ip();
		/*get ip for data base*/
		$checkip = DB::table('admin_users')
				->select('ip')
				->where('username','like',$credentials['username'])->get()->toArray();
				
		//print_r(empty($checkip));exit;
		$ip_new_array =[];
		if(!empty($checkip)){
			$checkip = array_values(json_decode($checkip['0']->ip, true) ?: []);
		
			
			$default_num = 0;
			foreach($checkip as $v){
				foreach($v as $v1){
					$ip_new_array[$default_num] = $v1;
					$default_num = $default_num+1;
				}
			}
			
				
			$message = '您的IP不允许访问';
			if(empty($ip_new_array)){
				$message = '您的帐号尚未设定IP白名单';
			}
				

		}
		
		
		
		if(in_array($user_ip,$ip_new_array)){
			
			
			
			if ($this->guard()->attempt($credentials, $remember)) {
				/*write login log*/
				DB::table('admin_operation_log')->insert(
					[
						'user_id'=> Admin::user()->id,
						'path'=>'admin/auth/login',
						'method'=>'OPTIONS',
						'ip'=>$request->ip(),
						'input'=>'{"action":"登入"}',
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s')
					]
				);
				
				return $this->sendLoginResponse($request);
			}else{
				$message = '您的帐号或密码输入错误';
			}
		}
		
			
		
		
        return back()->withInput()->withErrors([
            $this->username() => $message,
        ]);
    }

    /**
     * Get a validator for an incoming login request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function loginValidator(array $data)
    {
        return Validator::make($data, [
            $this->username()   => 'required',
            'password'          => 'required',
        ]);
    }

    /**
     * User logout.
     *
     * @return Redirect
     */
    public function getLogout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect(config('admin.route.prefix'));
    }

    /**
     * User setting page.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function getSetting(Content $content)
    {
        $form = $this->settingForm();
        $form->tools(
            function (Form\Tools $tools) {
                $tools->disableList();
            }
        );

        return $content
            ->header(trans('admin.user_setting'))
            ->body($form->edit(Admin::user()->id));
    }

    /**
     * Update user setting.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function putSetting()
    {
        return $this->settingForm()->update(Admin::user()->id);
    }

    /**
     * Model-form for user setting.
     *
     * @return Form
     */
    protected function settingForm()
    {
        $class = config('admin.database.users_model');

        $form = new Form(new $class());

		$form->tools(function (Form\Tools $tools) {
			
			//$tools->disableList();	//列表
			$tools->disableDelete();	//刪除
			$tools->disableView();	//查看
						
		});
		//關閉底部按鈕
		$form->footer(function ($footer) {

			// 去掉`重置`按钮
			$footer->disableReset();

			// 去掉`提交`按钮
			//$footer->disableSubmit();

			// 去掉`查看`checkbox
			$footer->disableViewCheck();

			// 去掉`继续编辑`checkbox
			$footer->disableEditingCheck();

			// 去掉`继续创建`checkbox
			$footer->disableCreatingCheck();

		});
		
        $form->display('username', trans('admin.username'));
        $form->text('name', trans('admin.name'))->rules('required');
        $form->image('avatar', trans('admin.avatar'));
        $form->password('password', trans('admin.password'))->rules('confirmed|required');
        $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
            ->default(function ($form) {
                return $form->model()->password;
            });

        $form->setAction(admin_base_path('auth/setting'));

        $form->ignore(['password_confirmation']);

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = bcrypt($form->password);
            }
        });

        $form->saved(function () {
            admin_toastr(trans('admin.update_succeeded'));

            return redirect(admin_base_path('auth/setting'));
        });

        return $form;
    }

    /**
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    protected function getFailedLoginMessage()
    {
        return Lang::has('auth.failed')
            ? trans('auth.failed')
            : 'These credentials do not match our records.';
    }

    /**
     * Get the post login redirect path.
     *
     * @return string
     */
    protected function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : config('admin.route.prefix');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        admin_toastr(trans('admin.login_successful'));

        $request->session()->regenerate();

		/*write user session id*/
				$user_session_id = session()->getId();
				DB::table('admin_users')
				->where('id','like',Admin::user()->id)
				->update(['session_id'=>$user_session_id,'updated_at' => date('Y-m-d H:i:s')]);
		/**/

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    protected function username()
    {
        return 'username';
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('admin');
    }
	
	//transform ip format to database ip
	protected function ipReturn($ip)
    {
		
		if(strlen($ip)!='15'){
			
			$return_ip = explode('.',$ip);
			
			foreach($return_ip as $k=>$v){
				
				$return_ip[$k] = str_pad($return_ip[$k],3,'0',STR_PAD_LEFT);
			}
			$return_ip = implode('.', $return_ip);
		}
		
		return $return_ip;
	}
}
