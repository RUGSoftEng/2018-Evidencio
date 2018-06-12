@extends('emails.base')

@section('content')

{{ _("Dear")}} {{$user->first_name}},<br>

<p>{{ _("Your Evidencio Patient Portal account has been successfully approved. You can now log in and create workflows.") }}</p>

{{ _("Regards") }},<br>
{{ _("Evidencio Patient Portal Administration") }}

@endsection
