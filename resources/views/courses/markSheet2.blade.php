@extends('layouts.printlayout')

 
@section('style')
<style>
    .marks{
         
height: auto;
margin: 0;
padding: 4px;
line-height: 24px;
border: 1px solid rgba(0,0,0,.12);
color: #212121;
box-sizing: border-box;
-webkit-transition: height .1s ease;
transition: height .1s ease;
border-radius: 0;
-webkit-appearance: none;
    }
</style>
@endsection
 @section('content')
   <div class="md-card-content">
@if(Session::has('success'))
            <div style="text-align: center" class="uk-alert uk-alert-success" data-uk-alert="">
                {!! Session::get('success') !!}
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
     <h3 class="heading_b uk-margin-bottom">Continuous Assesment</h3>
 <div style="">
     <div class="uk-margin-bottom" style="margin-left:897px" >
          
         
     <a href='{{ url("marksDownloadExcel/$mycode/code") }}'>   <button class="md-btn md-btn-small md-btn-primary uk-margin-right">Download to Excel</button></a>
     
  
         <div class="uk-button-dropdown" data-uk-dropdown="{mode:'click'}">
             <button class="md-btn md-btn-small md-btn-success"> columns <i class="uk-icon-caret-down"></i></button>
             <div class="uk-dropdown">
                 <ul class="uk-nav uk-nav-dropdown" id="columnSelector"></ul>
             </div>
         </div>
         

                          
                                                   
                                  <i title="click to print" onclick="javascript:printDiv('print')" class="material-icons md-36 uk-text-success"   >print</i>
                   
                            
                           
     </div>
 </div>
  

 <div class="uk-width-xLarge-1-1">
     
         
     <div class="uk-alert uk-alert-info " style="text-transform: uppercase">
                <center> <p>Course:<b> {!! $course !!}</b> |  Total Students = <b> {!! $total !!}  |  Academic Year = <b> {!! $year !!} |  Semester = <b> {!! $sem !!} sem</center></p>
            </div>
                
     
 </div>
 <center>
     
 <div class="uk-width-xLarge-1-1" style="margin-left:10px">
   
    <div class="md-card">
        <div class="md-card-content">
  <form  action=""  id="form" accept-charset="utf-8" method="POST" name="applicationForm"  v-form>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}"> 
          
   <div class="uk-overflow-container" id='print'>
         <center><span class="uk-text-success uk-text-bold">{!! $mark->total()!!} Record(s)</span></center>
               <table class="uk-table uk-table-hover uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id="ts_pager_filter"> 
             
               <thead>
                 <tr>
                     <th>N<u>o</u></th>
                     <th>Index N<u>o</u></th>
                     <th  style="text-align:">Student</th>
                      
                     <th style="text-align: ">Quiz1</th>

                     <th style="text-align: ">Quiz2</th>
                     <th style="text-align: ">Quiz3</th>
                     <th style="text-align: ">Mid-Sem I</th>
                     <th style="text-align:center">Total Class</th>
                     <th style="text-align: ">Exam Score(60%)</th>
                      <th style="text-align:center">Total</th>
                     <th  style="text-align:center">Grade</th>
                      
                                      
                </tr>
             </thead> 
      <tbody>
                                         <?php $count=0;?>
                                         @foreach($mark as $index=> $row) 
                                         
                                       <?php $count++;?>
                                         
                                        <tr align="">
                                            <td> {{ $mark->perPage()*($mark->currentPage()-1)+($index+1) }} </td>
                                            <td class="uk-text-success"> {{ $row->academic->INDEXNO }}</td>
                                            <input type="hidden" name="key[]" value="{{$row->id}}"/>
                                             <input type="hidden" name="student[]" value="{{$row->indexno}}"/>
                                             <td class="uk-text-primary"> {{ @$row->academic->NAME }}</td>
                                             <td><input style="text-align: center;width:87px" name="quiz1[]" maxlength="4" class="marks" type="text" value="{{@$row->quiz1}}"/></td>
                                            <td><input style="text-align: center;width:87px" name="quiz2[]"maxlength="4" class="marks" type="text" value="{{@$row->quiz2}}"/></td>
                                            <td><input style="text-align: center;width:87px"name="quiz3[]" maxlength="4" class="marks" type="text" value="{{@$row->quiz3}}"/></td>
                                            <td><input style="text-align: center;width:87px"name="midsem1[]" maxlength="4"class="marks" type="text" value="{{@$row->midSem1}}"/></td>
                                            <td style="text-align: center">{{@$row->quiz1+$row->quiz2+$row->quiz3+$row->midSem1}}</td>
                                          
                                            <td><input style="text-align: center;width:87px"name="exam[]"class="marks" maxlength="4" type="text" value="{{@$row->exam}}"/></td>
                                            <td style="text-align: center">{{@$row->total}}</td>
                                            
                                            <input type="hidden" name="course" value="{{$row->code}}"/>
                                            <input type="hidden" name="quiz1Old[]" value="{{$row->quiz1}}"/>
                                            <input type="hidden" name="quiz2Old[]" value="{{$row->quiz2}}"/>
                                            <input type="hidden" name="quiz3Old[]" value="{{$row->quiz3}}"/>
                                            <input type="hidden" name="midsemOld[]" value="{{$row->midSem1}}"/>
                                            <input type="hidden" name="examOld[]" value="{{$row->exam}}"/>
                                            <td style="text-align: center">{{@$row->grade}}</td>
                                           
                                            
                  
                                              
                                        </tr>
                                         @endforeach
                                    </tbody>
                                    
                             </table>
           {!! (new Landish\Pagination\UIKit($mark->appends(old())))->render() !!}
     </div>
            <center><div style="position: fixed;  bottom: 0px;left: 45%  ">
                        <p>
                            <input type="hidden" name="upper" value="{{$count++}}" id="upper" />
                            
                                  <button type="submit"  class="md-btn md-btn-success md-btn-small"><i class="fa fa-save" ></i>Save</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            
                                   
                        </p>
                    </div></center>    
  </form>
     </div>
 
 </div>
 </div></center>
  
@endsection
@section('js')
  
   
<script>
        $(document).ready(function(){
            $("#form").on("submit",function(event){
                event.preventDefault();
       UIkit.modal.alert('saving marks...');
         $(event.target).unbind("submit").submit();
    
                        
            });
            
    
                    
    
    });
</script>
 
 
@endsection