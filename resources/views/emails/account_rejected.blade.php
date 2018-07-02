@extends('emails.base')

@section('content')

{{ __("Dear :name",['name' => $userName])}},<br>

<p>{{ _("We are sorry, but your account has not been accepted by the administrator. This also means, that your account has been removed. If you have any questions, please contact us.") }}</p>

{{ _("Regards") }},<br>
{{ _("Evidencio Patient Portal Administration") }}

@endsection
