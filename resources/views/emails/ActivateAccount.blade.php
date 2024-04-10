@extends('emails.LayoutMail')

@section('content')
<p>Saludos {{$user->name}}</p>
<p>Puedes activar tu cuenta en el siguiente
    <a href="{{ $url }}">enlace</a>
</p>
@endsection
