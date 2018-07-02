@extends('layouts.app')

@section('content')

@include('partials.sidebar')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

<div class="container-fluid justify-content-center">

    <div class="card">

        <div class="card-header">
            <h3 style="display: inline-block;">{{ _('Users Verification')}}</h3>
        </div>

        <div class="card-body">

            @if(count($users) > 0)
            <div>
                <table class="table table-usersverification">
                    <tr class="d-none d-md-table-row">
                        <th>{{_("Name")}}</th>
                        <th>{{_("BIG Code")}}</th>
                        <th>{{_("Organisation")}}</th>
                        <th></th>
                    </tr>
                    @foreach($users as $user)
                    <tr class="d-md-none">
                        <td>
                            <dl class="row mt-4 mb-1">
                                <dt class="col-sm-3">{{_("Name")}}</dt>
                                <dd class="col-sm-9">{{ $user->first_name }} {{ $user->last_name }}</dd>
                                <dt class="col-sm-3">{{_("BIG Code")}}</dt>
                                <dd class="col-sm-9">
                                    @if($user->big_code)
                                    {{$user->big_code}}
                                    @else
                                    {{_("Not provided")}}
                                    @endif
                                </dd>
                                <dt class="col-sm-3">{{_("Organisation")}}</dt>
                                <dd class="col-sm-9">
                                    @if($user->organisation)
                                    {{$user->organisation}}
                                    @else
                                    {{_("Not provided")}}
                                    @endif
                                </dd>
                                <dt class="col-sm-3">{{_("Email")}}</dt>
                                <dd class="col-sm-9">{{ $user->email }}</dd>
                                <dt class="col-sm-3">{{_("Academic Degree")}}</dt>
                                <dd class="col-sm-9">
                                    @if($user->academic_degree)
                                    {{$user->academic_degree}}
                                    @else
                                    {{_("Not provided")}}
                                    @endif
                                </dd>
                                <dt class="col-sm-3">{{_("Bio")}}</dt>
                                <dd class="col-sm-9">
                                    @if($user->bio)
                                    {{$user->bio}}
                                    @else
                                    {{_("Not provided")}}
                                    @endif
                                </dd>
                                @foreach($user->registrationDocuments as $document)
                                <dt class="col-sm-3">{{__("Document :no", ['no' => $loop->iteration])}}</dt>
                                <dd class="col-sm-9">{{ $document->name }} <a class="badge badge-secondary" href="{{ route('usersverification.download',['id' => $document->id]) }}">{{_("Open")}}</a></dd>
                                @endforeach
                            </dl>

                            <form method="post" class="user-accept d-inline-block mt-1 mb-4" action="{{ route("usersverification.accept")}}">
                                {{ csrf_field() }}
                                <input type="hidden" name="user_id" value="{{$user->id}}">
                                <input type="submit" class="btn btn-success btn-sm" value="{{_("Accept")}}">
                            </form>
                            <form method="post" class="user-reject d-inline-block mt-1 mb-4" action="{{ route("usersverification.reject")}}">
                                {{ csrf_field() }}
                                <input type="hidden" name="user_id" value="{{$user->id}}">
                                <input type="submit" class="btn btn-danger btn-sm" value="{{_("Reject")}}">
                            </form>
                        </td>
                    </tr>
                    <tr class="d-none d-md-table-row">
                        <td>{{$user->first_name}} {{$user->last_name}}</td>
                        @if($user->big_code)
                        <td>{{$user->big_code}}</td>
                        @else
                        <td>{{_("Not provided")}}</td>
                        @endif
                        @if($user->organisation)
                        <td>{{$user->organisation}}</td>
                        @else
                        <td>{{_("Not provided")}}</td>
                        @endif
                        <td class="text-right"><a data-toggle="collapse" href="#collapse-{{$user->id}}" role="button" class="btn btn-info btn-sm">{{_("More")}}</a>
                        <form method="post" class="user-accept d-inline-block" action="{{ route("usersverification.accept")}}">
                            {{ csrf_field() }}
                            <input type="hidden" name="user_id" value="{{$user->id}}">
                            <input type="submit" class="btn btn-success btn-sm" value="{{_("Accept")}}">
                        </form>
                        <form method="post" class="user-reject d-inline-block" action="{{ route("usersverification.reject")}}">
                            {{ csrf_field() }}
                            <input type="hidden" name="user_id" value="{{$user->id}}">
                            <input type="submit" class="btn btn-danger btn-sm" value="{{_("Reject")}}">
                        </form>
                        </td>
                    </tr>
                    <tr class="d-none d-md-table-row">
                        <td colspan="5" class="px-3 py-0 border-top-0">
                            <div class="collapse" id="collapse-{{$user->id}}">
                                <dl class="row my-4">
                                    <dt class="col-sm-3">{{_("Email")}}</dt>
                                    <dd class="col-sm-9">{{ $user->email }}</dd>
                                    <dt class="col-sm-3">{{_("Academic Degree")}}</dt>
                                    <dd class="col-sm-9">
                                        @if($user->academic_degree)
                                        {{$user->academic_degree}}
                                        @else
                                        {{_("Not provided")}}
                                        @endif
                                    </dd>
                                    <dt class="col-sm-3">{{_("Bio")}}</dt>
                                    <dd class="col-sm-9">
                                        @if($user->bio)
                                        {{$user->bio}}
                                        @else
                                        {{_("Not provided")}}
                                        @endif
                                    </dd>
                                    @foreach($user->registrationDocuments as $document)
                                    <dt class="col-sm-3">{{__("Document :no", ['no' => $loop->iteration])}}</dt>
                                    <dd class="col-sm-9">{{ $document->name }} <a class="badge badge-secondary" href="{{ route('usersverification.download',['id' => $document->id]) }}">{{_("Open")}}</a></dd>
                                    @endforeach
                                </dl>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>
            @else
            <h4 class="text-center">{{_("No users to verify")}}</h4>
            @endif

        </div>


        </div>
    </div>

</div>

@endsection
