<?php $__env->startSection('style'); ?>
<style>
     @media  print {
	#page1	{page-break-before:always;}
	.condition	{page-break-before:always;}
	#page2	{page-break-before:always;}
        #page3	{page-break-before:always;}
       #page4	{page-break-before:always;}
        .school	{page-break-before:always;}
	.page9	{page-break-inside:avoid; page-break-after:auto}
	  }
    .biodata{
        padding: 1px;
    }
    .uk-table td{
        border:none;
    }
    .capitalize{
        font-size: 12px;
        
    }
    strong {
        font-size: 12px;
     
}
</style>
  
<?php $__env->stopSection(); ?>
<div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content">
<div id="page1">
            <center><u><h6 class="heading_c uk-margin-bottom uk-text-bold" >PERSONAL RECORDS OF <?php echo e($student->NAME); ?></h6></u></center>

<?php $__env->startSection('content'); ?>
 
     <?php $sys = app('App\Http\Controllers\SystemController'); ?>
     <center><h5>BIODATA</h5></center>
     <hr>
     <table class="uk-table uk-table-nowrap ">
        
        <tr>
          <td width="210" class="uppercase" align="right"><strong>INDEXNO N<u>O</u></strong></td>
          <td width="408" class="capitalize"><?php echo e($student->INDEXNO); ?></td>
          <td valign="top" rowspan="6" align="left" colspan="2">
                     <img   style="width:150px;height: auto; margin-left: 100px"  <?php
                                     $pic = $student->INDEXNO;
                                     echo $sys->picture("{!! url(\"public/albums/students/$pic.jpg\") !!}", 90)
                                     ?>   src='<?php echo e(url("public/albums/students/$pic.JPG")); ?>' onerror="this.onerror=function my(){return this.src='<?php echo e(url("public/albums/students/USER.JPG")); ?>';};this.src='<?php echo e(url("public/albums/students/$pic.jpg")); ?>';"    />
             </td>								
        </tr>
        <tr>
            <td width="210" class="uppercase" align="right"><strong>LEVEL</strong></td>
          
          <td width="408" class="capitalize"><?php echo e(@$student->levels->slug); ?></td>
        </tr>
        <tr>
          <td class="uppercase" align="right"><strong>SURNAME:</strong></td>
          <td class="capitalize"><?php echo strtoupper($student->SURNAME)  ?></td>
        </tr>
         <tr>
          <td class="uppercase" align="right"><strong>FIRST NAME:</strong></td>
          <td class="capitalize"><?php echo strtoupper($student->FIRSTNAME) ?></td>
        </tr>
        <tr>
          <td class="uppercase" align="right"><strong>AGE</strong>:</td>
          <td class="capitalize"><?php   echo  $student->AGE ?>yrs</td>
        </tr>
        <tr>
          <td class="uppercase" align="right"><strong>GENDER</strong>:</td>
          <td class="capitalize"><?php   echo strtoupper($student->SEX)?></td>
        </tr>
        <tr>
            <td class="uppercase" align="right"><strong>MARITAL STATUS</strong></td>
            <td class="capitalize"><?php echo strtoupper($student->MARITAL_STATUS); ?></td>

          <td class="uppercase"  width="150" align="right"><strong>PROGRAMME:</strong></td>
          <td class="capitalize"><?php echo strtoupper($student->program->PROGRAMME); ?></td>
          
        
        </tr>
       
      
       <tr>
          <td class="uppercase" align="right"><strong>FEES OWING:</strong></td>
          <td class="capitalize">GHC<?php echo  $student->BILL_OWING ?></td>
          <td class="uppercase" align="right"><strong>CLASS:</strong></td>
          <td class="capitalize"><?php echo strtoupper($student->CLASS); ?></td>
        </tr>
        
         <tr>
          <td class="uppercase" align="right"><strong>EMAIL:</strong></td>
          <td class="capitalize"><?php echo strtoupper($student->EMAIL); ?></td>
          <td class="uppercase" align="right"><strong>CGPA:</strong></td>
          <td class="capitalize"><?php echo strtoupper($student->CGPA); ?></td>
        </tr>
        <tr>
          <td class="uppercase" align="right"><strong>STATUS:</strong></td>
          <td class="capitalize"><?php echo strtoupper($student->STATUS); ?></td>
          <td class="uppercase" align="right"><strong>YEAR GROUP:</strong></td>
          <td class="capitalize"><?php echo $student->GRADUATING_GROUP; ?></td>
        </tr>
        <?php if(@\Auth::user()->department=='Tptop'): ?>
        <tr>
          <td class="uppercase" align="right"><strong>PHONE:</strong></td>
          <td class="capitalize"><?php echo "+233".\substr($student->TELEPHONENO,-9); ?></td>
        </tr>
        <?php endif; ?>
         
     </table>
     <fieldset class=""><legend class="uk-text-bold heading_c">LOCATION DATA</legend>
      <table>
          <tr>
              <td>
                  <table>
                      <tr>
                        <td class="uppercase" ><strong>HOMETOWN:</strong></td>
                        <td class="capitalize"><?php echo strtoupper($student->HOMETOWN); ?></td>

                      </tr>
                      <tr>
                        <td class="uppercase"><strong>CONTACT ADDRESS</strong></td>
                        <td class="capitalize"><?php echo strtoupper($student->ADDRESS); ?></td>

                      </tr>
                      
                  </table>
              </td>
              <td>
                  <table>
                  <tr>
                        <td class="uppercase"><strong>NATIONALITY</strong></td>
                        <td class="capitalize"><?php echo strtoupper($student->COUNTRY ); ?></td>

                      </tr>
                       <tr>
                        <td class="uppercase"><strong>RELIGION</strong></td>
                        <td class="capitalize"><?php echo strtoupper($student->RELIGION); ?></td>

                      </tr>
                  </table>
              </td>
              <td>
                <table>
                    <tr>
                        <td class="uppercase"  ><strong>RESIDENTIAL ADDRESS:</strong></td>
                        <td class="capitalize"><?php echo strtoupper($student->RESIDENTIAL_ADDRESS); ?></td>

                      </tr>
                      <tr>
                        <td class="uppercase"  ><strong>HOMETOWN REGION</strong></td>
                        <td class="capitalize"><?php echo strtoupper($student->REGION); ?></td>

                      </tr>
                </table>
                  
                      
              </td>
              <td>
               
              </td>
          </tr>
      </table>
      <table>
                 <tr>
                        <td class="uppercase"><strong>HOSTEL NAME</strong></td>
                        <td class="capitalize"><?php echo strtoupper( $student->HOSTEL); ?></td>
                
                  
                      </tr>
                      
                </table>
        </fieldset>
     <fieldset class=""><legend class="uk-text-bold heading_c">GUARDIAN DATA</legend>
      <table>
          <tr>
              <td>
                  <table>
                      <tr>
                        <td class="uppercase" ><strong>GUARDIAN NAME:</strong></td>
                        <td class="capitalize"><?php echo strtoupper($student->GUARDIAN_NAME); ?></td>

                      </tr>
                      <tr>
                        <td class="uppercase"><strong>GUARDIAN ADDRESS</strong></td>
                        <td class="capitalize"><?php echo strtoupper($student->GUARDIAN_ADDRESS); ?></td>

                      </tr>
                       
                  </table>
              </td>
              <td>
                <table>
                    <tr>
                        <td class="uppercase"  ><strong>GUARDIAN PHONE:</strong></td>
                        <td class="capitalize"><?php echo strtoupper($student->GUARDIAN_PHONE); ?></td>

                      </tr>
                      <tr>
                        <td class="uppercase"  ><strong>GUARDIAN OCCUPATION</strong></td>
                        <td class="capitalize"><?php echo strtoupper($student->GUARDIAN_OCCUPATION); ?></td>

                      </tr>
                       
                </table>
              </td>
          </tr>
          
          
      </table>
       </fieldset>
</div>
         <?php if(!empty($trail)): ?>
            <div id="page2">
                 <center><h5>ACADEMIC ISSUES (FLAGS)</h5></center>
                    <hr>
                  
                    <div class="uk-overflow-container" >
                <center><span class="uk-text-success uk-text-bold"><?php echo $trail->total(); ?> Records</span></center>
                <table class="uk-table " id="ts_pager_filter"> 
                    <thead>
                        <tr>
                            <th class="filter-false remove sorter-false" >NO</th>
                            <th data-priority="6">LEVEL</th>
                            <th data-priority="6">SEMESTER</th>
                            <th data-priority="6">COURSE</th>
                            <th data-priority="6">CREDIT</th>
                            <th>GRADE</th>
                            <th>ACADEMIC YEAR</th>

                   
                </tr></thead>
                <tbody>
                    <?php foreach($trail as $index=> $row): ?> 




                    <tr align="">
                        <td> <?php echo e($trail->perPage()*($trail->currentPage()-1)+($index+1)); ?> </td>
                        <td> <?php echo e(@$row->level); ?></td>
                         <td> <?php echo e(@$row->sem); ?></td>
                         <td> <?php echo e(strtoupper(@$row->courseMount->course->COURSE_NAME)); ?></td>
                           <td> <?php echo e(@$row->credits); ?></td>
                          <td> <?php echo e(@$row->grade); ?></td>
                           <td> <?php echo e(@$row->year); ?></td>

                    </tr>
                    <?php endforeach; ?>
                </tbody>
                </table>
                <?php else: ?>
                <span class="uk-text-success uk-text-bold">NO ACADEMIC ISSUES</span>
                <?php endif; ?>
                    </div>
                 
            </div>
            <P>&nbsp;</P>
                                <div class="visible-print text-center" align='center'>
                                    <?php echo QrCode::size(100)->generate(Request::url());; ?>


                                </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
 
<?php echo $__env->make('layouts.printlayout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>