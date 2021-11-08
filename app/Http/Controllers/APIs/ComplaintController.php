<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;
use Illuminate\Support\Facades\Validator;
use Auth;

class ComplaintController extends Controller
{
    /*
        getAll complaints
        user complaint
        create complaint
        getOne complaint
        update complaint
        delete complaint
    */
    public function getAll()
    {
        if (!Auth::user()->can('getAll complaint')) {
            return response(['Permission Denied']);
        }

        $complaint = Complaint::paginate(10);
        return response([
            'message'   => 'All Complaints',
            'data'      => $complaint
        ], 200);
    }

    public function get_user_complaint()
    {
        if (!Auth::user()->can('user complaint')) {
            return response(['Permission Denied']);
        }

        $complaint = Complaint::where('user_id', Auth::user()->id)->paginate(10);
        return response([
            'message'   => 'Your Complaints',
            'data'      => $complaint
        ], 200);
    }

    public function Create(Request $request)
    {
        if (!Auth::user()->can('create complaint')) {
            return response(['Permission Denied']);
        }

        $data = $request->all();
        //validator or request validator
        $validator = Validator::make($data, [
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 'Validation Error');
        }

        $complaint = Complaint::create([
            'user_id'   => Auth::user()->id,
            'content'   => $data['content']
        ]);

        return response(["message" => "Complaint record created"], 201);
    }


    public function getOne($id)
    {
        if (!Auth::user()->can('get complaint')) {
            return response(['Permission Denied']);
        }

        if (Complaint::where('id', $id)->exists()) {
            $complaint = Complaint::where('id', $id)->first();
            return response([
                'message'   => 'One Complaint',
                'data'      => $complaint
            ], 200);
        } else {
            return response(["message" => "Complaint not found"], 404);
        }
    }


    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('update complaint')) {
            return response(['Permission Denied']);
        }

        if (Complaint::where('id', $id)->exists()) {
            $complaint = Complaint::find($id);
            $complaint->content = is_null($request->content) ? $complaint->content : $request->content;
            $complaint->save();

            return response(["message" => "Complaint updated successfully"], 200);
        } else {
            return response(["message" => "Complaint not found"], 404);
        }
    }


    public function delete($id)
    {
        if (!Auth::user()->can('delete complaint')) {
            return response(['Permission Denied']);
        }

        if (Complaint::where('id', $id)->exists()) {
            $complaint = Complaint::find($id);
            $complaint->delete();
            return response(["message" => "Complaint record deleted"], 202);
        } else {
            return response(["message" => "Complaint not found"], 404);
        }
    }
}
