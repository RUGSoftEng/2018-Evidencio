@extends('graph')

@section{'sidebar'}

<script defer src="https://use.fontawesome.com/releases/v5.0.8/js/solid.js" integrity="sha384-+Ga2s7YBbhOD6nie0DzrZpJes+b2K1xkpKxTFFcx59QmVPaSA8c7pycsNaFwUK6l" crossorigin="anonymous"></script>
<script defer src="https://use.fontawesome.com/releases/v5.0.8/js/fontawesome.js" integrity="sha384-7ox8Q2yzO/uWircfojVuCQOZl+ZZBg2D2J5nkpLqzH1HY0C1dHlTKIbpRz/LG23c" crossorigin="anonymous"></script>

<nav id="sidebar">
  <div id="dismiss">
    <i class="fas fa-angle-left"></i>
  </div>

  <div class="sidebar-header">
    <h3>@if (session('status'))
          {{ Auth::user()->name }}
        @else
          User
        @endif</h3>
  </div>

  <ul class="list-unstyled components">
    <p>My Account</p>
    <li>
      <a class="somethingSomething" href="#homeSubmenu" data-toggle="collapse" aria-expanded="false">Workflow</a>
      <ul class="collapse list-unstyled" id="homeSubmenu">
        <li><a href="#">Approved</a></li>
        <li><a href="#">Rejected</a></li>
        <li><a href="#">Drafts</a></li>
      </ul>
    </li>
    <li>
      <a class="somethingSomething" href="#pageSubmenu" data-toggle="collapse" aria-expanded="false">Administrator</a>
      <ul class="collapse list-unstyled" id="pageSubmenu">
        <li><a href="#">Submitted Workflows</a></li>
        <li><a href="#">User Questions</a></li>
        <li><a href="#">User Requests</a></li>
      </ul>
      <a href="#">Edit Account Details</a>
    </li>
    <p class="paragraphInSideMenu" >Help</p>
    <li>
      <a href="#">Instructions</a>
    </li>
    <li>
      <a href="#">Contact Us</a>
    </li>
  </ul>
</nav>

<div class="overlay"></div>

<script src="{{ asset('js/sidebar.js') }}"></script>
<link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">

@endsection{'sidebar'}