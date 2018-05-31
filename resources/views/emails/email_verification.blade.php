@extends('emails.base')

@section('content')

{{ _("Dear")}} {{$user->first_name}},<br>

<p>{{ _("You have been registered on Evidencio Patient Portal. Please click the link below to verify your e-mail address:") }}</p>

<a href="{{route('emailverification',['token' => $user->email_token])}}">Verify E-mail</a><br>

{{ _("Regards") }},<br>
{{ _("Evidencio Patient Portal Administration") }}

@endsection
