<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;


class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ImprovementParameterSeeder::class);
        $this->call(engineerTechSeeder::class);
        $this->call(ImprovementStatusSeeder::class);
        //Plant dan Mesin
        $data = [
            'plants' => [
                'A',
                'B',
                'C',
                'D',
                'E',

                'SS',
                'SC',
                'PE',
                'QC',
                'GA',
                'MT',
                'FH',
                'FO',
                'QR'

            ],

            'mesins' => [
                'A' => [
                    'HD-10C',
                    'TW-RBD-CU',
                    'MD-1',
                    'MULTI DRAWING-1',
                    'MULTI DRAWING-2',
                    'MULTI DRAWING-3',
                    'BC-Samp1',
                    'BC-Samp2',
                    'BC-Samp3',
                    'BC-Samp4',
                    'BC-Samp5',
                    'BC-Samp6',
                    'BC-Samp7',
                    'BC-Samp8',
                    'BC-5',
                    'BC-6',
                    'AW-1',
                    'AW-2',
                    'EX-70+40',
                    'EX-60A',
                    'EX-60B',
                    'EX-80+60',
                    'EX-80H',
                    'EX-80G',
                    'CB-4C',
                    'CB-7E',
                    'TW-125C',
                    'ST-7F',
                    'EX-70A',
                    'CB-16B',
                    'CB-48A',
                    'ST-30A',
                    'ST-66',
                    'REW-30A',
                    'REW-67',
                    'REW-8A',
                    'EX-90+80',
                    'EX-90F',
                    'EX-90D',
                    'EX-90K',
                    'EX-120D',
                    'EX-120+70',
                    'CB-1F',
                    'CL-1',
                    'CL-2',
                    'CL-3',
                    'CL-4',
                    'CL-5',
                    'CL-6',
                    'CL-7',
                    'CL-8',
                    'MC-1',
                    'MC-2',
                    'MC-3',
                    'REW-AR-21',
                    'AR-21',
                    'REW-AR-72B-1',
                    'REW-AR-72B-2',
                    'AR-72B'
                ],
                'B' => [
                    'RBD-AL',
                    'HD-10A',
                    'HD-8C',
                    'HD-8E',
                    'MD-5',
                    'MD-6',
                    'MD-7',
                    'OVEN',
                    'ST-7H',
                    'ST-7I',
                    'ST-6B',
                    'ST-7D',
                    'ST-7A',
                    'ST-7J',
                    'ST-12B',
                    'ST-19C',
                    'ST-30B',
                    'ST-54A',
                    'ST-54C',
                    'ST-54E',
                    'EX-90B',
                    'EX-100B',
                    'CB-2B',
                    'CB-2C',
                    'CB-4B',
                    'REW-1',
                    'REW-2',
                    'REW-3',
                    'REW-4',
                    'ST-54B',
                    'ST-54D',
                    'AR-72',
                    'WR-1'

                ],
                'C' => [
                    'MD-2C',
                    'MD-3C',
                    'TW-MD',
                    'FD-9',
                    'FD-10',
                    'FD-11',
                    'FD-12',
                    'FD-20',
                    'FD-21',
                    'TIN-1',
                    'TIN-2',
                    'TIN-3',
                    'ELECTRO PLATING',
                    'BC-1C',
                    'BC-2C',
                    'BC-3C',
                    'BC-4C',
                    'BC-5C',
                    'BC-7',
                    'BC-8',
                    'BC-9',
                    'BC-10',
                    'TV-5',
                    'TV-6',
                    'TV-7',
                    'TV-8',
                    'TV-9',
                    'TV-10',
                    'TV-11',
                    'TV-12',
                    'EX-65 1C',
                    'EX-65 2C',
                    'EX-80+50 A',
                    'EX-80+50 B',
                    'EX-65 3C',
                    'EX-65 4C',
                    'TW-630 A',
                    'TW-630 B',
                    'TW-800A',
                    'TW-800B',
                    'TW-125A',
                    'TW-125B',
                    'CB-16A',
                    'CB-16C',
                    'CB-20A',
                    'EX-80J',
                    'EX-90E',
                    'EX-120F',
                    'EX-150C',
                    'EX-120A',
                    'EX-120/100D',
                    'ST-12A',
                    'REW-TWB-16',
                    'TWB-16A',
                    'TWB-16B',
                    'REW-TWB-24A',
                    'REW-TWB-24B',
                    'TWB-24A',
                    'TWB-24B',
                    'TWB-24C',
                    'TWB-24D',
                    'TWB-24E',
                    'REW-SWB-24A',
                    'REW-SWB-24B',
                    'SWB-24A',
                    'SWB-24B',
                    'SWB-24C',
                    'SWB-24D',
                    'SWB-24E',
                    'REW-SWB-32A',
                    'REW-SWB-32B',
                    'SWB-32A',
                    'SWB-32B',
                    'REW-SWB-48A',
                    'SWB-48',
                    'TP-10',

                ],
                'D' => [
                    'CCV-JHON ROYLE',
                    'CCV-PAMPASS',
                    'CCV-SUMITOMO',
                    'REW-5',
                    'REW-6',
                    'REW-7',
                    'TP-5',
                    'TP-6',
                    'TP-8',
                    'TP-2',
                    'TP-11',
                    'TP-1',
                    'TP-3',
                    'TP-4',
                    'EX--100A',
                    'CB-26A',
                    'CB-26B',
                    'EX--100C',
                    'EX--120B',
                    'EX--120C',
                    'EX--160A',
                    'TP-7'
                ],
                'E' => [
                    'COL-4A',
                    'COL-4B',
                    'COL-9F',
                    'COL-5F',
                    'COL-8F A',
                    'COL-8F B',
                    'EX-45-7F',
                    'EX-45-8F',
                    'EX-45-9F',
                    'EX-45-10F',
                    'EX-45-11F',
                    'EX-45-12F',
                    'EX-45-13F',
                    'EX-45-14F',
                    'EX-45-15F',
                    'EX-45-16F',
                    'EX-45-17F',
                    'EX-30F',
                    'EX-50F',
                    '"PATH CORD "',
                    'PIG TAILD',
                    'DC-1',
                    'DC-2',
                    'DC-3',
                    'CB-4F',
                    'CB-5F',
                    'CB-6F',
                    'CB-7F',
                    'CB-8F',
                    'CB-9F',
                    'EX-90G',
                    'EX-90H',
                    'EX-90I',
                    'EX-90J',
                ],

                'SC' => [],

                'SS' => [],

                'FO' => [],
                'FH' => [],
                'QR' => [],
                //Plant baru dengan daftar mesin KOSONG
                'PE' => [],
                'QC' => [],
                'GA' => [],
                'MT' => [],
            ]
        ];

        foreach ($data['plants'] as $plantName) {
            $plantId = DB::table('plants')->insertGetId([
                'name' => 'Plant ' . $plantName,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            if (isset($data['mesins'][$plantName])) {
                $machineBatch = [];

                foreach ($data['mesins'][$plantName] as $machineName) {
                    $machineBatch[] = [
                        'plant_id' => $plantId,
                        'name' => $machineName,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                }
                if (!empty($machineBatch)) {
                    DB::table('machines')->insert($machineBatch);
                }
            }
        }

        //Data Orang Engineer
        $engineers = [
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
        foreach ($engineers as $name) {
            $role = ($name === 'ANDY APRIADI' || $name === 'DANU MAMLUKAT') ? 'admin' : 'user';
            $usernameKey = Str::lower(str_replace(' ', '.', $name));

            User::create([
                'name' => ucwords(Str::lower($name)),
                'username' => $usernameKey,
                'email' => $usernameKey . '@jembo.com',
                'password' => Hash::make('jemboEngineer123'), //default password
                'role' => $role,
                'divisi' => 'Engineering',
            ]);
        }
    }
}
