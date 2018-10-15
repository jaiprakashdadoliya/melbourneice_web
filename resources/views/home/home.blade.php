@extends('layouts.default')

@section('title', 'Home')

@section('content')
  <nav class="navbar navbar-default">
      @include('includes.navigation')
  </nav>
  <div class="container">    
    <h1 class="title">Welcome <span>{{ $userName = UserHelper::get_profile_name() }}!</span></h1>
    <div class="inner-content home-page">
      <div class="row form-group">
        @if(Session::get('success') != '')
          <div class="alert alert-success alert-dismissable">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              {{ Session::get('success') }}
          </div>
        @endif
        @if(Session::get('message') != '')
          <div class="alert alert-danger alert-dismissable">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{ Session::get('message') }}
          </div>
        @endif
      </div>
      <div class="row form-group">        
        <div class="col-md-2 col-sm-3">Notification :</div>
        <div class="col-md-10 col-sm-9">
          @if(!empty($profileDetail))
            Your <span><a href="{{ route('profile') }}">profile </a></span>completed successfully.
          @else
            Complete your <span><a href="{{ route('profile') }}">profile</a></span>
          @endif
        </div>
      </div>
      <div class="row form-group">
        <div class="col-md-2 col-sm-3">News Centre :</div>
        <div class="col-md-10 col-sm-9"><span><a href="{{ route('profile') }}">Membership</a></span> out now!</div>
      </div>
    </div>

    <div class="inner-content">
      <h1 class="home_inner_title">WELCOME</h1>

      <p>The Melbourne Ice Community Portal is a revolution in Member and Fan engagement. The portal has been developed specifically to assist all our loyal members, fans and partners to connect and manage their interactions with the club and our community.</p>

      <p>Version 1 focuses on streamlining membership renewal and take up. Throughout 2018 we will be adding more features and services to the portal, creating a truly interactive community for all things Melbourne Ice.</p>

      <p>Thanks for being part of our family and we look forward to creating memories together.</p>

      <h1 class="home_inner_title">2018 MEMBERSHIP EXPLAINED</h1>

      <p>Melbourne Ice is arguably the most successful Australian Ice Hockey League (AIHL) club and 2017 will forever be remembered as a record setting year on and off the ice. A year that a family of many united to be the best they can; players, members, volunteers, partners, sponsors and fans to become National Champions together.</p>

      <p>We are very pleased to announce our 2018 memberships are now open to renew or join. For the first time, we have now aligned our memberships to the stadium seating and ticketing system to further improve access and flow in and around the stadium.</p>

      <p>Our aim is to provide all our members with a warm and welcoming family focused experience, on top of the best possible choice when it comes to enjoying game day.</p>

      <p>Membership has been simplified into 3 categories and naturally we have rates for Under 3, Juniors, Concession and Adults.</p>

      <p>Children under 3 years of age attend for free when accompanied by an adult. If that adult has a seated membership, the child can sit on their lap in the seated section.</p>

      <p>All 2017 members who have a reserved seat have had their seat(s) reserved automatically in their profile. At the point of renewal existing reserve seat holders will have the option to keep or change their seat(s). All 2017 reserved seats will remain reserved till the 1st of March 2018, when all unclaimed seats will be released for other members to choose.</p>

      <p>Welcome to our community, join the Champions and let’s create memories together.</p>

      <h1 class="home_inner_title">MEMBERSHIP CATEGORIES</h1>
      <p><b class="black">Under 3</b><br>
      All children under the age of 3 as of 31/12/2018.</p>

      <p><b class="black">Junior (3-14 years of age)</b><br>
      All children between the age of 3 and 14 as of 31/12/2018.</p>

      <p><b class="black">Concession and youth (15-18 years of age)</b><br>
      Concession memberships apply to Australian Pensioner Concession Card, full-time Student Card, Healthcare Card, Disability Concession, Seniors Business Discount Card holders or DVA Gold Card holders and all IHV registered players. A youth who is between the age of 15 and 18 as of 31/12/2018.</p>


      <p><b class="black">Adult</b><br>
      All members who are 18 years’ age as of the 01/01/2018.</p>
      
      <h1 class="home_inner_title">MEMBERSHIP OPTIONS</h1>
      <p><b class="black">SEATED</b><br>
      Reserved seat anywhere in the grand stands across Bays 1, 2, 3, 4 & 5.</p>

      <p><b class="black">GENERAL ADMISSION (GA)</b><br>
      General admission, is classified as standing room access only, for those who enjoy watching the game from the St Moritz Bar or along the glass.</p>

      <p><b class="black">DISTANT SUPPORTER</b><br>
      Can't make it to our games, then join the Champions and be part of our community from afar, watch our Livestream and wear your included special supporter’s jersey with pride. This membership does not include any game day ticketing.</p>

      <h1 class="home_inner_title">MEMBERSHIP PRICING</h1>
      <p>Melbourne Ice Membership pricing includes member fees, seating and extras.  Below is a matrix detailing the 2018 fee structure.</p>
      <div class="table-responsive">
        <table class="table table-striped table-bordered no-footer price-table">
          <thead>
            <tr>
              <th>CATEGORY</th>
              <th class="price-amnt">Membership</th>
              <th class="price-amnt">Ticketing</th>
              <th class="price-amnt">Total</th>
            </tr>
          </thead>
          <tbody>
          <tr>
            <td colspan="4">&nbsp;</td>
          </tr>
            <tr>
              <td colspan="4"><strong>RESERVED SEAT</strong></td>
            </tr>
            <tr>
              <td>Seated Under 3</td>
              <td class="price-amnt">$0.00</td>
              <td class="price-amnt">$0.00</td>
              <td class="price-amnt">$0.00</td>
            </tr>
            <tr>
              <td>Seated Junior (3-14yrs)</td>
              <td class="price-amnt">$29.00</td>
              <td class="price-amnt">$140.00</td>
              <td class="price-amnt">$169.00</td>
            </tr>
            <tr>
              <td>Seated Concession</td>
              <td class="price-amnt">$39.00</td>
              <td class="price-amnt">$190.00</td>
              <td class="price-amnt">$229.00</td>
            </tr>
            <tr>
              <td>Seated Adult (18yrs +)</td>
              <td class="price-amnt">$49.00</td>
              <td class="price-amnt">$250.00</td>
              <td class="price-amnt">$299.00</td>
            </tr>
            <tr>
            <td colspan="4">&nbsp;</td>
          </tr>
            <tr>
              <td colspan="4"><strong>GENERAL ADMISSION</strong> </td>
            </tr>
            <tr>
              <td>GA Under 3</td>
              <td class="price-amnt">$0.00</td>
              <td class="price-amnt">$0.00</td>
              <td class="price-amnt">$0.00</td>
            </tr>
            <tr>
              <td>GA Junior (3-14yrs)</td>
              <td class="price-amnt">$29.00</td>
              <td class="price-amnt">$80.00</td>
              <td class="price-amnt">$109.00</td>
            </tr>
            <tr>
              <td>GA Concession</td>
              <td class="price-amnt">$39.00</td>
              <td class="price-amnt">$140.00</td>
              <td class="price-amnt">$179.00</td>
            </tr>
            <tr>
              <td>GA Adult (18yrs +)</td>
              <td class="price-amnt">$49.00</td>
              <td class="price-amnt">$180.00</td>
              <td class="price-amnt">$229.00</td>
            </tr>
            <tr>
            <td colspan="4">&nbsp;</td>
          </tr>
            <tr>
              <td colspan="4"><strong>NON-TICKETED</strong></td>
            </tr>
            <tr>
              <td>Distance Supporter</td>
              <td class="price-amnt">$99.00</td>
              <td class="price-amnt">$0.00</td>
              <td class="price-amnt">$99.00</td>
            </tr>
          </tbody>
        </table>
      </div>

      
      <h1 class="home_inner_title"><i>IMPORTANT: </i></h1>
      <p><i>Membership has been successfully aligned to the stadium ticketing configuration, which has allowed us to remove all the confusion of the 'unreserved' seating membership category, providing members with more flexibility in seating and standing options and reducing the huge workload for our volunteers in setting up the stadium for game day.</i></p>
      <p><i>At the time of membership renewal or new take up, all existing and new members will select and lock in their reserved seat(s) anywhere in the grandstands across bays 1, 2, 3, 4 and 5. All of our 2017 members who had a reserved seat will have the option to keep their existing seat(s) or change to another seat that they prefer, all at the time of renewal.</i></p>

      <h1 class="home_inner_title">PAYMENT PLANS</h1>
      <p>A key feedback item was the option to pay for membership in instalments and we are pleased to announced that in 2018 you will have the option to pay in instalments or in a single lump sum. If choosing a ‘payment plan’ option in the checkout you must have a PayPal account.</p>

      <h1 class="home_inner_title">MEMBERSHIP BENEFITS</h1>

      <p>All Melbourne Ice Members pending your membership category are entitled to the following:</p>

      <ul>
        <li>Entry to all Melbourne Ice home games in the 2018 AIHL Regular season (14 games total) at O’Brien Group Arena (excludes distant supporters).</li>
        <li>Bonus game; entry to the traditional pre-season game against the Melbourne Mustangs (currently scheduled for Saturday 7th April 5PM, excludes distant supporters).</li>
        <li>Entry to all Melbourne Ice Women home games in the 2018/9 AWIHL regular season (currently projected to be 6 games in total) at O’Brien Group Arena (excludes distant supporters).</li>
        <li>Early entry via the member’s gates at all Melbourne Ice home games at O’Brien Group Arena (OBGA).</li>
        <li>Access to the after party in the St Moritz Bar.</li>
        <li>20% off merchandise purchased via the Melbourne Ice online store (excludes limited edition items and club memorabilia). A code will be sent to you once your membership has been approved and fully paid.</li>
        <li>Subscription to the ICEBLAST eNewsletter.</li>
        <li>Special offers, surprise gifts and invites to all Melbourne Ice events</li>
      </ul>
    </div>
  </div>
@endsection