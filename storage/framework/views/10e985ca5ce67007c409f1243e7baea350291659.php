 
<?php $__env->startSection('style'); ?>
       <?php $obj = app('App\Http\Controllers\SystemController'); ?>
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
  
 </div> 
 
  
     <div class="uk-width-large-8-10" style="margin-left: 100px">
         <h3 class="heading_c uk-margin-bottom">Update Phone Number</h3>
         <p>SMS notifications would be sent to this contact </p>
         <div class="md-card">
             <div class="md-card-content">
                
                 <form action="<?php echo e(url('users/update/phone')); ?>" method="post" class="form-horizontal row-border"   id="form" data-validate="parsley" >
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                     <div class="uk-grid" data-uk-grid-margin="">
                         
                        <div class="uk-width-medium-3-10">
                            <label for="product_search_price">Phone N<u>o</u></label>
                            <input  type="text" class="md-input" name="phone" required="" maxlength="10"   pattern='^[0-9]{10}$'  >
                        </div>
                      
                        <div class="uk-width-medium-2-10 uk-text-center">
                               <button type="submit" class="md-btn md-btn-primary uk-margin-small-top"><i class=" "></i>Update</button>
                        </div>
                    </div>
                </div>
                 
                 
                 
                 </form>

             </div>
         </div>
     
 <?php $__env->stopSection(); ?>
<?php $__env->startSection('js'); ?>
  
<script>
    
 
 var oTable = $('#gad').DataTable({
     
        
        processing: true,
        serverSide: true,
        ajax: {
            url:  "<?php echo route('power_users.data'); ?>"
             
        },
        columns: [
           
        
          {data: 'id', name: 'users.id'},
           {data: 'staffID', name: 'tpoly_workers.staffID'},
           
            {data: 'Photo', name: 'Photo', orderable: false, searchable: false},
            
              {data: 'name', name: 'users.name'},
               {data: 'email', name: 'users.email'},
            {data: 'department', name: 'users.department'},
            {data: 'role', name: 'users.role'},]
              
    });
    

    
</script>
 
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>