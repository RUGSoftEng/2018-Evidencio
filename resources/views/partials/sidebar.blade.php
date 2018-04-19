<script defer src="https://use.fontawesome.com/releases/v5.0.8/js/solid.js" integrity="sha384-+Ga2s7YBbhOD6nie0DzrZpJes+b2K1xkpKxTFFcx59QmVPaSA8c7pycsNaFwUK6l" crossorigin="anonymous"></script>
<script defer src="https://use.fontawesome.com/releases/v5.0.8/js/fontawesome.js" integrity="sha384-7ox8Q2yzO/uWircfojVuCQOZl+ZZBg2D2J5nkpLqzH1HY0C1dHlTKIbpRz/LG23c" crossorigin="anonymous"></script>

<div class="wrapper">
    <nav id="sidebar">
        <div id="dismiss">
            <i class="fas fa-angle-left"></i>
        </div>

        <div class="sidebar-header">
            <h3>
                @if (session('status'))
                    {{ Auth::user()->name }}
                @else
                    <h1>Failed</h1>
                @endif
            </h3>
        </div>

        <ul class="list-unstyled components">
            <p>My Account</p>
            <li>
                <a href="/myworkflows">My Workflows</a>
            </li>
            <li>
                <a class="somethingSomething" href="#pageSubmenu" data-toggle="collapse" aria-expanded="false">Administrator</a>
                <ul class="collapse list-unstyled" id="pageSubmenu">
                    <li><a class="doubleMenu" href="#">Submitted Workflows</a></li>
                    <li><a class="doubleMenu" href="#">User Questions</a></li>
                    <li><a class="doubleMenu" href="#">User Requests</a></li>
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
</div>
<script src="{{ asset('js/ya-simple-scrollbar.js') }}"></script>
<script src="{{ asset('js/sidebar.js') }}"></script>
<link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">
