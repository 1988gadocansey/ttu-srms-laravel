@extends('layouts.printlayout')


 
<style>
    
</style>
@inject('sys', 'App\Http\Controllers\SystemController')
<link rel="stylesheet" href="{!! url('public/assets/css/print.css') !!}" media="all">  
<style>
    html, body, #page3,  #page4, #page5 { float: none; }

   @media print
{
    table {float: none !important; }
  div { float: none !important; }
   #page3  { page-break-inside: avoid; page-break-before: always; }
   #page4  { page-break-inside: avoid; page-break-before: always; }
}
     
@page {
  size: A4;
}
 
table, figure {
  page-break-inside: avoid;
}
fieldset legend {
  page-break-before: always;
}
h1, h2, h3, h4, h5 {
  page-break-after: avoid;
}
.biodata{
        padding: 1px;
    }
    body{
        background: none;
    }
    .uppercase{
        font-size: 12px;
        text-align: right;
        font-weight: bolder;
    }
  td{
        font-size: 13px
    }
    .folder table{
        border-collapse: collapse;
    border-spacing: 0;
    
    margin-bottom: 15px;
    }
    .folder td{
        padding:4px;
    }
.folder table {
   border-collapse: collapse;
    border-spacing: 0;
    margin-bottom: 15px;
    
}
.watermark {
 
  display: block;
  position: relative;
}

.watermark::after {
  content: "";
 background:url(http://srms.tpolyonline.com/public/logins/images/logo.png);
  opacity: 0.2;
  top: 0;
  left: 30;
  bottom: 0;
  right: 0;
  position: absolute;
  z-index: -1;   
   background-size: contain;
  content: " ";
  display: block;
  position: absolute;
  height: 100%;
  width: 100%;
  background-repeat: no-repeat;
}
</style>
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="card">
       
            @if(Session::has('success'))
             <div class="card-panel light-green lighten-3">
            <div style="text-align: center" class=" white-text alert  alert-success"   >
                {!! Session::get('success') !!} <a href="{{url('form/step2')}}">Click to Move to Next Step</a>
            </div></div>
            @endif
             @if(Session::has('error'))
             <div class="card-panel red">
            <div style=" " class=" white-text alert  alert-danger"  >
                {!! Session::get('error') !!}
            </div></div>
            @endif

            @if (count($errors) > 0)

             <div class="card-content blue-grey">
                <div class=" alert  alert-danger  " style="background-color: red;color: white">

                    <ul>
                        @foreach ($errors->all() as $error)
                        <li> {{  $error  }} </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div> 
           
          <a onclick="javascript:printDiv('print')" class="btn btn-success">Click to print form</a>
        
          
              <div class="panel-body">
                  <div id='print'>
                
                    <div id='page1'>  
                         <table border='0'>
                            <tr>
                                <td> <img src='{{url("public/assets/img/admissions.jpg")}}' style=""  class="image-responsive"/> 

                                  
                            </tr>
                        </table>
                       
                     
                         <fieldset><legend style="background-color:#1A337E;color:white;">BIODATA INFORMATION</legend>
                            <table class=''><tr>

                                    <td>
                                        <table   class="folder table" >
                                            <tr>
                                                <td width="210" class="uppercase" align="right"><span>TITLE:</span></td>
                                                <td width="408" class="capitalize">{{ $student->TITLE }}</td>								
                                            </tr>
                                            <tr>
                                                <td width="210" class="uppercase" align="right"><span>SURNAME:</span></td>
                                                <td width="408" class="capitalize">{{ $student->SURNAME }}</td>								
                                            </tr>
                                            <tr>
                                                <td width="210" class="uppercase" align="right"><span>FIRST NAME:</span></td>

                                                <td width="408" class="capitalize">{{ $student->FIRSTNAME }}</td>
                                            </tr>
                                            <tr>
                                                <td class="uppercase" style=""align="right"><span>OTHERNAMES:</span></td>
                                                <td class="capitalize"><?php echo strtoupper($student->OTHERNAME) ?></td>
                                            </tr>
                                            <tr>
                                                <td class="uppercase" align="right"><span>GENDER:</span></td>
                                                <td class="capitalize"><?php echo strtoupper($student->GENDER) ?></td>
                                            </tr>
                                            <tr>
                                                <td class="uppercase" align="right"><span>DATE OF BIRTH</span>:</td>
                                                <td class="capitalize"><?php echo $student->DOB ?></td>
                                            </tr>
                                            <tr>
                                                <td class="uppercase" align="right"><span>SOURCE OF FINANCE</span>:</td>
                                                <td class="capitalize"><?php echo strtoupper($student->SOURCE_OF_FINANCE) ?></td>
                                            </tr>

                                            <tr>
                                                <td class="uppercase" align="right"><span>PHONE:</span></td>
                                                <td class="capitalize"><?php echo "+233" . \substr($student->PHONE, -9); ?></td>
                                            </tr>

                                           

                                            <tr>
                                                <td class="uppercase" align="right"><span>PREVIOUS SCHOOL:</span></td>
                                                <td class="capitalize"><?php echo strtoupper($student->SCHOOL) ?></td>


                                            </tr>
                                            <tr>
                                                <td class="uppercase" align="right"><span>EMAIL:</span></td>
                                                <td class="capitalize">{!!strtoupper($student->EMAIL) !!}</td>

                                            </tr>
                                            <tr>
                                                @if($student->PHYSICALLY_DISABLED=="YES")

                                                <td class="uppercased" align="right"><span>PHYSICALLY CHALLENGED: </span></td>
                                                  <td class="capitalize">      {!! strtoupper($student->DISABLED) !!}</td>
                                               @endif
                                            </tr>

                                        </table>

                                    </td>

                                    <td valign="top" >
                                        <img class="" style="width:77px;height: auto" src="http://application.ttuportal.com/public/uploads/photos/{{$student->APPLICATION_NUMBER}}.jpg"alt="photo"    /> 


                                         
                                    <td>
                                
                                </td>

                                </tr>
                            </table>
                           </fieldset>
                              <fieldset><legend  style="background-color:#1A337E;color:white;">OTHER INFORMATION</legend>
                             <table  class="folder table">
                                <tr>
                                    <td>
                                        <table >
                                            <tr>
                                               <td class="uppercase" ><strong>HOMETOWN:</strong></td>
                                                <td class="capitalize">{!! strtoupper($student->HOMETOWN) !!}</td>

                                            </tr>
                                            <tr>
                                               <td class="uppercase" style=""><strong>POSTAL ADDRESS:</strong></td>
                                                <td class="capitalize">{!! strtoupper($student->ADDRESS) !!}</td>

                                            </tr>
                                            <tr>
                                                <td class="uppercase"><strong>HALL:</strong></td>
                                                <td class="capitalize">{!!strtoupper( $student->PREFERED_HALL) !!}</td>
                                                
                                            </tr>
                                            <tr>
                                                 <td class="uppercase"><strong>MARITAL STATUS:</strong></td>
                                                <td class="capitalize">{!! strtoupper($student->MARITAL_STATUS) !!}</td>
                                               
                                            </tr>

                                        </table>
                                    </td>
                                    <td>
                                        <table>
                                            <tr>
                                                  <td class="uppercase"  ><strong>HOMETOWN ADDRESS:</strong></td>
                                                <td class="capitalize">{!! strtoupper($student->RESIDENTIAL_ADDRESS) !!}</td>

                                            </tr>
                                            <tr>
                                                 <td class="uppercase"  ><strong>HOMETOWN REGION:</strong></td>
                                                <td class="capitalize">{!! strtoupper($student->REGION) !!}</td>

                                            </tr>
                                            <tr>
                                                 <td class="uppercase"><strong>RELIGION:</strong></td>
                                                <td class="capitalize">{!! strtoupper($student->RELIGION) !!}</td>

                                                
                                            </tr>
                                            <tr>
                                                 <td class="uppercase"><strong>NATIONALITY:</strong></td>
                                                <td class="capitalize">{!! strtoupper($student->NATIONALITY )!!}</td>

                                            </tr>

                                        </table>
                                    </td>
                                </tr>


                            </table>
                               </fieldset> 
                    </div>
                      
                      <div id='page2'>
                          
                          
                      <fieldset><legend style="background-color:#1A337E;color:white;">GURADIAN INFORMATION</legend>
                             <table class="folder table">
                                <tr>
                                    <td>
                                        <table>
                                            <tr>
                                                <td class="uppercase" ><strong>GUARDIAN NAME:</strong></td>
                                                <td class="capitalize">{!! strtoupper($student->GURDIAN_NAME) !!}</td>

                                            </tr>
                                            <tr>
                                                <td class="uppercase"><strong>GURDIAN ADDRESS:</strong></td>
                                                <td class="capitalize">{!! strtoupper($student->GURDIAN_ADDRESS) !!}</td>

                                            </tr>

                                        </table>
                                    </td>
                                    <td>
                                        <table>
                                            <tr>
                                                <td class="uppercase"  ><strong>GUARDIAN PHONE:</strong></td>
                                                <td class="capitalize">{!! strtoupper($student->GURDIAN_PHONE) !!}</td>

                                            </tr>
                                            <tr>
                                                <td class="uppercase"  ><strong>GUARDIAN OCCUPATION:</strong></td>
                                                <td class="capitalize">{!! strtoupper($student->GURDIAN_OCCUPATION) !!}</td>

                                            </tr>

                                        </table>
                                    </td>
                                </tr>


                            </table>
                    </fieldset>
                        
                  
                            <fieldset><legend style="background-color:#1A337E;color:white;">CHOICE OF PROGRAMME</legend>
                                <table class="folder table">
                                <tr>
                                    <td>
                                        <table id='yt'>
                                            <tr>
                                                <td class="uppercase" ><strong>FIRST CHOICE:</strong></td>
                                                <td class="capitalize">{!!strtoupper($sys->getProgramName($student->FIRST_CHOICE)) !!}</td>

                                            </tr>
                                            <tr>
                                                <td class="uppercase"><strong>SECOND CHOICE:</strong></td>
                                                <td class="capitalize">{!! strtoupper($sys->getProgramName($student->SECOND_CHOICE)) !!}</td>

                                            </tr>

                                        </table>
                                    </td>
                                    <td>
                                        <table id='tt'>
                                            <tr>
                                                <td class="uppercase"  ><strong>THIRD CHOICE:</strong></td>
                                                <td class="capitalize">{!! strtoupper($sys->getProgramName($student->THIRD_CHOICE)) !!}</td>

                                            </tr>
                                            <tr>
                                                <td class="uppercase"  ><strong>ENTRY QUALIFICATION:</strong></td>
                                                <td class="capitalize">{!! strtoupper($student->ENTRY_QUALIFICATION) !!}</td>

                                            </tr>


                                        </table>
                                    </td>
                                </tr>


                            </table>
                              </fieldset>   
                            <p>&nbsp;&nbsp;</p>
                              <div id='page2'>
                          
                            <div class="row">
                                @if($data!="")
                                <fieldset><center><legend style="background-color:#1A337E;color:white;"><span> EXAMINATION RESULTS</span></legend></center>

                                   
                                <table class="table table-responsive table-striped">
                                    <thead>
                                        <tr>

                                            <th >INDEXNO</th>
                                            <th  >SUBJECT</th>
                                            <th  >GRADE</th>
                                            <th  >VALUE</th>
                                            <th  >EXAM TYPE</th>
                                            <th >SITTING</th>
                                            <th  >DATE OF EXAM</th>
                                            <th  >CENTER</th>

                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach($data as $index=> $row) 


                                        <tr align="">
                                            <td> {{ strtoupper(@$row->INDEX_NO) }}</td>
                                            @if($student->ENTRY_QUALIFICATION=="WASSSCE" ||$student->ENTRY_QUALIFICATION=="SSSCE")
                                           
                                            <td> {{ strtoupper(@$row->subject->NAME)	 }}</td>
                                            @else
                                            <td> {{ strtoupper(@$row->SUBJECT)   }}</td>
                                            @endif
                                            <td> {{ strtoupper(@$row->GRADE)	 }}</td>
                                            <td> {{ strtoupper(@$row->GRADE_VALUE)	 }}</td>
                                            <td> {{ strtoupper(@$row->EXAM_TYPE) }}</td>
                                            <td> {{ strtoupper(@$row->SITTING) }}</td>
                                            <td> {{ strtoupper(@$row->MONTH) }}</td>
                                            <td> {{ strtoupper(@$row->CENTER) }}</td>

                                        </tr> 
                                        @endforeach
                                    </tbody>
                                </table>
                                         </fieldset>

                           
                              @else
                              
                                <p>No results to display</p>
                                @endif
                        </div>
                          
                  </div>
                            <p>&nbsp;&nbsp;</p><p>&nbsp;&nbsp;</p>
                      <div id='page2'>
                          <div class="watermark">
                            <div> <fieldset><legend style="background-color:#1A337E;color:white;">DECLARATION</legend>
                                
                                
                                
                                    <p>I {{$student->NAME}} certify that the information provided above is true and will be held personally for its authencity and will bear  
                                      any consequences for any invalid information provided.
                                    </p>
                                </fieldset>
                            
                            
                            </div>
            <div>
                <p>&nbsp;</p>
                <p>&nbsp;</p>
               <fieldset><legend style="background-color:#1A337E;color:white;">CORROBORATIVE DECLARATION</legend>
                   <p>(Please read the instructions carefully before you endorse this form)</p>
                   <p></p>
                   <p>1. This declaration should be signed a person of high integrity and honour who must also endorse at least one of the candidate's passport size photographs on the reverse side and also satisfy him/herself that the examination grades indicated
                   on the form by the applicant are true.
                   <p>2. The application will not be valid if the declaration below is not signed</p>
                   <p>3.If the declaration proves to be false, the application shall be rejected; if falsely detected after admission, the student shall be dismissed.</p>
                   <p>&nbsp;</p>
                <p> 
                    I hereby declare that the photograph endorsed by me is the true likeness of the applicant {{$student->TITLE}} {{$student->NAME}} who is personally known to me. I have inspected his/her certificates against the results indicated on the form and I satisfied that they are true and name that appears on them is the same as that by which he/she is officially/personally known to me.
                    
                    </p>
                    <table>
                        <tr>
                            <td>
                                    <table>
                        <tr>
                            <td>SIGNATURE </td></tr>
                        <tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr>
                             <tr><td> ..........................................................................................</td></tr>
                      
                        </tr>
                         <tr>
                             <td>DATE </td></tr><tr><td>&nbsp;</td></tr>
                            <tr><td> ..........................................................................................</td></tr>
                      
                         <tr>
                            <td>NAME(BLOCKLETTERS) </td></tr><tr><td>&nbsp;</td></tr>
                            <tr><td> ..........................................................................................</td></tr>
                      
                         <tr>
                            <td>OCCUPATION</td></tr><tr><td>&nbsp;</td></tr>
                               
                     <tr><td> ..........................................................................................</td></tr>
                        
                        <tr>
                            <td>POSITION  </td></tr><tr><td>&nbsp;</td></tr>
                         <tr><td> ..........................................................................................</td></tr>
                      
                         
                         <tr>
                             <td>ADDRESS & OFFICIAL STAMP </td></tr><tr><td>&nbsp;</td></tr><tr><td>&nbsp;</td></tr>
                         <tr><td> ..........................................................................................</td></tr>
                        
                       
                    </table>
                            </td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
                            
                            </td>
                              <td>
                                 <!-- <img src='{{url("public/logo.png")}}' style="width:160px;height: auto"  class="image-responsive"/> -->
                             <img src="http://chart.apis.google.com/chart
?cht=qr&chs=150x150&chl={{@Auth::user()->password}}" />
                              
                              </td>
                        </tr>
                    </table>
                
                </fieldset> 
            </div>
              </div>
                      </div></div>
                        
 
    </div>
</div>
    </div>
</div>
@endsection
<script>
    $(document).ready(function(){
        // Wrap each tr and td's content within a div
        // (todo: add logic so we only do this when printing)
        $("table tbody th, table tbody td").wrapInner("<div></div>");
    })
</script>
<script>
$('#final').click(function(){
     /* when the submit button in the modal is clicked, submit the form */
    alert('Finalizing and submitting form to the University wait....');
    $('#formfield').submit();
});

</script>