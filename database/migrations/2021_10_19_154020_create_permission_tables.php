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
                /*
                    $get_all_invoices_permission = Permission::make(['name' => 'get_all_invoices', 'guard_name' => 'api']);
                    $get_all_invoices_permission->saveOrFail();
                    $ROLE_ADMIN->givePermissionTo();
                */

                $ROLE_ADMIN = Role::create(['name' => 'ROLE_ADMIN', 'guard_name' => 'api']);
                $ROLE_SELLER = Role::create(['name' => 'ROLE_SELLER', 'guard_name' => 'api']);
                $ROLE_COMPANY = Role::create(['name' => 'ROLE_COMPANY', 'guard_name' => 'api']);
                $ROLE_CUSTOMER = Role::create(['name' => 'ROLE_CUSTOMER', 'guard_name' => 'api']);
        
                //----------------------------- stores permissions ---------------------------
                Permission::create(['name' => 'get_all_stores', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'get_all_stores_by_category', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER)->assignRole($ROLE_ADMIN)->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                Permission::create(['name' => 'get_store_details', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER)->assignRole($ROLE_ADMIN)->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                Permission::create(['name' => 'get_user_store', 'guard_name' => 'api'])->assignRole($ROLE_COMPANY)->assignRole($ROLE_SELLER);
                Permission::create(['name' => 'create_store', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                Permission::create(['name' => 'update_store', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                Permission::create(['name' => 'delete_store', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                //----------------------------- products permissions ---------------------------
                Permission::create(['name' => 'get_all_store_products', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER)->assignRole($ROLE_ADMIN)->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                Permission::create(['name' => 'get_product_details', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER)->assignRole($ROLE_ADMIN)->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                Permission::create(['name' => 'create_product', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                Permission::create(['name' => 'update_product', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                Permission::create(['name' => 'delete_product', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                //----------------------------- orders permissions ---------------------------
                Permission::create(['name' => 'get_all_orders', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY)->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'get_order_details', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY)->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'create_order', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'confirm_order', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY)->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'update_order', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                Permission::create(['name' => 'delete_order', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                //----------------------------- invoices permissions ---------------------------
                Permission::create(['name' => 'get_all_invoices', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                Permission::create(['name' => 'get_invoice_details', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                Permission::create(['name' => 'create_invoice', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                Permission::create(['name' => 'update_invoice', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                Permission::create(['name' => 'delete_invoice', 'guard_name' => 'api'])->assignRole($ROLE_SELLER)->assignRole($ROLE_COMPANY);
                //----------------------------- complaints permissions ---------------------------
                Permission::create(['name' => 'get_all_complaints', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'get_user_complaints', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER)->assignRole($ROLE_COMPANY)->assignRole($ROLE_SELLER);
                Permission::create(['name' => 'get_complaint_details', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN)->assignRole($ROLE_CUSTOMER)->assignRole($ROLE_COMPANY)->assignRole($ROLE_SELLER);
                Permission::create(['name' => 'create_complaint', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER)->assignRole($ROLE_COMPANY)->assignRole($ROLE_SELLER);
                Permission::create(['name' => 'update_complaint', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER)->assignRole($ROLE_COMPANY)->assignRole($ROLE_SELLER);
                Permission::create(['name' => 'delete_complaint', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                //----------------------------- categories permissions ---------------------------
                Permission::create(['name' => 'get_all_categories', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN)->assignRole($ROLE_CUSTOMER)->assignRole($ROLE_COMPANY)->assignRole($ROLE_SELLER);
                Permission::create(['name' => 'get_categories_with_stores', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN)->assignRole($ROLE_CUSTOMER)->assignRole($ROLE_COMPANY)->assignRole($ROLE_SELLER);
                Permission::create(['name' => 'get_category_details', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'create_category', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'update_category', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'delete_category', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                //----------------------------- carts permissions ---------------------------
                Permission::create(['name' => 'get_all_carts', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'create_cart', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'increase_cart', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'decrease_cart', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'delete_cart', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                //----------------------------- comments permissions ---------------------------
                Permission::create(['name' => 'get_all_comments', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'get_comment', 'guard_name' => 'api'])->assignRole($ROLE_ADMIN);
                Permission::create(['name' => 'create_comment', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'update_comment', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'delete_comment', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER)->assignRole($ROLE_ADMIN);
                //----------------------------- ratings permissions ---------------------------
                Permission::create(['name' => 'get rating', 'guard_name' => 'api'])->assignRole($ROLE_COMPANY)->assignRole($ROLE_SELLER);
                Permission::create(['name' => 'user rating', 'guard_name' => 'api'])->assignRole($ROLE_COMPANY)->assignRole($ROLE_SELLER);
                Permission::create(['name' => 'product rating', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'create rating', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'update rating', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
                Permission::create(['name' => 'delete rating', 'guard_name' => 'api'])->assignRole($ROLE_CUSTOMER);
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
