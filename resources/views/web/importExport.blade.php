@extends('layouts.default')

@section('title', 'Memebership')

@section('content')
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<a class="navbar-brand" href="#">Import - Export in Excel and CSV</a>
			</div>
		</div>
	</nav>
	<div class="container">
		<form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 10px;" action="{{ URL::to('importExcel') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
			{{ csrf_field() }}
			<input type="file" name="import_file" /><br>
			<button class="btn btn-primary">Import File</button>
		</form>
	</div>
@endsection