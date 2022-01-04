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
use App\Models\File;
use App\Models\Avatar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{

    /*
        get
        update
        logout
    */
    //----------------------------------------------------- return user info -----------------------------------------------
    public function get(Request $request)
    {
        $user = User::where('id', Auth::user()->id)->with('avatar')->get();
        return response()->json([
            'message'   => trans('user.user.returned.successfully'),
            'data'      => $user
        ], 200);
    }

    //----------------------------------------------------- update user info  ----------------------------------------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'phone' => 'string',
            'address' => 'string',
            'zipcode' => 'string',
            'bio' => 'string',
            'avatar' => '',
            'gender' => 'in:male,female',
            'birth_date' => '',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        $user = Auth::user();
        if($request['name']){
            $user->name = $request['name'];
        }
        if($request['phone']){
            $user->phone = $request['phone'];
        }
        if($request['address']){
            $user->address = $request['address'];
        }
        if($request['zipcode']){
            $user->zipcode = $request['zipcode'];
        }
        if($request['bio']){
            $user->bio = $request['bio'];
        }
        if($request['gender'] && !$user->getHasCompanyProfileAttribute() ){
            $user->profile->gender = $request['gender'];
        }
        if($request['birth_date'] && !$user->getHasCompanyProfileAttribute() ){
            $user->profile->birth_date = $request['birth_date'];
        }
        if($request->hasfile('avatar')){
            //get request file data
            $avatar_file = $request->file('avatar');
            $avatar_file_name = $avatar_file->getClientOriginalName();
            $avatar_file_extention = $avatar_file->extension();
            $avatar_file_path = '/accounts/'.$user->id.'/avatar/';
            //make new file
            $file = new File();
            $file->name = $avatar_file_name;
            $file->path = $avatar_file_path;
            $file->extention = $avatar_file_extention;
            $avatar = new Avatar();
            $avatar->file()->save($file);
            $avatar->user()->associate($user)->save();
            $avatar_file->move(public_path().$avatar_file_path, $avatar_file_name);
        }
        $user->save();
        return response(["message" => trans('user.user.updated.successfully')], 200);
    }
    //----------------------------------------------------- Logout -----------------------------------------------------------
    public function logout(Request $request)
    {
        if(Auth::user()){
            $token = Auth::user()->token();
            $token->revoke();
            return response()->json(['message' => trans('auth.logged.out.successfully')], 200);
        }else{
            return response()->json(['message' => 'you must login first'], 404);
        }  
    }
}
