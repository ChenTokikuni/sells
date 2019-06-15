<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class member extends Model
{
	protected $table = 'member';
	protected $primaryKey = 'id';
	public $incrementing = false;
	
	protected $fillable = [
		'account','name','phone_number','mail','qq_number','bank_number','save_count','pay_count','total_save','total_pay','registration_date','last_login','offline_days',
    ];
}
