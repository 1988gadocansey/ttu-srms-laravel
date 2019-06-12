<?php $__env->startSection('content'); ?>

 
      
                    <?php $sys = app('App\Http\Controllers\SystemController'); ?>
          <div class="uk-width-xLarge-1-1" style="margin-left:1px;margin-top:-50px">
   
    <div class="md-card">
        <div class="md-card-content">         
          <div class="uk-width-xL">            
                      <div class="uk-overflow-container" id='print'>
                        <img class=" " style="width:200px;height: auto; margin-top: -25; margin-right: 150" src="<?php echo url('public/assets/img/logo.png'); ?>"  align="left" alt=" Logo of TTU here"    />
                             <p style="margin-top: 5; font-size: 14">ATTENDANCE SHEET<br/>
                             ACADEMIC YEAR :  <?php echo $year; ?><br/>
                             SEMESTER : 2<br/><br/>
                             PROGRAMME: <?php echo $sys->getProgram($course); ?>     </p>
     
                 <table   class="uk-table uk-table-hover uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id="ts_pager_filter"> 
             
               <thead>
                 <tr>
                     <th>N<u>o</u></th>
                     
                     <?php /* <th  style="text-align:">Photo</th> */ ?>
                      
                      <th>Index N<u>o</u></th>
                     <th data-priority="6"style="text-align:">Name</th>
                       <th style="text-align: ">Owing</th>
                     <th style="text-align">Signature</th>
                     
                      
                                      
                </tr>
             </thead>
             <tbody>
                                        
                             <?php foreach($mark as $index=> $row): ?> 



                            <tr align="">
                                <td width="10" style="font-size: 12"> <?php echo e($mark->perPage()*($mark->currentPage()-1)+($index+1)); ?>&nbsp;&nbsp;&nbsp;</td>
                                <?php /*<td><img class=" " style="width:100px;height: auto" src="<?php echo url('public/albums/students/'.$row->INDEXNO.'.JPG'); ?>" alt=" Picture of Student Here"    /></td>*/ ?>

                                <td width="40" style="font-size: 12"> <?php echo e($row->INDEXNO); ?>&nbsp;&nbsp;&nbsp;</td>
                                    <td width="80" style=" font-size: 12"> <?php echo e($row->NAME); ?>&nbsp;&nbsp;&nbsp;</td>
                               <td  style="font-size: 12"> <?php echo e($row->BILL_OWING); ?>&nbsp;&nbsp;&nbsp;</td>
                            
                                  <td valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>

                            </tr>
                             <?php endforeach; ?>
                        </tbody>
                                    
                             </table>
           
     </div>
  
          </div>
                 
           </div>
           </div></div>     
      
 
 
        
 <?php $__env->stopSection(); ?>
 
<?php $__env->startSection('js'); ?>
 <script type="text/javascript">
  
$(document).ready(function(){
window.print();
//window.close();
});

</script>
  
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.printlayout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>