<?php

namespace App\Admin\Controllers;

use App\Model\member;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;		//用戶判斷

use Illuminate\Support\Facades\DB;	//sql語法判斷

class MemberController extends Controller
{
	
   use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('会员')
            ->description('列表')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('会员')
            ->description('检视')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('会员')
            ->description('编辑')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('会员')
            ->description('新建')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new member);
		
		//關閉導出
		//$grid->disableExport();
		$grid->disableColumnSelector();//关闭数据表格列选择器
		
		//自訂
		$grid->filter(function($filter){

			$filter->disableIdFilter();

			// 在这里添加字段过滤器
			$filter->column(1/2, function ($filter) {
				
				$filter->like('account', '会员帐号')->select($this->getAccount());
				
				$filter->like('name', '姓名')->select($this->getName());
			
				$filter->between('registration_date', '注册日期')->datetime();
				
				$filter->between('last_login', '最后登录日')->datetime();
				
			});
			$filter->column(1/2, function ($filter) {
				
				$filter->group('save_count', '存款次数', function ($group) {
						
						$group->equal('等于');
						$group->notEqual('不等于');
						$group->gt('大于');
						$group->lt('小于');
						$group->nlt('大于等于');
						$group->ngt('小于等于');
						
						
				});
					
				$filter->group('pay_count', '出款次数', function ($group) {
						
						$group->equal('等于');
						$group->notEqual('不等于');
						$group->gt('大于');
						$group->lt('小于');
						$group->nlt('大于等于');
						$group->ngt('小于等于');
						
						
				});
				
				$filter->group('total_save', '总存款金额', function ($group) {
						
						$group->equal('等于');
						$group->notEqual('不等于');
						$group->gt('大于');
						$group->lt('小于');
						$group->nlt('大于等于');
						$group->ngt('小于等于');
						
						
				});
				
				$filter->group('total_pay', '总出款金额', function ($group) {
						
						$group->equal('等于');
						$group->notEqual('不等于');
						$group->gt('大于');
						$group->lt('小于');
						$group->nlt('大于等于');
						$group->ngt('小于等于');
						
						
				});
				
				$filter->group('offline_days', '多久没登录', function ($group) {
						
						$group->equal('等于');
						$group->notEqual('不等于');
						$group->gt('大于');
						$group->lt('小于');
						$group->nlt('大于等于');
						$group->ngt('小于等于');
						
						
				});
				
			});
		});
		
		
		if (!Admin::user()->can('data_edit')) {
			 
				//禁用操作列
				$grid->disableActions();
				
				//關閉新增按鈕
				$grid->disableCreateButton();
				
				//關閉導出
				$grid->disableExport();
				
				// 關閉選擇器
				$grid->disableRowSelector();
			 }

		// 關閉操作按鈕
		$grid->actions(function ($actions) {
				
				//$actions->disableEdit();
				
				$actions->disableView();
				
				//$actions->disableDelete();
			
		});
		
		
		$grid->model()->orderBy('created_at','DESC');
		
		$grid->column('account', '会员帐号');
		
		$grid->column('name', '姓名');
		
		$grid->column('phone_number', '手机号')->display(function () {
			
			return "<i class=\"fa fa-phone\"></i> $this->phone_number";
			
		});
		
		$grid->column('mail', '邮箱')->display(function () {
			
			return "<i class=\"fa fa-envelope\"></i> $this->mail";
			
		});

		$grid->column('qq_number', 'QQ号')->display(function () {
			
			return "<i class=\"fa fa-qq\"></i> $this->qq_number";
			
		});
		
		$grid->column('bank_number', '银行帐号')->display(function () {
			
			return "<i class=\"fa fa-bank\"></i> $this->bank_number";
			
		});
		
		$grid->column('save_count', '存款次数')->display(function() {
			
			return $this->pay_count.'次';
		});
		
		$grid->column('pay_count', '出款次数')->display(function() {
			
			return $this->pay_count.'次';
		});
		
		$grid->column('total_save', '总存款金额')->display(function() {
			
			return "<i class=\"fa fa-cny\"></i> ".$this->total_save.'元';
		});
		
		$grid->column('total_pay', '总出款金额')->display(function() {
			
			return "<i class=\"fa fa-cny\"></i> ".$this->total_pay.'元';
		});
		
		$grid->column('registration_date', '注册日期');
		
		$grid->column('last_login', '最后登录日');
		
		$grid->column('offline_days', '多久没登录')->display(function() {
			
			return $this->offline_days.'天';
		});
		
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(member::findOrFail($id));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new member);
		
		$form->tools(function (Form\Tools $tools) {
			$tools->disableView();
			$tools->disableDelete();
			/*
			$tools->disableList();
			$tools->disableBackButton();
			$tools->disableListButton();
			*/
		});
		
		$form->footer(function ($footer) {

			// 去掉`重置`按钮
			//$footer->disableReset();

			// 去掉`提交`按钮
			//$footer->disableSubmit();

			// 去掉`查看`checkbox
			$footer->disableViewCheck();

			// 去掉`继续编辑`checkbox
			$footer->disableEditingCheck();

			// 去掉`继续创建`checkbox
			$footer->disableCreatingCheck();

		});
		
		$form->text('account', '会员帐号');
		
		$form->text('name', '姓名');
		
		$form->mobile('phone_number', '手机号')->options(['mask' => '999 9999 9999']);
		
		$form->email('mail', '邮箱');
		
		$form->text('qq_number', 'QQ号');
		
		$form->text('bank_number', '银行帐号');
		
		$form->number('save_count', '存款次数')->default('0')->min('0');
		
		$form->number('pay_count', '出款次数')->default('0')->min('0');
		
		$form->currency('total_save', '总存款金额')->symbol('CNY');
		
		$form->currency('total_pay', '总出款金额')->symbol('CNY');
		
		$form->datetime('registration_date', '注册日期');
		
		$form->datetime('last_login', '最后登录日');
		
		$form->number('offline_days', '多久没登录')->default('0')->min('0');

        return $form;
    }
	//帳號選項
	public function getAccount(){
		$options = [];

		$rows = \App\Model\member::all();
		foreach ($rows as $row) {
			$options[$row->id] = $row->account;
		}
		return $options;
	}
	//姓名選項
	public function getName(){
		$options = [];

		$rows = \App\Model\member::all();
		foreach ($rows as $row) {
			$options[$row->id] = $row->name;
		}
		return $options;
	}
}

