<?php

namespace App\Admin\Controllers;

use App\Model\logs;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class LogsController extends Controller
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
            ->header('活动大厅')
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
            ->header('活动大厅')
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
            ->header('活动大厅')
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
            ->header('活动大厅')
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
        $grid = new Grid(new activity);
		$grid->disableExport();
	
		$grid->disableExport();
		// 關閉選擇器
		$grid->disableRowSelector();
		//自訂
		$grid->filter(function($filter){

    
			$filter->disableIdFilter();

			// 在这里添加字段过滤器
			$filter->like('name', '活动名称');
			$filter->like('place', '活动顺序');
			
   

		});
		// 關閉搜尋
		 //$grid->disableFilter(); 
		// 關閉刪除按鈕
		
		$grid->actions(function ($actions) {
			/*
			$actions->disableEdit();
			*/
			$actions->disableView();
			/*
			$actions->disableDelete();
			*/
		});

		//$grid->column('setting_id', '编号');
		//$grid->column('demo_account', '测试帐号');
		
		/*
		$grid->column('link1', '立即注册');
		$grid->column('link2', '线路检测');
		$grid->column('link3', '资讯端下载');
		$grid->column('link4', '投诉建议');
		$grid->column('link5', '在线客服');
		$grid->column('link6', '立即加入');
		$grid->column('link7', '前往投注');
		$grid->column('link1_blank', '连结一另开');
		$grid->column('link2_blank', '连结二另开');
		$grid->column('link3_blank', '连结三另开');
		$grid->column('link4_blank', '连结四另开');
		$grid->column('link5_blank', '连结五另开');
		$grid->column('link6_blank', '连结六另开');
		$grid->column('link7_blank', '连结七另开');
		$grid->column('m_link1', '手机连结一');
		$grid->column('m_link2', '手机连结二');
		$grid->column('m_link3', '手机连结三');
		$grid->column('m_link4', '手机连结四');
		$grid->column('m_link5', '手机连结五');
		$grid->column('m_link6', '手机连结六');
		*/
		$grid->model()->orderBy('place');
		
		$grid->column('name', '名称');
		$grid->column('place', '顺序')->sortable();
 
		$grid->column('img1', '首页活动选单图片')->image('/', 200);
		$grid->column('img3', '列表活动选单图片')->image('/', 200);
		$grid->column('img4', '列表活动选单图片hover')->image('/', 200);
		$grid->column('img5', '背景电脑版图片')->image('/', 200);
		$grid->column('img7', '背景手机版图片')->image('/', 200);
		$grid->column('img6', '标题图片')->image('/', 200);
		
		
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
        $show = new Show(activity::findOrFail($id));

        $show->setting_id('Setting id');
        $show->title('Title');
        $show->demo_account('Demo account');
        $show->link1('Link1');
        $show->link2('Link2');
        $show->link3('Link3');
        $show->link4('Link4');
        $show->link5('Link5');
        $show->link6('Link6');
        $show->link7('Link7');
        $show->link1_blank('Link1 blank');
        $show->link2_blank('Link2 blank');
        $show->link3_blank('Link3 blank');
        $show->link4_blank('Link4 blank');
        $show->link5_blank('Link5 blank');
        $show->link6_blank('Link6 blank');
        $show->link7_blank('Link7 blank');
        $show->m_link1('M link1');
        $show->m_link2('M link2');
        $show->m_link3('M link3');
        $show->m_link4('M link4');
        $show->m_link5('M link5');
        $show->m_link6('M link6');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new activity);

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
		
		
		$form->tab('活动设定', function ($form) {
			
			$form->text('name', '名称');
			$form->number('place', '顺序')->default('0')->rules(function ($form) {

			 
			 
			 $a = [];
			 $rows = \App\Model\activity::all();
			 
			foreach ($rows as $row) {
				
					$a[$row->id] = $row->place;
				
				}
				

				
			if ($id = $form->model()->id) {
				
					$b = $a[$id];
			return "unique:activity,place,$id,id";
					
				}
				
			if (!$id = $form->model()->id) {
				
			
			return 'unique:activity,place';
					
				}

		},['警告:已有的活动顺序']);
			
			//$form->text('demo_account', '测试帐号');
		})->tab('活动首页图片设定', function ($form) {
			
			$form->image('img1', '首页活动选单图片')->help('图片格式:180x50')->rules('dimensions:max_width=180,max_height=50',['请上传正确图片格式'])->name(function ($file) {
		$id = rand(0,999).time();
	return $id.'img1.'.$file->guessExtension();
});
			
		})->tab('活动頁面图片设定', function ($form) {
			
			$form->image('img3', '活动列表选单图片')->help('图片格式:175x68')->rules('dimensions:width=175,height=68',['请上传正确图片格式'])->name(function ($file) {
		$id = rand(0,999).time();
	return $id.'img2.'.$file->guessExtension();
});
			$form->image('img4', '活动列表选单图片hover')->help('图片格式:175x68')->rules('dimensions:width=175,height=68',['请上传正确图片格式'])->name(function ($file) {
		$id = rand(0,999).time();
   return $id.'img3.'.$file->guessExtension();
});
			$form->image('img5', '背景电脑版图片')->help('图片格式:1920x1080')->name(function ($file) {
		$id = rand(0,999).time();
	return $id.'img4.'.$file->guessExtension();
});
			$form->image('img7', '背景手机版图片')->help('图片格式:750x1334')->name(function ($file) {
		$id = rand(0,999).time();
	return $id.'img5.'.$file->guessExtension();
});
			$form->image('img6', '标题图片')->help('图片格式:326x78')->rules('dimensions:width=326,height=78',['请上传正确图片格式'])->name(function ($file) {
		$id = rand(0,999).time();
   return $id.'img6.'.$file->guessExtension();
});
			
		})->tab('活动内容设定', function ($form) {
			
			$form->ckeditor('text', '活动内容');

			
		});
        return $form;
    }
}

