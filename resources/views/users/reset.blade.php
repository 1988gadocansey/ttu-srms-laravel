@extends('layouts.app')

 
@section('style')
       @inject('obj', 'App\Http\Controllers\SystemController')
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
 @if (count($errors) > 0)

    <div class="uk-form-row">
        <div class="uk-alert uk-alert-danger" style="background-color: red;color: white">

              <ul>
                @foreach ($errors->all() as $error)
                  <li> {{  $error  }} </li>
                @endforeach
          </ul>
    </div>
  </div>
@endif
  </div>
  
 </div> 
 
  
     <div class="uk-width-large-8-10" style="margin-left: 100px">
         <h3 class="heading_c uk-margin-bottom">Reset Account Password</h3>

         <div class="md-card">
             <div class="md-card-content">
                
                 <form action="" method="post" class="form-horizontal row-border"   id="form" data-validate="parsley" >
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                     <div class="uk-grid" data-uk-grid-margin="">
                         
                        <div class="uk-width-medium-3-10">
                            <label for="product_search_price">Old Password</label>
                            <input type="password" class="md-input" name="oldPass" required="">
                        </div>
                       <div class="uk-width-medium-3-10">
                            <label for="product_search_price">New Password</label>
                            <input type="password" class="md-input" name="password" required="" min="7">
                        </div>
                        <div cclass="uk-width-medium-3-10">
                            <label for="product_search_price">Confirm New Password</label>
                            <input type="password" class="md-input" name="confirm" required="" >
                        </div>
                        <div class="uk-width-medium-2-10 uk-text-center">
                               <button type="submit" class="md-btn md-btn-primary uk-margin-small-top"><i class=" "></i>Reset</button>
                        </div>
                    </div>
                </div>
                 
                 
                 
                 </form>

             </div>
         </div>
     
 @endsection
@section('js')
  
<script>
    
 
 var oTable = $('#gad').DataTable({
     
        
        processing: true,
        serverSide: true,
        ajax: {
            url:  "{!! route('power_users.data') !!}"
             
        },
        columns: [
           
        
          {data: 'id', name: 'users.id'},
           {data: 'staffID', name: 'tpoly_workers.staffID'},
           
            {data: 'Photo', name: 'Photo', orderable: false, searchable: false},
            
              {data: 'name', name: 'users.name'},
               {data: 'email', name: 'users.email'},
            {data: 'department', name: 'users.department'},
            {data: 'role', name: 'users.role'},]
              
    });
    

    
</script>
 
@endsection