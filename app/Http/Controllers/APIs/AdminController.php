<?php

namespace App\Http\Controllers\APIs;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    
    
    //----------------------------------------------------- approve user ----------------------------------------------------
    public function approveUser(Request $request, $id)
    {       
        if(User::where('id', '=', $id)->exists()){
            $user = User::find($id);
            $user->account_activated = true;
            $user->account_activated_at = Carbon::now();
            $user->save(); 
            //notify user that his account was activated
            Event::fire(new UserAccountActivatedEvent($user));
            return response()->json(['message' => trans('auth.account.activated.successfully')], 200);
        }else{
            return response()->json(['message' => trans('auth.user.doesnt.exist')], 404);  
        }
    }

    //----------------------------------------------------- block user ----------------------------------------------------
    public function blockUser(Request $request, $id)
    {
        if(User::where('id', '=', $id)->exists()){
            $user = User::find($id);
            $user->account_activated = true;
            $user->account_activated_at = Carbon::now();
            $user->save(); 
            //notify user that his account was activated
            Event::fire(new UserAccountActivatedEvent($user));
            return response()->json(['message' => trans('auth.account.activated.successfully')], 200);
        }else{
            return response()->json(['message' => trans('auth.user.doesnt.exist')], 404);  
        }
    }


    //----------------------------------------------------- unblockUser user ----------------------------------------------------
    public function unblockUser(Request $request, $id)
    {
        if(User::where('id', '=', $id)->exists()){
            $user = User::find($id);
            $user->account_activated = true;
            $user->account_activated_at = Carbon::now();
            $user->save(); 
            //notify user that his account was activated
            Event::fire(new UserAccountActivatedEvent($user));
            return response()->json(['message' => trans('auth.account.activated.successfully')], 200);
        }else{
            return response()->json(['message' => trans('auth.user.doesnt.exist')], 404);  
        }
    }
}
