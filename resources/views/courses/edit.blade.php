@extends('layouts.app')


@section('style')
<style>
    .md-card{
        width: auto;

    }
    
</style>
  
 
@endsection
@section('content')
<div class="uk-width-xLarge-1-10">
    <div class="md-card">
        <div class="md-card-content" style="">

            <h5 class=" ">Editing Course</h5>
            <form    id="form" accept-charset="utf-8" method="POST" name="applicationForm"  v-form>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}"> 
                 <table id="paymentTable" class="uk-table"border="0" style="font-weight:bold">
	  <tr id="paymentRow" payment_row="payment_row"> 
             <td valign="top">Course Name &nbsp;<input type="text"   class="md-input md-input" required=""  value='{{$data->COURSE_NAME}}' v-model='NAME' v-form-ctrl=''  name="name" style="width:auto;"></td>

	  <td valign="top">Course Code &nbsp;<input type="text"  v-model='code' v-form-ctrl=''   class="md-input md-input" value='{{$data->COURSE_CODE}}' name="code" style="width:auto;"></td>

    
           <td>Program &nbsp;
                 
                   <select placeholder='select program'   style="" name="program" required="required" class= 'md-input'v-model='program' v-form-ctrl='' v-select=''>
                                                    
                                                    @foreach($program as $item=>$rows)

                                                   <option <?php
                                                                            if ($data->PROGRAMME==$rows->PROGRAMMECODE) {
                                                                                echo "selected='selected'";
                                                                            }
                                                                            ?> value="{{$rows->PROGRAMMECODE}}">{{$rows->PROGRAMME}} </option>
                                                 @endforeach
                                                    </select> 
                   
           </td>
          
        
          
	      
      </table>
      <table align="center">
       
        <tr><td><input type="submit" value="Save" id='save'v-show="applicationForm.$valid"  class="md-btn   md-btn-success uk-margin-small-top">
      <input type="reset" value="Cancel" class="md-btn   md-btn-default uk-margin-small-top">
    </td></tr></table>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
 
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
  department : "{{  $data->DEPTCODE }}",
  
  grade : "{{ $data->GRADING_SYSTEM }}",
   
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
      return (function(modal){ modal = UIkit.modal.blockUI("<div class='uk-text-center'>Edting Data<br/><img class='uk-thumbnail uk-margin-top' src='{!! url('public/assets/img/spinners/spinner.gif')  !!}' /></div>"); setTimeout(function(){ modal.hide() }, 50000) })();
    },
        
    go_to_fill_form_section : function (event){    
      vm.$data.in_payment_section=false
    }
  }
})

</script>
        
@endsection