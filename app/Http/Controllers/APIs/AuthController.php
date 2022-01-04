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
use App\Models\Cart;
use App\Models\Admin;
use App\Models\Seller;
use App\Models\Company;
use App\Models\Customer;
use App\Models\File;
use App\Models\NationalIdentity;
use App\Models\TaxCard;
use App\Models\CommercialRecord;
use App\Http\Requests\Auth\Register;
use App\Http\Requests\Auth\Login;
use Carbon\Carbon;
use App\Events\UserAccountActivatedEvent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /*
        register
        login
        activateUser
        sendForgotPasswordCode
        checkForgotPasswordCode
        resetPassword
    */
    //---------------------------------------------------- Register -----------------------------------------------------------
    public function register(Register $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'username' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|string',
            'address' => 'required|string',
            'zipcode' => 'required',
            'gender' => 'in:male,female',
            'birth_date' => '',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        $request['remember_token'] = Str::random(10);
        $user = new User;
        $user->name = $request['name'];
        $user->username = $request['username'];
        $user->email = $request['email'];
        $user->password =  Hash::make($request['password']);
        $user->phone = $request['phone'];
        $user->address = $request['address'];
        $user->zipcode = $request['zipcode'];
        $user->activation_token = Str::random(60);
        $user->account_activated = false;
        $user->blocked = false;
        $user->available_money_amnt = 0;
        $user->save();
        if($request['type'] == 0){
            $profile = new Admin();
            $profile->gender = $request['gender'];
            $profile->birth_date = $request['birth_date'];
            $profile->save();
            $profile->user()->save($user);
            $role = Role::where('name','ROLE_ADMIN')->first();
            $user->assignRole($role);
            $user->givePermissionTo($role->permissions);
        }else if($request['type'] == 1){
            $profile = new Customer();
            $profile->gender = $request['gender'];
            $profile->birth_date = $request['birth_date'];
            $profile->orders_number = 0;
            $profile->save();
            $profile->user()->save($user);
            $role = Role::where('name','ROLE_CUSTOMER')->first();
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
            $profile->save();
            $profile->user()->save($user);
            $role = Role::where('name','ROLE_SELLER')->first();
            $user->assignRole($role);
            $user->givePermissionTo($role->permissions);
            if($request->hasfile('national_identity')){
                $national_identity_file = $request->file('national_identity');
                $national_identity_file_name = $national_identity_file->getClientOriginalName();
                $national_identity_file_extention = $national_identity_file->extension();
                $national_identity_file_path = '/accounts/'.$user->id.'/national_identity/';
                $file = new File();
                $file->name = $national_identity_file_name;
                $file->path = $national_identity_file_path;
                $file->extention = $national_identity_file_extention;
                $national_identity = new NationalIdentity();
                $national_identity->file()->save($file);
                $national_identity->seller()->associate($profile)->save();
                $national_identity_file->move(public_path().$national_identity_file_path, $national_identity_file_name);
            }
        }else if($request['type'] == 3){
            $profile = new Company();
            $profile->has_store = false;
            $profile->badge = 'bronze';
            $profile->delivery_speed = 0;
            $profile->save();
            $profile->user()->save($user);
            $role = Role::where('name','ROLE_COMPANY')->first();
            $user->assignRole($role);
            $user->givePermissionTo($role->permissions);
            if($request->hasfile('tax_card')){
                $tax_card_file = $request->file('tax_card');
                $tax_card_file_name = $tax_card_file->getClientOriginalName();
                $tax_card_file_extention = $tax_card_file->extension();
                $tax_card_file_path = '/accounts/'.$user->id.'/tax_card/';
                $file = new File();
                $file->name = $tax_card_file_name;
                $file->path = $tax_card_file_path;
                $file->extention = $tax_card_file_extention;
                $tax_card = new TaxCard();
                $tax_card->file()->save($file);
                $tax_card->company()->associate($profile)->save();
                $tax_card_file->move(public_path().$tax_card_file_path, $tax_card_file_name);
            }
            if($request->hasfile('commercial_record')){
                $commercial_record_file = $request->file('commercial_record');
                $commercial_record_file_name = $commercial_record_file->getClientOriginalName();
                $commercial_record_file_extention = $commercial_record_file->extension();
                $commercial_record_file_path = '/accounts/'.$user->id.'/commercial_record/';
                $file = new File();
                $file->name = $commercial_record_file_name;
                $file->path = $commercial_record_file_path;
                $file->extention = $commercial_record_file_extention;
                $commercial_record = new CommercialRecord();
                $commercial_record->file()->save($file);
                $commercial_record->company()->associate($profile)->save();
                $commercial_record_file->move(public_path().$commercial_record_file_path, $commercial_record_file_name);
            }
        }else{
            return response()->json(['message' => trans('auth.determine.user.type')], 400);
        }
        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        return response()->json([
            'message'=> trans('auth.account.created.successfully'),
            'data' => [ 
                'token' => $token, 
                'role' => $user->roles[0]->name
                ],
            ], 201);
    }

    //----------------------------------------------------- Login -----------------------------------------------------------
    public function login(Login $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 401);
        }
        $user = User::where('email', $request['email'])->first();
        if ($user) {
            if (Hash::check($request['password'], $user->password) && !$user->blocked){
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                return response()->json([
                    'message' => trans('auth.logged.in.successfully'),
                    'token' => $token, 
                    'role' => $user->roles[0]->name
                ], 200);
            } else {
                return response()->json(['message' => trans('auth.password.mismatch')], 400);
            }
        } else {
            return response()->json(['message' => trans('auth.user.doesnt.exist')], 404);
        }
    }

    //----------------------------------------------------- activate user ----------------------------------------------------
    public function activateUser(Request $request, $id)
    {
        if(User::where('id', '=', $id)->exists()){
            $user = User::find($id);
            $user->account_activated = true;
            $user->account_activated_at = Carbon::now();
            $user->save(); 
            //notify user that his account was activated
            Event::dispatch(new UserAccountActivatedEvent($user));
            return response()->json(['message' => trans('auth.account.activated.successfully')], 200);
        }else{
            return response()->json(['message' => trans('auth.user.doesnt.exist')], 404);  
        }
    }

    //----------------------------------------------------- send code to reset password ----------------------------------------
    public function sendForgotPasswordCode(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'email' => 'required|email',
        ]);
        if ($validator->fails()){
            return response()->json(['message' => $validator->errors()], 400);   
        }
        if(User::where('email', '=', $request['email'])->exists()){
            Session::put('email', $request['email']);
            $user = User::where('email', '=', $request['email'])->first();
            $user->forgot_password_code = $user->generateForgotPasswordCode();
            $user->save();
            $mail_message = ['content' => trans('auth.forgot.password.mail.message', ['code' => $user->forgot_password_code])];
            Mail::to($user->email)->send(new ForgetPasswordMail($mail_message, $user->email));
            return response()->json(['message' => trans('auth.forgot.password.code.sent')], 200); 
        }else{
            return response()->json(['message' => trans('auth.user.doesnt.exist')], 404);  
        }
    }

    //------------------------------------------------- check if code is correct ---------------------------------------------
    public function checkForgotPasswordCode(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'code' => 'required|size:8'
        ]);
        if ($validator->fails()){
            return response()->json(['message' => $validator->errors()], 400);    
        }
        $email = Session::get('email');
        $user = User::where('email', $email)->first();
        if(!$user){
            return response()->json(['message' => trans('auth.user.doesnt.exist')], 404);  
        }
        if($request['code'] == $user->forgot_password_code){
            return response()->json(['message' => trans('auth.forgot.password.code.correct')], 200);
        }else{
            return response()->json(['message' => trans('auth.forgot.password.code.mismatch')], 400);
        }
    }

    //----------------------------------------------------- reset password -------------------------------------------------
    public function resetPassword(Request $request)
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
            return response()->json(['message' => trans('auth.user.doesnt.exist')], 404);   
        }
        if($request['new_password'] != $request['confirm_password']){
            return response()->json(['message' => trans('auth.reset.password.mismatch')], 400);  
        }
        $user->forgot_password_code = null;
        $user->password = $request['new_password'];
        $user->save();
        return response()->json(['message' => trans('auth.reset.password.successfully')], 200);
    }
}
