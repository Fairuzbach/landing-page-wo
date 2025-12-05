<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImprovementParameterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parameters = [
            [
                'code' => 'M',
                'name' => 'Machines'
            ],
            [
                'code' => 'MTR',
                'name' => 'Materials'
            ],
            [
                'code' => 'T',
                'name' => 'Tools'
            ]
        ];

        foreach ($parameters as $para) {
            // Gunakan updateOrInsert agar tidak duplikat jika di-seed ulang
            DB::table('improvement_parameters')->updateOrInsert(
                ['code' => $para['code']], // Cek berdasarkan code
                [
                    'name' => $para['name'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]
            );
        }
    }
}
