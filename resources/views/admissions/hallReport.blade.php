@extends('layouts.app')

 
@section('style')
 
@endsection
 @section('content')
  
   <div class="md-card-content">
        
 @if($messages=Session::get("success"))

    <div class="uk-form-row">
        <div style="text-align: center" class="uk-alert uk-alert-success" data-uk-alert="">

              <ul>
                @foreach ($messages as $message)
                  <li> {!!  $message  !!} </li>
                @endforeach
          </ul>
    </div>
  </div>
   </div>
@endif
 
  
     @if (count($errors) > 0)

    
        <div class="uk-alert uk-alert-danger  uk-alert-close" style="background-color: red;color: white" data-uk-alert="">

              <ul>
                @foreach ($errors->all() as $error)
                  <li>{!!$error  !!} </li>
                @endforeach
               </ul>
        </div>
   
@endif
 
 
 </div>
<h5 class="heading_c uk-margin-bottom">Hall Statistics</h5>
   
 <div style="">
     <div class="uk-margin-bottom" style="margin-left:1000px" >
         <i title="click to print" onclick="javascript:printDiv('print')" class="material-icons md-36 uk-text-success"   >print</i>
                   
          <div class="uk-button-dropdown" data-uk-dropdown="{mode:'click'}">
             <button class="md-btn md-btn-small md-btn-success"> show/hide columns <i class="uk-icon-caret-down"></i></button>
             <div class="uk-dropdown">
                 <ul class="uk-nav uk-nav-dropdown" id="columnSelector"></ul>
             </div>
         </div>
         <div class="uk-button-dropdown" data-uk-dropdown="{mode:'click'}">
                                <button class="md-btn md-btn-small md-btn-success uk-margin-right">Export <i class="uk-icon-caret-down"></i></button>
                                <div class="uk-dropdown">
                                    <ul class="uk-nav uk-nav-dropdown">
                                         <li><a href="#" onClick ="$('#ts_pager_filter').tableExport({type:'csv',escape:'false'});"><img src='{!! url("public/assets/icons/csv.png")!!}' width="24"/> CSV</a></li>
                                           
                                            <li class="uk-nav-divider"></li>
                                            <li><a href="#" onClick ="$('#ts_pager_filter').tableExport({type:'excel',escape:'false'});"><img src='{!! url("public/assets/icons/xls.png")!!}' width="24"/> XLS</a></li>
                                            <li><a href="#" onClick ="$('#ts_pager_filter').tableExport({type:'doc',escape:'false'});"><img src='{!! url("public/assets/icons/word.png")!!}' width="24"/> Word</a></li>
                                            <li><a href="#" onClick ="$('#ts_pager_filter').tableExport({type:'powerpoint',escape:'false'});"><img src='{!! url("public/assets/icons/ppt.png")!!}' width="24"/> PowerPoint</a></li>
                                            <li class="uk-nav-divider"></li>
                                           
                                    </ul>
                                </div>
                            </div>
     </div>
     
 </div>
 <!-- filters here -->
  @inject('fee', 'App\Http\Controllers\FeeController')
   @inject('sys', 'App\Http\Controllers\SystemController')
  
  
 <!-- end filters -->
 <div class="uk-width-xLarge-1-1">
 <div class="md-card">
 <div class="md-card-content">
  
 
     <div class="uk-overflow-container" id='print'>
          <center><span class="uk-text-success uk-text-bold">4 Records</span></center>
        
                 <table border="1" class="uk-table uk-table-hover uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id=" "> 
                                  <thead>
                                        <tr>
                                             <th class="uk-width-1-10">ID</th>
                                           
                                                <th>HALL NAME </th>
                                            <th>HALL LOCATION</th>
                                             <th class=" uk-text-small"  >HALL CAPACITY</th>
                                              <th>SPACE USED</th>
                                              <th class='md-bg-light-green-100 uk-text-small'>SPACE LEFT</th>
                                              <th>HALL BANK</th>
                                               <th>HALL ACCOUNT NO</th>
                                               <th  >HALL FEES</th>
<!--                                            <th>TOTAL </th>-->
                                        </tr>
                                    </thead>
                                    <tbody>
                                         
                                     @foreach($data as $index=> $row) 
                                     <tr>
                                         <td>{{$row->ID}}</td>
                                         <td  @if($row->ID % 2 == 0 )class='md-bg-purple-100 uk-text-small'@else class='md-bg-cyan-100 uk-text-small' @endif >{{$row->HALL_NAME}}</td>
                                         <td class='md-bg-cyan-100 uk-text-small'>{{$row->HALL_LOCATION}}</td> 
                                           <td class='md-bg-purple-100 uk-text-small'>{{$row->HALL_CAPACITY}}</td> 
                                           <td class='md-bg-purple-100 uk-text-small'>{{$row->SPACE_USED}}</td> 
                                           <td class='md-bg-light-green-100 uk-text-small'>{{$row->HALL_CAPACITY -$row->SPACE_USED }}</td> 
                                     
                                           <td class='md-bg-purple-100 uk-text-small'>{{strtoupper($row->BANK)}}</td> 
                                           <td class='md-bg-purple-100 uk-text-small'>{{$row->ACCOUNTNUMBER}}</td>
                                           <td class='md-bg-light-blue uk-text-small'>GHS{{$row->AMOUNT}}</td> 
                                     </tr>
                                     </tr>
                                     </tr>
                                     </tr>
                                        @endforeach
                                        
                                       
                                         
                                    </tbody>
                                    
                             </table>
          <table>
             
          </table>
        
           
     </div>
  
   
 </div>
 </div></div>
@endsection
@section('js')
 <script type="text/javascript">
      
$(document).ready(function(){
 
$(".parent").on('change',function(e){
 
   $("#group").submit();
 
});
});

</script>
<script src="{!! url('public/assets/js/select2.full.min.js') !!}"></script>
<script>
$(document).ready(function(){
  $('select').select2({ width: "resolve" });

  
});


</script>
 
 <!--  notifications functions -->
    <script src="public/assets/js/components_notifications.min.js"></script>
@endsection