<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDescription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Zerkxubas\EsewaLaravel\Facades\Esewa;

use function Pest\Laravel\withHeader;

class OrderController extends Controller
{
    public function order(Request $request)
    {
        // return $request;
        $user = User::find(Auth::user()->id);
        $order = new Order();
        $order->total_amount = $request->total_amount;
        $order->seller_id = $request->seller_id;
        $order->user_id = $user->id;
        $order->total_amount = $request->total_amount;
        $order->status = 'pending';
        $order->payment_method = $request->payment_method;
        $order->save();

        $carts = [];
        foreach ($user->carts as $c) {
            if ($c->product->seller_id == $request->seller_id) {
                $carts[] = $c;
                $c->delete();
            }
        }

        foreach ($carts as $c) {
            $orderDescription = new OrderDescription();
            $orderDescription->order_id = $order->id;
            $orderDescription->product_id = $c->product_id;
            $orderDescription->quantity = $c->quantity;
            $orderDescription->amount = $c->amount;
            $orderDescription->save();
        }
        Cookie::queue('order_id', $order->id);

        if ($request->payment_method == 'khalti') {
            $response = Http::withHeaders([
                "Authorization" => "key ffcf180e4d4b439ba184930aa35a625c",
                'Content-Type' => 'application/json'
            ])->post(
                'https://dev.khalti.com/api/v2/epayment/initiate/',
                [
                    'return_url' => route('khalti_callback'),
                    'website_url' => route('homepage'),
                    'amount' => $request->total_amount * 100,
                    'purchase_order_id' => $order->id,
                    'purchase_order_name' => $order->seller->name,
                ]
            );

            if ($response->successful()) {
                $paymentUrl = $response['payment_url'];
                return redirect($paymentUrl);
            } else {
                return back()->with('error', 'Failed to initiate Khalti payment.');
            }
        } elseif ($request->payment_method == 'esewa') {
            $pid = uniqid();
            $product_code = "EPAYTEST";
            $message = $order->total_amount.$pid.$product_code;
            $signature = base64_encode(hash_hmac('sha256', $message, '8gBm/:&EnhH.1/q', true));
            return view('frontend.esewa', compact('order', 'pid', 'product_code', 'signature'));
        } else {
            return view("frontend.404");
        }

        // return $response->payment_url;
    }

    public function khalti_callback(Request $request)
    {
        // if($request['pidx'])
        $response = Http::withHeaders([
            "Authorization" => "key ffcf180e4d4b439ba184930aa35a625c",
            'Content-Type' => 'application/json'
        ])->post(
            'https://dev.khalti.com/api/v2/epayment/lookup/',
            [
                'pidx' => $request['pidx']
            ]
        );
        // dd($response);
        if ($response["status"] == "Completed") {
            $order_id = Cookie::get('order_id');
            $order = Order::find($order_id);
            $order->status = 'paid';
            $order->save();
            return redirect('/');
        }

        return redirect('/');
    }

    public function success(Request $request)
    {
        $order_id = Cookie::get('order_id');
        $order = Order::find($order_id);
        $order->status = 'paid';
        $order->save();
        return redirect('/');
    }

    public function failure(Request $request)
    {
        return view("frontend.404");
    }
}
