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
 <h5 class="heading_c">Delete wrongly uploaded grades</h5>
 <p></p>
 <div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content">
            
                <form  action="{{url('grades/process/delete')}}"  method="post" accept-charset="utf-8" name="applicationForm"  v-form >
                   {!!  csrf_field()  !!}
                    <div class="uk-grid" data-uk-grid-margin="">

                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                    {!! Form::select('year', 
                                (['' => 'Select academic year'] +$year ), 
                                  old("year",""),
                                    ['class' => 'md-input year','id'=>"parent",'placeholder'=>'select academic year','required'=>"required",'v-model'=>'year','v-form-ctrl'=>'','v-select'=>''] )  !!}
                          <p class="uk-text-danger uk-text-small"  v-if="applicationForm.year.$error.required" >Academic year is required</p>

                            </div>
                        </div>
                         <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                 
                                              {!!  Form::select('semester', array('1'=>'1st sem','2'=>'2nd sem','3' => '3rd sem'), null, ['placeholder' => 'select semester','id'=>'parent','class'=>'md-input semester','required'=>"required",'v-model'=>'semester','v-form-ctrl'=>'','v-select'=>''],old("semester","")); !!}
                                              
                                <p class="uk-text-danger uk-text-small"  v-if="applicationForm.semester.$error.required" >Semester is required</p>

                            </div>
                        </div>
                        
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                     {!! Form::select('level', 
                                (['' => 'Select level'] +$level ), 
                                  old("level",""),
                                    ['class' => 'md-input parent level','id'=>"parent",'placeholder'=>'select level','required'=>"required",'v-model'=>'level','v-form-ctrl'=>'','v-select'=>''] )  !!}
                                    
                                <p class="uk-text-danger uk-text-small"  v-if="applicationForm.level.$error.required" >Level is required</p>

                            </div>
                        </div>
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                     {!! Form::select('course', 
                                ($course ), 
                                  old("course",""),
                                    ['class' => 'md-input parent', 'placeholder'=>'select course','v-model'=>'course','v-form-ctrl'=>'','v-select'=>''] )  !!}
                         
                            </div>
                        </div>
                        
                         <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                <input type="text" class="md-input"   v-form-ctrl="" placeholder="Index No" name="indexno" id="code" />
                                            
                            
                            </div>
                        </div>
                        
                        
                        
                        
                        
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                  {!! Form::select('program', 
                                (['' => 'Select program'] + $program), 
                                    null, 
                                    ["required"=>"required",'class' => 'md-input program','v-model'=>'program','v-form-ctrl'=>'','v-select'=>''] )  !!}
                                 
                                <p class="uk-text-danger uk-text-small"  v-if="applicationForm.program.$error.required" >Program is required</p>

                            </div>
                        </div>
                        
                         
                         
                         
                        
                        
                        
                        
                    
                    </div> 
                   <center>
                       <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">                            
                                
                                <button  v-show="applicationForm.$valid"  class="md-btn   md-btn-small md-btn-danger uk-margin-small-top actions" type="submit">Delete</button> 
                          
                            </div>
                        </div>
                   </center>
                   
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
   
   
 options: [    ]  
    
  },
   
})

</script>
 <script src="{!! url('public/assets/js/ajax.js') !!}"></script>

 
<script>
                    $(document).ready(function(){
            $('.action').on('click', function(e){


            var year = $('.year').val();
                    var program = $('.program').val();
                     var level = $('.level').val();
                     var semester = $('.semester').val();
                     var course = $('.course').val();
                     
                    UIkit.modal.confirm("Are you sure you want to delete this grades?? "
                            , function(){
                            modal = UIkit.modal.blockUI("<div class='uk-text-center'>Deleting grades <br/><img class='uk-thumbnail uk-margin-top' src='{!! url('public/assets/img/spinners/spinner.gif')  !!}' /></div>");
                                    //setTimeout(function(){ modal.hide() }, 500) })()            
                                    $.ajax({
                                     
                                            type: "POST",
                                             url:"{{url('grades/process/delete')}}",
                                            
                                            
                                            data: { program:program, year:year,course:course,level:level,semester:semester}, //your form data to post goes 
                                            dataType: "html",
                                    }).done(function(data){
                            modal.hide();
                                    
                                     UIkit.modal.alert("Grades  deleted successfully");
                                   // $("#ts_pager_filter").load(window.location + " #ts_pager_filter");
                                    // console.log(data);
                                   //  location.reload();
//        return (function(modal){ modal = UIkit.modal.blockUI("<div class='uk-text-center'>Processing Transcript Order<br/><img class='uk-thumbnail uk-margin-top' src='{!! url('public/assets/img/spinners/spinner.gif')  !!}' /></div>"); setTimeout(function(){ modal.hide() }, 500) })();
                            });
                            }
                    );
            });
            
           
            });</script>
@endsection