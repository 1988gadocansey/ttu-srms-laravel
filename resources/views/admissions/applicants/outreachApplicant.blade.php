@extends('layouts.app')

 @section('style')
<style>
    input{
        text-transform: uppercase
    }
    
</style>
 <script src="{!! url('public/assets/js/jquery.min.js') !!}"></script>
 
        <script src="{!! url('public/assets/js/jquery-ui.min.js') !!}"></script>
 
 
@endsection
 
       @inject('obj', 'App\Http\Controllers\SystemController')
 
 @section('content')
   <div class="md-card-content">
       <div style="text-align: center;display: none" class="uk-alert uk-alert-success" data-uk-alert="">

    </div>



    <div style="text-align: center;display: none" class="uk-alert uk-alert-danger" data-uk-alert="">

    </div>
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
         <h3 class="heading_c uk-margin-bottom">Add Outrach Applicant here</h3>

         <div class="md-card">
             <div class="md-card-content">
                
                 <form  method="post" class="form-horizontal row-border"   id="wizard_advanced_form" data-validate="parsley" >
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                     <div class="uk-grid" data-uk-grid-margin="">
                         
                        <div class="uk-width-medium-3-10">
                            <label for="product_search_price">Applicant Name</label>
                            <input type="text" class="md-input name" name="name" required="" >
                        </div>
                       <div class="uk-width-medium-3-10">
                           <label for="product_search_price">Phone N<u>o</u></label>
                           <input type="text" id="phone" name="phone" class="md-input" data-parsley-type="digits" minlength="10"  required=""  maxlength="10" v  pattern='^[0-9]{10}$'  v-model="gphone"  v-form-ctrl><span class="md-input-bar"></span>               
                                                           </div>
                        <div cclass="uk-width-medium-3-10">
                            <label for="product_search_price">Gender</label>
                            <p></p>
                               {!!   Form::select('gender',array("Male"=>"Male",'Female'=>"Female"),old('gender',''),array('placeholder'=>'Select gender',"required"=>"required","class"=>"md-input","v-model"=>"gender","v-form-ctrl"=>"","v-select"=>"gender"))  !!}
                                        
                        </div>
                          <div cclass="uk-width-medium-3-10">
                            <label for="product_search_price">Applicant Type</label>
                            <p></p>
                               {!!   Form::select('type',array('REGULAR'=>'Regular Applicants','MATURE' => 'Mature Applicants','CONDITIONAL' => 'Conditional Applicants','PROVISIONAL' => 'Provisional Applicants'),old('gender',''),array('placeholder'=>'Select applicant type',"required"=>"required","class"=>"md-input","v-model"=>"type","v-form-ctrl"=>"","v-select"=>"type"))  !!}
                                        
                        </div>
                          <div class="parsley-row" >
                                    <div class="uk-input-group">

                                        <label for="">Programme:</label>     
                                        <div class="md-input-wrapper md-input-filled">
                                             {!!   Form::select('programme',$programme ,array("required"=>"required","class"=>"md-input","id"=>"programme","v-model"=>"programme","v-form-ctrl"=>"","v-select"=>"programme")   )  !!}
                                             
                                        </div>    
                                       </div>
                                </div>
                         <div class="uk-modal" id="new_task">
    <div class="uk-modal-dialog">
        <div class="uk-modal-header">
            <h4 class="uk-modal-title">Send sms  here</h4>
        </div>
        

            <textarea cols="30" rows="4" name="message"class="md-input"  ></textarea>


            <div class="uk-modal-footer uk-text-right">
                <button type="button" class="md-btn md-btn-flat uk-modal-close md-btn-wave">Close</button>
            </div>
       
    </div>
</div>
                        <!-- <a  href="#new_task" data-uk-modal="{ center:true }"> <i title="click to send sms to students"   class="material-icons md-36 uk-text-success"   >phonelink_ring</i></a>-->

                         <button type="submit" class="md-btn md-btn-flat md-btn-flat-primary md-btn-wave gads"  id="snippet_new_save">Save data</button>    
                    
                    </div>
                </div>
                 
                 
                 
                 </form>

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
  <script>
                    $(document).ready(function(){
            $('.gad').on('click', function(e){


            var name = $('.name').val();
                   
                    UIkit.modal.confirm("Are you sure every data is accurate?? "
                            , function(){
                            modal = UIkit.modal.blockUI("<div class='uk-text-center'>Saving and Admitting " + name + "<br/><img class='uk-thumbnail uk-margin-top' src='{!! url('public/assets/img/spinners/spinner.gif')  !!}' /></div>");
                                    //setTimeout(function(){ modal.hide() }, 500) })()            
                                    $.ajax({
                                     
                                            type: "POST",
                                            url:"{{url('/outreach/add')}}",
                                            data: $('#wizard_advanced_form').serialize(), //your form data to post goes 
                                            dataType: "json",
                                    }).done(function(data){
                //  var objData = jQuery.parseJSON(data);
                modal.hide();
                        //                                    
                        //                                     UIkit.modal.alert("Action completed successfully");

                        //alert(data.status + data.data);
                        if (data.status == 'success'){
                $(".uk-alert-success").show();
                        $(".uk-alert-success").text(data.status + " " + data.message);
                        $(".uk-alert-success").fadeOut(4000);
                        // window.location.href="{{url('/workers')}}";
                }
                else{
                $(".uk-alert-danger").show();
                        $(".uk-alert-danger").text(data.status + " " + data.message);
                        $(".uk-alert-danger").fadeOut(4000);
                }


                });
                            }
                    );
            });
            
             
            });</script>

 
 
@endsection