<?php $__env->startSection('content'); ?>
<?php $help = app('App\Http\Controllers\SystemController'); ?>
<div align="center"  >
<style>
    td{
        font-size: 13px
    }
    .biodata{
        border-collapse: collapse;
    border-spacing: 0;
    
    margin-bottom: 15px;
    }
    .biodata td{
        padding:0px;
    }
    .uk-table {
    border-collapse: collapse;
    border-spacing: 0;
    margin-bottom: 15px;
    width:821px;
}
/*.uk-table td{
    border:none;
}
.uk-table th{
    border-collapse: collapse
}*/
        </style>
                 
               

                    <?php echo e($student); ?>

                    
                    <?php echo e($grade); ?>

                    
                    
               

</div>
        <?php $__env->stopSection(); ?>

        <?php $__env->startSection('js'); ?>
        <script type="text/javascript">

         //window.print();
 

        </script>

        <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.printlayout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>