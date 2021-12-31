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
    //---------------------------------------------------- Register -----------------------------------------------------------
    public function register(Register $request)
    {
        $request['remember_token'] = Str::random(10);
        $user = new User;
        $user->name = $request['name'];
        $user->username = $request['username'];
        $user->email = $request['email'];
        $user->password =  Hash::make($request['password']);
        $user->phone = $request['phone'];
        $user->address = $request['address'];
        $user->bio = $request['bio'];
        $user->zipcode = $request['zipcode'];
        $user->activation_token = Str::random(60);
        $user->save();
        if($request['type'] == 0){
            $profile = new Admin();
            $profile->name = 'Admin';
            $profile->user()->save($user);
            $role = Role::findByName('ROLE_ADMIN');
            $user->assignRole($role);
            $user->givePermissionTo($role->permissions);
        }else if($request['type'] == 1){
            $profile = new Customer();
            $profile->gender = $request['gender'];
            $profile->orders_number = 0;
            $profile->birth_date = $request['birth_date'];
            $profile->user()->save($user);
            $role = Role::findByName('ROLE_CUSTOMER');
            $user->assignRole($role);
            $user->givePermissionTo($role->permissions);
            //instantiate user cart 
            $cart = new Cart();
            $profile->cart()->save($cart);
        }else if($request['type'] == 2){
            $profile = new Seller();
            $profile->gender = $request['gender'];
            $profile->birth_date = $request['birth_date'];
            $profile->badge = 'bronze';
            $profile->has_store = false;
            $profile->delivery_speed = 0;
            $profile->user()->save($user);
            $role = Role::findByName('ROLE_SELLER');
            $user->assignRole($role);
            $user->givePermissionTo($role->permissions);
        }else if($request['type'] == 3){
            $profile = new Company();
            $profile->has_store = false;
            $profile->badge = 'bronze';
            $profile->delivery_speed = 0;
            $profile->user()->save($user);
            $role = Role::findByName('ROLE_COMPANY');
            $user->assignRole($role);
            $user->givePermissionTo($role->permissions);
        }else{
            return response()->json(['message' => 'please determine the type of user'], 404);
        }
        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        return response()->json([
            'message'=>'you have created account successfully', 
            'token'=>$token, 
            'role'=>$user->roles[0]->name],
             201);
    }

    //----------------------------------------------------- Login -----------------------------------------------------------
    public function login(Login $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                return response()->json([
                    'message' => 'logged in successfully',
                    'token' => $token, 
                    'role' => $user->roles[0]->name], 
                    200);
            } else {
                return response()->json(['message' => 'Password mismatch'], 422);
            }
        } else {
            return response()->json(['message' => 'User does not exist'], 422);
        }
    }

    //----------------------------------------------------- Logout -----------------------------------------------------------
    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        return response()->json(['message' => 'You have been successfully logged out!'], 200);
    }

    //----------------------------------------------------- Logout -----------------------------------------------------------
    public function forgotPasswordSendCode(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'email' => 'required|email:rfc',
        ]);
        if ($validator->fails()){
            return response()->json(['message' => $validator->errors()], 400);   
        }
        if(User::where('email', '=', $request['email'])->exists()){
            Session::put('email', $request['email']);
            $user = User::where('email', '=', $request['email'])->first();
            $user->forgot_password_code = $user->generateForgotPasswordCode();
            $user->save();
            $data = ['content' => `Reset Password Code is `.$user->forgot_password_code ];
            Mail::to($user->email)->send(new ForgetPasswordMail($data, $user->email));
            return response()->json(['message' => trans('lang.forgotPassword.sent_successfully')], 200); 
        }else{
            return response()->json(['message' => trans('lang.forgotPassword.no_user')], 401);  
        }
    }

    //----------------------------------------------------- Logout -----------------------------------------------------------
    public function activateForgotPasswordCode(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'code' => 'required'
        ]);
        if ($validator->fails()){
            return response()->json(['message' => $validator->errors()], 400);    
        }
        $email = Session::get('email');
        $user = User::where('email', $email)->first();
        if(!$user){
            return response()->json(['message' => trans('lang.forgotPassword.no_user')], 401);  
        }
        if($request['code'] == $user->forgot_password_code){
            return response()->json(['message' => trans('lang.forgotPassword.no_user')], 200);
        }else{
            return response()->json(['message' => trans('lang.forgotPassword.no_user')], 401);
        }
    }

    //----------------------------------------------------- Logout -----------------------------------------------------------
    public function submitResetPassword(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'new_password' => 'required',
            'confirm_password' => 'required'
        ]);
        if ($validator->fails()){
            return response()->json(['message' => $validator->errors()], 400);  
        }
        $user = User::where('email', Session::get('email'))->first();
        if(!$user){
            return response()->json(['message' => trans('lang.forgotPassword.no_user')], 401);   
        }
        if($request['new_password'] != $request['confirm_password']){
            return response()->json(['message' => trans('lang.forgotPassword.incorrect_code')], 400);  
        }
        $user->forgot_password_code = null;
        $user->password = $request['new_password'];
        $user->save();
        return response()->json(['message' => trans('lang.forgotPassword.reset_password_success')], 400);
    }
}
