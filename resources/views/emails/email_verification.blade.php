@extends('emails.base')

@section('content')

{{ _("Dear")}} {{$user->first_name}},<br>

<p>{{ _("You have been registered on Evidencio Patient Portal. Please click the link below to verify your e-mail address:") }}</p>

<a href="{{route('emailverification',['token' => $user->email_token])}}">Verify E-mail</a><br>

<p>{{ _("Note that the link will be available for only 24 hours. If you don't click it during that time, your account will be deleted.") }}</p>

{{ _("Regards") }},<br>
{{ _("Evidencio Patient Portal Administration") }}

@endsection
