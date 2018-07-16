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
 <div class="uk-modal" id="new_task">
     <div class="uk-modal-dialog uk-modal-dialog-large">
         <div class="uk-modal-header">
             <h4 class="uk-modal-title">Create Staff Account</h4>
         </div>
         <form action="power_users" method="POST">
             <input type="hidden" name="_token" value="{!! csrf_token() !!}">
         <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-medium-1-2">
                            <div class="uk-form-row">
                                <div class="uk-grid">
                                    <div class="uk-width-medium-1-2">
                                        <label>Staff ID</label>

                                       <input type="number" class="md-input md-input-success label-fixed" required="" name="staffID"/>

                                    </div>
                                    <div class="uk-width-medium-1-2">
                                        <label>Display name</label>
                                        <input type="text" name="name"  required=""class="md-input label-fixed md-input-success " />
                                    </div>
                                </div>
                            </div>
                            <div class="uk-form-row">
                                <label>Email(username)</label>
                                <input type="email" required="" name="email" class="md-input md-input label-fixed md-input-success"  />
                            </div>
                           @if( @\Auth::user()->department=='top' )
                            <div class="uk-form-row">
                                <label>Role</label>
                                <p></p>
                               {!!  Form::select('role', array('Accountant'=>'Accountant','Lecturer'=>'Lecturer','HOD'=>'HOD','Support'=>'Department Registrars','Registrar'=>'School Registrar','top'=>'Senior Officer'), null, ['placeholder' => 'select role','class'=>'md-input','required'=>''], old("","")); !!}

                            </div>
                           @else
                           <div class="uk-form-row">
                                <label>Role</label>
                                <p></p>
                               {!!  Form::select('role', array('Lecturer'=>'Lecturer','HOD'=>'HOD','Support'=>'Department Registrars'), null, ['placeholder' => 'select role','class'=>'md-input','required'=>''], old("","")); !!}

                            </div>
                           @endif
                           
                            <div class="uk-form-row">
                                <label>Phone</label>
                                <input type="number" class="md-input label-fixed md-input-success " minlength="10"  required="required"   maxlength="10"   pattern='^[0-9]{10}$'  name="phone"/>
                            </div>
                        </div>
                        <div class="uk-width-medium-1-2">
                            <div class="uk-form-row">
                                <label>Department/school</label>
                                <p></p>
                               {!! Form::select('department', 
                            (['' => 'Select department or school'] +$obj->getDepartmentList()+$obj->getSchoolList()  ), 
                            old("",""),
                            ['class' => 'md-input','id'=>"parent"] )  !!}

                            </div>
                            <div class="uk-form-row">
                                <label>Password</label>
                                <input type="password" class="md-input label-fixed md-input-success" required="" name="password"/>
                
                            </div>
                            <div class="uk-form-row">
                                <label>Confirm Password</label>
                               <input type="password" class="md-input label-fixed md-input-success " required=""  name="confirm"/>
                            </div>
                        </div>
                    </div>
                  <div class="uk-modal-footer uk-text-center" style="margin-left: 15px">
                    <button type="submit" class="md-btn md-btn-flat md-btn-flat-success md-btn-wave" id="snippet_new_save">Create Account</button>    
                    <button type="button" class="md-btn md-btn-flat uk-modal-close md-btn-wave">Close</button>
                </div>
         </form>
                </div>
     </div>
 </div> 
 <h4 class="heading_c uk-margin-bottom">Users Accounts</h4>
 
 <div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content">
     <div class="uk-overflow-container">
         <table class="uk-table uk-table-striped uk-table-align-vertical uk-table-nowrap " id="gad"> 
             <thead>
                 <tr>
                <th>N<u>O</u></th>
                <th>Staff No</th>
                 <th>Photo</th>
                  <th>Name</th>
                  <th>Username</th>
                 <th>Department</th>
                <th>Role</th>
               
                  
                </tr>
             </thead>

         </table>
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