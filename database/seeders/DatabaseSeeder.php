<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
      $this->call([
            UserSeeder::class,      // <-- Phải có dòng này mới có User
            ThietbiSeeder::class,   // <-- Phải có dòng này mới có Thiết bị
            // PhieuMuonSeeder::class, // (Nếu có thì bỏ comment ra)
        ]);
    
    }
}
