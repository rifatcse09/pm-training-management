<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignationSeeder extends Seeder
{
    public function run(): void
    {
        $designations = [
            ['grade' => '১', 'name' => 'সচিব', 'class' => 'প্রথম শ্রেণি'],
            ['grade' => '১', 'name' => 'সিনিয়র সচিব', 'class' => 'প্রথম শ্রেণি'],
            ['grade' => '২', 'name' => 'যুগ্ম সচিব', 'class' => 'প্রথম শ্রেণি'],
            ['grade' => '৩', 'name' => 'অতিরিক্ত সচিব', 'class' => 'প্রথম শ্রেণি'],
            ['grade' => '৪', 'name' => 'উপসচিব', 'class' => 'প্রথম শ্রেণি'],
            ['grade' => '৫', 'name' => 'সহকারী সচিব', 'class' => 'প্রথম শ্রেণি'],
            ['grade' => '৬', 'name' => 'সহকারী পরিচালক', 'class' => 'প্রথম শ্রেণি'],
            ['grade' => '৭', 'name' => 'সিনিয়র সহকারী সচিব', 'class' => 'প্রথম শ্রেণি'],
            ['grade' => '৮', 'name' => 'সহকারী সচিব', 'class' => 'প্রথম শ্রেণি'],
            ['grade' => '৯', 'name' => 'সহকারী পরিচালক', 'class' => 'প্রথম শ্রেণি'],
            ['grade' => '১০', 'name' => 'অফিসার, ক্লার্ক', 'class' => 'দ্বিতীয় শ্রেণি'],
            ['grade' => '১১', 'name' => 'জুনিয়র অফিসার, টাইপিস্ট', 'class' => 'তৃতীয় শ্রেণি'],
            ['grade' => '১২', 'name' => 'জুনিয়র অফিসার, টাইপিস্ট', 'class' => 'তৃতীয় শ্রেণি'],
            ['grade' => '১৩', 'name' => 'জুনিয়র অফিসার, টাইপিস্ট', 'class' => 'তৃতীয় শ্রেণি'],
            ['grade' => '১৪', 'name' => 'অফিস সহায়ক, পিওন', 'class' => 'চতুর্থ শ্রেণি'],
            ['grade' => '১৫', 'name' => 'অফিস সহায়ক, পিওন', 'class' => 'চতুর্থ শ্রেণি'],
            ['grade' => '১৬', 'name' => 'অফিস সহায়ক, পিওন', 'class' => 'চতুর্থ শ্রেণি'],
            ['grade' => '১৭', 'name' => 'অফিস সহায়ক, পিওন', 'class' => 'চতুর্থ শ্রেণি'],
            ['grade' => '১৮', 'name' => 'অফিস সহায়ক, পিওন', 'class' => 'চতুর্থ শ্রেণি'],
            ['grade' => '১৯', 'name' => 'অফিস সহায়ক, পিওন', 'class' => 'চতুর্থ শ্রেণি'],
            ['grade' => '২০', 'name' => 'অফিস সহায়ক, পিওন', 'class' => 'চতুর্থ শ্রেণি'],
        ];

        DB::table('designations')->insert($designations);
    }
}
