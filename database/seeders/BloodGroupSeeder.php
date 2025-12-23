<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BloodGroup;

class BloodGroupSeeder extends Seeder
{
    public function run()
    {
        $bloodGroups = [
            ['blood_grp_name' => 'A+'],
            ['blood_grp_name' => 'A-'],
            ['blood_grp_name' => 'B+'],
            ['blood_grp_name' => 'B-'],
            ['blood_grp_name' => 'O+'],
            ['blood_grp_name' => 'O-'],
            ['blood_grp_name' => 'AB+'],
            ['blood_grp_name' => 'AB-'],
        ];

        foreach ($bloodGroups as $group) {
            BloodGroup::updateOrCreate($group);
        }
    }
}
