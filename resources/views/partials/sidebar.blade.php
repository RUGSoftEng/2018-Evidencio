<div class="wrapper">
    <nav id="sidebar">
        <div id="dismiss">
            <i class="fo-icon icon-left-open">&#xe801;</i>
        </div>

        <div class="sidebar-header">
            <h3>
                    {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
            </h3>
        </div>

        <ul class="list-unstyled components">
            <p>My Account</p>
            <li>
                <a href="/myworkflows">My Workflows</a>
            </li>
            <li>
                @can('is-administrator')
                <a class="subMenu" href="#pageSubmenu" data-toggle="collapse" aria-expanded="false">Administrator</a>
                <ul class="collapse list-unstyled" id="pageSubmenu">
                    <li><a class="sidebar-listitem" href="{{ route('usersverification.index') }}">Users verification</a></li>
                    <li><a class="sidebar-listitem" href="#">Submitted Workflows</a></li>
                    <li><a class="sidebar-listitem" href="#">User Questions</a></li>
                    <li><a class="sidebar-listitem" href="#">User Requests</a></li>
                </ul>
                @endcan
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
<script src="{{ asset('js/sidebar.js') }}"></script>
<link href="{{ asset('css/sidebar.css') }}" rel="stylesheet">
