 
<?php $__env->startSection('style'); ?>
 
<?php $__env->stopSection(); ?>
 <?php $__env->startSection('content'); ?>
  
   <div class="md-card-content">
        
 <?php if($messages=Session::get("success")): ?>

    <div class="uk-form-row">
        <div style="text-align: center" class="uk-alert uk-alert-success" data-uk-alert="">

              <ul>
                <?php foreach($messages as $message): ?>
                  <li> <?php echo $message; ?> </li>
                <?php endforeach; ?>
          </ul>
    </div>
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
  
 <h5>Generate Fee Summary</h5>  
 <div style="">
     <div class="uk-margin-bottom" style="margin-left:1021px" >
         <a  href="#new_task" data-uk-modal="{ center:true }"> <i title="click to send sms to students owing"   class="material-icons md-36 uk-text-success"   >phonelink_ring message</i></a>

         <a href="#" class="md-btn md-btn-small md-btn-success uk-margin-right" id="printTable">Print Table</a>
         <div class="uk-button-dropdown" data-uk-dropdown="{mode:'click'}">
             <button class="md-btn md-btn-small md-btn-success"> columns <i class="uk-icon-caret-down"></i></button>
             <div class="uk-dropdown">
                 <ul class="uk-nav uk-nav-dropdown" id="columnSelector"></ul>
             </div>
         </div>
     </div>
 </div>
 <!-- filters here -->
  <?php $fee = app('App\Http\Controllers\FeeController'); ?>
   <?php $sys = app('App\Http\Controllers\SystemController'); ?>
 <div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content">
            
                <form action=""  method="POST" accept-charset="utf-8" novalidate id="group">
                   <?php echo csrf_field(); ?>

                    <div class="uk-grid" data-uk-grid-margin="">

                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                    <?php echo Form::select('program', 
                                (['' => 'All programs'] +$program ), 
                                  old("program",""),
                                    ['class' => 'md-input parent','id'=>"parent",'placeholder'=>'select program'] ); ?>

                         </div>
                        </div>
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                   <?php echo Form::select('level', array( '1'=>'1st years','2' => '2nd years', '3' => '3rd years','4'=>'4th years','400/1'=>'BTECH level 1','400/2'=>'BTECH level 2'), null, ['placeholder' => 'select level','id'=>'parent','class'=>'md-input parent'],old("level",""));; ?>

                          
                            </div>
                        </div>
                       
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                    <?php echo Form::select('year', 
                                (['' => 'Select year'] +$year ), 
                                  old("year",""),
                                    ['class' => 'md-input parent','id'=>"parent"] ); ?>

                         </div>
                        </div>
                        
                          
                         

                       
                           <div class="uk-width-medium-1-10" style=" ">
                            <div class="uk-margin-small-top">  
                            <div class="uk-button-dropdown" data-uk-dropdown="{mode:'click'}">
                                <button class="md-btn md-btn-small md-btn-success uk-margin-small-top">Export <i class="uk-icon-caret-down"></i></button>
                                <div class="uk-dropdown">
                                    <ul class="uk-nav uk-nav-dropdown">
                                         <li><a href="#" onClick ="$('#ts_pager_filter').tableExport({type:'csv',escape:'false'});"><img src='<?php echo url("public/public/assets/icons/csv.png"); ?>' width="24"/> CSV</a></li>
                                           
                                            <li class="uk-nav-divider"></li>
                                            <li><a href="#" onClick ="$('#ts_pager_filter').tableExport({type:'excel',escape:'false'});"><img src='<?php echo url("public/public/assets/icons/xls.png"); ?>' width="24"/> XLS</a></li>
                                            <li><a href="#" onClick ="$('#ts_pager_filter').tableExport({type:'doc',escape:'false'});"><img src='<?php echo url("public/public/assets/icons/word.png"); ?>' width="24"/> Word</a></li>
                                            <li><a href="#" onClick ="$('#ts_pager_filter').tableExport({type:'powerpoint',escape:'false'});"><img src='<?php echo url("public/public/assets/icons/ppt.png"); ?>' width="24"/> PowerPoint</a></li>
                                            <li class="uk-nav-divider"></li>
                                           
                                    </ul>
                                </div>
                            </div>
                        </div>
                           </div>
                            
                        <div class="uk-width-medium-1-10"  style="" >                            
                            <div class="uk-margin-small-top">
                                 <i title="click to print" onclick="javascript:printDiv('print')" class="material-icons md-36 uk-text-success"   >print</i>
                   
                            </div>
                          </div>
                         
                         
                    
                    </div>
                   
                </form> 
        </div>
    </div>
 </div>
 
 <!-- end filters -->
 <div class="uk-width-xLarge-1-1">
 <div class="md-card">
 <div class="md-card-content">
  
<?php if(!empty($data)): ?>
     <div class="uk-overflow-container" id='print'>
         <center><span>Fee summary for <?php echo $programme; ?>  <?php echo e(@$academicYear); ?> Academic year - Level  <?php echo e(@$level); ?></span> </center>
         <center><span class="uk-text-success uk-text-bold"><?php echo $data->total(); ?> Records</span></center>
                <table class="uk-table uk-table-hover uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id="ts_pager_filter"> 
                                  <thead>
                                        <tr>
                                            <th class="filter-false remove sorter-false"  >NO</th>
                                            <th>Fee</th>
                                            <th data-priority="critical">Amount (GHC)</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                    
                                         <?php foreach($data as $index=> $row): ?> 
                                         
                                        <tr align="">
                                            <td> <?php echo e($data->perPage()*($data->currentPage()-1)+($index+1)); ?> </td>
                                            <td> <?php echo e(@$row->NAME); ?></td>
                                            <td> <?php echo e(@$row->AMOUNT); ?></td>
                                          
                                        </tr>
                                         <?php endforeach; ?>
                                         
                                    </tbody>
                                    
                             </table>
         <div style="margin-left: 994px" class="uk-text-bold uk-text-success"><td colspan=" ">TOTAL GHC  <?php echo e(@$row->TOTALS); ?></td></div>
          <?php echo (new Landish\Pagination\UIKit($data->appends(old())))->render(); ?>

     </div>
  <?php endif; ?>
   
 </div>
 </div></div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
 <script type="text/javascript">

    $(document).ready(function () {

        $(".parent").on('change', function (e) {

            $("#group").submit();

        });
    });

</script>
<script src="<?php echo url('public/assets/js/select2.full.min.js'); ?>"></script>
<script>
     $(document).ready(function () {
         $('select').select2({width: "resolve"});


     });


</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>