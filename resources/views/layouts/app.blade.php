<!DOCTYPE html>
<html>
<head>
	<title>{{ APP_NAME }} | @yield('title')</title>
</head>
<body>
	@section('sidebar')

	@show

	<div class="container">
		@yield('content')
	</div>
</body>
</html>