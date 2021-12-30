<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use Illuminate\Support\Facades\Validator;
use Auth;

class InvoiceController extends Controller
{
    /*
        getAll invoices
        user invoice
        create invoice
        getOne invoice
        update invoice
        delete invoice
    */

    /* -------------------------------------------get all Invoices ------------------------------------------------ */
    public function index()
    {
        if (!Auth::user()->can('getAll invoice')) {
            return response()->json(['message'=> 'Permission Denied'], 401);
        }
        $invoices = Invoice::where('user_id', Auth::user()->id)->paginate(10);
        return response()->json([
            'message'   => 'invoices returned successfully',
            'data'      => $invoices
        ], 200);
    }

    /* -------------------------------------get one Invoice -------------------------------------- */
    public function get($id)
    {
        if (!Auth::user()->can('get invoice')) {
            return response()->json(['message'=>'Permission Denied'], 401);
        }
        if (Invoice::where('id', $id)->exists()) {
            $invoice = Invoice::where('id', $id)->get();
            return response()->json([
                'message'   => "one invoice returned",
                'data'      => $invoice
            ], 200);
        } else {
            return response()->json(["message" => "Invoice not found"], 404);
        }
    }

    /* ------------------------------------- create an Invoice -------------------------------------- */
    public function create(Request $request)
    {
        if (!Auth::user()->can('create invoice')) {
            return response()->json(['message'=>'Permission Denied'], 401);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'price' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 'Validation Error');
        }
        $invoice = new Invoice();
        $invoice->title = $request['title'];
        $invoice->code = $invoice->generateCode();
        $invoice->url = $request['url'];
        $invoice->customer_name = $request['customer_name'];
        $invoice->currency = $request['currency'];
        $invoice->expiration_date = $request['expiration_date'];
        $invoice->total_price = 0;
        $invoice->save();
        for($i = 0; $i < $request['number_of_items']; $i++){
            $product = new Product();
            $product->name = $request[$i+'_item_name'];
            $product->available_number = $request[$i+'_item_available_number'];
            $product->save();
        }
        $owner_profile = Auth::user()->profile();
        return response()->json(["message" => "invoice created successfully"], 201);
    }

    /* -------------------------------------update one Invoice -------------------------------------- */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('update invoice')) {
            return response(['Permission Denied']);
        }

        if (Invoice::where('id', $id)->exists()) {
            $invoice = Invoice::find($id);
            $invoice->price = is_null($request->price) ? $invoice->price : $request->price;
            $invoice->save();

            return response(["message" => "Invoice updated successfully"], 200);
        } else {
            return response(["message" => "Invoice not found"], 404);
        }
    }

    /* -------------------------------------delete Invoice -------------------------------------- */
    public function delete($id)
    {
        if (!Auth::user()->can('delete invoice')) {
            return response()->json(['message'=>'Permission Denied'], 401);
        }
        if (Invoice::where('id', $id)->exists()) {
            $invoice = Invoice::find($id);
            $invoice->delete();
            return response()->json(["message" => "Invoice deleted successfully"], 202);
        } else {
            return response()->json(["message" => "Invoice not found"], 404);
        }
    }
}
