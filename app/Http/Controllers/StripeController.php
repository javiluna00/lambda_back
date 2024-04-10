<?php

namespace App\Http\Controllers;
use Stripe\Stripe;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function getSessionInfo(Request $request)
    {
        $stripe = new \Stripe\StripeClient(config('stripe.sk'));
        $session = $stripe->checkout->sessions->retrieve($request->session_id, ['expand' => ['line_items']]);
        if(!$session){
            return response()->json(["message" => "No existe esa sesión"], 404);
        }
        return response()->json($session, 201);
    }
}
