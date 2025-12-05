<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class engineerTechSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tech = [
            'ABDUL HALID ANDRIYANTO',
            'ADI SUANDRI',
            'ADITYA RAMADHAN',
            'ANDY APRIADI',         // Admin
            'CHRISTIAN BAYU A S',
            'DAFFA ABDUL AZIZ',
            'DANU MAMLUKAT',        // Admin
            'DWI HASTUTI',
            'EDY MURTOPO',
            'HASIRI',
            'JOKO PURNOMO',
            'KHOIRUL MUNASYIKIN',
            'MAIDAFITRI DEWI PRIATI',
            'MULYANA',
            'MUHAMMAD ANDRIAN',
            'RAHMAT TAMMU',
            'SUDRANTO PURBA',
            'TEGUH MULYAWAN',
            'TRI WAHYU HIDAYAT',
            'YOSEP FAJAR BAYU KURNIAWAN',
        ];
        foreach ($tech as $engineerTech) {
            // Gunakan updateOrInsert agar tidak duplikat jika di-seed ulang
            DB::table('engineer_teches')->updateOrInsert(
                [
                    'name' => $engineerTech,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]
            );
        }
    }
}
