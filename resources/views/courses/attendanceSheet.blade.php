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
  
  
 </div>
 <div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content">
            
                <form  method="POST" accept-charset="utf-8"  >
                   {!!  csrf_field()  !!}
                    <div class="uk-grid" data-uk-grid-margin="">

                         
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                     {!! Form::select('level', 
                                (['' => 'All levels'] +$level ), 
                                  old("level",""),
                                    ['class' => 'md-input parent','id'=>"parent",'placeholder'=>'select level'] )  !!}
                      
                            </div>
                        </div>
                       
                                   

                        
                          <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                 
                                           {!! Form::select('year', 
                                ($year ), 
                                  old("year",""),
                                    ['class' => 'md-input parent','required'=>"required",'placeholder'=>'select year'] )  !!}
                          
                            </div>
                        </div>
                         
                         
                        
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                
                               {!! Form::select('course', 
                                (['' => 'select programme'] +$courses ), 
                                  old("course",""),
                                    ['class' => 'md-input parent','required'=>"required",'placeholder'=>'select programme'] )  !!}
                          
                            </div>
                        </div>
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                
                               <button class="md-btn  md-btn-small md-btn-success uk-margin-small-top" type="submit">Print</button> 
                           
                          
                            </div>
                        </div>
                        
                    
                    </div> 
                          
                   
                </form> 
        </div>
    </div>
 </div>
  
 
 
  
@endsection
@section('js')
  
 <script src="{!! url('public/assets/js/select2.full.min.js') !!}"></script>
<script>
$(document).ready(function(){
  $('select').select2({ width: "resolve" });

  
});


</script>
 <!--  notifications functions -->
    <script src="public/assets/js/components_notifications.min.js"></script>
@endsection