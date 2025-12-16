<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    public function run()
    {
        // 1. User Umum (Bisa dipakai banyak orang)
        User::updateOrCreate(
            ['email' => 'user@jembo.com'],
            [
                'name' => 'User',
                'username' => 'user',
                'password' => Hash::make('password'),
                'role' => 'user', // Role USER biasa
                'divisi' => 'General'
            ]
        );

        // 2. GA Admin
        User::updateOrCreate(
            ['email' => 'ga@jembo.com'],
            [
                'name' => 'Admin GA',
                'username' => 'adminga',
                'password' => Hash::make('password'),
                'role' => 'ga.admin', // Role Admin GA
                'divisi' => 'General Affair'
            ]
        );
        //Engineer
        User::updateOrCreate(
            ['email' => 'engineer@jembo.com'],
            [
                'name' => 'Admin Engineer',
                'username' => 'adminengineer',
                'password' => Hash::make('password'),
                'role' => 'eng.admin',
                'divisi' => 'Engineering'
            ]
        );
        //Facility
        User::updateOrCreate(
            ['email' => 'facility@jembo.com'],
            [
                'name' => 'Admin Facility',
                'username' => 'adminfacility',
                'password' => Hash::make('password'),
                'role' => 'fh.admin',
                'divisi' => 'Facility'
            ]
        );
        User::updateOrCreate(
            ['email' => 'maintenance@jembo.com'],
            [
                'name' => 'Admin Maintenance',
                'username' => 'adminmt',
                'password' => Hash::make('password'),
                'role' => 'mt.admin',
                'divisi' => 'Maintenance'
            ]
        );


        // ... Buat juga untuk mt.admin, eng.admin, fh.admin sesuai kebutuhan
    }
}
