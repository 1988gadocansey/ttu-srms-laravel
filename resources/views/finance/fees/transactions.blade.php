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
 
 
 </div>
 <div class="uk-modal" id="new_task">
     <div class="uk-modal-dialog">
         <div class="uk-modal-header">
             <h4 class="uk-modal-title">Send sms  here</h4>
         </div>
         <form action="{!! url('/fireOwingSMS')!!}" method="POST">
             <input type="hidden" name="_token" value="{!! csrf_token() !!}"> 


             <textarea cols="30" rows="4" name="message"class="md-input" required=""></textarea>


             <div class="uk-modal-footer uk-text-right">
                 <button type="submit" class="md-btn md-btn-flat md-btn-flat-primary md-btn-wave" id="snippet_new_save"><i   class="material-icons"   >smartphone</i>Send</button>    
                 <button type="button" class="md-btn md-btn-flat uk-modal-close md-btn-wave">Close</button>
             </div>
         </form>
     </div>
 </div>
 <h5>Students  payment reports</h5>  
 <div style="">
     <div class="uk-margin-bottom" style="margin-left:1080px" >
<!--         <a  href="#new_task" data-uk-modal="{ center:true }"> <i title="click to send sms to students owing"   class="material-icons md-36 uk-text-success"   >phonelink_ring message</i></a>-->

<!--         <a href="#" class="md-btn md-btn-small md-btn-success uk-margin-right" id="printTable">Print Table</a>
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
 <!-- filters here -->
  @inject('fee', 'App\Http\Controllers\FeeController')
   @inject('sys', 'App\Http\Controllers\SystemController')
 <div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content">
            
                <form action=" "  method="get" accept-charset="utf-8" novalidate id="group">
                   {!!  csrf_field()  !!}
                    <div class="uk-grid" data-uk-grid-margin="">

                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                    {!! Form::select('program', 
                                (['' => 'All programs'] +$program ), 
                                  old("program",""),
                                    ['class' => 'md-input parent','id'=>"parent",'placeholder'=>'select program'] )  !!}
                         </div>
                        </div>
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                    {!! Form::select('level', 
                                (['' => 'All levels'] +$level ), 
                                  old("level",""),
                                    ['class' => 'md-input parent','id'=>"parent",'placeholder'=>'select level'] )  !!}
                         </div>
                        </div>
                       
                       <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                    {!! Form::select('bank', 
                                (['' => 'Select Bank'] +$bank ), 
                                  old("bank",""),
                                    ['class' => 'md-input parent','id'=>"parent"] )  !!}
                         </div>
                        </div>
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                    {!! Form::select('year', 
                                (['' => 'Select Academic Year'] +$year ), 
                                  old("year",""),
                                    ['class' => 'md-input parent','id'=>"parent"] )  !!}
                         </div>
                        </div>
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                    {!! Form::select('users', 
                                (['' => 'Fees collections by cashiers'] +$users ), 
                                  old("users",""),
                                    ['class' => 'md-input parent','id'=>"parent"] )  !!}
                         </div>
                        </div>
                         
                         <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                  {!!  Form::select('filter', array('='=>'Equals','>='=>'Greater than or equal','<='=>'Less than or equal'), null, ['placeholder' => 'select filter','id'=>'parent','class'=>'md-input'], old("type","")); !!}
                         </div>
                        </div>

                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">                            
                                <input type="text" style=" " required="" name="amount" value="{{ old("amount") }}" class="md-input" placeholder="amount">
                            </div>
                        </div>
                         <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">                            
                                <input type="text" style=" "   name="indexno"  class="md-input" placeholder="search student by index number">
                            </div>
                        </div>
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">   
                                <div class="uk-input-group">
                                        <span class="uk-input-group-addon"><i class="uk-input-group-icon uk-icon-calendar"></i></span>
                             <input type="text"  style="" data-uk-datepicker="{format:'YYYY-MM-DD'}" value="{{ old("from_date") }}" name="from_date" id="invoice_dp" class="md-input" placeholder="Start of Financial Year ">
                             </div>
                         </div>
                        </div>

                        <div class="uk-width-medium-1-5">
                            
                            <div class="uk-margin-small-top">    
                               <div class="uk-input-group">
                                        <span class="uk-input-group-addon"><i class="uk-input-group-icon uk-icon-calendar"></i></span> 
                            <input type="text" style="" data-uk-datepicker="{format:'YYYY-MM-DD'}" value="{{ old("to_date") }}" name="to_date"  class="md-input" placeholder="End of Financial Year">
                               </div>
                            </div>
                        </div>
                        
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                
                                  {!!  Form::select('type', array('Part payment'=>'Part payment of school fees','Full payment'=>'Full payment of school fees','Late Registration Fee'=>'Late Registration Fee','Transcript Order'=>'Transcript Order'), null, ['placeholder' => 'select payment type','id'=>'parent','class'=>'md-input parent'], old("type","")); !!}
                        </div>
                        </div>
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                  {!!  Form::select('mode', array('PAY IN SLIP'=>'PAY IN SLIP','Cheque'=>'Cheque','Receipts'=>'Receipts','Cash'=>'Cash','Bursary'=>'Bursary','Scholarship'=>'Scholarship'), null, ['placeholder' => 'select payment mode','id'=>'parent','class'=>'md-input parent'], old("type","")); !!}
                         </div>
                        </div>
                         <div class="uk-width-medium-1-10" style=" ">
                            <div class="uk-margin-small-top">                            
                          
                            <button class="md-btn  md-btn-small md-btn-success uk-margin-small-top" type="submit"><i class="material-icons">search</i></button> 
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
  

     <div class="uk-overflow-container" id='print'>
         <center><span class="uk-text-success uk-text-bold">{!! $data->total()!!} Records</span></center>
                <table class="uk-table uk-table-hover uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id="ts_pager_filter"> 
                                  <thead>
                                        <tr>
                                     <th class="filter-false remove sorter-false" data-priority="6">NO</th>
                                     
                                  
                                     <th>INDEXNO</th>
                                      <th>NAME</th>
                                      <th>PROGRAMME</th>
                                      <th>LEVEL</th> 
                                   
                                      <th>YEAR</th>
                                      <th>BANK</th>
                                      <th>PAYMENT DETAILS</th>
                                    <th>AMOUNT</th>
                                      <th>RECEIPT</th>
                                      
                                      <th>BANK DATE</th>
                                      <th>ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                         @foreach($data as $index=> $row) 
                                         
                                        
                                        
                                         
                                        <tr align="">
                                            <td> {{ $data->perPage()*($data->currentPage()-1)+($index+1) }} </td>
                                                 <td> {{ @$row->INDEXNO }}</td>
                                            <td> {{ @$row->student->NAME }}</td>
                                            <td>{!! strtoupper(@$sys->getProgram($row->PROGRAMME ))!!}</td>
                                            <td> {{ @$row->levels->slug }}</td>
                                            
                                             <td> {{ @$row->YEAR }}</td>
                                            <td> {{ @$row->bank->NAME }}</td>
                                            <td> {{ @$row->PAYMENTTYPE }}</td>
                                               <td> {{ @$row->AMOUNT }}</td>
                                            <td> {{ @$row->RECEIPTNO }}</td>
                                           
                                            <td> {{ date('d/m/Y',@strtotime($row->TRANSDATE) )}}</td>
                                            <td>
                                                  
                                                      <a onclick="return MM_openBrWindow('{{url("printreceipt/" . trim(@$row->RECEIPTNO))}}', 'mark', 'width=800,height=500')" ><i title='Click to print receipt of this payment .. please allow popups on browser' class="md-icon material-icons">book</i></a> 
                           
                                                 {!!Form::open(['action' =>['FeeController@destroyPayment', 'id'=>$row->ID], 'method' => 'DELETE','name'=>'myform' ,'style' => 'display: inline;'])  !!}

                                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this payment??')" class="md-btn  md-btn-danger md-btn-small   md-btn-wave-light waves-effect waves-button waves-light" ><i  class="sidebar-menu-icon material-icons md-18">delete</i></button>
                                                        <input type='hidden'   value='{{$row->ID}}'/>  
                                                     {!! Form::close() !!}

                                                 
                                            </td>
                                               
                                        </tr>
                                         @endforeach
                                    </tbody>
                                    
                             </table>
         <div style="margin-left: 1089px" class="uk-text-bold uk-text-success"><td colspan=" ">TOTAL PAID GHC<u>  {{ $total }}</u></td></div>
          {!! (new Landish\Pagination\UIKit($data->appends(old())))->render() !!}
     </div>
     </div>
  
   
 </div>
 </div></div>
@endsection
@section('js')
 <script type="text/javascript">
      
$(document).ready(function(){
 
$(".parent").on('change',function(e){
 
   $("#group").submit();
 
});
});

</script>
 <script src="{!! url('public/assets/js/select2.full.min.js') !!}"></script>
<script>
$(document).ready(function(){
  $('select').select2({ width: "resolve" });

  
});


</script>
 <!--  notifications functions -->
    <script src="public/assets/js/components_notifications.min.js"></script>
@endsection