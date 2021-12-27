<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreatePermissionTables extends Migration
{

    public function up()
    {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $teams = config('permission.teams');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }
        if ($teams && empty($columnNames['team_foreign_key'] ?? null)) {
            throw new \Exception('Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');       // For MySQL 8.0 use string('name', 125);
            $table->string('guard_name'); // For MySQL 8.0 use string('guard_name', 125);
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create($tableNames['roles'], function (Blueprint $table) use ($teams, $columnNames) {
            $table->bigIncrements('id');
            if ($teams || config('permission.testing')) { // permission.testing is a fix for sqlite testing
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
                $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
            }
            $table->string('name');       // For MySQL 8.0 use string('name', 125);
            $table->string('guard_name'); // For MySQL 8.0 use string('guard_name', 125);
            $table->timestamps();
            if ($teams || config('permission.testing')) {
                $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
            } else {
                $table->unique(['name', 'guard_name']);
            }
        });

        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames, $teams) {
            $table->unsignedBigInteger(PermissionRegistrar::$pivotPermission);

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign(PermissionRegistrar::$pivotPermission)
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');

                $table->primary([$columnNames['team_foreign_key'], PermissionRegistrar::$pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            } else {
                $table->primary([PermissionRegistrar::$pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            }

        });

        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames, $teams) {
            $table->unsignedBigInteger(PermissionRegistrar::$pivotRole);

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign(PermissionRegistrar::$pivotRole)
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');

                $table->primary([$columnNames['team_foreign_key'], PermissionRegistrar::$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
            } else {
                $table->primary([PermissionRegistrar::$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary');
            }
        });

        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames) {
            $table->unsignedBigInteger(PermissionRegistrar::$pivotPermission);
            $table->unsignedBigInteger(PermissionRegistrar::$pivotRole);

            $table->foreign(PermissionRegistrar::$pivotPermission)
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign(PermissionRegistrar::$pivotRole)
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary([PermissionRegistrar::$pivotPermission, PermissionRegistrar::$pivotRole], 'role_has_permissions_permission_id_role_id_primary');
        });

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));

            DB::transaction(function () {
                $ROLE_ADMIN = Role::create(['name' => 'ROLE_ADMIN', 'guard_name' => 'api']);
                $ROLE_SELLER = Role::create(['name' => 'ROLE_SELLER', 'guard_name' => 'api']);
                $ROLE_COMPANY = Role::create(['name' => 'ROLE_COMPANY', 'guard_name' => 'api']);
                $ROLE_CUSTOMER = Role::create(['name' => 'ROLE_CUSTOMER', 'guard_name' => 'api']);
            
                Permission::create(['name' => 'getAll cart', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'create cart', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'increase cart', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'decrease cart', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'delete cart', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'getAll category', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'getstore category', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'get category', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'create category', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'update category', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'delete category', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'getAll comment', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'get comment', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'create comment', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'update comment', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'delete comment', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER)->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'getAll complaint', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'get complaint', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'user complaint', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'create complaint', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER)->assignRole($ROLE_COMPANY)->assignRole($ROLE_SELLER);
                Permission::create(['name' => 'update complaint', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER)->assignRole($ROLE_COMPANY)->assignRole($ROLE_SELLER);
                Permission::create(['name' => 'delete complaint', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'getAll invoice', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'get invoice', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'user invoice', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'create invoice', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                Permission::create(['name' => 'update invoice', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                Permission::create(['name' => 'delete invoice', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                Permission::create(['name' => 'getAll order', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                Permission::create(['name' => 'get order', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'create order', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'confirm order', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                Permission::create(['name' => 'time order', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                Permission::create(['name' => 'update order', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'delete order', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'getAll product', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'get product', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'store product', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'create product', 'guard_name' => 'api'])->assignRole($ROLE_SELLER);
                Permission::create(['name' => 'update product', 'guard_name' => 'api'])->assignRole($ROLE_SELLER);
                Permission::create(['name' => 'delete product', 'guard_name' => 'api'])->assignRole($ROLE_SELLER);
                Permission::create(['name' => 'get rating', 'guard_name' => 'api'])->assignRole($ROLE_COMPANY)->assignRole($ROLE_SELLER);
                Permission::create(['name' => 'user rating', 'guard_name' => 'api'])->assignRole($ROLE_COMPANY)->assignRole($ROLE_SELLER);
                Permission::create(['name' => 'product rating', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'create rating', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'update rating', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'delete rating', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'getAll stores', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER)->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'get store', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER)->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'create store', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                Permission::create(['name' => 'update store', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                Permission::create(['name' => 'delete store', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
            });
    }

    public function down()
    {
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');
        }

        Schema::drop($tableNames['role_has_permissions']);
        Schema::drop($tableNames['model_has_roles']);
        Schema::drop($tableNames['model_has_permissions']);
        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permissions']);
    }
}
