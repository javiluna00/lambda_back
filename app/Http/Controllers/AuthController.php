<?php

namespace App\Http\Controllers;

use App\Http\Resources\BeatResource;
use App\Http\Resources\BeatSaveResource;
use App\Http\Resources\CartResource;
use App\Http\Resources\PurchaseResource;
use App\Http\Resources\UserResource;
use App\Mail\ActivateAccount;
use App\Mail\ForgotPassword;
use App\Models\Beat;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Mail;




class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => ['login', 'register', 'loginOauth', 'checkUserGoogle', 'refresh', 'verify', 'sendForgotPasswordEmail', 'resetPassword']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Credenciales incorrectas'], 401);
        }
        if (auth()->user()->email_verified_at == null) {
            return response()->json(['error' => 'Cuenta no activada. Verifica tu correo.'], 401);
        }

        $token = JWTAuth::fromUser(auth()->user());

        $user = auth('api')->user();

        $cart = Cart::where('user_id', $user->id)->first();


        $savedBeatsIDS = DB::table('beat_saves')->where('user_id', auth()->user()->id)->get()->pluck('beat_id');
        $savedBeats = Beat::whereIn('id', $savedBeatsIDS)->get();


        return response()->json(['token' => $token, 'user' => UserResource::make($user), 'cart' => $cart ? CartResource::make($cart) : [], 'saves' => BeatResource::collection($savedBeats), 'purchases' => $user->purchases()], 200);
    }

    public function checkUserGoogle(Request $request)
    {
        $checkEmail = User::where([['email', "=", $request->email], ['google_id', "=", null]])->first();
        if ($checkEmail) {
            return response()->json(['message' => 'Ese email ya está registrado en una cuenta normal.'], 409);
        }


        $user = User::where('google_id', $request->google_id)->first();
        return response()->json($user);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $user = auth('api')->user();
        $cart = Cart::where('user_id', $user->id)->first();
        return response()->json(['user' => UserResource::make($user), 'cart' => $cart ? CartResource::make($cart) : null, 'saves' => $user->savedBeats(), 'purchases' => PurchaseResource::collection($user->purchases)], 200);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'artist_name' => 'string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);



        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'name' => $request->name,
            'artist_name' => $request->artist_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'email_verification_token' => bcrypt($request->email),
            'email_verified_at' => null
        ]);

        $user->assignRole('user');

        Mail::to($user->email)->send(new ActivateAccount($user));

        return response()->json([
            'message' => 'Cuenta registrada. Verifica tu correo para activar tu cuenta.',
        ], 201);

    }

    public function loginOauth(Request $request)
    {
        $requestUser = $request->user;
        $requestToken = $request->token;
        // Recibir los datos del frontend

        // Aquí puedes utilizar los datos para autenticar al usuario con Google OAuth
        // Por ejemplo, verificar el token de acceso con la API de Google y obtener información del usuario
        // Luego, puedes crear o actualizar el usuario en tu base de datos y generar un token JWT para este usuario

        // Ejemplo de respuesta

        $artistName = $requestUser['artist_name'] ?? null;

        $user = User::firstOrCreate(['google_id' => $requestUser['google_id']], [
            'name' => $requestUser['name'],
            'email' => $requestUser['email'],
            'google_id' => $requestUser['google_id'],
            'artist_name' => $artistName,
            'email_verified_at' => now(),
            'email_verification_token' => null
        ]);

        $user->assignRole('user');

        $token = JWTAuth::fromUser($user);

        $cart = Cart::where('user_id', $user->id)->first();

        $saves = $user->savedBeats();

        return response()->json(['token' => $token, 'user' => UserResource::make($user), 'cart' => $cart ? CartResource::make($cart) : [], 'saves' => $saves, 'purchases' => $user->purchases()], 200);
    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user = User::where('email_verification_token', $request->token)->first();
        if (!$user) {
            return response()->json(['message' => 'Token incorrecto.'], 400);
        }
        if ($user->email_verified_at == null) {
            $user->email_verified_at = now();
            $user->email_verification_token = null;
            $user->save();
            return response()->json(['message' => 'Cuenta verificada.'], 200);
        } else {
            return response()->json(['message' => 'Cuenta ya verificada.'], 400);
        }
    }

    public function sendForgotPasswordEmail(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }


        $user = User::where('email', $request->email)->first();
        if(!$user)
        {
            return response()->json(['message' => 'Ese email no existe.'], 400);
        }
        if($user->google_id != null){
            return response()->json(['message' => 'No puedes cambiar la contraseña en este tipo de cuenta. Inicia sesión desde Google.'], 409);
        }


        $user->reset_password_token = bcrypt($request->email . now());
        $user->save();
        Mail::to($user->email)->send(new ForgotPassword($user->id));
        return response()->json(['message' => 'Se envió un enlace a tu correo para cambiar la contraseña.'], 200);
    }

    public function resetPassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
            'reset_password_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::where('reset_password_token', $request->reset_password_token)->first();

        if (!$user) {
            return response()->json(['message' => 'Token incorrecto.'], 400);
        }


        $user->password = bcrypt($request->password);
        $user->reset_password_token = null;
        $user->save();
        return response()->json(['message' => 'Contraseña cambiada con éxito.'], 200);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }



}
