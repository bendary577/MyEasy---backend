<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redis;
use Auth;
use App\Models\Company;
use App\Models\Seller;
use App\Models\CompanyInvoice;
use App\Models\SellerInvoice;

class InvoiceController extends Controller
{
    /*
        getAll invoices
        getOne invoice
        create invoice
        update invoice
        delete invoice
    */
    
    /* -------------------------------------------get all Invoices ------------------------------------------------ */
    public function index()
    {
        $this->authorize('get_all_invoices');
        $invoices = Redis::get('invoices');
        if(isset($invoices)) {
            $invoices = json_decode($invoices, FALSE);
        }else{
            if(Auth::user()->getHasCompanyProfileAttribute()){
                $invoices = CompanyInvoice::where('company_id', Auth::user()->profile->id)->paginate(10);
            }else if(Auth::user()->getHasSellerProfileAttribute()){
                $invoices = SellerInvoice::where('company_id', Auth::user()->profile->id)->paginate(10);
            }
            Redis::set('invoices', $invoices);
        }
        return response()->json([
            'message' => trans('invoice.invoices.returned.successfully'),
            'data' => $invoices,
        ], 200);
    }

    /* -------------------------------------get one Invoice -------------------------------------- */
    public function get($id)
    {
        if (!Auth::user()->can('get_invoice_details')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        if (Invoice::where('id', $id)->exists()) {
            $invoice = Redis::get('invoice');
            if(isset($invoice)) {
                $invoice = json_decode($invoice, FALSE);
            }else{
                $invoice = Invoice::where('id', $id)->get();
                Redis::set('invoice', $invoice);
            }
            return response()->json([
                'message' => trans('invoice.invoice.returned.successfully'),
                'data' => $invoice,
            ], 200);
        } else {
            return response()->json(["message" => trans('invoice.not.found')], 404);
        }
    }

    /* ------------------------------------- create an Invoice -------------------------------------- */
    public function create(Request $request)
    {
        if (!Auth::user()->can('create_invoice')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|min:8|max:255',
            'number_of_items' => 'required|integer',
            'expiration_date' => 'required|date',
            'currency' => 'in:EGP,USD'
        ]);
        if ($validator->fails()) {
            return response(['message' => $validator->errors()], 400);
        }
        $invoice = new Invoice();
        $invoice->code = $invoice->generateCode();
        $invoice->customer_name = $request['customer_name'];
        $invoice->currency = $request['currency'];
        $invoice->expiration_date = $request['expiration_date'];
        $invoice->number_of_items = $request['number_of_items'];
        $invoice->paid = false;
        $invoice->total_price = 0;
        $invoice->save();
        for($i = 0; $i < $request['number_of_items']; $i++){
            $product = new Product();
            $product->name = $request[$i+'_item_name'];
            $product->price = $request[$i+'_item_price'];
            $product->save();
            $invoice_item = new InvoiceItem();
            $invoice_item->quantity = $request[$i+'_item_quantity'];
            $invoice_item->product()->save($product);
            $invoice->total_price += $product->price * $invoice_item->quantity ;
            $invoice->invoiceItems()->save($invoice_item);  
        }
        if(Auth::user()->getHasCompanyProfileAttribute()){
            $company_invoice = new CompanyInvoice();
            $company_invoice->invoice()->save($invoice);
            Auth::user()->profile()->invoices()->save($company_invoice);
        }else if(Auth::user()->getHasSellerProfileAttribute()){
            $seller_invoice = new SellerInvoice();
            $seller_invoice->invoice()->save($invoice);
            Auth::user()->profile()->invoices()->save($seller_invoice);
        }
        return response()->json(["message" =>  trans('invoice.created.successfull')], 201);
    }

    /* -------------------------------------update one Invoice -------------------------------------- */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('update_invoice')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|min:8|max:255',
            'number_of_items' => 'required|integer',
            'expiration_date' => 'required|date',
            'currency' => 'in:EGP,USD'
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        if (Invoice::where('id', $id)->exists()) {
            if($request['customer_name']){
                $invoice->customer_name = $request['customer_name'];
            }
            if( $request['currency']){
                $invoice->currency = $request['currency'];
            }
            if($request['expiration_date']){
                $invoice->expiration_date = $request['expiration_date'];
            }
            if($request['number_of_items']){
                $invoice->number_of_items = $request['number_of_items'];
                for($i = 0; $i < $request['number_of_items']; $i++){
                    $product = new Product();
                    $product->name = $request[$i+'_item_name'];
                    $product->price = $request[$i+'_item_price'];
                    $product->save();
                    $invoice_item = new InvoiceItem();
                    $invoice_item->quantity = $request[$i+'_item_quantity'];
                    $invoice_item->product()->save($product);
                    $invoice->total_price += $product->price * $invoice_item->quantity ;
                    $invoice->invoiceItems()->save($invoice_item);  
                }
            }
            $invoice->save();
            return response(["message" => trans('invoice.updated.successfully')], 200);
        } else {
            return response(["message" => trans('invoice.not.found')], 404);
        }
    }

    /* -------------------------------------delete Invoice -------------------------------------- */
    public function delete($id)
    {
        if (!Auth::user()->can('delete_invoice')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        if (Invoice::where('id', $id)->exists()) {
            $invoice = Invoice::find($id);
            $invoice->delete();
            return response()->json(["message" => trans('invoice.deleted.successfully')], 200);
        } else {
            return response()->json(["message" => trans('invoice.not.found')], 404);
        }
    }
}
