@extends('layouts.app')


@section('style')
<style>
    .md-card{
        width: auto;

    }
    
</style>
 
@endsection
@section('content')
 <div align="center">
<div class="uk-width-small-1-2">
    <div class="md-card">
        <div class="md-card-content" style="">

            <h5 class=" ">Create Calender here</h5>
            <form  action=""  id="form" accept-charset="utf-8" method="POST" name="applicationForm"  v-form>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}"> 
               
                                <div class="uk-grid uk-grid-width-medium-1-2 uk-grid-width-large-1-4" data-uk-grid-margin>
                               
                                    
                                   <div class="parsley-row">
                                        <div class="uk-input-group">
                                            
                                            <label for="wizard_phone">Academic year<span class="req uk-text-danger">*</span></label>
                                            <input placeholder="eg 2012/2013" type="text" class="md-input label-fixed " v-model='year' v-form-ctrl="" name="year" id="year" required=""/>
                                            <p class="uk-text-danger uk-text-small"  v-if="applicationForm.year.$error.required" >Academic year is required</p>

                                        </div>
                                    </div>
                                    
                                    <div class=" parsley-row">
                                        <div class="uk-input-group">
                                            
                                            <label for="wizard_email">Semester<span class="req uk-text-danger">*</span></label>
                                            <p></p>
                                             {!!  Form::select('semester', array('1'=>'1st sem','2'=>'2nd sem','3' => '3rd sem' ), null, ['placeholder' => 'select semester','id'=>'parent','class'=>'md-input','required'=>'required','v-model'=>'semester','v-form-ctrl'=>'','v-select'=>'','style'=>'width:200px'],old("semester","")); !!}
                           
                                            <p class="uk-text-danger uk-text-small"  v-if="applicationForm.semester.$error.required" >Semester is required</p>

                                        </div>
                                    </div>
                                   <!--  <div class="uk-grid">
                                    <div class="uk-width-small-1-2 parsley-row">
                                          <label>Program<span class="req uk-text-danger">(leave it if you want to open )</span></label>
                                <p></p>
                                        {!! Form::select('program', 
                                (['' => 'select program'] +$programme ), 
                                  old("program",""),
                                    ['class' => 'md-input gad','style'=>'width:400px','v-model'=>'program', 'v-form-ctrl'=>'','v-select'=>''] )  !!}
                                       
                                    </div>
                                </div>-->
                                    
                                    
                                </div>
                              
                
                
                
                
                
      <table align="center">
       
        <tr><td><input type="submit" value="Save" id='save'v-show="applicationForm.$valid"  class="md-btn   md-btn-success uk-margin-small-top">
      <input type="reset" value="Clear" class="md-btn   md-btn-default uk-margin-small-top">
    </td></tr></table>
            </form>
        </div>
    </div>
</div>
 </div>
@endsection

@section('js')
<script>
        $(document).ready(function(){
            $("#form").on("submit",function(event){
                event.preventDefault();
       UIkit.modal.alert('Creating Calender item...');
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