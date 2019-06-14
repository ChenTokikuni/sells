<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

use Encore\Admin\Widgets\InfoBox;	//infobox
use Encore\Admin\Widgets\Box;		//box
use Illuminate\Support\Facades\DB;	//sql for DB
use Encore\Admin\Tree;				//test
use Illuminate\Http\Request; 		//request data

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('首页')
            
            
            ->row(function ($row) {
				
				// get member count
				$member_count = $this->memberCount();
				
				$getLoginstatus = $this->getLoginstatus();
				
				//infobox
                $row->column(6, new InfoBox('目前在线帐号人数', 'users', 'green', config('admin.route.prefix') . '/auth/users', $getLoginstatus));
                $row->column(6, new InfoBox('会员列表总数', 'list-alt', 'orange', config('admin.route.prefix') . '/member', $member_count));
				
				//to do list box
				$listdatas = $this->listData();
				$listbox = view('admin.ToDoList', compact('listdatas'));
				$row->column(12, new Box('待办事项', $listbox));
            });

    }
	// get member count
	protected function memberCount()
	{
		$rows = \App\Model\member::count();
		return $rows;
	}
	protected function listData()
	{
		$listdata = DB::table('todolist')
						->select('id','text')
						->orderBy('id')
						->get()
						->toArray();

		//print_r($listdata);exit;
		return $listdata;
	}
	
	//to do list api when list data change
	protected function listChange(Request $request)
	{
		//check api request data
		if(isset($request)){
			$input = $request->all();
			$list_action = $input['action'];
			$list_id = $input['id'];
			$list_text = $input['text'];
			
		}else{
			$request = [
			'error'=>'错误'
			];
			return $request;
		}
		
		//data action edit
		if($list_action == 'edit'){
			$listdata = DB::table('todolist')
						->where('id' ,'=',$list_id)
						->update(['text'=>$list_text]);
						
						
			$request = [
			'error'=>'修改成功'
			];
		}
		
		//data action remove
		if($list_action == 'remove'){
			$listdata = DB::table('todolist')
						->delete($list_id);
						
			$request = [
			'error'=>'删除成功'
			];
		}
		
		//data action add
		if($list_action == 'add'){
			$last_num = DB::table('todolist')
						->max('id');
			$new_num = $last_num+1;
			$listdata = DB::table('todolist')
						->insert(['id'=>$new_num,'text' => $list_text]);
							
			$request = [
			'error'=>'新增成功'
			];
		}
		return $request;
	}
	protected function getLoginstatus(){
		
		//get all user id
		$user_id = DB::table('admin_users')
					->select('id','name')
					->get()
					->toArray();
		$online = $total_user = count($user_id);
		$user_ids =[];
		$default = 0;
		$outline = 0;
		
		$session_timeout = date("Y-m-d H:i:s ",strtotime(date("Y-m-d H:i:s ").'-2 hours'));
		foreach($user_id as $v){
			$user_lastlogin[$default] =$this->lastLogin($v->id,$session_timeout);
			$default = $default+1;
			
		}
		
		//print_r($user_lastlogin);exit;
		$user_lastlogout = [];
		$default = 0;
		foreach($user_lastlogin as $v){
			if(isset($v->created_at)){
				
			$last_time = date("Y-m-d H:i:s ",strtotime($v->created_at.'+2 hours'));
			$user_lastlogout[$default] = $this->lastLogout($v->user_id,$v->created_at,$last_time);
			$default = $default+1;
			}else{
				$online =$online-1;
			}
		}//print_r($user_lastlogout);exit;
		
		
		foreach($user_lastlogout as $v){
			if(isset($v)){
				$online = $online-1;
			}
		}
		return $online;
	}
	
	protected function lastLogin($user_ids,$session_timeout){
	
		$last_login = DB::table('admin_operation_log')
			->where('path', '=' ,'admin/auth/login')
			->where('user_id','=',$user_ids)
			->where('created_at','>',$session_timeout)
			->orderBy('id','desc')
			->first();
		
		return $last_login;
	}
	protected function lastLogout($user_id,$first_time,$last_time){
		
		$last_logout = DB::table('admin_operation_log')
			->where('path', '=' ,'admin/auth/logout')
			->where('user_id','=',$user_id)
			->whereBetween('created_at',[$first_time,$last_time])
			->orderBy('id','desc')
			->first();
			
		return $last_logout;
		
	}
}
