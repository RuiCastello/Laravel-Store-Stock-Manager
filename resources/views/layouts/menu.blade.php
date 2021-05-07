<div id="menu">

    <input type="checkbox">

    <div id="burger-btn">
      <span></span>
      <span></span>
      <span></span>
    </div>

    <nav id="nav-menu">

        @if(!empty(Auth::user()->role))
        <div id="shoe-store-menu">
           <ul>
                <h4>Shoes</h4>
                <li>
                    <a href="{{ route('shoe.index') }}">List</a>
                </li>
                @if(!empty(Auth::user()->role) && Auth::user()->role == "admin")
                <li>
                    <a href="{{ route('shoe.create') }}">Add</a>
                </li>
                @endif

            </ul>
        </div>
        <div id="shoe-store-menu2">
            <ul>
                <h4>Feedstock</h4>
                <li>
                    <a href="{{ route('feedstock.index') }}">List</a>
                </li>

                <li>
                    <a href="{{ route('feedstock.create') }}">Add</a>
                </li>
             </ul>
         </div>

         @else
         {{-- If not logged in, please sign in. --}}
         <div id="shoe-store-menu">

            <ul>

                 <h4>Welcome! </h4>
                    <p> Please log in or register an account to access the website.</p>
                 <li>
                     <a href="{{ route('login') }}">Login</a>
                 </li>
                 <li>
                     <a href="{{ route('register') }}">Register</a>
                 </li>
             </ul>
         </div>

         @endif
    </nav>
</div>
