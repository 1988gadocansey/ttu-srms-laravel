 
<?php $__env->startSection('style'); ?>
 
<?php $__env->stopSection(); ?>
 <?php $__env->startSection('content'); ?>
   <div class="md-card-content">
<?php if(Session::has('success')): ?>
            <div style="text-align: center" class="uk-alert uk-alert-success" data-uk-alert="">
                <?php echo Session::get('success'); ?>

            </div>
 <?php endif; ?>
 <?php if(Session::has('error')): ?>
            <div style="text-align: center" class="uk-alert uk-alert-danger" data-uk-alert="">
                <?php echo Session::get('error'); ?>

            </div>
 <?php endif; ?>
 
     <?php if(count($errors) > 0): ?>

    <div class="uk-form-row">
        <div class="uk-alert uk-alert-danger" style="background-color: red;color: white">

              <ul>
                <?php foreach($errors->all() as $error): ?>
                  <li> <?php echo e($error); ?> </li>
                <?php endforeach; ?>
          </ul>
    </div>
  </div>
<?php endif; ?>
  </div>
 
 <form  action=""  id="form" accept-charset="utf-8" method="POST" name="applicationForm"  v-form>
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
 <h5>Academic Calender</h5>
 <div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content">
            <div class="uk-overflow-container" id='print'>
                <center><span class="uk-text-success uk-text-bold"><?php echo $data->total(); ?> Records</span></center>
                    <table class="uk-table uk-table-hover uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id="ts_pager_filter"> 
                        <thead>
                            <tr>
                                <th class="filter-false remove sorter-false">NO</th>
                                <th>Year</th>
                                <th>Sem</th>
                                <?php if( @Auth::user()->department!='LA'): ?>
                                <th>Register</th>
                                <th>Exam</th>
                                <th>Exam</th>

                                <th>Assesment</th>
                                <th>Assesment</th>
                                <th>Result View</th>
                                <?php endif; ?>

                                    <th>Attachment</th>
                
                                <th  class="filter-false remove sorter-false uk-text-center">ACTION</th>   
                     
                            </tr>
                        </thead>
                        <tbody>

                            <?php foreach($data as $index=>$row): ?> 
 
                            <tr align="">
                                <td> <?php echo e($data->perPage()*($data->currentPage()-1)+($index+1)); ?> </td>
                                <?php if( @Auth::user()->department!='LA'): ?>
                                <td>
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><input type="text" id="year" name="year" v-form-ctrl  class="md-input uk-text-primary uk-text-bold"    v-model="year" value="<?php echo e(@$row->YEAR); ?>"/><span class="md-input-bar"></span>
                                        </div>         

                                    </div>
                                </td>
                                <td> <div class="uk-input-group col-md-4">

                                        <input type="text" id="sem" name="sem" v-form-ctrl  class="md-input uk-text-primary uk-text-bold col-md-4"    v-model="sem" value="<?php echo e(@$row->SEMESTER); ?>"/>         

                                    </div>
                                </td>
                        
                                <td class="uk-text-center">
                                    <?php if($row->STATUS==1): ?><span class="uk-badge uk-badge-success">Opened</span>
                                    <span> <a href='<?php echo e(url("fireCalender/$row->ID/id/closeReg/action")); ?>' ><i title='Click to close online registration' onclick="return confirm('Are you sure you want to close online registration?' );" class="md-icon material-icons uk-text-danger">power_settings_new</i></span>
                                    <?php else: ?> <span class="uk-badge uk-badge-danger">Closed</span><span> <a href='<?php echo e(url("fireCalender/$row->ID/id/openReg/action")); ?>' ><i title='Click to open online registration' onclick="return confirm('Are you sure you want to open online registration?' );" class="md-icon material-icons uk-text-success">power_settings_new</i></span> <?php endif; ?>
                                </td>
                     
                                <td>
                                    <div class="uk-input-group col-md-4">

                                        <input type="text" id="upload" name="upload" v-form-ctrl  class="md-input uk-text-primary uk-text-bold col-md-4"    v-model="upload" value="<?php echo e(@$row->RESULT_DATE); ?>"/>         

                                    </div>
                                </td>


                                <td class="uk-text-center"><?php if($row->ENTER_RESULT==1): ?><span class="uk-badge uk-badge-success">Opened</span><span> <a href='<?php echo e(url("fireCalender/$row->ID/id/closeMark/action")); ?>' ><i title='Click to close entering of marks' onclick="return confirm('Are you sure you want to close entering of marks?' );" class="md-icon material-icons uk-text-danger">power_settings_new</i></span> <?php else: ?> <span class="uk-badge uk-badge-danger">Closed</span><span> <a href='<?php echo e(url("fireCalender/$row->ID/id/openMark/action")); ?>' ><i onclick="return confirm('Are you sure you want to open entering of marks?' );" title='Click to open online registration'  class="md-icon material-icons uk-text-success">power_settings_new</i></span> <?php endif; ?>
                                </td>



                                <td> 
                                    <div class="uk-input-group col-md-4">

                                        <input type="text" id="qa" name="qa" v-form-ctrl  class="md-input uk-text-primary uk-text-bold col-md-4"    v-model="qa" value="<?php echo e(@$row->QA); ?>"/>         

                                    </div>
                                </td>

                                <td class="uk-text-center"><?php if($row->QAOPEN==1): ?><span class="uk-badge uk-badge-success">Opened</span><span> <a href='<?php echo e(url("fireCalender/$row->ID/id/closeQa/action")); ?>' ><i title='Click to close lecturer assesment' onclick="return confirm('Are you sure you want to close lecturer assesment?' );" class="md-icon material-icons uk-text-danger">power_settings_new</i></span> 
                                <?php else: ?> <span class="uk-badge uk-badge-danger">Closed</span><span> <a href='<?php echo e(url("fireCalender/$row->ID/id/openQa/action")); ?>' ><i onclick="return confirm('Are you sure you want to open lecturer assesment?' );" title='Click to open lecturer assesment'  class="md-icon material-icons uk-text-success">power_settings_new</i></span> <?php endif; ?>
                                </td>

                                <td> 
                                    <div class="uk-input-group col-md-4">

                                        <input type="text" id="result" name="result" v-form-ctrl  class="md-input uk-text-primary uk-text-bold col-md-4"    v-model="result" value="<?php echo e(@$row->RESULT_BLOCK); ?>"/>         

                                    </div>
                                </td>
                                <td class="uk-text-center">
                                    <input type="submit" value="Save" id='save'v-show="applicationForm.$valid"  class="md-btn   md-btn-success uk-margin-small-top">
                                </td>

                                <?php endif; ?>

                                    <td class="uk-text-center"><?php if($row->LIAISON==1): ?><span class="uk-badge uk-badge-success">Opened</span><span> <a href='<?php echo e(url("fireCalender/$row->ID/id/closeLia/action")); ?>' ><i title='Click to close registration for attachment' onclick="return confirm('Are you sure you want to close registration for attachment?' );" class="md-icon material-icons uk-text-danger">power_settings_new</i></span>
                                        <?php else: ?> <span class="uk-badge uk-badge-danger">Closed</span><span> <a href='<?php echo e(url("fireCalender/$row->ID/id/openLia/action")); ?>' ><i onclick="return confirm('Are you sure you want to open registration for attachment?' );" title='Click to open registration for attachment'  class="md-icon material-icons uk-text-success">power_settings_new</i></span> <?php endif; ?>
                                    </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                                    
                    </table>
           <?php echo (new Landish\Pagination\UIKit($data->appends(old())))->render(); ?>

         
            </div>
            
        </div>
    </div>
 </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
 
 
<?php $__env->stopSection(); ?>
</form>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>