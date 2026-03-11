<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\EnergyAudit;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'proximalumine@gmail.com'],
            [
                'name' => 'Proxima Lumine Super Admin',
                'password' => Hash::make('Gwiazda1!'),
                'role' => UserRole::SuperAdmin,
            ]
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@enesa.pl'],
            [
                'name' => 'Administrator ENESA',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
            ]
        );

        $auditor = User::updateOrCreate(
            ['email' => 'auditor@enesa.pl'],
            [
                'name' => 'Audytor ENESA',
                'password' => Hash::make('password'),
                'role' => UserRole::Auditor,
            ]
        );

        $client = User::updateOrCreate(
            ['email' => 'client@enesa.pl'],
            [
                'name' => 'Klient ENESA',
                'password' => Hash::make('password'),
                'role' => UserRole::Client,
            ]
        );

        $company = Company::updateOrCreate(
            ['name' => 'Zaklad Produkcyjny Poznan'],
            [
                'client_id' => $client->id,
                'auditor_id' => $auditor->id,
                'city' => 'Poznan',
            ]
        );

        Offer::updateOrCreate(
            ['title' => 'Oferta audytu 2026', 'company_id' => $company->id],
            [
                'status' => 'accepted',
                'created_by' => $admin->id,
            ]
        );

        EnergyAudit::updateOrCreate(
            ['title' => 'Audyty efektywnosci Q1', 'company_id' => $company->id],
            [
                'status' => 'in_progress',
                'auditor_id' => $auditor->id,
            ]
        );
    }
}
