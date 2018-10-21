@extends('layouts.app')

 
   
@section('style')
 
        <script src="{!! url('public/assets/js/jquery.min.js') !!}"></script>
 
        <script src="{!! url('public/assets/js/jquery-ui.min.js') !!}"></script>
 
<style>
     
</style>
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
 </div>
  @inject('sys', 'App\Http\Controllers\SystemController')
  <center>
   <h4 class="heading_b uk-margin-bottom">Sudent Program</h4>
     <p class="uk-text-primary uk-text-bold uk-text-small">Hotlines 0505284060, 0246091283</p>
              <hr>
  </center>
    <form id='form' method="POST" action="{{url('/processProgram')}}"  accept-charset="utf-8"  name="applicationForm"  v-form>
                 <input type="hidden" name="_token" value="{!! csrf_token() !!}"> 
            
            
            <div class="uk-grid" data-uk-grid-margin data-uk-grid-match="{target:'.md-card-content'}">
                <div class="uk-width-medium-3-5">
                    <div class="md-card">
                        <div class="md-card-content">
                                <table>
                                  <tr>
                                        <td  align=""> <div  align="right" class=" ">Old Programme</div></td>    
                                        <td>
                                          
                                            {{ @$sys->getProgram($data[0]->PROGRAMMECODE)}}
                                             
                                           
                                        </td>
                                        </tr>
                                    <tr>

                                            <td  align=""> <div  align="right" class=" ">New Programme</div></td>
                                        <td>
                                         
                                            {!! Form::select('program',
                                (['' => 'All programs'] + $pro ),
                                ['class' => 'md-input parent','id'=>"chapro",'name'=>"chapro",'placeholder'=>'select program'] )  !!}
                                             <p class="uk-text-danger uk-text-small"  v-if="applicationForm.type.$error.required" >Programme is required</p>
                                            
                                            
                                        </td>
                                        </tr>
                                     
                                        
                                         <tr>
                                            <td  align=""> <div  align="right" class="uk-text-success">Reason</div></td>
                                        <td>
                                            <input type="text"  required="" v-form-ctrl='' v-model='reason'   name="reason"   class="md-input">
                                             <p class="uk-text-danger uk-text-small"  v-if="applicationForm.reason.$error.required" >Reason is required</p>

                                            
                                        </td>
                                        </tr>
                                    </table>
                           
                        </div>
                    </div>
                    <p></p>
                  <center>
                     
                         <button  v-show="applicationForm.$valid" type="submit" class="md-btn md-btn-primary"><i class="fa fa-save" ></i>Submit</button>
                    
                  
            </center>
                
                </div>
                <div class="uk-width-medium-2-5">
                    <div class="md-card">
                        <div class="md-card-content">
                            <table>
                                <tr>
                                    <td>
                                        <table>
                                             @if($data[0]->YEAR==1 ||$data[0]->YEAR=='400/1' )
                                        <tr>
                                            <td  align=""> <div  align="right" >Admission Number: </div></td>
                                        <td>
                                            {{ $data[0]->STNO}}
                                             <input type="hidden" name="student" id="student" value="{{ $data[0]->STNO}}" />
                                            
                                        </td>
                                        </tr>
                                         @else
                                          <tr>
                                            <td  align=""> <div  align="right" >Index Number: </div></td>
                                        <td>
                                            {{ $data[0]->INDEXNO}}
                                             <input type="hidden" name="student" id="student" value="{{ $data[0]->INDEXNO}}" />
                                            
                                        </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td  align=""> <div  align="right" >Full Name: </div></td>
                                        <td>
                                            {{ $data[0]->NAME}}
                                            
                                        </td>
                                        </tr>
                                        <tr>
                                            <td  align=""> <div  align="right" >Level: </div></td>
                                        <td>
                                            {{ $data[0]->LEVEL}}
                                              
                                        </td>
                                        </tr>
                                        <tr>
                                            <td  align=""> <div  align="right" >Programme: </div></td>
                                        <td>
                                            {{ @$sys->getProgram($data[0]->PROGRAMMECODE)}}
                                             <input type="hidden" name="programme"  value="{{ $data[0]->PROGRAMMECODE}}" />
                                           
                                        </td>
                                        </tr>
                                       
                                        <tr>
                                            <td  align=""> <div  align="right" class="uk-text-primary">CGPA: </div></td>
                                        <td>
                                          {{ $data[0]->CGPA}}
                                            <input type="hidden" id="bill"  name="bill" value="{{$data[0]->CGPA}}"/>
                                      
                                        </td>
                                        </tr>
                                          
                                         
                                         <tr>
                                            <td  align=""> <div  align="right" class="uk-text-primary">CLASS: </div></td>
                                        <td>
                                          {{  $data[0]->CLASS}}
                                            </td>
                                        </tr>
                                        </table>
                                    </td>
                                    <td valign="top">
                                        <img   style="width:150px;height: auto;"  <?php
                                        $pic = $data[0]->INDEXNO;
                                        echo $sys->picture("{!! url(\"public/albums/students/$pic.jpg\") !!}", 90)
                                        ?>   src='{{url("public/albums/students/$pic.jpg")}}' alt="  Affix student picture here"    />
                                    </td>
                                </tr>
                            </table>
                                </div>
                    </div>
                  
                </div>
            
            
            
             
    </form>
 @endsection
 
@section('js')
 
<script>
        $(document).ready(function(){
            $("#form").on("submit",function(event){
                event.preventDefault();
       UIkit.modal.alert('Updating Programme. Please wait.....');
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