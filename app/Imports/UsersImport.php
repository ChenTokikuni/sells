<?php

namespace App\Imports;

use App\Model\member;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;

class UsersImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return User|null
     */
    public function model(array $member_data)
    {
		print_r($member_data);exit;
        return new Member([
		'account'     	 => $member_data[0],
		'name'     		 => $member_data[1],
		'phone_number'      => $member_data[2],
		'mail'      => $member_data[3],
		'qq_number'      => $member_data[4],
		'bank_number'      => $member_data[5],
		'save_count'      => $member_data[6],
		'pay_count'      => $member_data[7],
		'total_save'      => $member_data[8],
		'total_pay'      => $member_data[9],
		'registration_date'      => $member_data[10],
		'last_login'      => $member_data[11],
		'offline_days'      => $member_data[12],
        ]);
    }
}