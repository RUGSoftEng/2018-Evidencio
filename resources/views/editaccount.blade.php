@extends('layouts.app')

@section('content')

@include('partials.sidebar')

<link href="{{ asset('css/register.css') }}" rel="stylesheet">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Edit account') }}</div>

                <div class="card-body">

                    @if (session('status'))
                       <div class="alert alert-success">
                           {{ session('status') }}
                       </div>
                    @endif

                    <form method="POST" action="{{ route('editaccount.edit') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="first_name" class="col-md-4 col-form-label text-md-right">{{ __('First Name') }}</label>

                            <div class="col-md-6">
                                <input id="first_name" type="text" class="form-control-plaintext" readonly value="{{ $user->first_name }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="last_name" class="col-md-4 col-form-label text-md-right">{{ __('Last Name') }}</label>

                            <div class="col-md-6">
                                <input id="last_name" type="text" class="form-control-plaintext" readonly value="{{ $user->last_name }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control-plaintext" readonly value="{{ $user->email }}">

                                <small class="form-text text-muted">
                                    {{_("Please contant the administrator if you want to change the data above.")}}
                                </small>
                            </div>
                        </div>

                        {{-- TODO: photo, language --}}

                        <div class="form-group row">
                            <label for="academic_degree" class="col-md-4 col-form-label text-md-right">{{ __('Academic Degree') }}</label>

                            <div class="col-md-6">
                                <input id="academic_degree" maxlength="30" type="text" class="form-control{{ $errors->has('academic_degree') ? ' is-invalid' : '' }}" name="academic_degree" value="@if(old('academic_degree')){{ old('academic_degree') }}@else{{ $user->academic_degree }}@endif" autofocus>

                                @if ($errors->has('academic_degree'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('academic_degree') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="big_code" class="col-md-4 col-form-label text-md-right">{{ __('BIG Code') }}</label>

                            <div class="col-md-6">
                                <input id="big_code" maxlength="11" minlength="11" type="text" class="form-control{{ $errors->has('big_code') ? ' is-invalid' : '' }}" name="big_code" value="@if(old('big_code')){{ old('big_code') }}@else{{ $user->big_code }}@endif">

                                <small class="form-text text-muted">
                                    {{_("An 11-digit code issued to doctors in the Netherlands.")}}
                                </small>

                                @if ($errors->has('big_code'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('big_code') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="bio" class="col-md-4 col-form-label text-md-right">{{ __('Bio') }}</label>

                            <div class="col-md-6">
                                <textarea id="bio" maxlength="5000"  class="form-control{{ $errors->has('bio') ? ' is-invalid' : '' }}" name="bio">@if(old('bio')){{ old('bio') }}@else{{ $user->bio }}@endif</textarea>

                                @if ($errors->has('bio'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('bio') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="organisation" class="col-md-4 col-form-label text-md-right">{{ __('Organisation') }}</label>

                            <div class="col-md-6">
                                <input id="organisation" maxlength="255" type="text" class="form-control{{ $errors->has('organisation') ? ' is-invalid' : '' }}" name="organisation" value="@if(old('organisation')){{ old('organisation') }}@else{{ $user->organisation }}@endif">

                                @if ($errors->has('organisation'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('organisation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row required">
                            <label for="old-password" class="col-md-4 col-form-label text-md-right">{{ __('Old Password') }}</label>

                            <div class="col-md-6">
                                <input id="old-password" type="password" class="form-control{{ $errors->has('old-password') ? ' is-invalid' : '' }}" name="old-password">

                                @if ($errors->has('old-password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('old-password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('New Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password">

                                <small class="form-text text-muted">
                                    {{_("The password must be at least 6 characters long.")}}
                                </small>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm New Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation">

                                <small class="form-text text-muted required-tooltip">
                                    {{_(" - required field.")}}
                                </small>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Submit') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
