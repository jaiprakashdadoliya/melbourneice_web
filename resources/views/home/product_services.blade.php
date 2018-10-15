@extends('layouts.default')

@section('title', 'Prod & Serv')

@section('content')
  <nav class="navbar navbar-default">
      @include('includes.navigation')
  </nav>
  <div class="container">
      <h1 class="title">Welcome <span>{{ $userName = UserHelper::get_profile_name() }}!</span></h1>
      <div class="inner-content">
      
        <h3 class="page-title">Product & Services</h3> <!-- Please select anyone and proceed. -->
        @if(Session::get('message') != '')
          <div class="alert alert-danger alert-dismissable">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              {{ Session::get('message') }}
          </div>
        @endif
          <div class="select_atleast_one" style="display: none;"></div>
        <form name="productServices" method="POST" action="{{ route('membership') }}">
          {{ csrf_field() }}
          <div class="product-items clearfix">
            <ul class="list-unstyled">
              <li>
                <input type="radio" id="control_01" name="select_service" value="1">
                <label for="control_01">
                  <p>Membership-image</p>
                  <span>Membership </span>
                </label>
              </li>
              <!-- <li>
                <input type="radio" id="control_02" name="select_service" value="2" disabled>
                <label for="control_02">
                  <p>Retail-image</p>
                  <span>Retail</span>
                </label>
                <div class="coming-soon">Coming <br> Soon</div>
              </li>
              <li>
                <input type="radio" id="control_03" name="select_service" value="3" disabled>
                <label for="control_03">
                  <p>Media-image</p>
                  <span>Media</span>
                </label>
                <div class="coming-soon">Coming <br> Soon</div>
              </li>
              <li>
                <input type="radio" id="control_04" name="select_service" value="4" disabled>
                <label for="control_04">
                  <p>Ticket-image</p>
                  <span>Ticketing</span>
                </label>
                <div class="coming-soon">Coming <br> Soon</div>
              </li>
              <li>
                <input type="radio" id="control_06" name="select_service" value="6" disabled>
                <label for="control_06">
                  <p>seating-image</p>
                  <span>Seating</span>
                </label>
                <div class="coming-soon">Coming <br> Soon</div>
              </li>
              <li>
                <input type="radio" id="control_07" name="select_service" value="7" disabled>
                <label for="control_07">
                  <p>event-image</p>
                  <span>Event (Gala etc.)</span>
                </label>
                <div class="coming-soon">Coming <br> Soon</div>
              </li>
              <li>
                <input type="radio" id="control_05" name="select_service" value="5" disabled>
                <label for="control_05">
                  <p>player-image</p>
                  <span>Players</span>
                </label>
                <div class="coming-soon">Coming <br> Soon</div>
              </li> -->
            </ul>
          </div>
          <div class="clearfix text-right">
            
              <input type="submit" name="submit" value="Proceed" class="btn blue proceed_services">
            
          </div>
        </form>
      </div>
  </div>
@endsection