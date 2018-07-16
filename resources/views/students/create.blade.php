@extends('layouts.app')


@section('style')
<style>
    
    
</style>
 <script src="{!! url('public/assets/js/jquery.min.js') !!}"></script>
 
        <script src="{!! url('public/assets/js/jquery-ui.min.js') !!}"></script>
 
 
@endsection
@section('content')
<h6 class="heading_b uk-margin-bottom">Add Students</h6>
<div class="uk-width-xLarge-1-10">
    <div class="md-card">
        <div class="md-card-content" style="">

             
           
             <!--        <div class=" " style="float: right;">
                    <div id=" " style="margin-left:0px" class=" ">
                        <div  class="fileinput fileinput-new" data-provides="fileinput" align="center">
                            <div class="fileinput-new thumbnail" style="width: 200px; height: 186px;">
                                @inject('obj', 'App\Http\Controllers\SystemController')

                                <img class="" style="width:180px;height: auto"  <?php //$pic = $data->INDEXNO;
//echo $obj->picture("{!! url(\"albums/students/$pic.jpg\") !!}", 90) ?>   src="<?php //echo url("albums/students/$pic.jpg") ?>" alt=" Picture of Student Here"    /></a> 

                            </div>
                            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;">
                            </div>




                        </div>
                    </div>
        </div>   &nbsp;-->
        <form  novalidate id="wizard_advanced_form" class="uk-form-stacked"   action="" method="post" accept-charset="utf-8"  name="updateForm"  v-form>

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
                        <section id="fill_form_section" role="tabpanel" aria-labelledby="fill form section" class="body step-0 current" data-step="0" aria-hidden="false"   v-bind:class="{'uk-hidden': in_payment_section} ">

                            <div data-uk-grid-margin="" class="uk-grid uk-grid-width-medium-1-4 uk-grid-width-large-1-4">


                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">First Name :</label><input type="text" id="fname" name="fname" class="md-input"   required="required"     v-model="fname"  v-form-ctrl><span class="md-input-bar"></span></div>                
                                        <p  class=" uk-text-danger uk-text-small  "   v-if="updateForm.fname.$error.required">Please enter your first name</p>                                      
                                    </div>
                                </div>

                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Last Name :</label><input type="text" id="surname" name="surname" class="md-input"   required="required"       v-model="surname"  v-form-ctrl><span class="md-input-bar"></span></div>                
                                        <p  class=" uk-text-danger uk-text-small  "   v-if="updateForm.surname.$error.required">Please enter your surname</p>                                      
                                    </div>
                                </div>

                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_skype">Other Names :</label><input type="text" id="oname" name="othernames" v-form-ctrl  class="md-input"    v-model="othernames"      /><span class="md-input-bar"></span></div>         

                                    </div>
                                </div>

                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <label for="">Title :</label>     
                                        <div class="md-input-wrapper md-input-filled">
                                            {!!   Form::select('title',array("Mr"=>"Mr","Mrs"=>"Mrs","Miss"=>"Miss"),old('title',''),array('placeholder'=>'Select title',"required"=>"required","class"=>"md-input","v-model"=>"title","v-form-ctrl"=>"","v-select"=>"title"))  !!}
                                            <span class="md-input-bar"></span>
                                        </div>    
                                        <p class="uk-text-danger uk-text-small"  v-if="updateForm.title.$error.required">Title is required</p>                                        
                                    </div>
                                </div>

                            </div>

                             <div data-uk-grid-margin="" class="uk-grid uk-grid-width-medium-1-4 uk-grid-width-large-1-4">


                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <label for="">Gender :</label>     
                                        <div class="md-input-wrapper md-input-filled">
                                            {!!   Form::select('gender',array("Male"=>"Male",'Female'=>"Female"),old('gender',''),array('placeholder'=>'Select gender',"required"=>"required","class"=>"md-input","v-model"=>"gender","v-form-ctrl"=>"","v-select"=>"gender"))  !!}
                                            <span class="md-input-bar"></span>
                                        </div>    
                                        <p class="uk-text-danger uk-text-small"  v-if="updateForm.gender.$error.required">Gender is required</p>                                        
                                    </div>
                                </div>
                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <label for="">Marital Status :</label>     
                                        <div class="md-input-wrapper md-input-filled">
                                            {!!   Form::select('marital_status',array("Married"=>"Married",'Single'=>"Single"),old('marital_status',''),array('placeholder'=>'Select marital status',"required"=>"required","class"=>"md-input","v-model"=>"marital_status","v-form-ctrl"=>"","v-select"=>"marital_status"))  !!}
                                            <span class="md-input-bar"></span>
                                        </div>    
                                        <p class="uk-text-danger uk-text-small"  v-if="updateForm.marital_status.$error.required">Marital Status is required</p>                                        
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

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_twitter">Date of Birth :</label><input type="text" name="dob" class="md-input" data-uk-datepicker="{format:'DD/MM/YYYY'}" required="required"  v-model="dob"  v-form-ctrl   ><span class="md-input-bar"></span></div>
                                        <p class="uk-text-danger uk-text-small " v-if="updateForm.dob.$error.required" >Date of birth is required</p>                                           
                                    </div>
                                </div>

                            </div>

               <div data-uk-grid-margin="" class="uk-grid uk-grid-width-medium-1-4 uk-grid-width-large-1-4">
                                 <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_twitter">INDEX N<u>0</u> :</label><input type="text" name="indexno" class="md-input"    v-model="indexno"  v-form-ctrl   ><span class="md-input-bar"></span></div>
                                             </div>
                                </div>




                                <div class="parsley-row"  >
                                    <div class="uk-input-group">

                                        <label for="">Religious Denomination :</label>     
                                        <div class="md-input-wrapper md-input-filled">
                                          {!!   Form::select('religion',$religion,old('religion',''),array("required"=>"required","class"=>"md-input","id"=>"religion","v-model"=>"religion","v-form-ctrl"=>"","style"=>"","v-select"=>"religion")   )  !!}
                                    <span class="md-input-bar"></span>
                                        </div> 

                                      <p class="uk-text-danger uk-text-small"  v-if="updateForm.religion.$error.required">Religion is required</p>                                        
                                  </div>
                              </div>



                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_skype">Home Address :</label><input type="text" id="address" name="address"  required=""v-form-ctrl  class="md-input"    v-model="address"      /><span class="md-input-bar"></span></div>         
                                         <p class="uk-text-danger uk-text-small " v-if="updateForm.address.$error.required" >Home Address is required</p>                                           
                              
                                    </div>
                                </div>
                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Contact Address
                                                :</label><input type="text" id="contact" name="contact" class="md-input"   required="required"    v-model="contact"  v-form-ctrl><span class="md-input-bar"></span></div>                
                                        <p  class=" uk-text-danger uk-text-small  "   v-if="updateForm.contact.$error.required">Contact Address is required</p>                                      
                                    </div>
                                </div>



                            </div>
                            <div data-uk-grid-margin="" class="uk-grid uk-grid-width-medium-1-4 uk-grid-width-large-1-4">


                                <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Hometown :</label><input type="text" id="hometown" name="hometown" class="md-input"   required="required"      v-model="hometown"  v-form-ctrl><span class="md-input-bar"></span></div>                
                                        <p  class=" uk-text-danger uk-text-small  "   v-if="updateForm.hometown.$error.required">Hometown is required</p>                                      
                                    </div>
                                </div>

                               <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <label for="">Hometown Region:</label>     
                                        <div class="md-input-wrapper md-input-filled">
                                            {!!   Form::select('region',$region ,array("required"=>"required","class"=>"md-input","id"=>"region","v-model"=>"region","v-form-ctrl"=>"","v-select"=>"{{old('region')}}")   )  !!}
                                            <span class="md-input-bar"></span>
                                        </div>    
                                        <p class="uk-text-danger uk-text-small"  v-if="updateForm.region.$error.required">Region is required</p>                                        
                                    </div>
                                </div>

                                 <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <label for="">Nationality:</label>     
                                        <div class="md-input-wrapper md-input-filled">
                                             {!!   Form::select('nationality',$country ,array("required"=>"required","class"=>"md-input","id"=>"nationality","v-model"=>"nationality","v-form-ctrl"=>"","v-select"=>"nationality")   )  !!}
                                            <span class="md-input-bar"></span>
                                        </div>    
                                        <p class="uk-text-danger uk-text-small"  v-if="updateForm.nationality.$error.required">Nationality is required</p>                                        
                                    </div>
                                </div>

                                  <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <label for="">Student Category :</label>     
                                        <div class="md-input-wrapper md-input-filled">
                                            {!!   Form::select('category',array("Regular"=>"Regular","Evening"=>"Evening","Weekend"=>"Weekend",'Exchange programme'=>'Exchange programme'),old('category',''),array('placeholder'=>'Select category',"required"=>"required","class"=>"md-input","v-model"=>"category","v-form-ctrl"=>"","v-select"=>"category"))  !!}
                                            <span class="md-input-bar"></span>
                                        </div>    
                                        <p class="uk-text-danger uk-text-small"  v-if="updateForm.category.$error.required">Type is required</p>                                        
                                    </div>
                                </div>


                            </div>
                              <div data-uk-grid-margin="" class="uk-grid uk-grid-width-medium-1-4 uk-grid-width-large-1-4">


                              <div class="parsley-row">
                                    <div class="uk-input-group">

                                        <label for="">Level/Year :</label>     
                                        <div class="md-input-wrapper md-input-filled">
                                            {!!   Form::select('year',$level ,array("required"=>"required","class"=>"md-input","id"=>"year","v-model"=>"year","v-form-ctrl"=>"","v-select"=>"year")   )  !!}
                                            <span class="md-input-bar"></span>
                                        </div>    
                                        <p class="uk-text-danger uk-text-small"  v-if="updateForm.year.$error.required">year is required</p>                                        
                                    </div>
                                </div>

                                <div class="parsley-row" style="margin-left:10px">
                                    <div class="uk-input-group">

                                        <label for="">Programme:</label>     
                                        <div class="md-input-wrapper md-input-filled">
                                             {!!   Form::select('programme',$programme ,array("required"=>"required","class"=>"md-input","id"=>"programme","v-model"=>"programme","v-form-ctrl"=>"","v-select"=>"programme")   )  !!}
                                            <span class="md-input-bar"></span>
                                        </div>    
                                        <p class="uk-text-danger uk-text-small"  v-if="updateForm.programme.$error.required">programme is required</p>                                        
                                    </div>
                                </div>
                                   
                                  <div class="parsley-row" style="margin-left:80px">
                                    <div class="uk-input-group">

                                        <label for="">Residential Status :</label>     
                                        <div class="md-input-wrapper md-input-filled">
                                            {!!   Form::select('type',array("Resident"=>"Resident",'Non Resident'=>"Non Resident"),old('type',''),array('placeholder'=>'Select residential status', "style"=>"","class"=>"md-input","v-model"=>"type","v-form-ctrl"=>"","v-select"=>"type"))  !!}
                                            <span class="md-input-bar"></span>
                                        </div>    
                                         <p class="uk-text-danger uk-text-small"  v-if="updateForm.type.$error.required">Residential Status is required</p>                                        
                                 
                                    </div>
                                </div>
                                   
                                  <div class="parsley-row" v-if ="type=='Non Resident'">
                                    <div class="uk-input-group">

                                         
                                       <div class="md-input-wrapper md-input-filled"><label for="wizard_referer">Hostel :</label><input type="text" id="hostel" name="hostel" class="md-input"   required="required"    v-model="hostel"  v-form-ctrl><span class="md-input-bar"></span></div>                
                                        <p  class=" uk-text-danger uk-text-small  "   v-if="updateForm.hostel.$error.required">Hostel Name is required</p>                                      
                                    

                                       </div>
                              </div>
                                <div class="parsley-row" v-if ="type=='Resident'">
                                    <div class="uk-input-group">

                                        <label for="">Hall :</label>     
                                        <div class="md-input-wrapper md-input-filled">
                                          {!!   Form::select('halls',$hall,array("required"=>"required","class"=>"md-input","id"=>"halls","v-model"=>"halls","v-form-ctrl"=>"","style"=>"width: 226px;","v-select"=>"halls")   )  !!}
                                    <span class="md-input-bar"></span>
                                        </div> 

                                      <p class="uk-text-danger uk-text-small"  v-if="updateForm.halls.$error.required">Hall is required</p>                                        
                                  </div>
                              </div>
                            </div>
                            
                            


      </section>

      <!-- second section -->
      {{-- <h3 id="payment-heading-1" tabindex="-1" class="title">Payment</h3> --}}
      <section id="payment_section" role="tabpanel" aria-labelledby="payment section" class="body step-1 "  v-bind:class="{'uk-hidden': !in_payment_section} "  data-step="1"  aria-hidden="true">
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
         <div class="parsley-row">
                                    <div class="uk-input-group">
                                        
                                        <div class="md-input-wrapper md-input-filled"><label for="wizard_email">BILLS:</label><input type="text" id="" name="bill" class="md-input"   disabled=""v-model="bill"v-form-ctrl  ><span class="md-input-bar"></span></div>                                            
                                        
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
</section>

</div>
<div class="actions clearfix "  >
    <ul aria-label="Pagination" role="menu">
        <li class="button_previous " aria-disabled="true"  v-on:click="go_to_fill_form_section()"  v-show="in_payment_section==true"  >
            <a role="menuitem" href="#previous" >
                <i class="material-icons"></i> Previous
            </a>
        </li>
        <li class="button_next button"   v-on:click="go_to_payment_section()"  aria-hidden="false" aria-disabled="false"  v-show="updateForm.$valid && in_payment_section==false"  > 
            <a role="menuitem" href="#next"  >Next 
                <i class="material-icons">
                </i>
            </a>
        </li>
        <li class="button_finish "    aria-hidden="true"  v-show="updateForm.$valid && in_payment_section==true"  >
            <input class="md-btn md-btn-primary uk-margin-small-top" type="submit" name="submit_order"  value="Submit"   v-on:click="submit_form"  />
        </li>
    </ul>
</div>
</div>
</form>

            <div class="uk-modal" id="confirm_modal"   >
                <div class="uk-modal-dialog"  v-el:confirm_modal>
                    <div class="uk-modal-header uk-text-large uk-text-success uk-text-center" >Confirm Data</div>
                    Are you certain of all the info
                    {{-- <div class="uk-modal-footer ">
        <center>
          <button class="md-btn md-btn-primary uk-margin-small-top" type="submit" name="submit_order" > Cancel</button>
          <button class="md-btn md-btn-primary uk-margin-small-top" type="submit" name="submit_order" > Ok</button>
          </center>
        </div> --}}
                </div>
            </div>
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
      return (function(modal){ modal = UIkit.modal.blockUI("<div class='uk-text-center'>Saving Data<br/><img class='uk-thumbnail uk-margin-top' src='{!! url('public/assets/img/spinners/spinner_success.gif')  !!}' /></div>"); setTimeout(function(){ modal.hide() }, 50000) })();
    },
        
    go_to_fill_form_section : function (event){    
      vm.$data.in_payment_section=false
    }
  }
})

</script>
@endsection