@auth
<link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">
<div class="wrapper">
    <nav id="sidebar">
        <div id="dismiss">
            <i class="fo-icon icon-left-open">&#xe801;</i>
        </div>

        <div class="sidebar-header">
            <h3>
                    {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
            </h3>
            <small><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"> {{ __('Logout') }} </a></small>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
        <ul class="list-unstyled components">
            <li>
                <a  href="{{ route('home') }}">Home</a>
                <a  href="{{ route('myworkflows') }}">My Workflows</a>
            </li>
            <li>
                <a class="subMenu" href="#pageSubmenu" data-toggle="collapse" aria-expanded="false">Administrator</a>
                <ul class="collapse list-unstyled" id="pageSubmenu">
                    <li><a class="sidebar-listitem" href="{{ route('usersverification.index') }}">Users verification</a></li>
                    <li><a class="sidebar-listitem" href="#">Submitted Workflows</a></li>
                    <li><a class="sidebar-listitem" href="#">User Questions</a></li>
                    <li><a class="sidebar-listitem" href="#">User Requests</a></li>
                </ul>
                @endcan
                <a href="{{ route('editaccount.index') }}">Edit Account Details</a>
            </li>
            <br/><br/><br/>
            <li>
                <a  href="{{ route('userguide') }}">User Guide</a>
            </li>
            <li>
                <a  href="https://www.evidencio.com/contact">Contact Us</a>
                <a  href="{{ route('about') }}">About</a>
                <a  href="{{ route('privacypolicy') }}">Privacy Policy</a>
                <a  href="{{ route('termsandconditions') }}">Terms & Conditions</a>
                <a  href="{{ route('disclaimer') }}">Disclaimer</a>
            </li>
        </ul>
    </nav>
</div>
<script src="{{ asset('js/sidebar.js') }}"></script>
@endauth
