@extends('emails.LayoutMail')

@section('content')
<p>Saludos {{$email}}</p>
<p>¡El beat ha sido comprado con éxito!</p>
<div style="width:100%; height:16px; background-color:crimson;">
    @foreach($licenses as $license)
    <button onclick="window.location.href='{{$license->url}}'">Descargar beat {{$license->beat->name}}</button>
    @endforeach

</div>

@endsection
