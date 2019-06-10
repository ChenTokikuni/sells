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
				
				//infobox
                $row->column(6, new InfoBox('目前在线帐号人数', 'users', 'green', config('admin.route.prefix') . '/auth/users', '2'));
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
}
