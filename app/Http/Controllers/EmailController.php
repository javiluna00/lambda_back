<?php

namespace App\Http\Controllers;

use App\Mail\SuccessfulPurchase;
use App\Mail\TestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class EmailController extends Controller
{
    public function sendSuccessfulPurchaseEmail(Request $request)
    {

        $isLogged = $request->input('isLogged');
        $email = $request->input('email');
        $licenses = $request->input('licenses');
        $userId = $request->input('user_id');

        $user = User::find($userId);
        $title = '¡Gracias por tu compra!';

        Mail::to($email)->send(new SuccessfulPurchase($title, $isLogged, $user, $licenses));

        return "Email sent successfully!";
    }

    public function sendEmail(Request $request)
    {
        $email = $request->input('email');
        $title = $request->input('title');
        $message = $request->input('message');

        Mail::to($email)->send(new TestMail($title, $message, $email));
    }
}
