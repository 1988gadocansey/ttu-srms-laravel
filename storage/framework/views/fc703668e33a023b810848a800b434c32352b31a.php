<?php $__env->startSection('style'); ?>
    <style>
        .md-card{
            width: auto;

        }

    </style>
    <script src="<?php echo url('public/assets/js/jquery.min.js'); ?>"></script>

    <script src="<?php echo url('public/assets/js/jquery-ui.min.js'); ?>"></script>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php if(Session::has('success')): ?>
        <div style="text-align: center" class="uk-alert uk-alert-success" data-uk-alert="">
            <?php echo Session::get('success'); ?>

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
    <div class="uk-width-xLarge-1-10">
        <div class="md-card">
            <div class="md-card-content" style="">

                <h5 class=" ">Create Zones here</h5>
                <form    id="form" accept-charset="utf-8" method="POST" name="applicationForm"  v-form>
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <table id="paymentTable" class="uk-table"border="0" style="font-weight:bold">
                        <tr id="paymentRow" payment_row="payment_row">
                            <td valign="top">Zone Name &nbsp;<input type="text"   class="md-input md-input" required=""   v-model='zones[]' v-form-ctrl='' name="zones[]"  ></td>




                            <td valign="top">Sub Zone &nbsp;<input type="text"   class="md-input md-input" required=""   v-model='sub[]' v-form-ctrl='' name="sub[]"  ></td>



                            <td valign="top" id="insertPaymentCell"><button  type="button" id="insertPaymentRow" class="md-btn md-btn-primary md-btn-small " title='click to add more ' ><i class="sidebar-menu-icon material-icons">add</i></button></td></tr>

                    </table>
                    <table align="center">

                        <tr><td><input type="submit" value="Save" id='save'v-show="applicationForm.$valid"  class="md-btn   md-btn-success uk-margin-small-top">
                                <input type="reset" value="Cancel" class="md-btn   md-btn-default uk-margin-small-top">
                            </td></tr></table>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>

        
         
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>