@extends('emails.LayoutMail')

@section('content')
<p>Saludos {{$user->name}}</p>
<p>¡El beat ha sido comprado con éxito!</p>
<div style="padding:10px; background-color:#ef4444; border-radius:0.375rem;">
    <h2 style="width: 100%; background-color:#ef4444; color:white; margin-bottom: 10px;">Descargas</h2>
    @foreach($licenses_bought as $license)
    <div style="martin-top:20px; margin-bottom:20px; padding:5px; border-radius:0.375rem; background-color:white;">
        <a href="{{config('app.url')}}:8000/api/beatlicense/{{$license->download_key}}/download" style="padding:16px; background-color:#ef4444; color:white; text-decoration:none; font-weight:bold; border-radius:1rem;">Descargar beat {{$license->name}}</a>
    </div>
    @endforeach
</div>

@endsection
