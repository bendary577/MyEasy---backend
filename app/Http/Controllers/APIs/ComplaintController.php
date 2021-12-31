<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\ComplaintAttachment;
use App\Models\File;
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
    /* -------------------------------------get one store -------------------------------------- */
    public function index()
    {
        if (!Auth::user()->can('getAll complaint')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        $complaints = Redis::get('complaints');
        if(isset($complaints)) {
            $complaints = json_decode($complaints, FALSE);
        }else{
            $complaints = Complaint::paginate(10);
            Redis::set('complaints', $complaints);
        }
        return response()->json([
            'message'   => trans('complaint.complaints.returned.successfully'),
            'data'      => $complaints
        ], 200);
    }

    /* -------------------------------------get one store -------------------------------------- */
    public function get($id)
    {
        if (!Auth::user()->can('user complaint')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        if (Complaint::where('id', $id)->exists()) {
            $complaint = Redis::get('complaint');
            if(isset($complaint)) {
                $complaint = json_decode($complaint, FALSE);
            }else{
                $complaint = Complaint::where('id', $id)->with('attachments')->get();
                Redis::set('complaint', $complaint);
            }
            return response()->json([
                'message' => trans('complaint.complaint.returned.successfully'),
                'data' => $complaint
            ], 200);
        } else {
            return response()->json(["message" => trans('complaint.not.found')], 404);
        }
    }

    /* -------------------------------------get one store -------------------------------------- */
    public function Create(Request $request)
    {
        if (!Auth::user()->can('create complaint')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        $validator = Validator::make($request->all(), [
            'content' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        $complaint = new Complaint();
        $complaint->has_reply = false;
        $complaint->status = 'pending';
        $complaint->content = $request['content'];
        if($request->hasfile('complaint_attachments')){
            foreach($request->file('complaint_attachments') as $complaint_attachment_file){
                $complaint_attachment_file_name = $complaint_attachment_file->getClientOriginalName();
                $complaint_attachment_file_size = $complaint_attachment_file->getClientSize();
                $complaint_attachment_file_extention = $complaint_attachment_file->extension();
                $complaint_attachment_file_path = '/complaints/'.Auth::user()->id.'/attachments/';
                //make new file
                $file = new File();
                $file->name = $complaint_attachment_file_name;
                $file->path = $complaint_attachment_file_path;
                $file->extention = $complaint_attachment_file_extention;
                $file->size = $complaint_attachment_file_size;
                $file->save();
                $complaint_attachment = new ComplaintAttachment();
                $complaint_attachment->file()->save($file);
                $complaint_attachment_file->move(public_path().$complaint_attachment_file_path, $complaint_attachment_file_name);
            }
        }
        $complaint->save();
        $complaint->user()->save(Auth::user());
        return response(["message" => trans('complaint.complaint.created.successfully')], 201);
    }

    /* -------------------------------------get one store -------------------------------------- */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('update complaint')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        $validator = Validator::make($request->all(), [
            'content' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        if (Complaint::where('id', $id)->exists()) {
            $complaint = Complaint::find($id);
            $complaint->content = $request['content'];
            $complaint->save();
            return response(["message" => trans('complaint.complaint.updated.successfully')], 200);
        } else {
            return response(["message" => trans('complaint.not.found')], 404);
        }
    }

    /* -------------------------------------get one store -------------------------------------- */
    public function delete($id)
    {
        if (!Auth::user()->can('delete complaint')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        if (Complaint::where('id', $id)->exists()) {
            $complaint = Complaint::find($id);
            $complaint->delete();
            return response(["message" => trans('complaint.complaint.deleted.successfully')], 202);
        } else {
            return response(["message" => trans('complaint.not.found')], 404);
        }
    }
}
