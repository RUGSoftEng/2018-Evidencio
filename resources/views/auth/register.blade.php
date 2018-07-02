@extends('layouts.app')

@section('content')

<link href="{{ asset('css/register.css') }}" rel="stylesheet">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row required">
                            <label for="first_name" class="col-md-4 col-form-label text-md-right">{{ __('First Name') }}</label>

                            <div class="col-md-6">
                                <input id="first_name" maxlength="255" type="text" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" name="first_name" value="{{ old('first_name') }}" required autofocus>

                                @if ($errors->has('first_name'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('first_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row required">
                            <label for="last_name" class="col-md-4 col-form-label text-md-right">{{ __('Last Name') }}</label>

                            <div class="col-md-6">
                                <input id="last_name" maxlength="255" type="text" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" name="last_name" value="{{ old('last_name') }}" required>

                                @if ($errors->has('last_name'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('last_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row required">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- TODO: photo, language --}}

                        <div class="form-group row">
                            <label for="academic_degree" class="col-md-4 col-form-label text-md-right">{{ __('Academic Degree') }}</label>

                            <div class="col-md-6">
                                <input id="academic_degree" maxlength="30" type="text" class="form-control{{ $errors->has('academic_degree') ? ' is-invalid' : '' }}" name="academic_degree" value="{{ old('academic_degree') }}">

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
                                <input id="big_code" maxlength="11" minlength="11" type="text" class="form-control{{ $errors->has('big_code') ? ' is-invalid' : '' }}" name="big_code" value="{{ old('big_code') }}">

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
                                <textarea id="bio" maxlength="5000"  class="form-control{{ $errors->has('bio') ? ' is-invalid' : '' }}" name="bio">{{ old('bio') }}</textarea>

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
                                <input id="organisation" maxlength="255" type="text" class="form-control{{ $errors->has('organisation') ? ' is-invalid' : '' }}" name="organisation" value="{{ old('organisation') }}">

                                @if ($errors->has('organisation'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('organisation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">

                            <span class="col-md-4 col-form-label text-md-right">{{ __('Documents') }}</span>

                            <div class="col-md-6" id="files">
                                <document-input v-for="(item,index) in fileList" v-bind:file="index" v-bind:key="item.id"></document-input>
                                <div class="text-right">
                                    <button class="btn btn-primary add-document" v-on:click="addButton" type="button" v-show="fileList.length < maxFileNum">+</button>
                                </div>

                                <small class="form-text text-muted">
                                    {{_("Provide documents that verify you as a medical professional. Only pdf format is accepted.")}}
                                </small>

                                @if ($errors->has('file.*'))
                                    <span class="invalid-feedback d-block">
                                        <strong>{{ $errors->first('file.*') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>

                        <div class="form-group row required">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

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

                        <div class="form-group row required">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>

                                <small class="form-text text-muted required-tooltip">
                                    {{_(" - required field.")}}
                                </small>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/registration.js') }}"></script>
@endsection
