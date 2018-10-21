 
   
<?php $__env->startSection('style'); ?>
 
        <script src="<?php echo url('public/assets/js/jquery.min.js'); ?>"></script>
 
        <script src="<?php echo url('public/assets/js/jquery-ui.min.js'); ?>"></script>
 
    <link rel="stylesheet" href="<?php echo url('public/assets/css/jquery-ui.css'); ?>" media="all">
        
<?php $__env->stopSection(); ?>
 <?php $__env->startSection('content'); ?>
  <div class="md-card-content">
<?php if(Session::has('success')): ?>
            <div style="text-align: center" class="uk-alert uk-alert-success  uk-alert-close" data-uk-alert="">
                <?php echo Session::get('success'); ?>

            </div>
 <?php endif; ?>
 
  
     <?php if(Session::has('error')): ?>

    
        <div class="uk-alert uk-alert-danger  uk-alert-close" style="background-color: red;color: white" data-uk-alert="">
  <?php echo Session::get('error'); ?>

               
        </div>
   
<?php endif; ?>
 
 
 </div>
 <div align="center">
  <h5 class="uk-heading_c">Student Transcript</h5>
 <div class="uk-width-small-1-2">
     <div class="md-card">
         <div class="md-card-content" style="">
            
            <center>
             <form method="POST" action=""  accept-charset="utf-8"  name="applicationForm"  v-form>
                  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
             <div class="uk-grid" data-uk-grid-margin>
                 
                 <div class="uk-width-medium-1-2">
                     <div class="uk-form-row">
                         <div class="uk-grid" data-uk-grid-margin>
                             <div class="uk-width-1-1">

                                 <input type="text" value="" id="q" class="md-input md-input-success" required="" name="q" placeholder="type index no or name" v-model="q" v-form-ctrl=""/>
                             </div>

                         </div>
                     </div>
                     <p>&nbsp;</p>
                     <div class="uk-grid"  >
                         <div class="uk-width-1-1">
                             <input type="submit"  v-show="applicationForm.$valid" class="md-btn md-btn-primary" value="Go"  />
                         </div>
                     </div>
                 </div>
             </div>
             </div>
             </form></center>
         </div>
     </div>
 </div></div></center>
  
  <script type="text/javascript">
           //Javascript
$(function()
{
	 $( "#q" ).autocomplete({
	  source: "<?php echo e(url('search/autocomplete')); ?>",
	  minLength: 3,
	  select: function(event, ui) {
	  	$('#q').val(ui.item.value);
                
	  }
	});
});
        </script>
 <?php $__env->stopSection(); ?>
 
<?php $__env->startSection('js'); ?>
 
  <script>


//code for ensuring vuejs can work with select2 select boxes
Vue.directive('select', {
  twoWay: true,
  priority: 1000,
  params: [ 'options'],
  bind: function () {
    var self = this
    $(this.el)
      .select2({
        data: this.params.options,
         width: "resolve"
      })
      .on('change', function () {
        self.vm.$set(this.name,this.value)
        Vue.set(self.vm.$data,this.name,this.value)
      })
  },
  update: function (newValue,oldValue) {
    $(this.el).val(newValue).trigger('change')
  },
  unbind: function () {
    $(this.el).off().select2('destroy')
  }
})


var vm = new Vue({
  el: "body",
  ready : function() {
  },
 data : {
   
   
 options: [    ]  
    
  },
   
})

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>