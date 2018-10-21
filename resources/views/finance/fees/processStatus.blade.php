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
   <h4 class="heading_b uk-margin-bottom">Sudent Status</h4>
     <p class="uk-text-primary uk-text-bold uk-text-small">Hotlines 0505284060, 0246091283</p>
              <hr>
  </center>
    <form id='form' method="POST" action="{{url('/processStatus')}}"  accept-charset="utf-8"  name="applicationForm"  v-form>
                 <input type="hidden" name="_token" value="{!! csrf_token() !!}"> 
            
            
            <div class="uk-grid" data-uk-grid-margin data-uk-grid-match="{target:'.md-card-content'}">
                <div class="uk-width-medium-1-2">
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-overflow-container">
                                <table>
                                    <tr>
                                            <td  align=""> <div  align="right" class=" "> New Status</div></td>
                                        <td>
                                            <select name="status" required="" class="md-input" v-form-ctrl='' v-model='status' v-select=''>
                                                <option>Select status</option>
                                                <option value="In school">In school</option>
                                                <option value="Deferred">Deferred</option>
                                                <option value="Abandoned">Abandoned</option>
                                                <option value="Rusticated">Rusticated</option>
                                                <option value="Withdrawn">Withdrawn</option>
                                                <option value="Unknown">Unknown</option>
                                                                                               
                                            </select>
                                             <p class="uk-text-danger uk-text-small"  v-if="applicationForm.type.$error.required" >Status is required</p>

                                            
                                        </td>
                                        </tr>
                                     <tr>
                                            <td  align=""> <div  align="right" class=" ">Duration</div></td>
                                        <td>
                                            <select name="duration" required="" class="md-input" v-form-ctrl='' v-model='duration' v-select=''>
                                                <option>Select duration</option>
                                                <option value="#">N / A</option>
                                                <option value="1">1 sem</option>
                                                <option value="1">1 year</option>
                                                <option value="2">2 years</option>
                                                <option value="3">3 years</option>
                                                <option value="10">Indefinate</option>
                                                 
                                            
                                              
                                            </select>
                                             <p class="uk-text-danger uk-text-small"  v-if="applicationForm.action.$error.required" >Duration is required</p>

                                            
                                        </td>
                                        </tr>
                                        <tr>
                                            <td  align=""> <div  align="right" class=" ">Level</div></td>
                                        <td>
                                            <select name="level" required="" class="md-input" v-form-ctrl='' v-model='level' v-select=''>
                                                <option>Select level</option>
                                                <option value='100NT'>100 NON TER</option>
                                                <option value='200NT'>200 NON TER</option>
                                                <option value='100H'>100 HND</option>
                                                <option value='200H'>200 HND</option>
                                                <option value='300H'>300 HND</option>
                                                <option value='100BTT'>100 BTECH TOP UP</option>
                                                <option value='200BTT'>200 BTECH TOP UP</option>
                                                <option value='100BT'>100 BTECH (4 yrs)</option>
                                                <option value='200BT'>200 BTECH (4 yrs)</option>
                                                <option value='300BT'>300 BTECH (4 yrs)</option>
                                                <option value='400BT'>400 BTECH (4 yrs)</option>
                                                <option value='500MT'>100 MASTERS</option>
                                                <option value='600MT'>200 MASTERS</option>
                                                 
                                            
                                              
                                            </select>
                                             

                                            
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
                    </div>
                    <p></p>
                  <center>
                     
                         <button  v-show="applicationForm.$valid" type="submit" class="md-btn md-btn-primary"><i class="fa fa-save" ></i>Submit</button>
                    
                  
            </center>
                
                </div>
                <div class="uk-width-medium-1-2">
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
                                        <tr>
                                            <td  align=""> <div  align="right" class="uk-text-primary">STATUS: </div></td>
                                        <td>
                                          {{  $data[0]->STATUS}}
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
       UIkit.modal.alert('Updating Status.Please wait.....');
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