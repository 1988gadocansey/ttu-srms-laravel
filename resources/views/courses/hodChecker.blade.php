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
  <form  action="{{url('/check/course')}}"  method="post" accept-charset="utf-8" >
 
 <div style="">
     <div class="uk-margin-bottom" style="margin-left:750px" >
          
         <a href="#" class="md-btn md-btn-small md-btn-success uk-margin-right" id="printTable">Print Table</a>
    <!--      <a href="#" class="md-btn md-btn-small md-btn-success uk-margin-right" id="">Import from Excel</a>
     -->    
         <div class="uk-button-dropdown" data-uk-dropdown="{mode:'click'}">
             <button class="md-btn md-btn-small md-btn-success"> columns <i class="uk-icon-caret-down"></i></button>
             <div class="uk-dropdown">
                 <ul class="uk-nav uk-nav-dropdown" id="columnSelector"></ul>
             </div>
         </div>
         

                       
                          
                           
         <div style="margin-top: -5px" class="uk-button-dropdown" data-uk-dropdown="{mode:'click'}">
                                <button class="md-btn md-btn-small md-btn-success uk-margin-small-top">Export <i class="uk-icon-caret-down"></i></button>
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
                       
                           
                            
                                                   
                                  <i title="click to print" onclick="javascript:printDiv('print')" class="material-icons md-36 uk-text-success"   >print</i>
                   
                          
                   <div class="uk-margin-small-top">
                                
                               <button class="md-btn  md-btn-large md-btn-primary uk-margin-small-top" type="submit">Click to approve this courses for registration</button> 
                           
                          
                    </div>   {!!  csrf_field()  !!}
                    
                    </form>
     </div>
 </div>
           
 <div class="uk-width-xLarge-1-1">
    
     <h5 class="heading_b">Mounted Courses</h5>
 <div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content">
            <div class="uk-width-medium-1-5">
                
                        </div>
   <div class="uk-overflow-container" id='print'>
         <center><span class="uk-text-success uk-text-bold">{!! $data->total()!!} Records</span></center>
                <table class="uk-table uk-table-hover uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id="ts_pager_filter"> 
               <thead>
                 <tr>
                     <th class="filter-false remove sorter-false" data-priority="6">NO</th>
                      <th>COURSE</th>
                     <th  style="text-align:center">CODE</th>
                     <th>PROGRAMME</th> 
                     <th style="text-align:center">CREDIT</th>

                     <th style="text-align:">YEAR</th>
                     <th style="text-align:center">SEMESTER</th>
                     <th style="text-align:center">ACADEMIC YEAR</th>
                     <th style="text-align:center">TYPE</th>
                     <th style="text-align:">LECTURER</th> 
                        @if(@\Auth::user()->role=='HOD'|| @\Auth::user()->role=='Admin'|| @\Auth::user()->department=='top')
                     <th  class="filter-false remove sorter-false uk-text-center" colspan="2" data-priority="1">ACTION</th>   
                       @endif
                </tr>
             </thead>
      <tbody>
                                        
                                         @foreach($data as $index=> $row) 
                                         
                                        
                                        
                                         
                                        <tr align="">
                                            <td> {{ $data->perPage()*($data->currentPage()-1)+($index+1) }} </td>
                                            <td> {{ @$row->course->COURSE_NAME }}</td>
                                            <td> {{ @$row->course->COURSE_CODE	 }}</td>
                                            <td> {{ @$sys->getProgramName($row->PROGRAMME)	 }}</td>
                                            <td> {{ @$row->COURSE_CREDIT	 }}</td>
                                            <td> {{ @$row->levels->slug }}</td>
                                           <td> {{ @$row->COURSE_SEMESTER }}</td>
                                           <td> {{ @$row->COURSE_YEAR }}</td>
                                           <td> {{ @$row->COURSE_TYPE }}</td>
                                           <td> {{ @$row->lecturer->fullName }}</td>
                                           @if(@\Auth::user()->role=='HOD' || @\Auth::user()->role=='Admin'|| @\Auth::user()->department=='top'|| @\Auth::user()->role=='Support')
                                            <td>
                                                 <a href='{{url("/mounted/$row->ID/edit")}}'><i  title="click to edit mounted course" class="md-icon material-icons">&#xE254;</i></a>
                                              {!!Form::open(['action' =>['CourseController@destroy_mounted', 'id'=>$row->ID], 'method' => 'DELETE','name'=>'myform' ,'style' => 'display: inline;'])  !!}

                                                    <button type="submit" onclick="return confirm('Are you sure you want to delete   {{ @$row->course->programme->PROGRAMME	 }}-{!! @$row->course->COURSE_NAME !!}')" class="md-btn  md-btn-danger md-btn-small   md-btn-wave-light waves-effect waves-button waves-light" ><i  class="sidebar-menu-icon material-icons md-18">delete</i></button>
                                                        
                                                     {!! Form::close() !!}

                                            </td>
                                            @endif
                                              
                                        </tr>
                                         @endforeach
                                    </tbody>
                                    
                             </table>
           {!! (new Landish\Pagination\UIKit($data->appends(old())))->render() !!}
     </div>
     </div>
<div class="md-fab-wrapper">
    <a class="md-fab md-fab-small md-fab-accent md-fab-wave" title="click to mount more courses" href="{!! url('/mount_course') !!}">
            <i class="material-icons md-18">&#xE145;</i>
        </a>
    </div>
 </div>
</div>
  
@endsection
@section('js')
 <script type="text/javascript">
      
$(document).ready(function(){
 
$(".checker").on('change',function(e){
 
   $("#checker").submit();
 
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
  </form>
    @endsection