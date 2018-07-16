@extends('layouts.app')

 
@section('style')
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" >
@endsection
 @section('content')
   <div class="md-card-content">
@if(Session::has('success'))
            <div style="text-align: center" class="uk-alert uk-alert-success" data-uk-alert="">
                {!! Session::get('success') !!}
            </div>
 @endif
 
    @if(Session::has('error'))
            <div style="text-align: center" class="uk-alert uk-alert-danger" data-uk-alert="">
                {!! Session::get('error') !!}
            </div>
 @endif
  
  </div>
  
 <h4 class="heading_c uk-margin-bottom">Users Accounts</h4>
 
 <div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content">
     <div class="uk-overflow-container">
       <div class="container">

		<a href="{{ URL::to('downloadExcel/xls') }}"><button class="btn btn-success">Download Excel xls</button></a>

		<a href="{{ URL::to('downloadExcel/xlsx') }}"><button class="btn btn-success">Download Excel xlsx</button></a>

		<a href="{{ URL::to('downloadExcel/csv') }}"><button class="btn btn-success">Download CSV</button></a>

		<form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 10px;" action="{{ URL::to('importExcel') }}" class="form-horizontal" method="post" enctype="multipart/form-data">

			<input type="file" name="import_file" />

			<button class="btn btn-primary">Import File</button>

		</form>

	</div>
     </div>
<div class="md-fab-wrapper">
    <a class="md-fab md-fab-small md-fab-accent md-fab-wave" title="create new account" href="#new_task" data-uk-modal="{ center:true }">
            <i class="material-icons md-18">&#xE145;</i>
        </a>
    </div>
 </div>
    </div>
 </div>
@endsection
@section('js')
  
 
 
@endsection