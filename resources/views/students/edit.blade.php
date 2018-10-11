@extends('layouts.app')


@section('style')
<style>
    
    
</style>
  
@endsection
@section('content')
<h6 class="heading_b uk-margin-bottom">Update student data</h6>
<div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content" style="">

             
            <p class="uk-text-danger uk-text-center">
              <p class="uk-text-danger uk-text-center"><blinks>Contact 0246091283 / 0243348522 for any assistance</blinks></p>
        <div class=" " style="float: right;">
                    <div id=" " style="margin-left:0px" class=" ">
                        <div  class="fileinput fileinput-new" data-provides="fileinput" align="center">
                            <div class="fileinput-new thumbnail" style="width: 200px; height: 186px;">
                                @inject('obj', 'App\Http\Controllers\SystemController')

                                <img class="" style="width:180px;height: auto"  <?php $pic = $data->INDEXNO;
echo $obj->picture("{!! url(\"public/albums/students/$pic.jpg\") !!}", 90) ?>   src='{{url("public/albums/students/$pic.JPG")}}' onerror="this.onerror=function my(){return this.src='{{url("public/albums/students/USER.JPG")}}';};this.src='{{url("public/albums/students/$pic.jpg")}}';"    /></a> 

                            </div>
                            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;">
                            </div>




                        </div>
                    </div>
        </div>   &nbsp;
        <form  novalidate id="wizard_advanced_form" class="uk-form-stacked"   action="" method="post" accept-charset="utf-8"  name="updateForm"  v-form>
            <input type="hidden" name="id" value="{{$data->ID}}">
                {!!  csrf_field() !!}
                <div data-uk-observe="" id="wizard_advanced" role="application" class="wizard clearfix">
                    <div class="steps clearfix">
                        <ul role="tablist">
                            <li role="tab" class="fill_form_header first current" aria-disabled="false" aria-selected="true" v-bind:class="{ 'error' : !in_payment_section}">
                                <a aria-controls="wizard_advanced-p-0" href="#wizard_advanced-h-0" id="wizard_advanced-t-0">
                                    <span class="current-info audible">current step: </span><span class="number">1</span> <span class="title">Biodata and Academics</span>
                                </a>
                            </li>
                            <li role="tab" class="payment_header disabled" aria-disabled="true"   v-bind:class="{ 'error' : in_payment_section}" >
                                <a aria-controls="wizard_advanced-p-1" href="#wizard_advanced-h-1" id="wizard_advanced-t-1">
                                    <span class="number">2</span> <span class="title">Guardian Info and Others</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class=" clearfix " style="box-sizing: border-box;display: block;padding:15px!important;position: relative;">

                        <!-- first section -->
                        {{-- <h3 id="wizard_advanced-h-0" tabindex="-1" class="title current">Fill Form</h3> --}}
                        

                            <div data-uk-grid-margin="" class="uk-grid uk-grid-width-medium-1-4 uk-grid-width-large-1-4">

                                @if( @\Auth::user()->role=="Support")
                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">First Name :</label><input type="text" id="fname" name="fname" class="md-input" required="required"  readonly="" disabled="" class="md-input uk-text-bold uk-text-primary"   v-model="fname"  v-form-ctrl><span class="md-input-bar"></span></div>                
                                        <p  class=" uk-text-danger uk-text-small  "   v-if="updateForm.fname.$error.required">Please enter your first name</p>                                      
                                    </div>
                                </div>

                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Last Name :</label><input type="text" id="surname" name="surname" class="md-input" readonly="" disabled="" class="md-input uk-text-bold uk-text-primary"  required="required"       v-model="surname"  v-form-ctrl><span class="md-input-bar"></span></div>                
                                        <p  class=" uk-text-danger uk-text-small  "   v-if="updateForm.surname.$error.required">Please enter your surname</p>                                      
                                    </div>
                                </div>

                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_skype">Other Names :</label><input type="text" id="oname" name="othernames" v-form-ctrl  class="md-input"  readonly="" disabled="" class="md-input uk-text-bold uk-text-primary"  v-model="othernames"      /><span class="md-input-bar"></span></div>         

                                    </div>
                                </div>
                                
                                @else
                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">First Name :</label><input type="text" id="fname" name="fname" class="md-input"  required="required"     v-model="fname"  v-form-ctrl><span class="md-input-bar"></span></div>                
                                        <p  class=" uk-text-danger uk-text-small  "   v-if="updateForm.fname.$error.required">Please enter your first name</p>                                      
                                    </div>
                                </div>

                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Last Name :</label><input type="text" id="surname" name="surname" class="md-input" required="required"       v-model="surname"  v-form-ctrl><span class="md-input-bar"></span></div>                
                                                                             
                                    </div>
                                </div>

                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_skype">Other Names :</label><input type="text" id="oname" name="othernames" v-form-ctrl  class="md-input"    v-model="othernames"      /><span class="md-input-bar"></span></div>         

                                    </div>
                                </div>
                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Title :</label><input type="text" id="title" name="title" class="md-input" readonly="" disabled="" class="md-input uk-text-bold uk-text-primary"  required="required"       v-model="title"  v-form-ctrl>     
                                        
                                            <span class="md-input-bar"></span>
                                        </div>                                       
                                    </div>
                                </div>

                            </div>

                             <div data-uk-grid-margin="" class="uk-grid uk-grid-width-medium-1-4 uk-grid-width-large-1-4">


                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Gender :</label><input type="text" id="gender" name="gender" class="md-input" readonly="" disabled="" class="md-input uk-text-bold uk-text-primary"  required="required"       v-model="gender"  v-form-ctrl>     
                                        
                                            <span class="md-input-bar"></span>
                                        </div> 

                                                                                
                                    </div>
                                </div>
                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Marital Status :</label><input type="text" id="marital_status" name="marital_status" class="md-input" readonly="" disabled="" class="md-input uk-text-bold uk-text-primary"  required="required"       v-model="marital_status"  v-form-ctrl>     
                                        
                                            <span class="md-input-bar"></span>
                                        </div> 

                                                                              
                                    </div>
                                </div>
                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Phone N<u>o</u> :</label><input type="text" id="phone" name="phone" class="md-input" data-parsley-type="digits" minlength="10"  required="required"   maxlength="10"   pattern='^[0-9]{10}$'  v-model="phone"  v-form-ctrl><span class="md-input-bar"></span></div>                
                                        <p  class=" uk-text-danger uk-text-small  "   v-if="updateForm.phone.$invalid">Please enter a valid phone number of 10 digits</p>                                      
                                    </div>
                                </div>



                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Date of Birth :</label><input type="text" id="dob" name="dob" class="md-input" readonly="" disabled="" class="md-input uk-text-bold uk-text-primary"  required="required"       v-model="dob"  v-form-ctrl>     
                                        
                                            <span class="md-input-bar"></span>
                                        </div> 

                                                                                   
                                    </div>
                                </div>

                            </div>

               <div data-uk-grid-margin="" class="uk-grid uk-grid-width-medium-1-4 uk-grid-width-large-1-4">
                                 <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_twitter">INDEX N<u>0</u> :</label><input type="text" name="indexno" class="md-input"  v-model="indexno"  v-form-ctrl   ><span class="md-input-bar"></span></div>
                                             </div>
                                </div>




                                <div class="parsley-row"  >
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Religious Denomination :</label><input type="text" id="religion" name="religion" class="md-input" readonly="" disabled="" class="md-input uk-text-bold uk-text-primary"  required="required"       v-model="religion"  v-form-ctrl>     
                                        
                                            <span class="md-input-bar"></span>
                                        </div> 


                                                                                
                                  </div>
                              </div>



                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_skype">Home Address :</label><input type="text" id="address" name="address"  required=""v-form-ctrl  class="md-input" readonly="" disabled=""  v-model="address"      /><span class="md-input-bar"></span></div>         
                                         <p class="uk-text-danger uk-text-small " v-if="updateForm.address.$error.required" >Home Address is required</p>                                           
                              
                                    </div>
                                </div>
                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Contact Address
                                                :</label><input type="text" id="contact" name="contact" class="md-input"   required="required"  readonly="" disabled=""  v-model="contact"  v-form-ctrl><span class="md-input-bar"></span></div>                
                                        <p  class=" uk-text-danger uk-text-small  "   v-if="updateForm.contact.$error.required">Contact Address is required</p>                                      
                                    </div>
                                </div>



                            </div>
                            <div data-uk-grid-margin="" class="uk-grid uk-grid-width-medium-1-4 uk-grid-width-large-1-4">


                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Hometown :</label><input type="text" id="hometown" name="hometown" class="md-input"   required="required"  readonly="" disabled=""    v-model="hometown"  v-form-ctrl><span class="md-input-bar"></span></div>                
                                        <p  class=" uk-text-danger uk-text-small  "   v-if="updateForm.hometown.$error.required">Hometown is required</p>                                      
                                    </div>
                                </div>

                               <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Hometown Region :</label><input type="text" id="region" name="region" class="md-input" readonly="" disabled="" class="md-input uk-text-bold uk-text-primary"  required="required"  readonly="" disabled=""     v-model="region"  v-form-ctrl>     
                                        
                                            <span class="md-input-bar"></span>
                                        </div> 

                                                                                
                                    </div>
                                </div>

                                 <div class="parsley-row">
                                    <div class="uk-input-group">
                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Nationality :</label><input type="text" id="nationality" name="nationality" class="md-input" readonly="" disabled="" class="md-input uk-text-bold uk-text-primary"  required="required"  readonly="" disabled=""     v-model="nationality"  v-form-ctrl>     
                                        
                                            <span class="md-input-bar"></span>
                                        </div> 
                                        
                                    </div>
                                </div>

                                  <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Student Category :</label><input type="text" id="category" name="category" class="md-input" readonly="" disabled="" class="md-input uk-text-bold uk-text-primary"  required="required"       v-model="category"  v-form-ctrl>     
                                        
                                            <span class="md-input-bar"></span>
                                        </div>

                                                                                
                                    </div>
                                </div>


                            </div>
                              <div data-uk-grid-margin="" class="uk-grid uk-grid-width-medium-1-4 uk-grid-width-large-1-4">


                              <div class="parsley-row">
                                    <div class="uk-input-group">
                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Level/Year :</label><input type="text" id="year" name="year" class="md-input" readonly="" disabled="" class="md-input uk-text-bold uk-text-primary"  required="required"       v-model="year"  v-form-ctrl>     
                                        
                                            <span class="md-input-bar"></span>
                                        </div>

                                                                                
                                    </div>
                                </div>

                               
                                   
                                 <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <label for="">Residential Status :</label>     
                                        <div class="md-input-wrapper md-input-filled">
                                            {!!   Form::select('type',array("1"=>"Resident",'0'=>"Non Resident"),old('type',$data->STUDENT_TYPE),array('placeholder'=>'Select residential status', "style"=>"","class"=>"md-input","v-model"=>"type","v-form-ctrl"=>"","v-select"=>"type"))  !!}
                                            <span class="md-input-bar"></span>
                                        </div>    
                                         <p class="uk-text-danger uk-text-small"  v-if="updateForm.type.$error.required">Residential Status is required</p>                                        
                                 
                                    </div>
                                </div>
                                <div class="parsley-row">
                                    <div class="uk-input-group">
                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Hall :</label><input type="text" id="halls" name="halls" class="md-input" class="md-input uk-text-bold uk-text-primary"         v-model="halls"  v-form-ctrl>     
                                        
                                            <span class="md-input-bar"></span>
                                        </div>

                                       
                                  </div>
                              </div>
                              <div class="parsley-row">


                                    <div class="uk-input-group">
                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Hostel :</label><input type="text" id="hostel" name="hostel" class="md-input" class="md-input uk-text-bold uk-text-primary"        v-model="hostel"  v-form-ctrl>     
                                        
                                            <span class="md-input-bar"></span>
                                        </div>

                                                                                
                                    </div>

                                    
                              </div>
                                </div>
                                 <div data-uk-grid-margin="" class="uk-grid uk-grid-width-medium-1-4 uk-grid-width-large-1-4">  
                                  
                                
                               <div class="parsley-row" style="margin-left:10px">
                                    <div class="uk-input-group">

                                        <label for="">Programme:</label>     
                                        <div class="md-input-wrapper md-input-filled">
                                             {!!   Form::select('programme', (['' => 'Select programs'] + $programme ),old('programme',$programme),array("required"=>"required","class"=>"md-input","id"=>"programme","v-model"=>"programme","v-form-ctrl"=>"","v-select"=>"programme")   )  !!}
                                            <span class="md-input-bar"></span>
                                        </div>    
                                        <p class="uk-text-danger uk-text-small"  v-if="updateForm.programme.$error.required">programme is required</p>                                        
                                    </div>
                                </div>

                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Student Status :</label><input type="text" id="status" name="status" class="md-input" readonly="" disabled="" class="md-input uk-text-bold uk-text-primary"  required="required"       v-model="status"  v-form-ctrl>     
                                        
                                            <span class="md-input-bar"></span>
                                        </div>

                                        
                                         </div>
                                </div>
                            </div>
                            
                            


      

      <!-- second section -->
      {{-- <h3 id="payment-heading-1" tabindex="-1" class="title">Payment</h3> --}}
      
        <h2 class="heading_a">
         
     <div data-uk-grid-margin="" class="uk-grid uk-grid-width-medium-1-4 uk-grid-width-large-1-4">


                                <div class="parsley-row">
                                    <div class="uk-input-group">
                                        
                                         <div class="md-input-wrapper md-input-filled"><label for="wizard_email">Email :</label><input type="email" id="email" name="email" class="md-input"   v-model="email"v-form-ctrl  ><span class="md-input-bar"></span></div>                                            
                                         <p class="uk-text-danger uk-text-small "  v-if="updateForm.email.$invalid"  >Please enter a valid email address</p>
                                    
                                    </div>
                                </div>

                               
                                  <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <label for="">Disability:</label>     
                                        <div class="md-input-wrapper md-input-filled">
                                            {!!   Form::select('disable',array('Disabled'=>'Disabled','None' => 'None'),old('year',''),array('placeholder'=>'Select response',"class"=>"md-input","v-model"=>"disable","v-form-ctrl"=>"","v-select"=>"disable"))  !!}
                                            <span class="md-input-bar"></span>
                                        </div>    
                                        </div>
                                   </div>

                                <div class="parsley-row" v-if ="disable=='Disabled'">
                                    <div class="uk-input-group">

                                         
                                       <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Disabilty Name :</label><input type="text" id="disabilty" name="disabilty" class="md-input"   required="required"      v-model="disabilty"  v-form-ctrl><span class="md-input-bar"></span></div>                
                                        

                                       </div>
                              </div>
                               <div class="parsley-row">
                                    <div class="uk-input-group">
                                        
                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_email">NHIS N<u>O</u> :</label><input type="text" id="" name="nhis" class="md-input"   v-model="nhis"v-form-ctrl  ><span class="md-input-bar"></span></div>                                            
                                        
                                    </div>
                                </div>

                            </div>

              <div data-uk-grid-margin="" class="uk-grid uk-grid-width-medium-1-4 uk-grid-width-large-1-4">


                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Guardian Name :</label><input type="text" id="gname" name="gname" class="md-input"      v-model="gname"  v-form-ctrl><span class="md-input-bar"></span></div>                
                                       
                                    </div>
                                </div>

                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Guardian Phone N<u>o</u> :</label><input type="text" id="gphone" name="gphone" class="md-input" data-parsley-type="digits" minlength="10"     maxlength="10" v  pattern='^[0-9]{10}$'  v-model="gphone"  v-form-ctrl><span class="md-input-bar"></span></div>                
                                        <p  class=" uk-text-danger uk-text-small  "   v-if="updateForm.gphone.$invalid">Please enter a valid phone number of 10 digits</p>                                      
                                    </div>
                                </div>

                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_skype">Guardian Address :</label><input type="text" id="onamesk" name="gaddress" v-form-ctrl  class="md-input"    v-model="gaddress"      /><span class="md-input-bar"></span></div>         

                                    </div>
                                </div>

                                 <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_skype">Guardian Occupation :</label><input type="text" id="onames" name="goccupation" v-form-ctrl  class="md-input"    v-model="goccupation"      /><span class="md-input-bar"></span></div>         

                                    </div>
                                </div>
                   

                            </div>
            @endif


</div>
<div class="actions clearfix "  >
    {{--
    <ul aria-label="Pagination" role="menu">
         
        <li class="button_finish " >
            <input class="md-btn md-btn-primary uk-margin-small-top" type="submit" name="submit_order"  value="Submit"   v-on:click="submit_form"  />
        </li>
    </ul>
    --}}
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

<script>


//code for ensuring vuejs can work with select2 select boxes
Vue.directive('select', {
  twoWay: true,
  priority: 1000,
  params: [ 'options'],
  bind: function () {
    var self = this
    $(this.el)
      .select2({
        data: this.params.options,
         width: "resolve"
      })
      .on('change', function () {
        self.vm.$set(this.name,this.value)
        Vue.set(self.vm.$data,this.name,this.value)
      })
  },
  update: function (newValue,oldValue) {
    $(this.el).val(newValue).trigger('change')
  },
  unbind: function () {
    $(this.el).off().select2('destroy')
  }
})


var vm = new Vue({
  el: "body",
  ready : function() {
  },
 data : {
  fname : "{{  $data->FIRSTNAME }}",
  surname : "{{ $data->SURNAME }}",
  othernames : "{{ $data->OTHERNAMES }}",
  gender : "{{ $data->SEX }}",
  marital_status: "{{  $data->MARITAL_STATUS }}",
  address: "{{  $data->ADDRESS }}",
  contact: "{{  $data->RESIDENTIAL_ADDRESS }}",
   phone : "{{$data->TELEPHONENO }}",
    title : "{{  $data->TITLE }}",
    nhis : "{{  $data->NHIS }}",
    indexno : "{{  $data->INDEXNO }}",
    email : "{{  $data->EMAIL }}",
    region : "{{  $data->REGION }}",
    religion : "{{  $data->RELIGION }}",
    dob : "{{  $data->DATEOFBIRTH }}",
status: "{{  $data->STATUS }}",
     hometown : "{{  $data->HOMETOWN }}",
    programme : "{{  $data->PROGRAMMECODE }}",
    nationality : "{{  $data->COUNTRY }}",
    disability : "{{  $data->DISABILITY }}",
    gname : "{{  $data->GUARDIAN_NAME }}",
    gaddress: "{{  $data->GUARDIAN_ADDRESS }}",
    gphone : "{{  $data->GUARDIAN_PHONE }}",
    goccupation : "{{  $data->GUARDIAN_OCCUPATION }}",
    hostel : "{{  $data->HOSTEL }}",
    type : "{{  $data->STUDENT_TYPE }}",
    category : "{{  $data->TYPE }}",
    halls : "{{  $data->HALL }}",
    year : "{{  $data->YEAR }}",

    
 options: [      
    ],
    in_payment_section : false,
  },
  methods : {
    go_to_payment_section : function (event){
    UIkit.modal.confirm(vm.$els.confirm_modal.innerHTML, function(){
        
      vm.$data.in_payment_section=true
})

    },
    submit_form : function(){
      return (function(modal){ modal = UIkit.modal.blockUI("<div class='uk-text-center'>Saving Data<br/><img class='uk-thumbnail uk-margin-top' src='{!! url('assets/img/spinners/spinner_success.gif')  !!}' /></div>"); setTimeout(function(){ modal.hide() }, 50000) })();
    },
        
    go_to_fill_form_section : function (event){    
      vm.$data.in_payment_section=false
    }
  }
})

</script>
@endsection