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
				$listbox = view('admin.ToDoList');
				$row->column(12, new Box('待办事项', $listbox));
            });

    }
	// get member count
	protected function memberCount()
	{
		$rows = \App\Model\member::count();
		return $rows;
	}
	
}
