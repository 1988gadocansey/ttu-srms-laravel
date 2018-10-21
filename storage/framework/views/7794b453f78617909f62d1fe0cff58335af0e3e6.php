 
<?php $__env->startSection('style'); ?>
  <!-- additional styles for plugins -->
        <!-- weather icons -->
        <link rel="stylesheet" href="public/assets/plugins/weather-icons/css/weather-icons.min.css" media="all">
        <!-- metrics graphics (charts) -->
        <link rel="stylesheet" href="public/assets/plugins/metrics-graphics/dist/metricsgraphics.css">
        <!-- chartist -->
        <link rel="stylesheet" href="public/assets/plugins/chartist/dist/chartist.min.css">
    
<?php $__env->stopSection(); ?>
 <?php $__env->startSection('content'); ?>
 <div class="md-card-content">

    <?php if(Session::has('success')): ?>
            <div style="text-align: center" class="uk-alert uk-alert-success" data-uk-alert="">
                <?php echo Session::get('success'); ?>

            </div>
 <?php endif; ?>


    <?php if(count($errors) > 0): ?>


    <div class="uk-alert uk-alert-danger  uk-alert-close" style="background-color: red;color: white" data-uk-alert="">

        <ul>
            <?php foreach($errors->all() as $error): ?>
            <li><?php echo $error; ?> </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <?php endif; ?>


</div>
   
   <?php $sys = app('App\Http\Controllers\SystemController'); ?>
  
      
         <div class="uk-grid uk-grid-width-large-1-4 uk-grid-width-medium-1-2 uk-grid-medium">
                 <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class=""><i class="sidebar-menu-icon material-icons md-36">access_time</i></span></div>
                            <span class="uk-text-bold uk-text-small">Last Visit</span>
                            <h5 class="uk-margin-remove"><span class="uk-text-small uk-text-success"> <?php echo e($lastVisit); ?></span></h5>
                        </div>
                    </div>
                </div>
                  <?php if( @Auth::user()->department=='top' || @Auth::user()->department=='Rector'  || @Auth::user()->department=='Tpmid' || @Auth::user()->department=='Tptop'): ?>
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class=" "><i class="sidebar-menu-icon material-icons md-36">account_balance</i></span></div>
                            <span class="uk-text-muted uk-text-small">Owing - <span class="uk-text-bold uk-text-danger ">GH<?php echo e($owing); ?></span></span><br/>
                            <span class="uk-text-muted uk-text-small">Paid - <span class="uk-text-bold uk-text-success ">GH<?php echo e($paid); ?></span></span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if( @Auth::user()->department=='Planning' || @Auth::user()->role=='Support'): ?>
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class=" "><i class="sidebar-menu-icon material-icons md-36">account_balance</i></span></div>
                            <span class="uk-text-muted uk-text-small">Total Fees Owed</span><br/>
                            <span class="uk-text-muted uk-text-small"><span class="uk-text-bold uk-text-success ">GH<?php echo e($owing); ?></span></span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
             <div>
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class=""><i class="sidebar-menu-icon material-icons md-36">event_note</i></span></div>
                            <span class="uk-text-muted uk-text-small">Total Students - <span class="uk-text-bold uk-text-primary "><?php echo e($total); ?> </span></span><br/>
                            <span class="uk-text-muted uk-text-small">Registered - <span class="uk-text-bold uk-text-success "><?php echo $totalRegistered; ?> </span></span>
                        </div>
                    </div>
                </div>
             
                <div>
                    <div class="md-card">
                        <div class="md-card-content">
                         <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class=""><i class="sidebar-menu-icon material-icons md-36">event_note</i></span></div>
                            <span class="uk-text-muted uk-text-small">Academic Calender</span>
                            <h5 class="uk-margin-remove"><span class="uk-text-small uk-text-success "> Semester <?php echo e($sem); ?> : Year <?php echo e($year); ?></span></h5>
                        </div>
                    </div>
                </div>
                  <?php if( @Auth::user()->role=='Lecturer' || @Auth::user()->role=='HOD'): ?>
             
               <div>
                    <div class="md-card">
                        <div class="md-card-content">
                         <div class="uk-float-right uk-margin-top uk-margin-small-right"><span class=""><i class="sidebar-menu-icon material-icons md-36">event_note</i></span></div>
                            <span class="uk-text-muted uk-text-small">Class Size</span>
                            <h5 class="uk-margin-remove"><span class="uk-text-small uk-text-success "> Your Class Size = <?php echo e($register); ?>  </span></h5>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

           
            <div class="uk-grid uk-grid-width-small-1-2 uk-grid-width-large-1-3 uk-grid-width-xlarge-1-5 uk-text-center uk-sortable sortable-handler" id="dashboard_sortable_cards" data-uk-sortable data-uk-grid-margin>
              <?php if(@Auth::user()->department=='Planning' || @Auth::user()->role=='HOD' || @Auth::user()->department=='top' || @Auth::user()->department=='Rector' || @Auth::user()->role=='Support'): ?>        
                <div>
                    <div class="md-card md-card-hover md-card-overlay">
                        <div class="md-card-content">
                            <a target="_" href='<?php echo e(url("http://records.ttuportal.com/course_registration")); ?>'>  <img src="<?php echo e(url('public/assets/img/dashboard/registration.png')); ?>"/></a>
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
                      <?php endif; ?>
                      <?php if(@Auth::user()->department=='Planning' || @Auth::user()->role=='Support'): ?>        
                <div>
                    <div class="md-card md-card-hover md-card-overlay">
                        <div class="md-card-content">
                            <a  href='<?php echo e(url("/students")); ?>'>  <img src="<?php echo e(url('public/assets/img/dashboard/classlist.png')); ?>"/></a>
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
                      <?php endif; ?>
                      <?php if(@Auth::user()->department=='Planning'): ?>        
                <div>
                    <div class="md-card md-card-hover md-card-overlay">
                        <div class="md-card-content">
                            <a  href='<?php echo e(url("/upload_marks")); ?>'>  <img src="<?php echo e(url('public/assets/img/dashboard/classgroup.png')); ?>"/></a>
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
                      <?php endif; ?>
                 
                    <?php if( @Auth::user()->department=='top' ||  @Auth::user()->role=='Lecturer' ||  @Auth::user()->role=='HOD'): ?>
                <div>
                    <div class="md-card md-card-hover md-card-overlay">
                        <div class="md-card-content">
                            <a  href='<?php echo e(url("/upload_marks")); ?>'>  <img src="<?php echo e(url('public/assets/img/dashboard/results.png')); ?>"/></a>
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
                    <?php endif; ?>
                    <?php if( @Auth::user()->department=='top' ||  @Auth::user()->role=='Lecturer' ||  @Auth::user()->role=='HOD' || @Auth::user()->department=='Tpmid' || @Auth::user()->department=='Tptop'): ?>
                <div>
                    <div class="md-card md-card-hover md-card-overlay">
                        <div class="md-card-content">
                            <a  href='<?php echo e(url("/download_registered")); ?>'>  <img src="<?php echo e(url('public/assets/img/dashboard/downloadexcel.png')); ?>"/></a>
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
                    <?php endif; ?>
                 <?php if( @Auth::user()->department=='top' ||  @Auth::user()->role=='HOD' || @Auth::user()->department=='Rector' || @Auth::user()->department=='Tpmid' || @Auth::user()->department=='Tptop' || @Auth::user()->role=='Support'): ?>
                
                <div>
                    <div class="md-card md-card-hover md-card-overlay">
                        <div class="md-card-content">
                            <a  href='<?php echo e(url("/mounted_view")); ?>'>  <img src="<?php echo e(url('public/assets/img/dashboard/uploadnotes.png')); ?>"/></a>
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
                 <?php endif; ?>
                 <?php if( @Auth::user()->department=='top' ||  @Auth::user()->role=='HOD' || @Auth::user()->department=='Rector' || @Auth::user()->department=='Tpmid' || @Auth::user()->department=='Tptop' || @Auth::user()->department=='Planning' || @Auth::user()->role=='Support'): ?>
                
                <div>
                    <div class="md-card md-card-hover md-card-overlay">
                        <div class="md-card-content">
                            <a  href='<?php echo e(url("/download_results")); ?>'>  <img src="<?php echo e(url('public/assets/img/dashboard/nabptex.png')); ?>"/></a>
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
                 <?php endif; ?>
                 <?php if( @Auth::user()->department=='top' ||  @Auth::user()->role=='HOD' || @Auth::user()->department=='Rector' || @Auth::user()->department=='Tpmid' || @Auth::user()->department=='Tptop' ): ?>
                
                <div>
                    <div class="md-card md-card-hover md-card-overlay">
                        <div class="md-card-content">
                            <a  href='<?php echo e(url("/broadsheet/noticeboard")); ?>'>  <img src="<?php echo e(url('public/assets/img/dashboard/academicboard.png')); ?>"/></a>
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
                 <?php endif; ?>
                  <?php if( @Auth::user()->role=='Lecturer'  || @Auth::user()->role=='HOD'): ?>
                <div>
                     <div class="md-card md-card-hover md-card-overlay">
                        <div class="md-card-content">
                            <a  >  <img src="<?php echo e(url('public/assets/img/dashboard/uploadvideos.png')); ?>"/></a>
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
                            <a>  <img src="<?php echo e(url('public/assets/img/dashboard/uploadnotes.png')); ?>"/></a>
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
                  
                   
                <?php endif; ?>
                  <?php if( !@Auth::user()->department=='LA'): ?>
                  <div>
                    <div class="md-card md-card-hover md-card-overlay">
                        <div class="md-card-content">
                            <a  href='<?php echo e(url("/transcript")); ?>'>  <img src="<?php echo e(url('public/assets/img/dashboard/transcript.png')); ?>"/></a>
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
                 <?php endif; ?>

                   <?php if( @Auth::user()->department=='top'): ?>
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
                   <?php endif; ?>
            </div> 
  

            <div class="uk-grid" data-uk-grid-margin data-uk-grid-match="{target:'.md-card-content'}">
                
                <div class="uk-width-medium-1-4">
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-overflow-container">
                                <table class="uk-table">
                                    <thead>
                                        <tr style="background-color: #697c9a;">
                                            <th colspan="2" style="color: #ffffff;" class="uk-text-nowrap" >GENDER - FEMALE</th>

                                        </tr>
                                        
                                    </thead>
                                    <tbody>

                                    <tr>
                                        <td>        
                                        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Freshers</p>
                                        <input  type="text" value="<?php echo e($FemaleFresh); ?>" data-width="110" data-height="110" class="dial" readonly >
                                        <br/><br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Female: <?php echo e($FemaleFresh1); ?>

                                            <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Male: <?php echo e($MaleFresh1); ?>

                                        </td>

                                        <td>
                                        <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Graduands</p>
                                        <input  type="text" value="<?php echo e($FemaleFinal); ?>" data-width="110" data-height="110" class="dial" readonly >
                                        <br/><br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Female: <?php echo e($FemaleFinal1); ?>

                                            <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Male: <?php echo e($MaleFinal1); ?>

                                        </td>
                                    </tr>                                        
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="uk-width-medium-1-4">
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-overflow-container">
                                <table class="uk-table">
                                    <thead>
                                        <tr style="background-color: #ffffff;">
                                            <th colspan="5" style="color: #888888;" class="uk-text-nowrap" >ADMITTED - IN SCHOOL</th>

                                        </tr>
                                        
                                    </thead>
                                    <tbody>

                                    <tr>
                                        <td>
                                            <div style = 'min-height: 30vh'>
                                        <canvas id="barChartInSchool"></canvas>
                                    </div>

                                        </td>
                                    </tr>                                    
                                    <tr>
                                        <td>
                                            p = previous year &nbsp;&nbsp;&nbsp;&nbsp;c = current year


                                        </td>
                                    </tr>                                           
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="uk-width-medium-1-4">
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-overflow-container">
                                <table class="uk-table">
                                    <thead>
                                         <tr style="background-color: #ffffff;">
                                            <th colspan="5" style="color: #888888;" class="uk-text-nowrap" >CLASS - HND 2017/2018</th>
                                        
                                    </thead>
                                    <tbody>

                                    <tr>
                                        <td>
                                            <div >
                                        <canvas id="doughnutClass"></canvas>
                                    </div>
                                    
                                
                                        </td>
                                    </tr>                                    
                                    

                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>


                <div class="uk-width-medium-1-4">
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-overflow-container">
                                <table class="uk-table">
                                    <thead>
                                        <tr style="background-color: #697c9a;">
                                            <th colspan="5" style="color: #ffffff;" class="uk-text-nowrap" >OUR BEST STUDENTS || HND 2017/2018</th>
                                                                                        
                                        </tr>
                                        <tr style="border-bottom-style:solid; border-bottom-color:#ffffff">
                                            <th class="uk-text-nowrap"><strong>IndexNo</strong></th>
                                            
                                            <th class="uk-text-nowrap"><strong>Program</strong></th>
                                            <th class="uk-text-nowrap"><strong>CGPA</strong></th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $eku = 0;
                                        while ($eku < 5 ){

                                        if ($eku == 0 || $eku == 2 || $eku == 4 || $eku == 6 || $eku == 8 || $eku == 10 || $eku == 12) {
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
                                            
                                            <td><?php $am=$sys->getStudentsHighestCGPA_HND($eku);
                                            $ind=$am[0]->INDEXNO; echo $ind;?></td>
                                            <td><?php $am=$sys->getStudentsHighestCGPA_HND($eku);
                                            $ind=$am[0]->SLUG; echo $ind;?></td>
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
            </div>

<div class="uk-grid uk-grid-width-large-1-6 uk-grid-width-xlarge-1-6 uk-grid-width-medium-1-4 uk-grid-width-small-1-2 uk-grid-medium uk-sortable" style="background-color: white; margin-left:0">
             
    <div>
                    <div class="md-card-content" style="border:0.2px solid #cccccc; padding-left:30px; margin:15px; margin-left: 0;">
                            <div class="uk-float-right uk-margin-top "><span class=""
                                ><i class="sidebar-menu-icon material-icons md-36"style="margin-right: 15px;">book</i></span></div>
                            <h4 class="uk-margin-remove"><span class="uk-text-small uk-text-muted "> 100 Non-Ter </span></h4>
                            <h1 class="uk-text-bold numscroller uk-text-primary" data-min='0' data-max=<?php echo e($nt100); ?> data-delay='5' data-increment='100' style="margin:0"><?php echo e($nt100); ?></h1>
                            
                        
                        </div>
                </div>

                    <div>
                    <div class="md-card-content" style="border:0.2px solid #cccccc; padding-left:30px; margin:15px; margin-left: 0;">
                            <div class="uk-float-right uk-margin-top "><span class="">
                                <i class="sidebar-menu-icon material-icons md-36"style="margin-right: 15px;">school</i></span></div>
                                <h4 class="uk-margin-remove"><span class="uk-text-small uk-text-muted "> 200 Non-Ter </span></h4>
                            <h1 class="uk-text-bold uk-text-success counter" style="margin:0"><?php echo e($nt200); ?></h1>
                            
                        </div>
                </div>


                 <div>
                    <div id="counterRefresh" class="md-card-content" style="border:0.2px solid #cccccc; padding-left:30px; margin:15px;margin-left: 0;">
                            <div class="uk-float-right uk-margin-top "><span class=""
                                ><i class="sidebar-menu-icon material-icons md-36"style="margin-right: 15px;">book</i></span></div>
                           
                            <h4 class="uk-margin-remove"><span class="uk-text-small uk-text-muted "> 100 HND </span></h4>
                             <h1 class="uk-text-bold numscroller uk-text-primary" data-min='0' data-max=<?php echo e($hnd100); ?> data-delay='5' data-increment='100' style="margin:0"><?php echo e($hnd100); ?></h1>
                        </div>

                </div>
                 
                 <div>
                    <div class="md-card-content" style="border:0.2px solid #cccccc; padding-left:30px; margin:15px;margin-left: 0;">
                            <div class="uk-float-right uk-margin-top "><span class=""
                                ><i class="sidebar-menu-icon material-icons md-36"style="margin-right: 15px;">book</i></span></div>                            
                            <h4 class="uk-margin-remove"><span class="uk-text-small uk-text-muted "> 200 HND </span></h4>
                            <h1 class="uk-text-bold  counter" style="color: #976800;margin:0"><?php echo e($hnd200); ?></h1>
                        </div>
                </div>
               
               <div>
                    <div class="md-card-content" style="border:0.2px solid #cccccc; padding-left:30px; margin:15px;margin-left: 0;">
                            <div class="uk-float-right uk-margin-top "><span class="">
                                <i class="sidebar-menu-icon material-icons md-36" style="margin-right: 15px;">school</i></span></div>
                            
                            <h4 class="uk-margin-remove"><span class="uk-text-small uk-text-muted "> 300 HND </span></h4>
                            <h1 class="uk-text-bold uk-text-success counter" style="margin:0"><?php echo e($hnd300); ?></h1>
                        </div>
                </div>
                
              <div>
                    <div class="md-card-content" style="border:0.2px solid #cccccc; padding-left:30px; margin:15px;margin-left: 0;">
                            <div class="uk-float-right uk-margin-top "><span class=""
                                ><i class="sidebar-menu-icon material-icons md-36"style="margin-right: 15px;">book</i></span></div>
                            <h4 class="uk-margin-remove"><span class="uk-text-small uk-text-muted "> 100 BT Top up </span></h4>
                            <h1 class="uk-text-bold numscroller uk-text-primary" data-min='0' data-max=<?php echo e($btt100); ?> data-delay='5' data-increment='100' style="margin:0"><?php echo e($btt100); ?></h1>
                            
                        </div>
                </div>
             
    
             
     <div>
                    <div class="md-card-content" style="border:0.2px solid #cccccc; padding-left:30px; margin:15px;margin-left: 0;">
                            <div class="uk-float-right uk-margin-top "><span class="">
                                <i class="sidebar-menu-icon material-icons md-36" style="margin-right: 15px;">school</i></span></div>
                            <h4 class="uk-margin-remove"><span class="uk-text-small uk-text-muted "> 200 BT Top up </span></h4>
                            <h1 class="uk-text-bold uk-text-success counter" style="margin:0"><?php echo e($btt200); ?></h1>
                            
                        </div>
                </div>

                     <div>
                    <div class="md-card-content" style="border:0.2px solid #cccccc; padding-left:30px; margin:15px;margin-left: 0;">
                            <div class="uk-float-right uk-margin-top "><span class=""
                                ><i class="sidebar-menu-icon material-icons md-36"style="margin-right: 15px;">book</i></span></div>
                            <h4 class="uk-margin-remove"><span class="uk-text-small uk-text-muted "> 100 BT(4yrs) </span></h4>
                            <h1 class="uk-text-bold numscroller uk-text-primary" data-min='0' data-max=<?php echo e($bt100); ?> data-delay='5' data-increment='100' style="margin:0"><?php echo e($bt100); ?></h1>
                            
                        </div>
                </div>


                  <div>
                    <div class="md-card-content" style="border:0.2px solid #cccccc; padding-left:30px; margin:15px;margin-left: 0;">
                            <div class="uk-float-right uk-margin-top "><span class=""
                                ><i class="sidebar-menu-icon material-icons md-36"style="margin-right: 15px;">book</i></span></div>
                            <h4 class="uk-margin-remove"><span class="uk-text-small uk-text-muted "> 200 BT(4yrs) </span></h4>
                            <h1 class="uk-text-bold  counter" style="color: #976800 ;margin:0"><?php echo e($bt200); ?></h1>
                            
                        </div>
                </div>
                 
                 <div>
                    <div class="md-card-content" style="border:0.2px solid #cccccc; padding-left:30px; margin:15px;margin-left: 0;">
                            <div class="uk-float-right uk-margin-top "><span class="">
                                <i class="sidebar-menu-icon material-icons md-36" style="margin-right: 15px;">school</i>
                            </span></div>
                            <h4 class="uk-margin-remove"><span class="uk-text-small uk-text-muted "> 300 BT(4yrs) </span></h4>
                            <h1 class="uk-text-bold uk-text-success counter" style="margin:0"><?php echo e($bt300); ?></h1>
                            
                        </div>
                </div>
               
                <div>
                    <div class="md-card-content" style="border:0.2px solid #cccccc; padding-left:30px; margin:15px;margin-left: 0;">
                            <div class="uk-float-right uk-margin-top "><span class=""
                                ><i class="sidebar-menu-icon material-icons md-36"style="margin-right: 15px;">book</i></span></div>
                            <h4 class="uk-margin-remove"><span class="uk-text-small uk-text-muted "> 100 Masters </span></h4>
                            <h1 class="uk-text-bold numscroller uk-text-primary" data-min='0' data-max=<?php echo e($mt100); ?> data-delay='5' data-increment='100' style="margin:0"><?php echo e($mt100); ?></h1>
                            
                        </div>
                </div>
                
             <div>
                    <div class="md-card-content" style="border:0.2px solid #cccccc; padding-left:30px; margin:15px;margin-left: 0;">
                            <div class="uk-float-right uk-margin-top "><span class=""
                                ><i class="sidebar-menu-icon material-icons md-36"style="margin-right: 15px;">school</i></span></div>
                            <h4 class="uk-margin-remove"><span class="uk-text-small uk-text-muted "> 200 Masters </span></h4>
                            <h1 class="uk-text-bold uk-text-success counter" style="margin:0"><?php echo e($mt200); ?></h1>
                            
                        </div>
                </div>
             

            </div>




            <div class="uk-grid" data-uk-grid-margin data-uk-grid-match="{target:'.md-card-content'}">
                <div class="uk-width-medium-2-5">
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-overflow-container">
                                <table class="uk-table">
                                    <thead>
                                        <tr style="background-color: #ffffff;">
                                            <th colspan="5" style="color: #888888;" class="uk-text-nowrap" >AVERAGE PERFORMANCE</th>

                                        </tr>
                                        
                                    </thead>
                                    <tbody>

                                    <tr>
                                        <td>
                                            <div style = 'min-height: 40vh'>
                                        <canvas id="lineChartGender"></canvas>
                                    </div>

                                        </td>
                                    </tr>                                    
                                    <tr>
                                        <td>
                                            pre = previous year &nbsp;&nbsp;&nbsp;&nbsp;cur = current year


                                        </td>
                                    </tr>                                           
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="uk-width-medium-3-5">
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-overflow-container">
                                <table class="uk-table">
                                    <thead>
                                        <tr style="background-color: #ffffff;">
                                            <th colspan="5" style="color: #888888;" class="uk-text-nowrap" >CLASS BY PROGRAMME IN %</th>

                                        </tr>
                                        
                                    </thead>
                                    <tbody>

                                    <tr>
                                        <td>
                                            <div style = 'min-height: 45vh'>
                                        <canvas id="lineChart"></canvas>
                                    </div>

                                        </td>
                                    </tr>                                    
                                                                            
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        <div class="uk-grid" data-uk-grid-margin data-uk-grid-match="{target:'.md-card-content'}">
                
                <div class="uk-width-medium-1-4">
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-overflow-container">
                                <table class="uk-table">
                                    <thead>
                                        <tr style="background-color: #697c9a;">
                                            <th colspan="5" style="color: #ffffff;" class="uk-text-nowrap" >FRESHERS - REGISTERED</th>
                                                                                        
                                        </tr>
                                        <tr style="border-bottom-style:solid; border-bottom-color:#ffffff">
                                            <th class="uk-text-nowrap"><strong>Level</strong></th>
                                            <th class="uk-text-nowrap"><strong>Total</strong></th>
                                            <th class="uk-text-nowrap"><strong>Reg</strong></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                                                      
                                       <tr class="uk-table-middle" style="background-color: #e0e7f5;">
                                            <td>Non tertiary </td>   

                                            <td class=''><?php $at=$sys->getStudentsTotalPerLevelAll('100NT'); echo $at;?></td>
                                            <td class=''><?php $ar=$sys->getStudentsTotalPerLevelAllRegistered('100NT'); echo $ar;?></td>
                                        </tr>
                                        
                                        <tr class="uk-table-middle">
                                            <td>HND</td>
                                            
                                            <td class=''><?php $ct=$sys->getStudentsTotalPerLevelAll('100H'); echo $ct;?></td>
                                            <td class=''><?php $cr=$sys->getStudentsTotalPerLevelAllRegistered('100H'); echo $cr;?></td>
                                        </tr>
                                      

                                         <tr class="uk-table-middle" style="background-color: #e0e7f5;">
                                            <td>BTech top up</td>
                                            
                                            <td class=''><?php $ft=$sys->getStudentsTotalPerLevelAll('100BTT'); echo $ft;?></td>
                                            <td class=''><?php $fr=$sys->getStudentsTotalPerLevelAllRegistered('100BTT'); echo $fr;?></td>
                                        </tr>

                                        <tr class="uk-table-middle">
                                            <td>BTech (4 years)</td>
                                            
                                            <td class=''><?php $pt=$sys->getStudentsTotalPerLevelAll('100BT'); echo $pt;?></td>
                                            <td class=''><?php $pr=$sys->getStudentsTotalPerLevelAllRegistered('100BT'); echo $pr;?></td>
                                        </tr>
                                        <tr class="uk-table-middle" style="background-color: #e0e7f5;">
                                            <td>Masters</td>
                                            
                                            <td class=''><?php $ht=$sys->getStudentsTotalPerLevelAll('500MT'); echo $ht;?></td>
                                            <td class=''><?php $hr=$sys->getStudentsTotalPerLevelAllRegistered('500MT'); echo $hr;?></td>
                                        </tr>
                                        <tr style="border-bottom-style:solid; border-bottom-color:#ffffff">
                                            <th class="uk-text-nowrap"><strong>Sum</strong></th>
                                            <th class="uk-text-nowrap"><strong><?php echo $at+$ct+$pt+$ft+$ht;?></strong></th>
                                            <th class="uk-text-nowrap"><strong><?php echo $ar+$cr+$pr+$fr+$hr;?></strong></th>
                                        </tr>
                                        <tr>
                                            <td colspan="3">Total = those who have made either full or part payment of fees</td>
                                        </tr> 
                                        
                                    </tbody>
                                </table>
                                        
                            </div>
                        </div>
                    </div>
                </div>

                <div class="uk-width-medium-1-4">
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-overflow-container">
                                <table class="uk-table">
                                    <thead>
                                        <tr style="background-color: #697c9a;">
                                            <th colspan="5" style="color: #ffffff;" class="uk-text-nowrap" >CONTINUING - REGISTERED</th>
                                                                                        
                                        </tr>
                                        <tr style="border-bottom-style:solid; border-bottom-color:#ffffff">
                                            <th class="uk-text-nowrap"><strong>Level</strong></th>
                                            <th class="uk-text-nowrap"><strong>Total</strong></th>
                                            <th class="uk-text-nowrap"><strong>Reg</strong></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                         
                                        
                                          <tr class="uk-table-middle" style="background-color: #e0e7f5;">
                                            <td>Level 200 BTech (4 years)</td>
                                            
                                            <td class=''><?php $gt=$sys->getStudentsTotalPerLevelAll('200BT'); echo $gt;?></td>
                                            <td class=''><?php $gr=$sys->getStudentsTotalPerLevelAllRegistered('200BT'); echo $gr;?></td>
                                        </tr>

                                        <tr class="uk-table-middle">
                                            <td>Level 300 BTech (4 years)</td>
                                            
                                            <td class=''><?php $ft=$sys->getStudentsTotalPerLevelAll('300BT'); echo $ft;?></td>
                                            <td class=''><?php $fr=$sys->getStudentsTotalPerLevelAllRegistered('300BT'); echo $fr;?></td>
                                        </tr>
                                                           
                                        <tr class="uk-table-middle" style="background-color: #e0e7f5;">
                                            <td>Level 200 HND</td>
                                           
                                            <td class=''><?php $dt=$sys->getStudentsTotalPerLevelAll('200H'); echo $dt;?></td>
                                            <td class=''><?php $dr=$sys->getStudentsTotalPerLevelAllRegistered('200H'); echo $dr;?></td>
                                        </tr>
                                        <tr style="border-bottom-style:solid; border-bottom-color:#ffffff">
                                            <th class="uk-text-nowrap"><strong>Sum</strong></th>
                                            <th class="uk-text-nowrap"><strong><?php echo $gt+$ft+$dt;?></strong></th>
                                            <th class="uk-text-nowrap"><strong><?php echo $gr+$fr+$dr;?></strong></th>
                                        </tr>
                                        <tr>
                                            <td colspan="3">Total = those who wrote exams last semester</td>
                                        </tr> 
                                        
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="uk-width-medium-1-4">
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-overflow-container">
                                <table class="uk-table">
                                    <thead>
                                        <tr style="background-color: #697c9a;">
                                            <th colspan="5" style="color: #ffffff;" class="uk-text-nowrap" >FINAL YEAR - REGISTERED</th>
                                                                                        
                                        </tr>
                                        <tr style="border-bottom-style:solid; border-bottom-color:#ffffff">
                                            <th class="uk-text-nowrap"><strong>Level</strong></th>
                                            <th class="uk-text-nowrap"><strong>Total</strong></th>
                                            <th class="uk-text-nowrap"><strong>Reg</strong></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                                                      
                                       <tr class="uk-table-middle" style="background-color: #e0e7f5;">
                                            <td>Non tertiary </td>   

                                            <td class=''><?php $at=$sys->getStudentsTotalPerLevelAll('200NT'); echo $at;?></td>
                                            <td class=''><?php $ar=$sys->getStudentsTotalPerLevelAllRegistered('200NT'); echo $ar;?></td>
                                        </tr>
                                        
                                        <tr class="uk-table-middle">
                                            <td>HND</td>
                                            
                                            <td class=''><?php $ct=$sys->getStudentsTotalPerLevelAll('300H'); echo $ct;?></td>
                                            <td class=''><?php $cr=$sys->getStudentsTotalPerLevelAllRegistered('300H'); echo $cr;?></td>
                                        </tr>
                                      

                                         <tr class="uk-table-middle" style="background-color: #e0e7f5;">
                                            <td>BTech top up</td>
                                            
                                            <td class=''><?php $ft=$sys->getStudentsTotalPerLevelAll('200BTT'); echo $ft;?></td>
                                            <td class=''><?php $fr=$sys->getStudentsTotalPerLevelAllRegistered('200BTT'); echo $fr;?></td>
                                        </tr>

                                        <tr class="uk-table-middle">
                                            <td>BTech (4 years)</td>
                                            
                                            <td class=''><?php $pt=$sys->getStudentsTotalPerLevelAll('400BT'); echo $pt;?></td>
                                            <td class=''><?php $pr=$sys->getStudentsTotalPerLevelAllRegistered('400BT'); echo $pr;?></td>
                                        </tr>
                                        <tr class="uk-table-middle" style="background-color: #e0e7f5;">
                                            <td>Masters</td>
                                            
                                            <td class=''><?php $ht=$sys->getStudentsTotalPerLevelAll('600MT'); echo $ht;?></td>
                                            <td class=''><?php $hr=$sys->getStudentsTotalPerLevelAllRegistered('600MT'); echo $hr;?></td>
                                        </tr>
                                        <tr style="border-bottom-style:solid; border-bottom-color:#ffffff">
                                            <th class="uk-text-nowrap"><strong>Sum</strong></th>
                                            <th class="uk-text-nowrap"><strong><?php echo $at+$ct+$pt+$ft+$ht;?></strong></th>
                                            <th class="uk-text-nowrap"><strong><?php echo $ar+$cr+$pr+$fr+$hr;?></strong></th>
                                        </tr>
                                          
                                        <tr>
                                            <td colspan="3">Total = those who wrote exams last semester</td>
                                        </tr> 
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="uk-width-medium-1-4">
                    <div class="md-card">
                        <div class="md-card-content">
                            <div class="uk-overflow-container">
                                <table class="uk-table">
                                    <thead>
                                        <tr style="background-color: #697c9a;">
                                            <th colspan="5" style="color: #ffffff;" class="uk-text-nowrap" >GRADUANDS</th>
                                                                                        
                                        </tr>
                                        <tr style="border-bottom-style:solid; border-bottom-color:#ffffff">
                                            <th class="uk-text-nowrap"><strong>Level</strong></th>
                                            <th class="uk-text-nowrap"><strong>M</strong></th>
                                            <th class="uk-text-nowrap"><strong>F</strong></th>
                                            <th class="uk-text-nowrap"><strong>T</strong></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                                                      
                                       <tr class="uk-table-middle" style="background-color: #e0e7f5;">
                                            <td>Non tertiary </td>
                                            <td class=''><?php $am=$sys->getStudentsTotalPerLevelAllGenGrad('200NT','Male','2017/2018'); echo $am;?></td>
                                            <td class=''><?php $af=$sys->getStudentsTotalPerLevelAllGenGrad('200NT','Female','2017/2018'); echo $af;?></td>
                                            <td class=''><?php $at=$sys->getStudentsTotalPerLevelAllGrad('200NT','2017/2018'); echo $at;?></td>
                                        </tr>
                                        
                                        <tr class="uk-table-middle">
                                            <td>HND</td>
                                            <td class=''><?php $cm=$sys->getStudentsTotalPerLevelAllGenGrad('300H','Male','2017/2018'); echo $cm;?></td>
                                            <td class=''><?php $cf=$sys->getStudentsTotalPerLevelAllGenGrad('300H','Female','2017/2018'); echo $cf;?></td>
                                            <td class=''><?php $ct=$sys->getStudentsTotalPerLevelAllGrad('300H','2017/2018'); echo $ct;?></td>
                                        </tr>
                                      

                                         <tr class="uk-table-middle" style="background-color: #e0e7f5;">
                                            <td>BTech top up</td>
                                            <td class=''><?php $bm=$sys->getStudentsTotalPerLevelAllGenGrad('200BTT','Male','2017/2018'); echo $bm;?></td>
                                            <td class=''><?php $bf=$sys->getStudentsTotalPerLevelAllGenGrad('200BTT','Female','2017/2018'); echo $bf;?></td>
                                            <td class=''><?php $bt=$sys->getStudentsTotalPerLevelAllGrad('200BTT','2017/2018'); echo $bt;?></td>
                                        </tr>

                                        <tr class="uk-table-middle">
                                            <td>BTech (4 years)</td>
                                            <td class=''><?php $dm=$sys->getStudentsTotalPerLevelAllGenGrad('400BT','Male','2017/2018'); echo $dm;?></td>
                                            <td class=''><?php $df=$sys->getStudentsTotalPerLevelAllGenGrad('400BT','Female','2017/2018'); echo $df;?></td>
                                            <td class=''><?php $dt=$sys->getStudentsTotalPerLevelAllGrad('400BT','2017/2018'); echo $dt;?></td>
                                        </tr>
                                        <tr class="uk-table-middle" style="background-color: #e0e7f5;">
                                            <td>Masters</td>
                                            <td class=''><?php $em=$sys->getStudentsTotalPerLevelAllGenGrad('600MT','Male','2017/2018'); echo $em;?></td>
                                            <td class=''><?php $ef=$sys->getStudentsTotalPerLevelAllGenGrad('600MT','Female','2017/2018'); echo $ef;?></td>
                                            <td class=''><?php $et=$sys->getStudentsTotalPerLevelAllGrad('600MT','2017/2018'); echo $et;?></td>
                                        </tr>
                                        <tr style="border-bottom-style:solid; border-bottom-color:#ffffff">
                                            <th class="uk-text-nowrap"><strong>Sum</strong></th>
                                            <th class="uk-text-nowrap"><strong><?php echo $am+$bm+$cm+$dm+$em;?></strong></th>
                                            <th class="uk-text-nowrap"><strong><?php echo $af+$bf+$cf+$df+$ef;?></strong></th>
                                            <th class="uk-text-nowrap"><strong><?php echo $at+$bt+$ct+$et+$dt;?></strong></th>
                                        </tr>
                                          
                                        <tr>
                                            <td colspan="3">Total = those who wrote exams last semester</td>
                                        </tr> 
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
                       
          
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Counter-Up/1.0.0/jquery.counterup.js"></script>
    <script src="jquery.counterup.min.js"></script>


 <script src=https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js charset=utf-8></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-Knob/1.2.13/jquery.knob.min.js"></script>
<script>
    var newTotalArray = new Array();
    var newTotalMale = new Array();
    var newTotalFemale = new Array();
    var years = new Array();
    var newPrgramClass = new Array();
    var new1stClass = new Array();
    var new2ndUClass = new Array();
    var new2ndLClass = new Array();
    var newPassClass = new Array();
    var newFailClass = new Array();
    <?php
    foreach($prgram1stClass as $key=> $val){?>
    new1stClass.push('<?php echo $val; ?>');
    <?php    }


    ?>
    <?php
    foreach($prgram2ndUClass as $key=> $val){?>
    new2ndUClass.push('<?php echo $val; ?>');
    <?php    }


    ?>
    <?php
    foreach($prgram2ndLClass as $key=> $val){?>
    new2ndLClass.push('<?php echo $val; ?>');
    <?php    }


    ?>
    <?php
    foreach($prgramPassClass as $key=> $val){?>
    newPassClass.push('<?php echo $val; ?>');
    <?php    }


    ?>
    <?php
    foreach($prgramFailClass as $key=> $val){?>
    newFailClass.push('<?php echo $val; ?>');
    <?php    }


    ?>
    <?php
    foreach($totalYearTotal as $key=> $val){?>
    newTotalArray.push('<?php echo $val; ?>');
    <?php    }


    ?>

    <?php
    foreach($totalMalePerYear as $key=> $val){?>
    newTotalMale.push('<?php echo $val; ?>');
    <?php    }


    ?>

    <?php
    foreach($totalFemalePerYear as $key=> $val){?>
    newTotalFemale.push('<?php echo $val; ?>');
    <?php    }


    ?>


    <?php
    foreach($prgramClass as $key=> $val){?>
    newPrgramClass.push('<?php echo $val; ?>');
    <?php    }


    ?>

    <?php
    foreach($yearData as $key=> $val){?>
    years.push('<?php echo $val; ?>');
    <?php    }


        ?>

    

var stClass = '<?php echo  $stClass; ?>';
var ndClassU = '<?php echo  $ndClassU; ?>';
var ndClassL = '<?php echo  $ndClassL; ?>';
var pass = '<?php echo  $pass; ?>';
var fail = '<?php echo  $fail; ?>';

var admitPreviousHnd = '<?php echo $admitPreviousHnd;?>';
var admitPreviousBtt = '<?php echo $admitPreviousBtt;?>';
var admitPreviousBt = '<?php echo $admitPreviousBt;?>';
var admitPreviousNt = '<?php echo $admitPreviousNt;?>';
var admitPreviousMt = '<?php echo $admitPreviousMt;?>';

var admitCurrentHnd = '<?php echo $admitCurrentHnd;?>';
var admitCurrentBtt = '<?php echo $admitCurrentBtt;?>';
var admitCurrentBt = '<?php echo $admitCurrentBt;?>';
var admitCurrentNt = '<?php echo $admitCurrentNt;?>';
var admitCurrentMt = '<?php echo $admitCurrentMt;?>';

var currentTotal = '<?php echo $currentTotal;?>';
var previousTotal = '<?php echo $previousTotal;?>';

     
     
 
$(function() {
        $(".dial").knob();
    });                            

        window.onload = function() {
        //lineChart.canvas.parentNode.style.height = '128px';
        const ctx = document.getElementById("lineChart").getContext("2d");
        //ctx.height = '100px';
        window.myBar = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: newPrgramClass,
                datasets: [
                    {
                        label: "1st",
                        fill: true,
                        lineTension: 0.0,
                        borderWidth:0,
                        backgroundColor: "rgba(132,236,142,0.8)",
                        borderColor: "rgba(132,236,142,1)",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        BorderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(132,236,142,1)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(132,236,142,1)",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 1,
                        pointRadius: 1,
                        data: new1stClass,
                    },

                    {
                        label: "2nd U",
                        fill: true,
                        lineTension: 0.0,
                        borderWidth:0,
                        backgroundColor: "rgba(209,211,41,0.8)",
                        borderColor: "rgba(209,211,41,1)",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        BorderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(209,211,41,1)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(209,211,41,1)",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 1,
                        pointRadius: 1,
                        data: new2ndUClass,
                    },

                    {
                        label: "2nd L",
                        fill: true,
                        lineTension: 0.0,
                        borderWidth:0,
                        backgroundColor: "rgba(132,132,236,0.8)",
                        borderColor: "rgba(132,132,236,1)",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        BorderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(132,132,236,1)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(132,132,236,1)",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 1,
                        pointRadius: 1,
                        data: new2ndLClass,
                    },

                    {
                        label: "Pass",
                        fill: true,
                        lineTension: 0.0,
                        borderWidth:0,
                        backgroundColor: "rgba(158,139,41,0.8)",
                        borderColor: "rgba(158,139,41,1)",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        BorderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(158,139,41,1)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(158,139,41,1)",
                        pointHoverBorderColor: "rgba(250,20,20,1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 1,
                        pointRadius: 1,
                        data: newPassClass,
                    },

                    {
                        label: "Fail",
                        fill: true,
                        lineTension: 0.0,
                        borderWidth:0,
                        backgroundColor: "rgba(253,41,41,0.8)",
                        borderColor: "rgba(253,41,41,1)",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        BorderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(253,41,41,1)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(253,41,41,1)",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 1,
                        pointRadius: 1,
                        data: newFailClass,
                    }
                ]
            },
            options: { maintainAspectRatio: false,
                legend: {
                    position : 'bottom',
                    labels: {
                    boxWidth: 20,
                    padding: 20,
                    }
                },
                scales:{
                    xAxes: [{
                        stacked: true,
                        ticks: {
                            display: false
                        }
                            }],
                    yAxes:[{
                        stacked: true,
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        })

         const ctxGender = document.getElementById("lineChartGender").getContext("2d");
         //ctx.height = '100px';
         window.myBar = new Chart(ctxGender, {
             type: 'line',
             data: {
                 labels: years,
                 datasets: [
                     {
                         label: "Male",
                         fill: true,
                         lineTension: 0.0,
                         borderWidth:0,
                         backgroundColor: "rgba(255,255,255,0.2)",
                         borderColor: "rgba(132,236,142,1)",
                         borderCapStyle: 'butt',
                         borderDash: [],
                         BorderDashOffset: 0.0,
                         borderJoinStyle: 'miter',
                         pointBorderColor: "rgba(132,236,142,1)",
                         pointHoverRadius: 5,
                         pointHoverBackgroundColor: "rgba(132,236,142,1)",
                         pointHoverBorderColor: "rgba(220,220,220,1)",
                         pointHitRadius: 10,
                         pointBorderWidth: 1,
                         pointRadius: 1,
                         data: newTotalMale,
                     },

                     {
                         label: "Female",
                         fill: true,
                         lineTension: 0.0,
                         borderWidth:0,
                         backgroundColor: "rgba(255,255,255,0.2)",
                         borderColor: "rgba(132,132,236,1)",
                         borderCapStyle: 'butt',
                         borderDash: [],
                         BorderDashOffset: 0.0,
                         borderJoinStyle: 'miter',
                         pointBorderColor: "rgba(132,132,236,1)",
                         pointHoverRadius: 5,
                         pointHoverBackgroundColor: "rgba(132,132,236,1)",
                         pointHoverBorderColor: "rgba(220,220,220,1)",
                         pointHitRadius: 10,
                         pointBorderWidth: 1,
                         pointRadius: 1,
                         data: newTotalFemale,
                     },

                     {
                         label: "Total",
                         fill: true,
                         lineTension: 0.0,
                         borderWidth:0,
                         backgroundColor: "rgba(255,255,255,0.2)",
                         borderColor: "rgba(236,147,132,1)",
                         borderCapStyle: 'butt',
                         borderDash: [],
                         BorderDashOffset: 0.0,
                         borderJoinStyle: 'miter',
                         pointBorderColor: "rgba(236,147,132,1)",
                         pointHoverRadius: 5,
                         pointHoverBackgroundColor: "rgba(236,147,132,1)",
                         pointHoverBorderColor: "rgba(250,20,20,1)",
                         pointHitRadius: 10,
                         pointBorderWidth: 1,
                         pointRadius: 1,
                         data: newTotalArray,
                     }
                 ]
             },
             options: { maintainAspectRatio: false,
                 legend: {
                     position : 'bottom',
                     labels: {
                     boxWidth: 20,
                     padding: 20,
                     }
                 },
                 scales:{
                     yAxes:[{
                         ticks: {
                             beginAtZero: true
                         }
                     }]
                 }
             }
         })

         const ctx1 = document.getElementById("doughnutClass").getContext("2d");
        window.myBar = new Chart(ctx1, {
            type: 'doughnut',
            data: {
    datasets: [{
        data: [stClass, ndClassU, ndClassL, pass, fail],
        backgroundColor:['#a9ec84','#cdec84','#ecda84','#ecb584','#ec8484']
    }],

    // These labels appear in the legend and in the tooltips when hovering different arcs
    labels: [
        '1st',
        '2nd U',
        '2nd L',
        'Pass',
        'Fail'
    ]
},
options:{
     legend: {
                    position : 'left',
                    labels: {
                    boxWidth: 20,
                    
                    }
                },
}
            
        })


        const ctx3 = document.getElementById("barChartInSchool").getContext("2d");
        window.myBar = new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: ['p ('+ previousTotal +')', 'c ('+ currentTotal +')'],
                datasets: [
                    {
                        label: "Non-Ter",
                        fill: true,
                        lineTension: 0.0,
                        borderWidth: 0,
                        backgroundColor: "rgba(87,156,215,0.8)",
                        borderColor: "rgba(87,156,215,1)",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        BorderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(87,156,215,1)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(87,156,215,1)",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 1,
                        pointRadius: 1,
                        data: [admitPreviousNt,admitCurrentNt],

                    },

                    {
                        label: "HND",
                        fill: true,
                        lineTension: 0.0,
                        borderWidth: 0,
                        backgroundColor: "rgba(237,125,49,0.8)",
                        borderColor: "rgba(237,125,49,1)",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        BorderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(237,125,49,1)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(237,125,49,1)",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 1,
                        pointRadius: 1,
                        data: [admitPreviousHnd,admitCurrentHnd],
                    },

                    {
                        label: "BTT",
                        fill: true,
                        lineTension: 0.0,
                        borderWidth: 0,
                        backgroundColor: "rgba(165,165,165,0.8)",
                        borderColor: "rgba(165,165,165,1)",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        BorderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(165,165,165,1)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(165,165,165,1)",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 1,
                        pointRadius: 1,
                        data: [admitPreviousBtt,admitCurrentBtt],
                    },

                    {
                        label: "BT(4yrs)",
                        fill: true,
                        lineTension: 0.0,
                        borderWidth: 0,
                        backgroundColor: "rgba(255,192,0,0.8)",
                        borderColor: "rgba(255,192,0,1)",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        BorderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(255,192,0,1)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(255,192,0,1)",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 1,
                        pointRadius: 1,
                        data: [admitPreviousBt,admitCurrentBt],
                    },

                    {
                        label: "Mst",
                        fill: true,
                        lineTension: 0.0,
                        borderWidth: 0,
                        backgroundColor: "rgba(126,213,88,0.8)",
                        borderColor: "rgba(126,213,88,1)",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        BorderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(126,213,88,1)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(126,213,88,1)",
                        pointHoverBorderColor: "rgba(250,20,20,1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 1,
                        pointRadius: 1,
                        data: [admitPreviousMt,admitCurrentMt],
                    }
                ]
            },
            options: { maintainAspectRatio: false,
                legend: {
                    position : 'right',
                    labels: {
                    boxWidth: 20,
                    padding:7,
                    }
                },
                scales:{
                    xAxes: [{
                        stacked: true
                            }],
                    yAxes:[{
                        stacked: true,
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        })
    }

    // $('.counter').counterUp({
                
    //       });
</script>

<script type="text/javascript">
    setInterval(function(){

       location.reload("true"); 
    }, 600000)


    /* setInterval(function(){
        $("#counterRefresh").load("dashboard");
    // }, 2000)*/
</script>
 
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>