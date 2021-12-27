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

    //----------------------------------------------------- Register -----------------------------------------------------------
    public function register(Register $request)
    {

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
                $profile = Admin::create([
                    'name'  => 'Admin'
                ]);
                $profile->user()->save($user);
                $role = Role::where(['name' => 'ROLE_ADMIN'])->first();
                $user->assignRole($role);
                $user->givePermissionTo($role->permissions);
                break;

            case 1:
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
                return response(['message' => 'please determine the type of user']);
                break;
        }

        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        $response = ['token' => $token, 'role' => $user->roles[0]->name];
        return response($response, 200);
    }

    //----------------------------------------------------- Login -----------------------------------------------------------
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

    //----------------------------------------------------- Logout -----------------------------------------------------------
    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }
}
