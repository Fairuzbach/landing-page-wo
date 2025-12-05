<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImprovementStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $statusses = [
            [
                'status' => 'Pending',
                'color' => 'secondary'
            ],
            [
                'status' => 'In Progress',
                'color' => 'primary',
            ],
            [
                'status' => 'Completed',
                'color' => 'success',
            ],
            [
                'status' => 'Cancelled',
                'color' => 'danger',
            ]
        ];
        foreach ($statusses as $status) {
            DB::table('improvement_statuses')->insert([
                'status' => $status['status'],
                'color' => $status['color'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }
}
