@extends('layouts.app')
@section('content') @include('partials.sidebar')
<br>
<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
        <div class=" text-center">
          <br />
          <img style="max-width:100%; height:auto;" src="{{ URL::to('/images/evidenciologo.png') }}">
          <h3>Patient Portal</h3>
          <br />
        </div>
        <br>
            <form action="/search">
              <input type="text" name="search" class="form-control" style="width:100%; font-size:x-large; height:50px;" placeholder="Search for Models..."></input>
            </form>
        </div>
    </div>
</div>
@endsection
