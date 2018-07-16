@extends('layouts.printlayout')

@section('content')

 <div align="" style="margin-left: 1px">
      
         <div class="md-card" >
             <div   class="uk-grid" data-uk-grid-margin>
               <div class="uk-grid-1-1 uk-container-center">
                     @inject('sys', 'App\Http\Controllers\SystemController')
                  <?php for ($i = 1; $i <= 1; $i++) {?>

  <table   border="0">
        <tr>
          <td   style="border:dashed; text-align: left;"><table width="638" height="451" border="0" cellspacing="1">
            <tr>
              <td colspan="4">
                  <table   border="0">
                    <tr>
                      <td width="10">&nbsp;</td>
                      <td width="622"><div align="center" >
                        <div  class=" uk-margin-bottom-remove" >
                            
                            <img src='{{url("public/assets/img/logo.png")}}' style="float:left;width:200px;height: auto; margin-top: -20;"/>
                            <h3>Directorate of Finance - {!! $transaction->FEE_TYPE !!}  Receipt</h3></div>
                              <P></P>
                              <span class="uk-text-bold">Total academic year fees GHC{!!  @$student->BILLS!!}</span>
                         
                      </div>
                      <div align="center"></div></td>
                    </tr>
                    </table>
              </td>
            </tr>
            <tr>
              <td colspan="4"><table width="669" border="0">
                <tr>
                  <td><table width="658" border="0">
                    <tr>
                      <td width="103"><div align="right"><strong>
                                                Date:</strong></div></td>
                      <td width="281" >  {!! date('D, d/m/Y, g:i a',strtotime(@$transaction->TRANSDATE))  !!}&nbsp;</td>
                      <td width="172"><div align="right"><strong>Receipt No.</strong></div></td>
                      <td width="184" >{!!  @$transaction->RECEIPTNO; !!}&nbsp;</td>
                      </tr>
                    <tr>
                        <td align="right"><strong>Programme:</strong></td>
                      <td>{!! @$sys->getProgram(@$student->PROGRAMMECODE )!!}</td>
                      <td><div align="right"><strong>Level:</strong></div></td>
                      <td >{!! @$student->levels->slug !!}</td>
                      </tr>
                  </table></td>
                </tr>
              </table></td>
              </tr>
               <tr>
              <td width="120" align="right"><strong>Index No:&nbsp;&nbsp;</strong></td>
              <td width="502" colspan="3" style=" border-bottom-style:dotted"><strong>{!!@$student->INDEXNO !!}</strong></td>
            </tr>
            <tr>
              <td align="right"><strong>Name:&nbsp;&nbsp;</strong></td>
              <td width="502" colspan="3" style=" border-bottom-style:dotted"><strong>{!!@$student->NAME !!}</strong></td>
            </tr>
            <tr>
            </tr>
            @if(@$transaction->bank->NAME!="")
            <tr>
              <td align="right"><strong>Bank Paid to:&nbsp;&nbsp;</strong></td>
              <td colspan="3" style=" border-bottom-style:dotted"><strong>{!! strtoupper(@$transaction->bank->NAME)!!}</strong></td>
            </tr>
            @endif
             
            <tr>
              <td align="right"><strong>&nbsp;&nbsp;&nbsp;&nbsp;Amount Paid:&nbsp;&nbsp;</strong></td>
              <td colspan="3" style=" border-bottom-style:dotted"><strong>GHC{!! @$transaction->AMOUNT!!}.00</strong>&nbsp;(<span > {!! $words !!}</span> )</td>
            </tr>
             <tr>
              <td align="right"><strong>Balance:&nbsp;&nbsp;</strong></td>
              <td colspan="3" style=" border-bottom-style:dotted"><strong>GHC{!! @$student->BILL_OWING!!}.00</strong>&nbsp;</td>
            </tr>
            <tr>
              <td rowspan="2" align="right"><strong><div class="visible-print text-center" align='center'>
                                 {!! QrCode::size(100)->generate(Request::url()); !!} 

                                </div>&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
              <td colspan="3" align="center">&nbsp;
                <div style="width:90%">Goto records.ttuportal.com and register for the semester <br/><br/>Your Username is <b>{!! @$student->INDEXNO!!}</b> And your Password is <b>{!! @$sys->getStudentPassword(@$student->INDEXNO)!!}</b></div></div></td>
            </tr>
           
            <?php  \Session::forget('students');?>
          </table></td>
        </tr>
      </table>
                    

 <?php }
?>

                 
                </div>

         </div>
     </div>
 
 </div>
  
        
 @endsection
 
@section('js')
 <script type="text/javascript">
  
$(document).ready(function(){
window.print();
//window.close();
});

</script>
  
@endsection