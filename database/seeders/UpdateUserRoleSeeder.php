<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UpdateUserRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Actualizar todos los usuarios existentes a admin
        User::query()->update([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->command->info('✅ Todos los usuarios actualizados a rol admin y estado activo');
    }
}
