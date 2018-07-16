@extends('layouts.app')

 
@section('style')
  <!-- additional styles for plugins -->
        <!-- weather icons -->
        <link rel="stylesheet" href="public/assets/plugins/weather-icons/css/weather-icons.min.css" media="all">
        <!-- metrics graphics (charts) -->
        <link rel="stylesheet" href="public/assets/plugins/metrics-graphics/dist/metricsgraphics.css">
        <!-- chartist -->
        <link rel="stylesheet" href="public/assets/plugins/chartist/dist/chartist.min.css">
    
@endsection
 @section('content')
 <div class="md-card-content">

    @if(Session::has('success'))
            <div style="text-align: center" class="uk-alert uk-alert-success" data-uk-alert="">
                {!! Session::get('success') !!}
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
   
   @inject('sys', 'App\Http\Controllers\SystemController')
  
      
         <div class="uk-grid uk-grid-width-large-1-4 uk-grid-width-medium-1-2 uk-grid-medium uk-sortable ">
                 <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class=""><i class="sidebar-menu-icon material-icons md-36">access_time</i></span></div>
                            <span class="uk-text-bold uk-text-small">Last Visit</span>
                            <h5 class="uk-margin-remove"><span class="uk-text-small uk-text-success"> {{$lastVisit}}</span></h5>
                        </div>
                    </div>
                </div>
                  @if( @Auth::user()->department=='top' || @Auth::user()->department=='Rector'  || @Auth::user()->department=='Tpmid' || @Auth::user()->department=='Tptop')
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class=" "><i class="sidebar-menu-icon material-icons md-36">account_balance</i></span></div>
                            <span class="uk-text-muted uk-text-small">Owing - <span class="uk-text-bold uk-text-danger ">GH{{$owing}}</span></span><br/>
                            <span class="uk-text-muted uk-text-small">Paid - <span class="uk-text-bold uk-text-success ">GH{{$paid}}</span></span>
                        </div>
                    </div>
                </div>
                @endif
                @if( @Auth::user()->department=='Planning' || @Auth::user()->role=='Support')
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class=" "><i class="sidebar-menu-icon material-icons md-36">account_balance</i></span></div>
                            <span class="uk-text-muted uk-text-small">Total Fees Owed</span><br/>
                            <span class="uk-text-muted uk-text-small"><span class="uk-text-bold uk-text-success ">GH{{$owing}}</span></span>
                        </div>
                    </div>
                </div>
                @endif
             <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class=""><i class="sidebar-menu-icon material-icons md-36">event_note</i></span></div>
                            <span class="uk-text-muted uk-text-small">Total Students - <span class="uk-text-bold uk-text-primary ">{{$total}} </span></span><br/>
                            <span class="uk-text-muted uk-text-small">Registered - <span class="uk-text-bold uk-text-success ">{!!$totalRegistered!!} </span></span>
                        </div>
                    </div>
                </div>
             
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                         <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class=""><i class="sidebar-menu-icon material-icons md-36">event_note</i></span></div>
                            <span class="uk-text-muted uk-text-small">Academic Calender</span>
                            <h5 class="uk-margin-remove"><span class="uk-text-small uk-text-success "> Semester {{$sem}} : Year {{$year}}</span></h5>
                        </div>
                    </div>
                </div>
                  @if( @Auth::user()->role=='Lecturer' || @Auth::user()->role=='HOD')
             
               <div>
                    <div class="md-card">
                        <div class="md-card-content">
                         <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class=""><i class="sidebar-menu-icon material-icons md-36">event_note</i></span></div>
                            <span class="uk-text-muted uk-text-small">Class Size</span>
                            <h5 class="uk-margin-remove"><span class="uk-text-small uk-text-success "> Your Class Size = {{$register}}  </span></h5>
                        </div>
                    </div>
                </div>
                @endif
            </div>

           
            <div class="uk-grid uk-grid-width-small-1-2 uk-grid-width-large-1-3 uk-grid-width-xlarge-1-5 uk-text-center uk-sortable sortable-handler" id="dashboard_sortable_cards" data-uk-sortable data-uk-grid-margin>
              @if(@Auth::user()->department=='Planning' || @Auth::user()->role=='HOD' || @Auth::user()->department=='top' || @Auth::user()->department=='Rector' || @Auth::user()->role=='Support')        
                <div>
                    <div class="md-card md-card-hover md-card-overlay">
                        <div class="md-card-content">
                            <a target="_" href='{{url("http://records.ttuportal.com/course_registration")}}'>  <img src="{{url('public/assets/img/dashboard/registration.png')}}"/></a>
                        </div>
                        <div class="md-card-overlay-content">
                            <div class="uk-clearfix md-card-overlay-header">
                                <i class="md-icon material-icons md-card-overlay-toggler">&#xE5D4;</i>
                                <h3 class="uk-text-center uk-text-upper">
                                    REGISTER STUDENTS
                                </h3>
                            </div>
                           Assist students to register
                        </div>
                    </div>
                </div>
                      @endif
                      @if(@Auth::user()->department=='Planning' || @Auth::user()->role=='Support')        
                <div>
                    <div class="md-card md-card-hover md-card-overlay">
                        <div class="md-card-content">
                            <a  href='{{url("/students")}}'>  <img src="{{url('public/assets/img/dashboard/classlist.png')}}"/></a>
                        </div>
                        <div class="md-card-overlay-content">
                            <div class="uk-clearfix md-card-overlay-header">
                                <i class="md-icon material-icons md-card-overlay-toggler">&#xE5D4;</i>
                                <h3 class="uk-text-center uk-text-upper">
                                    CLASS LIST
                                </h3>
                            </div>
                           View students
                        </div>
                    </div>
                </div>
                      @endif
                      @if(@Auth::user()->department=='Planning')        
                <div>
                    <div class="md-card md-card-hover md-card-overlay">
                        <div class="md-card-content">
                            <a  href='{{url("/upload_marks")}}'>  <img src="{{url('public/assets/img/dashboard/classgroup.png')}}"/></a>
                        </div>
                        <div class="md-card-overlay-content">
                            <div class="uk-clearfix md-card-overlay-header">
                                <i class="md-icon material-icons md-card-overlay-toggler">&#xE5D4;</i>
                                <h3 class="uk-text-center uk-text-upper">
                                    STAFF DIRECTORY
                                </h3>
                            </div>
                           View students
                        </div>
                    </div>
                </div>
                      @endif
                 
                    @if( @Auth::user()->department=='top' ||  @Auth::user()->role=='Lecturer' ||  @Auth::user()->role=='HOD')
                <div>
                    <div class="md-card md-card-hover md-card-overlay">
                        <div class="md-card-content">
                            <a  href='{{url("/upload_marks")}}'>  <img src="{{url('public/assets/img/dashboard/results.png')}}"/></a>
                        </div>
                        <div class="md-card-overlay-content">
                            <div class="uk-clearfix md-card-overlay-header">
                                <i class="md-icon material-icons md-card-overlay-toggler">&#xE5D4;</i>
                                <h3 class="uk-text-center uk-text-upper">
                                   UPLOAD RESULTS
                                </h3>
                            </div>
                            <p>Upload semester results here.</p>
                            <button class="md-btn md-btn-primary">More</button>
                        </div>
                    </div>
                </div>
                    @endif
                    @if( @Auth::user()->department=='top' ||  @Auth::user()->role=='Lecturer' ||  @Auth::user()->role=='HOD' || @Auth::user()->department=='Tpmid' || @Auth::user()->department=='Tptop')
                <div>
                    <div class="md-card md-card-hover md-card-overlay">
                        <div class="md-card-content">
                            <a  href='{{url("/download_registered")}}'>  <img src="{{url('public/assets/img/dashboard/downloadexcel.png')}}"/></a>
                        </div>
                        <div class="md-card-overlay-content">
                            <div class="uk-clearfix md-card-overlay-header">
                                <i class="md-icon material-icons md-card-overlay-toggler">&#xE5D4;</i>
                                <h3 class="uk-text-center uk-text-upper">
                                   DOWNLOAD EXCEL
                                </h3>
                            </div>
                            <p>Download student list</p>
                            <button class="md-btn md-btn-primary">More</button>
                        </div>
                    </div>
                </div>
                    @endif
                 @if( @Auth::user()->department=='top' ||  @Auth::user()->role=='HOD' || @Auth::user()->department=='Rector' || @Auth::user()->department=='Tpmid' || @Auth::user()->department=='Tptop' || @Auth::user()->role=='Support')
                
                <div>
                    <div class="md-card md-card-hover md-card-overlay">
                        <div class="md-card-content">
                            <a  href='{{url("/mounted_view")}}'>  <img src="{{url('public/assets/img/dashboard/uploadnotes.png')}}"/></a>
                        </div>
                        <div class="md-card-overlay-content uk-badge-success">
                            <div class="uk-clearfix md-card-overlay-header">
                                <i class="md-icon material-icons md-card-overlay-toggler">&#xE5D4;</i>
                                <h3 class="uk-text-center uk-text-upper uk-text-red">
                                  MOUNTED COURSES
                                </h3>
                            </div>
                           <p>View mounted courses here</p>
                        </div>
                    </div>
                </div>
                 @endif
                 @if( @Auth::user()->department=='top' ||  @Auth::user()->role=='HOD' || @Auth::user()->department=='Rector' || @Auth::user()->department=='Tpmid' || @Auth::user()->department=='Tptop' || @Auth::user()->department=='Planning' || @Auth::user()->role=='Support')
                
                <div>
                    <div class="md-card md-card-hover md-card-overlay">
                        <div class="md-card-content">
                            <a  href='{{url("/download_results")}}'>  <img src="{{url('public/assets/img/dashboard/nabptex.png')}}"/></a>
                        </div>
                        <div class="md-card-overlay-content uk-badge-success">
                            <div class="uk-clearfix md-card-overlay-header">
                                <i class="md-icon material-icons md-card-overlay-toggler">&#xE5D4;</i>
                                <h3 class="uk-text-center uk-text-upper uk-text-red">
                                  NABPTEX BROADSHEET
                                </h3>
                            </div>
                           <p>NABPTEX broadsheet</p>
                        </div>
                    </div>
                </div>
                 @endif
                 @if( @Auth::user()->department=='top' ||  @Auth::user()->role=='HOD' || @Auth::user()->department=='Rector' || @Auth::user()->department=='Tpmid' || @Auth::user()->department=='Tptop' )
                
                <div>
                    <div class="md-card md-card-hover md-card-overlay">
                        <div class="md-card-content">
                            <a  href='{{url("/broadsheet/noticeboard")}}'>  <img src="{{url('public/assets/img/dashboard/academicboard.png')}}"/></a>
                        </div>
                        <div class="md-card-overlay-content uk-badge-success">
                            <div class="uk-clearfix md-card-overlay-header">
                                <i class="md-icon material-icons md-card-overlay-toggler">&#xE5D4;</i>
                                <h3 class="uk-text-center uk-text-upper uk-text-red">
                                  GPA ACADEMIC BOARD
                                </h3>
                            </div>
                           <p>Academic board report</p>
                        </div>
                    </div>
                </div>
                 @endif
                  @if( @Auth::user()->role=='Lecturer'  || @Auth::user()->role=='HOD')
                <div>
                     <div class="md-card md-card-hover md-card-overlay">
                        <div class="md-card-content">
                            <a  >  <img src="{{url('public/assets/img/dashboard/uploadvideos.png')}}"/></a>
                        </div>
                        <div class="md-card-overlay-content">
                            <div class="uk-clearfix md-card-overlay-header">
                                <i class="md-icon material-icons md-card-overlay-toggler">&#xE5D4;</i>
                                <h3 class="uk-text-center uk-text-upper">
                                    UPLOAD VIDEOS
                                </h3>
                            </div>
                           Upload lecture videos
                        </div>
                    </div>
                </div>
                 
                  
                <div>
                     <div class="md-card md-card-hover md-card-overlay">
                        <div class="md-card-content">
                            <a>  <img src="{{url('public/assets/img/dashboard/uploadnotes.png')}}"/></a>
                        </div>
                        <div class="md-card-overlay-content">
                            <div class="uk-clearfix md-card-overlay-header">
                                <i class="md-icon material-icons md-card-overlay-toggler">&#xE5D4;</i>
                                <h3 class="uk-text-center uk-text-upper">
                                   UPLOAD NOTES
                                </h3>
                            </div>
                           Upload lecture notes
                        </div>
                    </div>
                </div>
                  
                   
                @endif
                
                <div>
                    <div class="md-card md-card-hover md-card-overlay">
                        <div class="md-card-content">
                            <a  href='{{url("/transcript")}}'>  <img src="{{url('public/assets/img/dashboard/transcript.png')}}"/></a>
                        </div>
                        <div class="md-card-overlay-content">
                            <div class="uk-clearfix md-card-overlay-header">
                                <i class="md-icon material-icons md-card-overlay-toggler">&#xE5D4;</i>
                                <h3 class="uk-text-center uk-text-upper">
                                    TRANSCRIPTS
                                </h3>
                            </div>
                            Check performance of students
                        </div>
                    </div>
                </div>
                 
                
                   @if( @Auth::user()->department=='top')  
                        <div>
                            <div class="md-card md-card-hover md-card-overlay">
                                <div class="md-card-content">
                                    <div class="epc_chart" data-percent="37" data-bar-color="#607d8b">
                                        <span class="epc_chart_icon"><i class="material-icons">&#xE7FE;</i></span>
                                    </div>
                                </div>
                                <div class="md-card-overlay-content">
                                    <div class="uk-clearfix md-card-overlay-header">
                                        <i class="md-icon material-icons md-card-overlay-toggler">&#xE5D4;</i>
                                        <h3>
                                          CREATE USERS
                                        </h3>
                                    </div>
                                    Create a new user account
                                </div>
                            </div>
                        </div>
                   @endif
            </div> 
         
        
            <!-- tasks -->
            <div class="uk-grid" data-uk-grid-margin data-uk-grid-match="{target:'.md-card-content'}">
                <div class="uk-width-medium-1-2">
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-overflow-container">
                                <table class="uk-table">
                                    <thead>
                                        <tr style="background-color: #697c9a;">
                                            <th colspan="5" style="color: #ffffff;" class="uk-text-nowrap" >STUDENT POPULATION BY LEVEL</th>
                                                                                        
                                        </tr>
                                        <tr style="border-bottom-style:solid; border-bottom-color:#ffffff">
                                            <th class="uk-text-nowrap"><strong>Level</strong></th>
                                            <th class="uk-text-nowrap"><strong>M</strong></th>
                                            <th class="uk-text-nowrap"><strong>F</strong></th>
                                            
                                            <th class="uk-text-nowrap"><strong>Total</strong></th>
                                            <th class="uk-text-nowrap"><strong>Reg</strong></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       
                                       <tr class="uk-table-middle" style="background-color: #e0e7f5;">
                                            <td>Non Tertiary Level 100 </td>
                                            <td class=''><?php $am=$sys->getStudentsTotalPerLevelAllGen('100NT','Male'); echo $am;?></td>
                                            <td class=''><?php $af=$sys->getStudentsTotalPerLevelAllGen('100NT','Female'); echo $af;?></td>
                                            
                                            <td class=''><?php $at=$sys->getStudentsTotalPerLevelAll('100NT'); echo $at;?></td>
                                            <td class=''><?php $ar=$sys->getStudentsTotalPerLevelAllRegistered('100NT'); echo $ar;?></td>
                                        </tr>
                                        <tr class="uk-table-middle">
                                            <td>Non Tertiary Level 200</td>
                                            <td class=''><?php $bm=$sys->getStudentsTotalPerLevelAllGen('200NT','Male'); echo $bm;?></td>
                                            <td class=''><?php $bf=$sys->getStudentsTotalPerLevelAllGen('200NT','Female'); echo $bf;?></td>
                                            
                                            <td class=''><?php $bt=$sys->getStudentsTotalPerLevelAll('200NT'); echo $bt;?></td>
                                            <td class=''><?php $br=$sys->getStudentsTotalPerLevelAllRegistered('200NT'); echo $br;?></td>
                                        </tr>
                                        <tr class="uk-table-middle" style="background-color: #e0e7f5;">
                                            <td>Level 100 HND</td>
                                            <td class=''><?php $cm=$sys->getStudentsTotalPerLevelAllGen('100H','Male'); echo $cm;?></td>
                                            <td class=''><?php $cf=$sys->getStudentsTotalPerLevelAllGen('100H','Female'); echo $cf;?></td>
                                            
                                            <td class=''><?php $ct=$sys->getStudentsTotalPerLevelAll('100H'); echo $ct;?></td>
                                            <td class=''><?php $cr=$sys->getStudentsTotalPerLevelAllRegistered('100H'); echo $cr;?></td>
                                        </tr>
                                      <tr class="uk-table-middle">
                                            <td>Level 200 HND</td>
                                            <td class=''><?php $dm=$sys->getStudentsTotalPerLevelAllGen('200H','Male'); echo $dm;?></td>
                                            <td class=''><?php $df=$sys->getStudentsTotalPerLevelAllGen('200H','Female'); echo $df;?></td>
                                            
                                            <td class=''><?php $dt=$sys->getStudentsTotalPerLevelAll('200H'); echo $dt;?></td>
                                            <td class=''><?php $dr=$sys->getStudentsTotalPerLevelAllRegistered('200H'); echo $dr;?></td>
                                        </tr>
                                      <tr class="uk-table-middle" style="background-color: #e0e7f5;">
                                            <td>Level 300 HND</td>
                                            <td class=''><?php $em=$sys->getStudentsTotalPerLevelAllGen('300H','Male'); echo $em;?></td>
                                            <td class=''><?php $ef=$sys->getStudentsTotalPerLevelAllGen('300H','Female'); echo $ef;?></td>
                                            
                                            <td class=''><?php $et=$sys->getStudentsTotalPerLevelAll('300H'); echo $et;?></td>
                                            <td class=''><?php $er=$sys->getStudentsTotalPerLevelAllRegistered('300H'); echo $er;?></td>
                                        </tr>
                                   
                                         <tr class="uk-table-middle">
                                            <td>Level 100 BTECH TOP UP</td>
                                            <td class=''><?php $fm=$sys->getStudentsTotalPerLevelAllGen('100BTT','Male'); echo $fm;?></td>
                                            <td class=''><?php $ff=$sys->getStudentsTotalPerLevelAllGen('100BTT','Female'); echo $ff;?></td>
                                            
                                            <td class=''><?php $ft=$sys->getStudentsTotalPerLevelAll('100BTT'); echo $ft;?></td>
                                            <td class=''><?php $fr=$sys->getStudentsTotalPerLevelAllRegistered('100BTT'); echo $fr;?></td>
                                        </tr>
                                          <tr class="uk-table-middle" style="background-color: #e0e7f5;">
                                            <td>Level 200 BTECH TOP UP</td>
                                            <td class=''><?php $gm=$sys->getStudentsTotalPerLevelAllGen('200BTT','Male'); echo $gm;?></td>
                                            <td class=''><?php $gf=$sys->getStudentsTotalPerLevelAllGen('200BTT','Female'); echo $gf;?></td>
                                            
                                            <td class=''><?php $gt=$sys->getStudentsTotalPerLevelAll('200BTT'); echo $gt;?></td>
                                            <td class=''><?php $gr=$sys->getStudentsTotalPerLevelAllRegistered('200BTT'); echo $gr;?></td>
                                        </tr>
                                        <tr class="uk-table-middle">
                                            <td>Level 100 MASTERS</td>
                                            <td class='grey'><?php $hm=$sys->getStudentsTotalPerLevelAllGen('500MT','Male'); echo $hm;?></td>
                                            <td class=''><?php $hf=$sys->getStudentsTotalPerLevelAllGen('500MT','Female'); echo $hf;?></td>
                                            
                                            <td class=''><?php $ht=$sys->getStudentsTotalPerLevelAll('500MT'); echo $ht;?></td>
                                            <td class=''><?php $hr=$sys->getStudentsTotalPerLevelAllRegistered('500MT'); echo $hr;?></td>
                                        </tr>
                                        <tr class="uk-table-middle" style="background-color: #e0e7f5;">
                                            <td>Level 200 MASTERS</td>
                                            <td class=''><?php $im=$sys->getStudentsTotalPerLevelAllGen('600MT','Male'); echo $im;?></td>
                                            <td class=''><?php $if=$sys->getStudentsTotalPerLevelAllGen('600MT','Female'); echo $if;?></td>
                                            
                                            <td class=''><?php $it=$sys->getStudentsTotalPerLevelAll('600MT'); echo $it;?></td>
                                            <td class=''><?php $ir=$sys->getStudentsTotalPerLevelAllRegistered('600MT'); echo $ir;?></td>
                                        </tr>
                                        
                                        
                                        <tr>
                                            <td><strong>Total</strong></td>
                                            <td><strong><?php echo $am+$bm+$cm+$dm+$em+$fm+$gm+$hm+$im?>
                                            </strong></td>
                                            <td><strong><?php echo $af+$bf+$cf+$df+$ef+$ff+$gf+$hf+$if?>
                                            </strong></td>
                                            
                                            <td><strong><?php echo $at+$bt+$ct+$dt+$et+$ft+$gt+$ht+$it?>
                                            </strong></td> 
                                            <td><strong><?php echo $ar+$br+$cr+$dr+$er+$fr+$gr+$hr+$ir?>
                                            </strong></td>                                          
                                        </tr>
                                        
                                    </tbody>
                                      
                                    </tbody>
                                </table>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="uk-width-medium-1-2">
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-overflow-container">
                                <table class="uk-table">
                                    <thead>
                                        <tr style="background-color: #697c9a;">
                                            <th colspan="5" style="color: #ffffff;" class="uk-text-nowrap" >OUR 10 BEST STUDENTS || HND LEVEL 300 || PROVISIONAL</th>
                                                                                        
                                        </tr>
                                        <tr style="border-bottom-style:solid; border-bottom-color:#ffffff">
                                            <th class="uk-text-nowrap"><strong>No</strong></th>
                                            <th class="uk-text-nowrap"><strong>IndexNo</strong></th>
                                            
                                            <th class="uk-text-nowrap"><strong>Program</strong></th>
                                            <th class="uk-text-nowrap"><strong>CGPA</strong></th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $eku = 0;
                                        while ($eku < 10 ){

                                        if ($eku == 0 || $eku == 2 || $eku == 4 || $eku == 6 || $eku == 8) {
                                         ?>
                                         <tr class="uk-table-middle" style="background-color: #e0e7f5;">
                                            
                                            <?php
                                        }
                                        else {
                                         ?>
                                         <tr class="uk-table-middle" >
                                            
                                            <?php   
                                        }
                                        ?>
                                            <td><?php echo $eku+1;?>&nbsp;&nbsp;</td>
                                            <td><?php $am=$sys->getStudentsHighestCGPA_HND($eku);
                                            $ind=$am[0]->INDEXNO; echo $ind;?></td>
                                            <td><?php $am=$sys->getStudentsHighestCGPA_HND($eku);
                                            $ind=$am[0]->PROGRAMME; echo $ind;?></td>
                                            <td><?php $am=$sys->getStudentsHighestCGPA_HND($eku);
                                            $ind=$am[0]->CGPA; echo $ind;?></td>
                                            
                                        </tr>
                                        <?php
                                        $eku++;
                                    }
                                        ?>

                                        
                                    </tbody>
                                      
                                    </tbody>
                                </table>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="uk-width-medium-1-2">
                    <div class="md-card">
                        <div class="md-card-content">
                            <h3 class="heading_a uk-margin-bottom">Statistics</h3>
                            <div id="ct-chart" class="chartist"></div>
                        </div>
                    </div>
                </div>
            </div>

          
@endsection
@section('js')
  <!-- d3 -->
        <script src="public/assets/plugins/d3/d3.min.js"></script>
        <!-- metrics graphics (charts) -->
        <script src="public/assets/plugins/metrics-graphics/dist/metricsgraphics.min.js"></script>
        <!-- chartist (charts) -->
        <script src="public/assets/plugins/chartist/dist/chartist.min.js"></script>
        <!-- maplace (google maps) -->
          <script src="public/assets/plugins/maplace-js/dist/maplace.min.js"></script>
        <!-- peity (small charts) -->
        <script src="public/assets/plugins/peity/jquery.peity.min.js"></script>
        <!-- easy-pie-chart (circular statistics) -->
        <script src="public/assets/plugins/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js"></script>
        <!-- countUp -->
        <script src="public/assets/plugins/countUp.js/dist/countUp.min.js"></script>
        <!-- handlebars.js -->
        <script src="public/assets/plugins/handlebars/handlebars.min.js"></script>
        <script src="public/assets/js/custom/handlebars_helpers.min.js"></script>
        <!-- CLNDR -->
        <script src="public/assets/plugins/clndr/clndr.min.js"></script>
        <!-- fitvids -->
        <script src="public/assets/plugins/fitvids/jquery.fitvids.js"></script>

        <!--  dashbord functions -->
        <script src="public/assets/js/pages/dashboard.min.js"></script>
 
@endsection