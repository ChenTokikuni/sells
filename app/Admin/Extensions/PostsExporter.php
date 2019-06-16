<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid\Exporters\ExcelExporter; 

class PostsExporter extends ExcelExporter
{
    protected $fileName = '会员列表.csv';

    protected $columns = [
        'account'     	 => '会员帐号',
		'name'     		 => '姓名',
		'phone_number'      => '手机号',
		'mail'      => '邮箱',
		'qq_number'      => 'QQ号',
		'bank_number'      => '银行帐号',
		'save_count'      => '存款次数',
		'pay_count'      => '出款次数',
		'total_save'      => '总存款金额',
		'total_pay'      => '总出款金额',
		'registration_date'      => '注册日期',
		'last_login'      => '最后登录日',
		'offline_days'      => '多久没登录'
    ];
}