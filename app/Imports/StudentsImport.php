<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class StudentsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return User|null
     */
    public function model(array $row)
    {
        return new Student([
            'username' => $row['username'],
            'email' => "",
            'password' => bcrypt($row['password']),
            'gender' => $row['gender'],
            'level' => 0,
            'score' => 0,
            'work_max_num' => 1,
            'work_comment_enable' => 1,
            'groups_id' => $row['groups_id'],
            'order_in_group' => $row['order_in_group'],
            'sclasses_id' => $row['sclasses_id'],
            'is_lock' => 0,
            'remember_token' => Str::random(10),
        ]);
    }
}