<!-- Header -->
@include('admin.header')
<!-- Sidebar -->
@include('admin.sidebar')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Dashboard
      <small>Control panel</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{ route('getIndex') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Dashboard</li>
    </ol>
  </section>

  <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- left column -->
        <div class="col-md-6">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">{{ $box_title }}</h3>
            </div>
            @if ($errors->any())
                <div class="alert alert-danger">
                    @if($errors->has('email'))
                      <p>{{trans('messages.alert_email_wrong')}}</p>
                    @endif
                </div>
            @endif

            @if ( Session::get('message') != '' )
                <div class='alert alert-danger'>
                  {{ Session::get('message') }}
                </div>  
            @endif 
            <!-- /.box-header -->
            <!-- form start -->
            <form method="post" action="{{ route('saveProfile') }}" enctype="multipart/form-data">
              <input type="hidden" name="_token" value="{{ csrf_token() }}" />
              <div class="box-body">
                <div class="form-group">
                  <label for="exampleInputName1">Name</label>
                  <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="{{ $userDetails->name }}" required>
                </div>
                <div class="form-group">
                  <label for="exampleInputEmail1">Email address</label>
                  <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" value="{{ $userDetails->email }}" required>
                </div>
                <div class="form-group">
                  <label for="exampleInputPassword1">Password</label>
                  <input type="password" class="form-control" id="password" name="password" placeholder="Password" value="">
                  <p class="help-block">Please leave empty if not change</p>
                </div>
                <div class="form-group">
                  <label for="exampleInputFile">Photo</label>
                  <input type="file" id="photo" name="photo">
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </form>
          </div>
          <!-- /.box -->
        </div>
      </div>
    </section> <!-- Main Content End -->
</div><!-- Content Wrapper -->

<!-- Footer -->
@include('admin.footer')