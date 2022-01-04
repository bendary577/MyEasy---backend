<?php

namespace App\Http\Controllers\APIs;

use App\Events\MailActivateAccountRequestEvent;
use App\Events\NewOrderEvent;
use App\Events\NewOrderNotification;
use App\Events\OrderCanceledEvent;
use App\Events\OrderDeliveredEvent;
use App\Events\OrderStatusChangedEvent;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Customer;
use App\Models\Store;
use App\Models\OrderProduct;
use Auth;
use Illuminate\Support\Facades\Redis;

class OrderController extends Controller
{
    /*
        getAll orders
        create order
        getOne order
        update order
        delete order
        confirm order
        time order
    */
    /* -------------------------------------------get all Orders ------------------------------------------------ */
    public function index()
    {
        if (!Auth::user()->can('get_all_orders')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        $orders = Redis::get('orders');
        if(isset($orders)) {
            $orders = json_decode($orders, FALSE);
        }else{
            if(Auth::user()->getHasCustomerProfileAttribute()){
                $orders = Order::where('customer_id', Auth::user()->profile->id)->with('orderProduct')->paginate(10);
            }else{
                $orders = Order::where('store_id', Auth::user()->profile->store->id)->with('orderProduct')->paginate(10);
            }
            Redis::set('orders', $orders);
        }
        if(count(array($orders)) <= 1){
            return response()->json([
                'message'   => "sorry, you have no orders registered in the system",
            ], 404);
        }
        return response()->json([
            'message' => trans('order.orders.returned.successfully'),
            'data' => $orders,
        ], 200);
    }

    /* ------------------------------------- Get One Order ---------------------------------------- */
    public function get($id){
        if (!Auth::user()->can('get_order_details')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        $order = Order::find($id);
        if (Order::where('id', $id)->exists()) {
            $order = Redis::get('order');
            if(isset($order)) {
                $order = json_decode($order, FALSE);
            }else{
                $order = Order::where('id', $id)->with('storeProducts', 'logo')->get();
                Redis::set('order', $order);
            }
            return response()->json([
                'message' => trans('order.order.returned.successfully'),
                'data' => $order,
            ], 200);
        } else {
            return response()->json(["message" => trans('order.not.found')], 404);
        }
    }

    /* ------------------------------------- create an Order -------------------------------------- */
    public function create(Request $request, $product_id)
    {
        if (!Auth::user()->can('create_order')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        $product = Product::where('id', $product_id)->first();
        $new_product = new Product();
        $new_product->name = $product->name;
        $new_product->price = $product->price;
        $new_product->save();
        $order_product = new OrderProduct();
        $order_product->save();
        $order_product->product()->save($new_product);
        //make new order product
        $order = new Order();
        $order->total_price = $product->price;
        $order->customer_confirm = false;
        $order->store_confirm = false;
        $order->status = 'pending';
        $order->save();
        $order->orderProduct()->save($order_product);
        //increase customer orders number
        Auth::user()->profile->orders_number = Auth::user()->profile->orders_number + 1;
        Auth::user()->profile->save();
        //increase store customers number and orders number
        $store = $product->product->store;
        $store->orders_number = $store->orders_number + 1;
        //increase store number of customers if this is the first time to order from this store
        $number_of_past_orders = Order::where('customer_id', Auth::user()->profile->id)->where('store_id', $store->id)->count();
        if($number_of_past_orders == 0){
            $store->customers_number = $store->customers_number + 1;
        }
        $store->save(); 
        $order->customer()->associate(Auth::user()->profile)->save();
        $order->store()->associate($store)->save();
        //notify store that a customer has made a new order to one of his products
        Event::dispatch(new NewOrderEvent($order));
        return response(["message" =>  trans('order.order.created.successfully')], 201);
    }

    /* -------------------------------------update one order -------------------------------------- */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->can('update_order')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,received,canceled,being prepared,on the way,delivered',
        ]);
        if ($validator->fails()){
            return response()->json(['message' => $validator->errors()], 400);
        }
        if(Order::where('id', $id)->exists()){
            $order = Order::find($id);
            if(!Auth::user()->profile->ownThisStore($order->store_id)){
                return response()->json(["message" => "you dont have the authority to update this order"], 403);
            }else{
                $order->status = $request['status'];
                $order->save();
                //notify customer his order status changed
                Event::dispatch(new OrderStatusChangedEvent($order));
                return response()->json(["message" => "Order updated successfully"], 200);
            }
        }else{
            return response()->json(["message" => "Order not found"], 404);
        }
    }

    /* -------------------------------------------get all Orders ------------------------------------------------ */
    public function confirm(Request $request, $id)
    {
        if (!Auth::user()->can('confirm_order')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        if (Order::where('id', $id)->exists()){
            $order = Order::find($id);
            $user = Auth::user();
            if($user->getHasCustomerProfileAttribute()){
                if($order->customer_confirm == true){
                    return response()->json(["message" => trans('order.order.confirmed')], 200);
                }else{
                    $order->customer_confirm = true;
                    $order->save();
                    //notify customer and store  that order has been delivered successfully
                    Event::dispatch(new OrderDeliveredEvent($order));
                    return response()->json(["message" => trans('order.delivered.successfully')], 200);
                }
            }else{
                if($order->store_confirm == true){
                    return response()->json(["message" => trans('order.order.confirmed')], 200);
                }else{
                    $order->store_confirm = true;
                    $order->save();
                    return response()->json(["message" => trans('order.confirmed.successfully')], 200);
                }
            }
        } else {
            return response()->json(["message" => trans('order.not.found')], 404);
        }
    }

    /* -------------------------------------delete order -------------------------------------- */
    public function delete($id)
    {
        if (!Auth::user()->can('delete_order')) {
            return response()->json(['message'=> trans('permission.permission.denied')], 401);
        }
        if(Order::where('id', $id)->exists()){
            $order = Order::find($id);
            //if order had been made more than a day age, it can't be canceled
            if($order->created_at < Carbon::now()->subDays(1)->toDateTimeString()){
                return response()->json(["message" => trans('order.cant.be.canceled')], 200);
            }else{
                Auth::user()->profile->orders_number = Auth::user()->profile->orders_number - 1;
                Auth::user()->profile->save();
                $store = Store::where('id', $order->store_id)->first();
                $number_of_past_orders = Order::where('customer_id', Auth::user()->profile->id)->where('store_id', $store->id)->count();
                if($number_of_past_orders <= 1){
                    $store->customers_number = $store->customers_number - 1;
                }
                $store->orders_number = $store->orders_number - 1;
                $store->save();
                $order->orderProduct->delete();
                $order->delete();
                //notify customer and store  that order has been delivered canceled
                Event::dispatch(new OrderCanceledEvent($order));
                return response()->json(["message" => trans('order.deleted.successfully')], 200);
            }
        }else{
            return response()->json(["message" => trans('order.not.found')], 404);
        }
    }
}
