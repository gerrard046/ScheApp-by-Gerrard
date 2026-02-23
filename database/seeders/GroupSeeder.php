<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari admin untuk ditugaskan sebagai penanggung jawab grup
        $admin = \App\Models\User::where('role', 'admin')->first();
        
        if (!$admin) {
            // Jika tidak ada admin, ambil user pertama
            $admin = \App\Models\User::first();
        }

        if (!$admin) return;

        // 1. Hapus grup lama yang tidak sesuai
        \App\Models\Group::where('name', 'Rekayasa Keamanan Siber A')->delete();

        // 2. Definisi Grup Baru
        $groupsData = [
            'Taruna Pratama' => ['A', 'B', 'C', 'D'],
            'Taruna Muda'    => ['A', 'B', 'C', 'D', 'E'],
            'Taruna Madya'   => ['A', 'B', 'C', 'D', 'E'],
            'Taruna Satria'  => ['A', 'B', 'C', 'D'],
        ];

        foreach ($groupsData as $level => $units) {
            foreach ($units as $unit) {
                $groupName = "Unit $unit $level";
                
                \App\Models\Group::updateOrCreate(
                    ['name' => $groupName],
                    ['admin_id' => $admin->id]
                );
            }
        }
    }
}
