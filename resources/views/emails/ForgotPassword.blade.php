@extends('emails.LayoutMail')

@section('content')
<tr>
    <h3 style="width: 100%; text-align: center; margin-bottom: 10px; font-size: 32px;">Saludos {{$name}}</h3>
</tr>
<tr>
    <p style="font-size: 14px; text-align: center; margin-bottom: 10px;">Si no has solicitado restablecer tu contraseña, ignora este correo. Si no, puedes restablecerla en el siguiente botón.</p>
</tr>
<tr>
    <td align="center">
        <a style="display:inline-block; margin: 0 auto; padding:20px;font-size:14px; background-color:#ef4444; color:white; text-decoration:none; font-weight:bold; border-radius:0.5rem;" href="{{ config('app.url') }}:5173/reset-password?token={{ $reset_password_token }}">Restablecer contraseña</a>
    </td>
</tr>
<tr style="padding:10px; margin-top:20px; border-radius:0.375rem;">
    <p style="font-size:12px; text-align:center;">Si no te redirige el botón, puedes hacerlo manualmente en el siguiente enlace : </p>
    <a style="font-size: 12px; text-align: center;" href="{{ config('app.url') }}:5173/reset-password?token={{ $reset_password_token }}">{{ config('app.url') }}:5173/reset-password?token={{ $reset_password_token }}</a>
</tr>

@endsection
