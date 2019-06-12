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

<h3 class="heading_b uk-margin-bottom">Lecturers Assessment Report | Single printout</h3>
<div style="" class="">
    <!--    <div class="uk-margin-bottom" style="margin-left:910px" >-->
    <div class="uk-margin-bottom" style="">
        <?php if(@\Auth::user()->department=="Tpmid" || @\Auth::user()->department=="Tptop"): ?>
            <a href="#new_task" data-uk-modal="{ center:true }"> <i title="click to send sms to students"
                                                                    class="material-icons md-36 uk-text-success">phonelink_ring
                    message</i></a>
        <?php endif; ?>

        <a href="#" class="md-btn md-btn-small md-btn-success uk-margin-right" id="printTable">Print Table</a>
        <div class="uk-button-dropdown" data-uk-dropdown="{mode:'click'}">
            <button class="md-btn md-btn-small md-btn-success"> columns <i class="uk-icon-caret-down"></i></button>
            <div class="uk-dropdown">
                <ul class="uk-nav uk-nav-dropdown" id="columnSelector"></ul>
            </div>
        </div>


        <div style="margin-top: -5px" class="uk-button-dropdown" data-uk-dropdown="{mode:'click'}">
            <button class="md-btn md-btn-small md-btn-success uk-margin-small-top">Export <i
                        class="uk-icon-caret-down"></i></button>
            <div class="uk-dropdown">
                <ul class="uk-nav uk-nav-dropdown">

                    <li class="uk-nav-divider"></li>
                    <li><a href="#" onClick="$('#ts_pager_filter').tableExport({type:'excel',escape:'false'});"><img
                                    src='<?php echo url("public/assets/icons/xls.png"); ?>' width="24"/> Excel</a></li>
                    <li class="uk-nav-divider"></li>

                </ul>
            </div>
        </div>


        <i title="click to print" onclick="javascript:printDiv('print')"
           class="material-icons md-36 uk-text-success">print</i>


    </div>
</div>
<div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content">

            <form action=" "  method="get" accept-charset="utf-8" novalidate id="group">
                <?php echo csrf_field(); ?>

                <div class="uk-grid" data-uk-grid-margin="">

                    <div class="uk-width-medium-1-5">
                        <div class="uk-margin-small-top">
                            <?php echo Form::select('year',
                            (['' => 'All academic years'] +$year ),
                            old("program",""),
                            ['class' => 'md-input parent','id'=>"parent",'placeholder'=>'select academic year'] ); ?>

                        </div>
                    </div>
                    <div class="uk-width-medium-1-5">
                        <div class="uk-margin-small-top">
                            <?php echo Form::select('level',
                            (['' => 'All levels'] +$level ),
                            old("level",""),
                            ['class' => 'md-input parent','id'=>"parent",'placeholder'=>'select level'] ); ?>

                        </div>
                    </div>

                    <div class="uk-width-medium-1-5">
                        <div class="uk-margin-small-top">

                            <?php echo Form::select('semester', array('1'=>'1st sem','2'=>'2nd sem','3' => '3rd sem'), null, ['placeholder' => 'select semester','id'=>'parent','class'=>'md-input parent'],old("semester",""));; ?>


                        </div>
                    </div>
                    <div class="uk-width-medium-1-5">
                        <div class="uk-margin-small-top">
                            <?php echo Form::select('department',
                            (['' => 'departments'] +$department  ),
                            old("department",""),
                            ['class' => 'md-input parent','id'=>"parent"] ); ?>

                        </div>
                    </div>
                    <div class="uk-width-medium-1-5">
                        <div class="uk-margin-small-top">
                            <?php echo Form::select('lecturer',
                            (['' => 'lecturer'] +$lecturer  ),
                            old("lecturer",""),
                            ['class' => 'md-input parent','id'=>"parent"] ); ?>

                        </div>
                    </div>
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

                            <?php echo Form::select('by', array('COURSE_CODE'=>'Course Code','COURSE_NAME'=>'Course Name' ), null, ['placeholder' => 'select criteria','class'=>'md-input'],old("by",""));; ?>


                        </div>
                    </div>
                    <div class="uk-width-medium-1-5">
                        <div class="uk-margin-small-top">
                            <input type="text" style=" "   name="search"  class="md-input" placeholder="search lecturer name or staff id">
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
 <p>&nbsp;</p>
<div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content">
            <div class="uk-overflow-container" id='print'>
                <center><span class="uk-text-success uk-text-bold"><?php echo $data->total(); ?> Records</span></center>
                <table class="uk-table uk-table-hover uk-table-condensed uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id="ts_pager_filter">
                    <thead>
                    <tr>
                        <th class="filter-false remove sorter-false" data-priority="6">NO</th>
                        <th>CODE</th>
                        <th  style="text-align:center">COURSE</th>
                        <th>PROGRAMME</th>
                        <th style="text-align:center">CREDIT</th>

                        <th style="text-align:center">LEVEL</th>
                        <th style="text-align:center">SEMESTER</th>
                        <th style="text-align:center">ACADEMIC YEAR</th>
                        <th style="text-align:left">LECTURER</th>

                        <th  class="filter-false remove sorter-false uk-text-center" colspan="2" data-priority="1">ACTION</th>

                    </tr>
                    </thead>
                    <tbody>

                    <?php $n=0;?>
                    <?php foreach($data as   $row): ?>
                        <?php $n++;?>



                        <tr align="">
                            <td><?php echo $n;?></td>
                            <td><?php echo e(@strtoupper($row->coursecode)); ?></td>
                            <td><?php echo e(@strtoupper($row->courseDetails->course->COURSE_NAME)); ?></td>
                            <td><?php echo e(@strtoupper($row->courseDetails->course->programme->PROGRAMME)); ?></td>
                            <td><?php echo e(@strtoupper($row->courseDetails->course->COURSE_CREDIT)); ?></td>
                            <td><?php echo e(@strtoupper($row->courseDetails->course->COURSE_LEVEL)); ?></td>
                            <td><?php echo e(@strtoupper($row->courseDetails->course->COURSE_SEMESTER)); ?></td>
                            <td><?php echo e(@strtoupper($row->academic_year)); ?></td>
                            <td><?php echo e(@strtoupper($row->lecturerDetails->fullName)); ?></td>
                            <td>


                                <a onclick="return MM_openBrWindow('<?php echo e(url("print_report_qa/$row->lecturer/lecturer/$row->semester/sem/$row->course/course")); ?> ', 'mark', 'width=800,height=500')">Print Lecturer Report</a>

                            </td>


                        </tr>

                    <?php endforeach; ?>
                    </tbody>

                </table>
                <?php echo (new Landish\Pagination\UIKit($data->appends(old())))->render(); ?>

            </div>
        </div>
        <div class="md-fab-wrapper">
            <a class="md-fab md-fab-small md-fab-accent md-fab-wave" href="<?php echo url('/create_course'); ?>">
                <i class="material-icons md-18">&#xE145;</i>
            </a>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
<script type="text/javascript">

    $(document).ready(function(){

        $(".parent").on('change',function(e){

            $("#group").submit();

        });
    });

</script>
<script src="<?php echo url('public/assets/js/select2.full.min.js'); ?>"></script>
<script>
    $(document).ready(function(){
        $('select').select2({ width: "resolve" });


    });


</script>
<!--  notifications functions -->
<script src="public/assets/js/components_notifications.min.js"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>