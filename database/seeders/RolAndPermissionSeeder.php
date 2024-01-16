<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use Spatie\Permission\Models\{Permission, Role};
use App\Enums\{PermissionEnum, RolEnum};

class RolAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::firstOrCreate(['name' => PermissionEnum::UPDATE_ARTICLE->value]);
        Permission::firstOrCreate(['name' => PermissionEnum::DELETE_ARTICLE->value]);

        $roleAdmin = Role::create(["name" => RolEnum::ADMIN->value]);
        Role::create(["name" => RolEnum::EDITOR->value])->givePermissionTo([PermissionEnum::DELETE_ARTICLE->value, PermissionEnum::UPDATE_ARTICLE->value]);
        Role::create(["name" => RolEnum::CLIENT->value]);

        $roleAdmin->givePermissionTo([PermissionEnum::UPDATE_ARTICLE->value, PermissionEnum::DELETE_ARTICLE->value, ]);


        
        //User::factory()->rolAdmin()->create();
        //User::factory()->count(2)->rolEditor()->create();
        //User::factory()->count(3)->rolClient()->create();

    }
}
