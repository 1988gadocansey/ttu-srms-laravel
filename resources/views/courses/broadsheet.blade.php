@extends('layouts.app')

 
@section('style')
 
@endsection
 @section('content')
 @inject('sys', 'App\Http\Controllers\SystemController')
<div class="md-card-content">
<div style="text-align: center;display: none" class="uk-alert uk-alert-success" data-uk-alert="">

    </div>



    <div style="text-align: center;display: none" class="uk-alert uk-alert-danger" data-uk-alert="">

    </div>

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
  
 <div style="">
     <div class="uk-margin-bottom" style="margin-left:900px" >
          
         
         <a href="#" class="md-btn md-btn-small md-btn-success uk-margin-right" id="printTable">Print Table</a>
        <!--  <a href="#" class="md-btn md-btn-small md-btn-success uk-margin-right" id="">Import from Excel</a>
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
                         <a  href="{{url('/report/sms')}}"  onclick="return confirm('This will send bulk grades score notification to all parents')"  title="sent bulk admission notification to applicants"> <i   title="click to sent bulk admission notification to applicants"  class="material-icons md-36 uk-text-success"   >phonelink_ring</i></a>

                       <a href="{{url('/report/broadsheet')}}" ><i   title="refresh this page" class="uk-icon-refresh uk-icon-medium "></i></a>
                           
                            
                           
     </div>
 </div>
  
 <div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content">
            
              <form action=" "  method="get" accept-charset="utf-8" novalidate id="group">
                   {!!  csrf_field()  !!}
                    <div class="uk-grid" data-uk-grid-margin="">

                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                    {!! Form::select('program', 
                                (['' => 'All programs'] +$program ), 
                                  old("program",""),
                                    ['class' => 'md-input parent','id'=>"parent",'placeholder'=>'select program'] )  !!}
                         </div>
                        </div>
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                    {!! Form::select('level', 
                                (['' => 'All levels'] +$level ), 
                                  old("level",""),
                                    ['class' => 'md-input parent','id'=>"parent",'placeholder'=>'select level'] )  !!}
                         </div>
                        </div>
                       
                         <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                 
                                              {!!  Form::select('semester', array('1'=>'1st sem','2'=>'2nd sem','3' => '3rd sem'), null, ['placeholder' => 'select semester','id'=>'parent','class'=>'md-input parent'],old("semester","")); !!}
                          
                            </div>
                        </div>
                        

                        
                         <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">                            
                                <input type="text" style=" "   name="search"  class="md-input" placeholder="search by course name or course code">
                            </div>
                        </div>
                         
                         
                        
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                
                                {!!  Form::select('by', array('COURSE_CODE'=>'Course Code','COURSE_NAME'=>'Course Name' ), null, ['placeholder' => 'select criteria','class'=>'md-input'],old("by","")); !!}
                          
                            </div>
                        </div>
                        
                        
                    
                    </div> 
                         <div  align='center'>
                            
                            <button class="md-btn  md-btn-small md-btn-success uk-margin-small-top" type="submit"><i class="material-icons">search</i></button> 
                             
                        </div>
                   
                </form> 
        </div>
    </div>
 </div>
 <h5 class="heading_b">Broadsheet</h5>
 @if(Request::isMethod('post'))
 <div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content">
   <div class="uk-overflow-container" id='print'>
        
         <table border='1' class="uk-table uk-table-hover uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id="ts_pager_filter"> 
               <thead>
                 <tr>
                     <th class="filter-false remove sorter-false"  >NO</th>
                      <th>STUDENT</th>
                      <?php   
                      
                                   $count=0;
                                   $mark=array();
                      ?>
                       @foreach($headers as $header=> $td) 
                     
                       <th> {{  strtoupper(@$td['courseCode'])	 }}</th>
                       
                       @endforeach
                </tr>
             </thead>
      <tbody>
          
      
          
       
          
            @foreach($student as $stud=> $pupil)  <?php  $count++;?>
            <tr>
                <td><?php $students[]=$pupil->indexNo;
                                                 \Session::put('students', $students);echo $count?></td>
            <td> {{  strtoupper(@$pupil->student->name)	 }}</td>
            
            <?php
               $a=$pupil->indexNo;
               for($i=0;$i<count($course);$i++){  
                                           
                                           // print_r($courseArray); "<td>$courseArray[$i]</td>";
                                        print_r("<td>".  round(@$sys->getCourseGrade($course[$i],$years,$term,$a)). "  -   " .$sys->getGradeScore(@$sys->getCourseGrade($course[$i],$years,$term,$a),'WASSCE')."</td>");

               }            
               ?>
            
            
            
            </tr>
                       
            
            @endforeach
            
       
      </tbody>
        
 </table>
       
      
       
       
     </div>
     </div>
<div class="md-fab-wrapper">
        <a class="md-fab md-fab-small md-fab-accent md-fab-wave" href="{{url('/teachers/subject/create')}}"  >
            <i class="material-icons md-18">&#xE145;</i>
        </a>
    </div>
 </div>
</div>
  @endif
@endsection
@section('js')
 
 <script type="text/javascript">
      
 

</script>
  <script src="{!! url('public/assets/js/select2.full.min.js') !!}"></script>
<script>
                    $(document).ready(function () {
            $('.selects').select2({width: "resolve"});
            });</script>
 

 
@endsection