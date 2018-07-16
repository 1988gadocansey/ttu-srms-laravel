@extends('layouts.app')





@section('style')

<style>

    .md-card{

        width: auto;



    }

    

</style>

 <script src="{!! url('public/assets/js/jquery.min.js') !!}"></script>

 

        <script src="{!! url('public/assets/js/jquery-ui.min.js') !!}"></script>

 @inject('sys', 'App\Http\Controllers\SystemController')

@endsection

@section('content')

<div class="uk-width-xLarge-1-10">

    <div class="md-card">

        <div class="md-card-content" style="">



            <h5 class=" ">Mount Courses here</h5>

            <form  action=""  id="form" accept-charset="utf-8" method="POST" name="applicationForm"  v-form>

                <input type="hidden" name="_token" value="{!! csrf_token() !!}"> 

          

                   <div class="uk-grid" data-uk-grid-margin>

              

                        <div class="uk-width-medium-1-2">

                            <div class="uk-form-row">

                                <div class="uk-width-medium-1-2">

                                        <label>Programme</label>

                                       

                                        <p></p>

                                       {!! Form::select('program', 

                                (['' => 'Select program'] + $program), 

                                    null, 

                                    ["required"=>"required",'class' => 'md-input','v-model'=>'program','v-form-ctrl'=>'','v-select'=>''] )  !!}

                                    <p class="uk-text-danger uk-text-small"  v-if="applicationForm.program.$error.required" >Program is required</p>



                                    </div>

                                <div class="uk-grid">

                                    <div class="uk-width-medium-1-2">

                                        <label>Course</label>

                                       

                                        <p></p>

                                        <select placeholder='type courses' class="form-control" style="width:490px" name="course" required="required" class= 'md-input'v-model='course' v-form-ctrl='' v-select=''>

                                           <option selected="">type course name here</option>

                                           @foreach($course as $item)

                                        

                                          <option value="{{$item->COURSE_CODE.','.$item->COURSE_NAME}}">{{$item->COURSE_NAME}} - {{ $item->COURSE_CODE }} - {{ $item->COURSE_LEVEL }} - Sem{{ $item->COURSE_SEMESTER}})</option>

                                        @endforeach

                                      </select>



                                        

                                        <p class="uk-text-danger uk-text-small"  v-if="applicationForm.course.$error.required" >course is required</p>



                                    </div>

                                     

                                </div>

                            </div>

                             

                             

                                <div class="uk-form-row">

                                <label>Level</label>

                                <p></p>

                               {!!   Form::select('level',$level ,array("required"=>"required","class"=>"md-input","id"=>"level","v-model"=>"level","v-form-ctrl"=>"","v-select"=>"level")   )  !!}

                                         

                                  <p class="uk-text-danger uk-text-small"  v-if="applicationForm.level.$error.required" >Level is required</p>



                               </div>

                                 <div class="uk-form-row">

                                <label for="wizard_email">Course Credit<span class="req uk-text-danger">*</span></label>

                                            <input type="number" class="md-input" v-model='credit' v-form-ctrl  name="credit" id="credit" required="" />

                                             <p class="uk-text-danger uk-text-small"  v-if="applicationForm.credit.$error.required" >Course Credit is required</p>



                               </div>

                                

                         <div class="uk-form-row">

                                <label>Type</label>

                                <p></p>

                                {{ Form::select('type', array(''=>'select type','Resit'=>'Resit', 'Elective'=>'Elective','Core'=>'Core'), null, ['class' => 'md-input','v-model'=>'type','v-form-ctrl'=>'','v-select'=>'']) }}

                                  

                               </div>

                            

                        </div>

                        <div class="uk-width-medium-1-2">

                            <div class="uk-form-row">

                                        <label>Lecturer</label>

                                       

                                        <p></p>

                                       {!! Form::select('lecturer', 

                                (['' => 'Select lecturer'] + $lecturer), 

                                    null, 

                                    [ 'class' => 'md-input','v-model'=>'lecturer','v-form-ctrl'=>'','v-select'=>''] )  !!}

                                   

                            </div>

                            

                                <div class="uk-form-row">

                                <label>Semester</label>

                                <p></p>

                                {{ Form::select('semester', array(''=>'select semester','1'=>'1', '2'=>'2','3'=>'3'), null, ["required"=>"required",'class' => 'md-input','v-model'=>'semester','v-form-ctrl'=>'','v-select'=>'']) }}

                                  <p class="uk-text-danger uk-text-small"  v-if="applicationForm.semester.$error.required" >Semester is required</p>



                               </div>

               

                            <div class="uk-form-row">

                                <label>Academic year</label>

                                <p></p>

                                       {!! Form::select('year', 

                                (array(''=>'Select year','2015/2016' => '2015/2016', '2016/2017' => '2016/2017','2017/2018'=>'2017/2018','2018/2019'=>'2018/2019') ), 

                                    null, 

                                    ["required"=>"required",'class' => 'md-input','v-model'=>'year','v-form-ctrl'=>'','v-select'=>''] )  !!}

                                    <p class="uk-text-danger uk-text-small"  v-if="applicationForm.year.$error.required" >Year is required</p>



                             </div>

                            

                 

                             

                            

                                  

                             

                        </div>

                             

                        </div>

                    </div>              

                             

                              

                 <div class="uk-grid" align='center'>

                            <div class="uk-width-1-1">

                                <button type="submit" v-show="applicationForm.$valid" class="md-btn md-btn-success"><i class="fa fa-save" ></i>Save</button>

                            </div>

                </div>

                    <p>&nbsp;</p>

                

                

                

                

      

            </form>

        </div>

    </div>

</div>

@endsection



@section('js')

<script>

        $(document).ready(function(){

            $("#form").on("submit",function(event){

                event.preventDefault();

       UIkit.modal.alert('Mounting Course...');

         $(event.target).unbind("submit").submit();

    

                        

            });

            

    

                    

    

    });

</script>

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

   

   

 options: [    ]  

    

  },

   

})



</script>

@endsection    