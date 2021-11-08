<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


use App\Models\User;
use App\Models\Admin;
use App\Models\Seller;
use App\Models\Company;
use App\Models\Customer;
use App\Http\Requests\Auth\Register;
use App\Http\Requests\Auth\Login;

class AuthController extends Controller
{
    private function CreatePermission()
    {
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
    }

    // Register
    public function register(Register $request)
    {
        if (Count(Permission::get()) == 0) {
            $this->CreatePermission();
        }

        $request['password'] = Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);

        $user = new User;
        $user->first_name   = $request['first_name'];
        $user->second_name  = $request['second_name'];
        $user->email        = $request['email'];
        $user->password     = $request['password'];
        $user->phone_number = $request['phone_number'];
        $user->address      = $request['address'];
        $user->photo_path   = '1.jpg'/*$request['photo']*/;
        $user->bio          = $request['bio'];
        $user->type         = $request['type'];
        $user->zipcode      = $request['zipcode'];
        $user->activation_token = Str::random(60);

        switch ($request->type) {
            case 0:
                // Admin
                $profile = Admin::create([
                    'name'  => 'Admin'
                ]);
                $profile->user()->save($user);

                $role = Role::where(['name' => 'ROLE_ADMIN'])->first();
                $user->assignRole($role);
                $user->givePermissionTo($role->permissions);

                break;

            case 1:
                // Customer
                $profile = Customer::create([
                    'gender'        => $request['gender'],
                    'orders_number' => 0,
                    'birth_date'    => $request['birth_date']
                ]);
                $profile->user()->save($user);

                $role = Role::where(['name' => 'ROLE_CUSTOMER'])->first();
                $user->assignRole($role);
                $user->givePermissionTo($role->permissions);

                break;

            case 2:
                // Seller
                $profile = Seller::create([
                    'customers_number'  => 0,
                    'orders_number'     => 0,
                    'delivery_speed'    => 0,
                    'has_store'         => 0,
                    'birth_date'        => $request['birth_date'],
                    'gender'            => $request['gender'],
                    'badge'             => $request['badge'],
                    'specialization'    => $request['specialization'],
                ]);
                $profile->user()->save($user);

                $role = Role::where(['name' => 'ROLE_SELLER'])->first();
                $user->assignRole($role);
                $user->givePermissionTo($role->permissions);

                break;

            case 3:
                // Company
                $profile = Company::create([
                    'customers_number'  => 0,
                    'orders_number'     => 0,
                    'delivery_speed'    => 0,
                    'has_store'         => 0,
                    'badge'             => $request['badge'],
                    'specialize'    => $request['specialize'],
                ]);
                $profile->user()->save($user);

                $role = Role::where(['name' => 'ROLE_COMPANY'])->first();
                $user->assignRole($role);
                $user->givePermissionTo($role->permissions);

                break;
            default:
                return response(['message' => 'Determind Type of User.']);
                break;
        }

        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        $response = ['token' => $token, 'role' => $user->roles[0]->name];
        return response($response, 200);
    }

    // Login
    public function login(Login $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                // return $user->roles[0]->name;
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                $response = ['token' => $token, 'role' => $user->roles[0]->name];
                return response($response, 200);
            } else {
                $response = ["message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["message" => 'User does not exist'];
            return response($response, 422);
        }
    }

    // Logout
    public function logout(Request $request)
    {
        $token = $request->user()->token();
        // return $token;
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }
}
