@extends('layouts.app')

@section('content')
<div class="container-fluid justify-content-center">

    <div class="card">

        <div class="card-header">
            <h3>{{ _('Waiting for verification')}}</h3>
        </div>

        <div class="card-body">

            {{ _("Your account is waiting to be verified by an administrator. When the process is finished and you get approved, you will be able to create workflows. We will send you an email as soon as your verification is complete.")}}

        </div>


        </div>
    </div>

</div>
@endsection
