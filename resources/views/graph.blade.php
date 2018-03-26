@extends('layouts.app')

@section('content')

  <nav id="sidebar">
    <div id="dismiss">
      <i class="glyphicon glyphicon-arrow-left"></i>
    </div>

    <div class="sidebar-header">
      <h3>Bootstrap Sidebar</h3>
    </div>

    <ul class="list-unstyled components">
      <p>Dummy Heading</p>
      <li class="active">
        <a href="#homeSubmenu" data-toggle="collapse" aria-expanded="false">Home</a>
        <ul class="collapse list-unstyled" id="homeSubmenu">
          <li><a href="#">Home 1</a></li>
          <li><a href="#">Home 2</a></li>
          <li><a href="#">Home 3</a></li>
        </ul>
      </li>
      <li>
        <a href="#">About</a>
        <a href="#pageSubmenu" data-toggle="collapse" aria-expanded="false">Pages</a>
        <ul class="collapse list-unstyled" id="pageSubmenu">
          <li><a href="#">Page 1</a></li>
          <li><a href="#">Page 2</a></li>
          <li><a href="#">Page 3</a></li>
        </ul>
      </li>
      <li>
        <a href="#">Portfolio</a>
      </li>
      <li>
        <a href="#">Contact</a>
      </li>
    </ul>
  </nav>

  <div class="overlay"></div>

  <div id="content">
    <div class="navbar-header">
      <button type="button" id="sidebarCollapse" class="btn btn-info navbar-btn">
        <i class="glyphicon glyphicon-align-left"></i>
        <span>Open Sidebar</span>
      </button>
    </div>
  </div>

  {{--<script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>--}}
  {{--<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>--}}
  {{--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">--}}

  <script src="{{ asset('js/sidebar.js') }}"></script>
  <link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">



<div class="card graphcard">
  <div class="card-body graphcard">
    <h4 class="card-title">Workflow</h4>
    <!--<button onclick="addNode(0,'new')">Add</button>-->
    <div id="cy">







    </div>
    <link href="{{ asset('css/workflow.css') }}" rel="stylesheet">
    <script src="{{ asset('js/workflow.js') }}" type="text/javascript"> </script>
  </div>
</div>





@endsection('content')