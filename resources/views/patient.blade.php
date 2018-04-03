

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-12">
          <div class="jumbotron text-center" style="display">
        <div class="jumbotron text-center">
          <br />
          <h1>E V I D E N C I O</h1>
          <br />

        </div>

      </div>
            <form action="/search">
              <input type="text" name="search" class="form-control" style="width:100%; font-size:x-large; height:50px;" placeholder="Search for Models..."></input>

            </form>
        </div>
    </div>
</div>
@endsection
