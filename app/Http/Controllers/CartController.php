<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Models\BeatLicense;
use App\Models\Cart;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;


class CartController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => ['pay']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $authUser = auth()->user();
        if(!in_array("admin",$authUser->getRoleNames()->toArray())){
            return response()->json(['message' => 'No autorizado'], 401);
        }

        $carts = Cart::all();
        return response()->json(CartResource::collection($carts), 201);

    }

    public function addItem(Request $request)
    {

        $validator = Validator($request->all(), [
            'beat_id' => 'required|exists:beats,id',
            'license_id' => 'required|exists:licenses,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }

        $authUser = auth()->user();
        $cart = Cart::where('user_id', $authUser->id)->first();

        $beat_license_id = BeatLicense::where('beat_id', $request->beat_id)->where('license_id', $request->license_id)->first()->id;
        if(!BeatLicense::find($beat_license_id)){
            return response()->json(["message" => "No existe ese beat"], 404);
        }

        if(!$cart) {
            $cart = Cart::create([
                'user_id' => $authUser->id
            ]);


            $cart->beatLicenses()->attach($beat_license_id);
        }
        else{

            if($cart->beatLicenses->contains($beat_license_id)){
                return response()->json(["message" => "Ese beat ya está en el carrito", "cart" => CartResource::make($cart)], 401);
            }
            else
            {
                $cart->beatLicenses()->attach($beat_license_id);
            }

        }


        return response()->json(["message" => "Beat agregado al carrito", "cart" => CartResource::make($cart)], 201);
    }

    public function removeItem(Request $request)
    {
        $authUser = auth()->user();
        $cart = Cart::where('user_id', $authUser->id)->first();
        if(!$cart) {
            return response()->json(["message" => "No existe el carrito"], 404);
        }
        if(!$cart->beatLicenses->contains($request->beat_license_id)){
            return response()->json(["message" => "Ese beat no está en el carrito", "cart" => CartResource::make($cart)], 201);
        }
        $cart->beatLicenses()->detach($request->beat_license_id);
        return response()->json(["message" => "Beat eliminado del carrito", "cart" => CartResource::make($cart)], 201);
    }

    public function clearCart()
    {
        $authUser = auth()->user();
        $cart = Cart::where('user_id', $authUser->id)->first();
        if(!$cart) {
            return response()->json(["message" => "No existe el carrito"], 404);
        }
        $cart->beatLicenses()->detach();
        return response()->json(["message" => "Carrito vaciado", "cart" => CartResource::make($cart)], 201);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $authUser = auth()->user();
        $cart = Cart::create([
            'user_id' => $authUser->id
        ]);
        return response()->json($cart, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cart $cart)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cart $cart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cart $cart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cart $cart)
    {
        //
    }

    public function payLogged (Request $request)
    {

        $validator = Validator($request->all(), [
            'cart' => 'required',
            'email' => 'required|email',
            'isLogged' => 'required'
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }


        $cart = collect($request->cart);
        $lineItems = $cart->map(function ($item) {

            $license = BeatLicense::find($item['id']);

            return [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['beat']['name'],
                        'images' => [$item['beat']['cover_path']],
                        'description' => $item['license']['name'],
                    ],
                    'unit_amount' => round($item['price']) * 100,
                ],
                'quantity' => 1,
            ];
        });

        $lineItemsBien = $lineItems->toArray();



        Stripe::setApiKey(config('stripe.sk'));

        $metadata = [
            'isLogged' => $request->isLogged,
            'email' => $request->email,
        ];



        $licenses_bought = [];

        foreach ($cart as $index => $item) {
            $beat_license = BeatLicense::find($item['id']);
            $licenses_bought[] = ['id' => $beat_license->id, 'download_key' => $beat_license->download_key, 'cover_url' => $beat_license->beat->cover_path, 'name' => $beat_license->beat->name, 'price' => $beat_license->price];
        }



        $metadata['licenses_bought'] = json_encode($licenses_bought);

        if($metadata['isLogged'])
        {
            $user_auth = auth()->user();
            $metadata['user_id'] = $user_auth->id;
        }


        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItemsBien,
            'mode' => 'payment',
            'success_url' => 'http://localhost:5173/checkout/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'http://localhost:5173/checkout/error',
            'metadata' => $metadata
        ]);


        return response()->json($session, 201);



    }
}
