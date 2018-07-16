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
 <div class="uk-modal" id="new_task">
     <div class="uk-modal-dialog">
         <div class="uk-modal-header">
             <h4 class="uk-modal-title">Send sms  here</h4>
         </div>
         <form action="{!! url('/fireOwingSMS')!!}" method="POST">
             <input type="hidden" name="_token" value="{!! csrf_token() !!}"> 


             <textarea cols="30" rows="4" name="message"class="md-input" required=""></textarea>


             <div class="uk-modal-footer uk-text-right">
                 <button type="submit" class="md-btn md-btn-flat md-btn-flat-primary md-btn-wave" id="snippet_new_save"><i   class="material-icons"   >smartphone</i>Send</button>    
                 <button type="button" class="md-btn md-btn-flat uk-modal-close md-btn-wave">Close</button>
             </div>
         </form>
     </div>
 </div>
 <h5 class="uk-heading_c">User logs</h5>  
 <div style="">
     <div class="uk-margin-bottom" style="margin-left:900px" >
<!--         <a  href="#new_task" data-uk-modal="{ center:true }"> <i title="click to send sms to students owing"   class="material-icons md-36 uk-text-success"   >phonelink_ring message</i></a>-->

         <a href="#" class="md-btn md-btn-small md-btn-default uk-margin-right" id="printTable">Print Table</a>
         <div class="uk-button-dropdown" data-uk-dropdown="{mode:'click'}">
             <button class="md-btn md-btn-small md-btn-warning"> columns <i class="uk-icon-caret-down"></i></button>
             <div class="uk-dropdown">
                 <ul class="uk-nav uk-nav-dropdown" id="columnSelector"></ul>
             </div>
         </div>
           <div class="uk-button-dropdown" data-uk-dropdown="{mode:'click'}">
                                <button class="md-btn md-btn-small md-btn-primary  ">Export <i class="uk-icon-caret-down"></i></button>
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
  
  
 
 <!-- end filters -->
 <div class="uk-width-xLarge-1-1">
 <div class="md-card">
 <div class="md-card-content">
  

     <div class="uk-overflow-container" id='print'>
         <center><span class="uk-text-success uk-text-bold">{!! $data->total()!!} Records</span></center>
                <table class="uk-table uk-table-hover uk-table-condensed uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id="ts_pager_filter"> 
                                  <thead>
                                        <tr>
                                     <th class="filter-false remove sorter-false" data-priority="6">NO</th>
                                     <th>User</th>
                                     <th>Role</th>
                                     <th>Phone</th>
                                     <th>Department</th>
                                     <th data-priority="critical">Data Altered/Created</th>
                                     <th>Object Visited</th>
                                      <th>User Agent</th> 
                                      <th>IP Address</th>
                                       
                                      <th>Hostname</th>
                                      <th>Datetime</th>
                                      
                                      <th>ACTION</th>
                                             
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                         @foreach($data as $index=> $row) 
                                         
                                        
                                        
                                         
                                        <tr align="">
                                            <td> {{ $data->perPage()*($data->currentPage()-1)+($index+1) }} </td>
                                              <td> {{ @$row->user->name }}</td>
                                               <td> {{ @$row->user->role }}</td>
                                              <td> {{ @$row->user->phone }}</td>
                                            <td> {{ @$row->user->department }}</td>
                                             <td> {{ @$row->properties }}</td>
                                             
                                            <td> {{ @$row->causer_type }}</td>
                                            <td> {{ @$row->causer_userAgent }}</td>
                                             <td> {{ @$row->causer_ip}}</td>
                                            <td> {{ @$row->causer_hostname }}</td>
                                            <td> {{ @$row->created_at }}</td>
                                            
                                            <td class="uk-text-center">
                                                <a href="components_tables_examples.html#"><i class="md-icon material-icons">&#xE254;</i></a>
                                                <a href="components_tables_examples.html#"><i class="md-icon material-icons">&#xE88F;</i></a>
                                            </td>
                                            
                                           
                                              
                                        </tr>
                                         @endforeach
                                    </tbody>
                                    
                             </table>
           {!! (new Landish\Pagination\UIKit($data->appends(old())))->render() !!}
     </div>
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