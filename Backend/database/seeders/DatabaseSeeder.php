<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Créer l'admin (Shouk)
        User::create([
            'name' => 'Admin',
            'email' => 'admin123@gmail.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);

        // Créer des étudiants de test
        User::factory(10)->create([
            'role' => 'student'
        ]);

        // Créer des workshops de test
        Workshop::create([
            'title' => 'Introduction aux Algorithmes',
            'description' => 'Apprenez les bases des algorithmes: tri, recherche, et complexité',
            'instructor' => 'Mr Hakim',
            'date' => now()->addDays(7),
            'time' => '14:00',
            'location' => 'Amphi c',
            'max_participants' => 30
        ]);

        Workshop::create([
            'title' => 'Structures de Données Avancées',
            'description' => 'Arbres, graphes, tables ect...',
            'instructor' => 'Prof. Hakim',
            'date' => now()->addDays(14),
            'time' => '10:00',
            'location' => 'Amphi c',
            'max_participants' => 50
        ]);

        Workshop::create([
            'title' => 'Programmation Dynamique',
            'description' => 'Techniques avancées de résolution de problèmes',
            'instructor' => 'Prof .',
            'date' => now()->addDays(21),
            'time' => '15:30',
            'location' => 'Salle 9',
            'max_participants' => 25
        ]);
    }
}
