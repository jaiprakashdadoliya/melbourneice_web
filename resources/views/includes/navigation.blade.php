<div class="container tabheader">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="{{ route('home') }}"><img src="{{ HOME_PAGE_LOGO }}"></a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
      @php 
        $activeHome = '';
        $activeProfile = '';
        $activeProduct = '';
        $activeMemberShip = '';
        $activeContact = '';
        $activeSetting ='';
        $urlSegment = Request::segment(1);
        
        if($urlSegment == 'home'):
          $activeHome = 'active';
        elseif($urlSegment == 'profile'):
          $activeProfile = 'active';
        elseif($urlSegment == 'prod&serv'):
          $activeProduct = 'active';
        elseif($urlSegment == 'membserships'):
          $activeMemberShip = 'active';
        elseif($urlSegment == 'contact'):
          $activeContact = 'active';
        elseif($urlSegment == 'setting'):
          $activeSetting = 'active';  
        else:
          $activeClass = ''; 
        endif  

      @endphp
      <ul class="nav navbar-nav">
        <li class='{{ $activeHome }}'><a href="{{ route('home') }}">Home</a></li>
        <li class='{{ $activeProfile }}'><a href="{{ route('profile') }}">Profile</a></li>
        <li class='{{ $activeProduct }}'><a href="{{ route('prod&serv') }}">Product & Services</a></li>
        <!-- <li class='{{ $activeMemberShip }}'><a href="{{ route('membershipStatus') }}">Membership</a></li> -->
        <li class='{{ $activeContact }}'><a href="{{ route('contact') }}">Contact Us</a></li>
        <li class='{{ $activeSetting }}'><a href="{{ route('setting') }}">Settings</a></li>
        <li><a href="{{ route('logout') }}">Logout</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <!-- <li class="search text-right"><input type="text" value="" placeholder=""></li> -->
        @php
            $totalCount = UserHelper::checkout_session_data();
            if($totalCount){
              $totalcount = count($totalCount); 
            } else {
              $totalcount = 0;
            }
        @endphp
        <li class="cart pull-right"><a href="{{ route('cartdetails') }}"><img src="{{ asset('public/web/images/cart.png') }}"><span class="cart-value">{{ $totalcount }}</span></a></li>
      </ul>
    </div><!--/.nav-collapse -->
  </div><!--/.container-fluid -->
</div>