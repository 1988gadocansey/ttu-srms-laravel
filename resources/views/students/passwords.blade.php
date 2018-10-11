@extends('layouts.app')

 
@section('style')
 
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
 @inject('sys', 'App\Http\Controllers\SystemController')
 
 <div class="uk-modal" id="new_task">
        <div class="uk-modal-dialog ">
            <div class="uk-modal-header">
                <h4 class="uk-modal-title">Create Student Account</h4>
            </div>
                        <form action="create_account" method="POST">
                    <input type="hidden" name="_token" value="{!! csrf_token() !!}"> 
                    
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-medium-1-2">
                            <div class="uk-form-row">
                                <div class="uk-grid" data-uk-grid-margin>
                                    <div class="uk-width-medium-1-2">
                                        <label>Index number</label>
                                        <input type="text" class="md-input md-input-success" required="" name="username"/>
                                    </div>
                                    <div class="uk-width-medium-1-2">
                                        <label>password</label>
                                        <input type="password" class="md-input md-input-success" required="" name="password"/>
                                    </div>
                                  <div class="uk-width-medium-1-2">
                             {!! Form::select('program', 
                            (['' => 'All programs'] + $programme ), 
                            old("program",""),
                            ['class' => 'md-input parent','id'=>"parent",'placeholder'=>'select program'] )  !!}
                      </div>
                     
                                </div>
                            </div>
                            
                        </div>
                        <div class="uk-width-medium-1-2">
                            <div class="uk-form-row">
                                <div class="uk-grid" data-uk-grid-margin>
                                     
                                    <div class="uk-width-medium-1-2">
                                        <label>Confirm password</label>
                                        <input type="password" class="md-input md-input-success" required=""  name="confirm"/>
                                    </div>
                                    <div class="uk-width-medium-1-2">
                                        <label>Level</label>
                                        {!!  Form::select('level', array('100NT'=>'DIPLOMA 100','200NT' => 'DIPLOMA 200NT','100H'=>'HND 100','200H' => 'HND 200', '300H' => 'HND 300','100BTT'=>'BTECH TOP UP 100','200BTT'=>'BTECH TOP UP 200','100BT'=>'BTECH (4yr) 100','200BT'=>'BTECH (4yr) 200','300BT'=>'BTECH (4yr) 300','400BT'=>'BTECH (4yr) 400','500MT'=>'MASTERS 100','600MT'=>'MASTERS 200'), null, ['placeholder' => 'select level','id'=>'parent','class'=>'md-input parent'],old("level","")); !!}

                                    </div>
                                </div>
                            </div>
                            
                        </div>
                         
                     
                     
                    
                        <div class="uk-modal-footer uk-text-center" style="margin-left: 15px">
                    <button type="submit" class="md-btn md-btn-flat md-btn-flat-success md-btn-wave" id="snippet_new_save">Create Account</button>    
                    <button type="button" class="md-btn md-btn-flat uk-modal-close md-btn-wave">Close</button>
                </div>
            </form>
        </div>
        </div></div>
 <h4 class="heading_c uk-margin-bottom">Students User Accounts</h4>
 
 <div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content">
     <div class="uk-overflow-container">
         <table class="uk-table uk-table-striped uk-table-align-vertical uk-table-nowrap " id="gad"> 
             <thead>
                 <tr>
                <th>N<u>O</u></th>
                <th>Index Number</th>
                 <th>Photo</th>
                    <th>Name</th>
                 
                <th>Level</th>
               
                <th>Password</th>
                
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
            url:  "{!! route('search_password.data') !!}"
             
        },
        columns: [
           
        
          {data: 'ID', name: 'tpoly_students.ID'},
           {data: 'INDEXNO', name: 'tpoly_students.INDEXNO'},
           
            {data: 'Photo', name: 'Photo', orderable: false, searchable: false},
            
            {data: 'NAME', name: 'tpoly_students.NAME'},
            
            {data: 'LEVEL', name: 'tpoly_students.LEVEL'},
            {data: 'real_password', name: 'tpoly_log_portal.real_password'},]
             
    });
    

    
</script>
 
@endsection