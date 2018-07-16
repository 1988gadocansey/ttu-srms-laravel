@extends('layouts.app')

 
@section('style')
 
@endsection
 @section('content')
   <div class="md-card-content">
@if(Session::has('success'))
            <div style="text-align: center" class="uk-alert uk-alert-success" data-uk-alert="">
                {!! Session::get('success') !!}
            </div>
 @endif
 @if(Session::has('error'))
            <div style="text-align: center" class="uk-alert uk-alert-danger" data-uk-alert="">
                {!! Session::get('error') !!}
            </div>
 @endif
 
     @if (count($errors) > 0)

    <div class="uk-form-row">
        <div class="uk-alert uk-alert-danger" style="background-color: red;color: white">

              <ul>
                @foreach ($errors->all() as $error)
                  <li> {{  $error  }} </li>
                @endforeach
          </ul>
    </div>
  </div>
@endif
  </div>
    @inject('sys', 'App\Http\Controllers\SystemController')
 <h5 class="heading_c">Registration Statistics for the {{$years}}  {{$sem}}Sem Academic year</h5>
 
 <div style="">
     <div class="uk-margin-bottom" style="margin-left:750px" >
         <!-- <a  href="#new_task" data-uk-modal="{ center:true }"> <i title="click to send sms to students owing"   class="material-icons md-36 uk-text-success"   >phonelink_ring message</i></a>
 -->
         <a href="#" class="md-btn md-btn-small md-btn-success uk-margin-right" id="printTable">Print Table</a>
        <!--  <a href="#" class="md-btn md-btn-small md-btn-success uk-margin-right" id="">Import from Excel</a>
         -->
         <div class="uk-button-dropdown" data-uk-dropdown="{mode:'click'}">
             <button class="md-btn md-btn-small md-btn-success"> columns <i class="uk-icon-caret-down"></i></button>
             <div class="uk-dropdown">
                 <ul class="uk-nav uk-nav-dropdown" id="columnSelector"></ul>
             </div>
         </div>
         

                       
                          
                           
         <div style="margin-top: -5px" class="uk-button-dropdown" data-uk-dropdown="{mode:'click'}">
                                <button class="md-btn md-btn-small md-btn-success uk-margin-small-top">Export <i class="uk-icon-caret-down"></i></button>
                                <div class="uk-dropdown">
                                    <ul class="uk-nav uk-nav-dropdown">
                                         <li><a href="#" onClick ="$('#ts_pager_filter').tableExport({type:'csv',escape:'false'});"><img src='{!! url("public/assets/icons/csv.png")!!}' width="24"/> CSV</a></li>
                                           
                                            <li class="uk-nav-divider"></li>
                                            <li><a href="#" onClick ="$('#ts_pager_filter').tableExport({type:'excel',escape:'false'});"><img src='{!! url("public/assets/icons/xls.png")!!}' width="24"/> XLS</a></li>
                                            <li><a href="#" onClick ="$('#ts_pager_filter').tableExport({type:'doc',escape:'false'});"><img src='{!! url("public/assets/icons/word.png")!!}' width="24"/> Word</a></li>
                                            <li><a href="#" onClick ="$('#ts_pager_filter').tableExport({type:'powerpoint',escape:'false'});"><img src='{!! url("public/assets/icons/ppt.png")!!}' width="24"/> PowerPoint</a></li>
                                            <li class="uk-nav-divider"></li>
                                           
                                    </ul>
                                </div>
                            </div>
                       
                           
                            
                                                   
                                  <i title="click to print" onclick="javascript:printDiv('print')" class="material-icons md-36 uk-text-success"   >print</i>
                   
                            
                           
     </div>
 </div>
 <div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content">
            
                <form action=" "  method="get" accept-charset="utf-8" novalidate id="group">
                   {!!  csrf_field()  !!}
                    <div class="uk-grid" data-uk-grid-margin="">

                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                    {!! Form::select('year', 
                                (['' => 'All years'] +$year ), 
                                  old("year",""),
                                    ['class' => 'md-input parent','id'=>"parent",'placeholder'=>'select academic year'] )  !!}
                         </div>
                        </div>
                         <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                 
                                              {!!  Form::select('semester', array('1'=>'1st sem','2'=>'2nd sem','3' => '3rd sem'), null, ['placeholder' => 'select semester','id'=>'parent','class'=>'md-input parent'],old("semester","")); !!}
                          
                            </div>
                        </div>
                        
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                 
                                             {!!  Form::select('level', array( '100H'=>'HND Level 100','200H' => 'HND Level 200', '300H' => 'HND Level 300','100BTT'=>'BTECH TOP UP 100','200BTT'=>'BTECH TOP UP 200','100NT'=>'NON-TER 100','200NT'=>'NON-TER 200'), null, ['placeholder' => 'select level','id'=>'parent','class'=>'md-input parent'],old("level","")); !!}
                          
                            </div>
                        </div>
                       
                        

                        
                         <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">                            
                                
                              <button class="md-btn save md-btn-small md-btn-success uk-margin-small-top" type="submit"><i class="material-icons">search</i></button> 
                          
                            </div>
                        </div>
                         
                         
                        
                        
                        
                        
                    
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
          <center><span class="uk-text-success uk-text-bold">{!! $data->total()!!} Records</span></center>
        
                 <table class="uk-table uk-table-hover uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id="ts_pager_filter"> 
                                  <thead>
                                        <tr>
                                            <th class="uk-width-1-10">N<u>o</u></th>
                                            <th class=" uk-text-small"  >PROGRAMME</th>
                                           
                                            
                                            <th>HND 100</th>
                                            <th>HND 200</th>
                                            <th>HND 300</th>
                                            <th>BTT 100</th>
                                            <th>BTT 200</th>
                                            <th>NT 100</th>
                                            <th>NT 200</th>
                                            <th class=" uk-text-small"  >TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                     <?php
                                    $tot100H = 0;
                                    $tot200H = 0;
                                    $tot300H = 0;
                                    $tot100BTT = 0;
                                    $tot200BTT = 0;
                                    $tot100NT = 0;
                                    $tot200NT = 0;
                                    $tots100H = 0;
                                    $tots200H = 0;
                                    $tots300H = 0;
                                    $tots100BTT = 0;
                                    $tots200BTT = 0;
                                    $tots100NT = 0;
                                    $tots200NT = 0;
                                     ?>    
                                     @foreach($data as $index=> $row) 
                                     <tr>
                                         <td> {{ $data->perPage()*($data->currentPage()-1)+($index+1) }} </td>
                                         
                                         <td class="uk-text-upper">{{@$sys->getProgram($row->PROGRAMMECODE)}}
                                        <?php 
                                        $tot100H = $sys->getStudentsTotalPerProgramLevel2($row->PROGRAMMECODE,'100H');
                                        $tot200H = $sys->getStudentsTotalPerProgramLevel2($row->PROGRAMMECODE,'200H');
                                        $tot300H = $sys->getStudentsTotalPerProgramLevel2($row->PROGRAMMECODE,'300H');
                                        $tot100BTT = $sys->getStudentsTotalPerProgramLevel2($row->PROGRAMMECODE,'100BTT');
                                        $tot200BTT = $sys->getStudentsTotalPerProgramLevel2($row->PROGRAMMECODE,'200BTT');
                                        $tot100NT = $sys->getStudentsTotalPerProgramLevel2($row->PROGRAMMECODE,'100NT');
                                        $tot200NT = $sys->getStudentsTotalPerProgramLevel2($row->PROGRAMMECODE,'200NT'); 
                                        ?>
                                       </td>


                                        <td style="text-align: center"class=''>{{$tot100H}} </td> <?php $tots100H += $tot100H; ?>
                                        <td style="text-align: center"class=''>{{$tot200H}} </td> <?php $tots200H += $tot200H; ?>
                                        <td style="text-align: center"class=''>{{$tot300H}}</td> <?php $tots300H += $tot300H; ?>
                                    <td style="text-align: center"class=''>{{$tot100BTT}}</td> <?php $tots100BTT += $tot100BTT; ?>
                                    <td style="text-align: center"class=''>{{$tot200BTT}}</td> <?php $tots200BTT += $tot200BTT; ?>
                                    <td style="text-align: center"class=''>{{$tot100NT}}</td> <?php $tots100NT += $tot100NT; ?>
                                    <td style="text-align: center"class=''>{{$tot200NT}}</td> <?php $tots200NT += $tot200NT; ?>
                                     <td style="text-align: center"><?php echo ($tot100H+$tot200H+$tot300H+$tot100BTT+$tot200BTT+$tot100NT+$tot200NT); ?></td>
                                     </tr>
                                        @endforeach
                                    <tr>
                                        <td style="text-align: center"></td>
                                        <td style="text-align: center">TOTAL</td>
                                        
                                        <td style="text-align: center"><?php echo $tots100H; ?></td>
                                        <td style="text-align: center"><?php echo $tots200H; ?></td>
                                        <td style="text-align: center"><?php echo $tots300H; ?></td>
                                        <td style="text-align: center"><?php echo $tots100BTT; ?></td>
                                        <td style="text-align: center"><?php echo $tots200BTT; ?></td>
                                        <td style="text-align: center"><?php echo $tots100NT; ?></td>
                                        <td style="text-align: center"><?php echo $tots200NT; ?></td>
                                        <td style="text-align: center"><?php echo ($tots100H+$tots200H+$tots300H+$tots100BTT+$tots200BTT+$tots100NT+$tots200NT); ?></td>
                                    </tr>    
                                         
                                    </tbody>
                                    
                             </table>
          <table>
             
          </table>
        
           
     </div>
  
     </div>
 
 </div>
</div>
  
@endsection
@section('js')
 
 <script src="{!! url('public/assets/js/select2.full.min.js') !!}"></script>
<script>
$(document).ready(function(){
  $('select').select2({ width: "resolve" });

  
});


</script>
 <!--  notifications functions -->
    <script src="public/assets/js/components_notifications.min.js"></script>
@endsection