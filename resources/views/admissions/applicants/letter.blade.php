@extends('layouts.printlayout')
<style>
    @page {
  size: A4;
}
    body{
        background-image:url("{{url('public/assets/img/background.jpgs') }}");
        background-repeat: no-repeat;
    background-attachment: fixed;
    line-height:1.5;
    }
    .watermark {
 
  position:absolute;
overflow:hidden;
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
 @media print {
    .watermark {
      display: block;
      table {float: none !important; }
  div { float: none !important; }
    }
    .uk-grid, to {display: inline !important} s
    #page1	{page-break-before:always;}
	.condition	{page-break-before:always;}
	#page2	{page-break-before:always;}
        .school	{page-break-before:always;}
	.page9	{page-break-inside:avoid; page-break-after:auto}
	 a,
  a:visited {
    text-decoration: underline;
  }
  body{font-size: 14px}
  size:A4;
  a[href]:after {
    content: " (" attr(href) ")";
  }

  abbr[title]:after {
    content: " (" attr(title) ")";
  }

   
  a[href^="javascript:"]:after,
  a[href^="#"]:after {
    content: "";
  }
.uk-grid, to {display: inline !important}

  }
</style>
@section('content')
@inject('sys', 'App\Http\Controllers\SystemController')
<div class="containers">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                

                <div class="panel-body" id='gad'>
                    
                         @if(!empty($data))
                             <a  onclick="javascript:printDiv('print')" class="md-btn md-btn-flat md-btn-flat-primary md-btn-wave">Click to print form</a>
        <div id='print'>
            <div id='page1'>
                        <table border='0'>
                            <tr>
                                <td> <img  style="width:767px;height: auto" src='{{url("public/assets/img/header.jpg")}}' style=""  class="image-responsive"/> 

                                 <td>
                                <p>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</p>
                                      <img   style="width:106px;height: 134px;margin-left: -3px;    margin-top: -34px; "  <?php
                                        $pic = $data->APPLICATION_NUMBER;
                                        echo $sys->picture("{!! url(\"public/albums/applicants/$pic.jpg\") !!}", 90)
                                        ?>  src="http://application.ttuportal.com/public/uploads/photos/{{$data->APPLICATION_NUMBER}}.jpg"alt="photo"     />
                                  
                                </td>
                            </tr>
                        </table>
                        
                      
                        @if($data->ADMISSION_TYPE=='technical')
                        
                        <div class="content" id="technical">  <div class="watermark">
                             <div style="margin-left: 10px">
                                 <p style="text-transform: capitalize">DEAR <span style="text-transform: capitalize"> {{$data->TITLE}}.  {{$data->NAME}}</span></p>
                           
                            <div style="margin-left: 0px;text-align: justify">
                               
                                <centerd><b><p class="">OFFER OF ADMISSION  - {{ strtoupper(@$data->admitedProgram->department->schools->FACULTY)}}  -  ADMISSION N<u>O </u>: {{$data->APPLICATION_NUMBER}}</p></b></center>
                                <hr>
                                <p>We write on behalf of the Academic Board to offer you admission to Takoradi  Technical University to pursue a programme of study leading to the award of 
                       @if(0===strpos($data->PROGRAMME_ADMITTED,'B'))
                                   @elseif(0===strpos($data->PROGRAMME_ADMITTED,'C') || 0===strpos($data->PROGRAMME_ADMITTED,'A'))
                                    
                                      @elseif(0===strpos($data->PROGRAMME_ADMITTED,'H'))
                                    
                                       @elseif(0===strpos($data->PROGRAMME_ADMITTED,'D'))
                                        
                                      @else
                                      @endif<b> {{$sys->getProgram($data->PROGRAMME_ADMITTED)}}</b>. The duration of the programme is {{$sys->getProgramDuration($data->PROGRAMME_ADMITTED)}} Academic years. A change of Programme is <strong><b>NOT ALLOWED</b>.</strong></p>
                                <p><b>Note A mandatory Preparatory course in Engineering Mathematics</b>  and <b> Engineering Science </b>will be organized for all applicants from Technical Institutions to build up their capacity in<b> Elective Mathematics required for HND programme. </b>The preparatory course starts from <b>Monday 24th July</b> and ends on <b>Friday18th August 2017</b>. You are therefore required to pay a <b>non-refundable special tuition fee of GH¢200</b> at any branch of <b>Capital Bank, into Accounts Number, 2220001961011</b>. There is an <b>option for accommodation on campus during the preparatory course at a fee of GH¢ 75 for interested individuals also to be paid into the same Bank Accounts above</b>.
                                </p>
                                <p>1. Your admission is for the<b> {{$year}} </b>Academic year. If you fail to enroll or withdraw from the programme without prior approval of the University, you will forfeits the admission automatically.</p>
                              
                                <p>2. The<b> {{$year}} academic year</b> is scheduled to begin on <b> Monday 28th August   {{date('Y')}}</b>. You are expected to report for medical examination and registration from <b>Monday 28th August {{date('Y')}} to Friday 8th September  {{date('Y')}}</b>.You are mandated to participate in orientation programme which will run from <b>Monday 4th September to Friday 8th September {{date('Y')}}</b>.</p>
                                
                                 <p>3. You are required to make <b>PROVISIONAL PAYMENT</b> of <b>GHS{{ $data->ADMISSION_FEES}}</b> at any branch of
                               
                                  @if($data->admitedProgram->TYPE=="NON TERTIARY")
                                     <b> UNIBANK into Account Number   1570105703613</b>. If you do not indicate acceptance by paying the fees before <b> Monday 28th August,{{date('Y')}}</b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make <b>PROVISIONAL PAYMENT</b> of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year and non-refundable</b>.</p>
                                   @elseif(strpos($sys->getProgram($data->PROGRAMME_ADMITTED),"Evening")!==false)  
                                         <b> Ecobank into Account Number   0189104488868901</b>. If you do not indicate acceptance by paying the fees before <b> Monday 28th August,{{date('Y')}}</b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make <b>PROVISIONAL PAYMENT</b> of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year and non-refundable</b>.</p>
                              
                                 @else
                                        <b>{{strtoupper(@$data->admitedProgram->department->schools->banks->NAME)}} into Account Number {{@$data->admitedProgram->department->schools->banks->ACCOUNT_NUMBER}}</b>. If you do not indicate acceptance by paying the fees before <b> Monday 28th August,{{date('Y')}}</b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make <b>PROVISIONAL PAYMENT</b> of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year and non-refundable</b>.</p>
                                   
                                      @endif
                            <p>4. You will be on probation for the full duration of your programme and may be dismissed at any time for unsatisfactory academic work or misconduct. You will be required to adhere to <b>ALL</b> the rules and regulations of the University as contained in the University Statutes, Examination Policy, Ethics Policy and Students' Handbook.</p>
                            
                             <p>5. You are also to note that your admission is subject to being declared medically fit to pursue the programme of study in this University. You <b>are therefore required to undergo a medical examination at the University Clinic before registration.</b> <b>You will be withdrawn from the University if you fail to do the medical examination</b>.</p>
                           
                            <p>6. Applicants will also be held personally for any false statement or omission made in their applications.</p>
                           
                            <p>7. The University does not give financial assistance to students. It is therefore the responsibility of students to arrange for their own sponsorship and maintenance during the period of study.</p>
                            </div>
                            <p>8. You are to note that the University is a secular institution and is therefore not bound by observance of any religious or sectarian practices. As much as possible the University lectures and / or examination would be scheduled to take place within normal working days, but where it is not feasible, lectures and examination would be held on other days.</p>
                           <div id='page2'>
                            <p>9. As a policy of the University, all students shall be required to register under the National Health Insurance Scheme (NHIS) on their own to enable them access medical care whilst on campus.</p>
                           
                          @if($data->RESIDENTIAL_STATUS==0) 
                            <p>10. You are affiliated to<b> {{strtoupper($data->HALL_ADMITTED)}} Hall. </b></p>
                          
                           @else
                           <p>10. You have been given Hall Accommodation at<b> {{strtoupper($data->HALL_ADMITTED)}} Hall </b>. You will be required to make payment of  GHS{{$sys->hallFees($data->HALL_ADMITTED)}} into any branch of Zenith Bank Ghana with account number  {{$sys->hallAccount($data->HALL_ADMITTED)}}. <b/>You shall report to your assigned hall of residence with the original copy of pay-in-slip
                           NOTE: Hall fees paid is not refundable.
                           </p>
                            @endif
                            <p>11. Any applicant who falsified results will be withdrawn from the university and will forfeits his/her fees paid.</p>
                            <p>You are required to make <b>PROVISIONAL PAYMENT</b> of all fees before or on Monday 28th August {{date('Y')}}. </p>
 
                           <p>Please, accept my congratulations on your admission to the University.</p>
                          
                           <div>
                           <table>
                           <tr>
                           <td>
                               <p>Yours faithfully</p>
                               <p><img src='{{url("public/assets/img/signature.png")}}' style="width:90px;height:auto;" /></p>
                               <p>SNR. ASSISTANT REGISTRAR(ADMISSIONS)<br/>For: REGISTRAR</p>
                               </td>
                               <td><div class="visible-print text-center"  style="margin-left:258px">
                            {!! QrCode::size(100)->generate(Request::url()); !!}

                        </div></td>
                       
                               </tr>

                               </table>
                           </div>
                            <td> <img src='{{url("public/assets/img/footer.jpg")}}' style=""  class="image-responsive"/> 

                           </div></div></div></div>
                       
                        
                        @elseif($data->ADMISSION_TYPE=='provisional')
                        
                        <div class="content" id="provisional">  <div class="watermark">
                             <div style="margin-left: 10px">
                                 <p style="text-transform: capitalize">DEAR <span style="text-transform: capitalize"> {{$data->TITLE}}.  {{$data->NAME}}</span></p>
                           
                            <div style="margin-left: 0px;text-align: justify">
                               
                                <centerd><b><p class="">OFFER OF ADMISSION(<b>PROVISONAL</b>)  - {{ strtoupper(@$data->admitedProgram->department->schools->FACULTY)}}  -  ADMISSION N<u>O </u>: {{$data->APPLICATION_NUMBER}}</p></b></center>
                                <hr>
                                <p>We write on behalf of the Academic Board to offer you admission to Takoradi  Technical University to pursue a programme of study leading to the award of 
                                    
                                     
                                    @if(0===strpos($data->PROGRAMME_ADMITTED,'B'))
                                   @elseif(0===strpos($data->PROGRAMME_ADMITTED,'C') || 0===strpos($data->PROGRAMME_ADMITTED,'A'))
                                   
                                      @elseif(0===strpos($data->PROGRAMME_ADMITTED,'H'))
                                    
                                       @elseif(0===strpos($data->PROGRAMME_ADMITTED,'D'))
                                       
                                      @else
                                      @endif<b> {{$sys->getProgram($data->PROGRAMME_ADMITTED)}}</b>. The duration of the programme is {{$sys->getProgramDuration($data->PROGRAMME_ADMITTED)}} Academic years. A change of Programme is <strong><b>NOT ALLOWED</b>.</strong></p>
                                <p><b><i>Note: Your admission is <b>PROVISIONAL</b>, you are therefore, required to present your results to the university’s admissions office after it is published, to enable the office regularlised your admission.</b></i></p>
                               
                                <p>1. Your admission is for the<b> {{$year}} </b>Academic year. If you fail to enroll or withdraw from the programme without prior approval of the University, you will forfeits the admission automatically.</p>
                              
                                <p>2. The<b> {{$year}} academic year</b> is scheduled to begin on <b> Monday 28th August   {{date('Y')}}</b>. You are expected to report for medical examination and registration from <b>Monday 28th August {{date('Y')}} to Friday 8th September  {{date('Y')}}</b>.You are mandated to participate in orientation programme which will run from <b>Monday 4th September to Friday 8th September {{date('Y')}}</b>.</p>
                                
                                 <p>3. You are required to make <b>PROVISIONAL PAYMENT</b> of <b>GHS{{ $data->ADMISSION_FEES}}</b> at any branch of 
                                      @if($data->admitedProgram->TYPE=="NON TERTIARY")
                                     <b> UNIBANK into Account Number   1570105703613</b>. If you do not indicate acceptance by paying the fees before <b> Monday 28th August,{{date('Y')}}</b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make <b>PROVISIONAL PAYMENT</b> of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year and non-refundable</b>.</p>
                                   @elseif(strpos($sys->getProgram($data->PROGRAMME_ADMITTED),"Evening")!==false)  
                                         <b> Ecobank into Account Number   0189104488868901</b>. If you do not indicate acceptance by paying the fees before <b> Monday 28th August,{{date('Y')}}</b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make <b>PROVISIONAL PAYMENT</b> of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year and non-refundable</b>.</p>
                               
                                 @else
                                        <b>{{strtoupper(@$data->admitedProgram->department->schools->banks->NAME)}} into Account Number {{@$data->admitedProgram->department->schools->banks->ACCOUNT_NUMBER}}</b>. If you do not indicate acceptance by paying the fees before <b> Monday 28th August,{{date('Y')}}</b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make <b>PROVISIONAL PAYMENT</b> of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year and non-refundable</b>.</p>
                                   
                                      @endif
                            <p>4. You will be on probation for the full duration of your programme and may be dismissed at any time for unsatisfactory academic work or misconduct. You will be required to adhere to <b>ALL</b> the rules and regulations of the University as contained in the University Statutes, Examination Policy, Ethics Policy and Students' Handbook.</p>
                            
                             <p>5. You are also to note that your admission is subject to being declared medically fit to pursue the programme of study in this University. You <b>are therefore required to undergo a medical examination at the University Clinic before registration.</b> <b>You will be withdrawn from the University if you fail to do the medical examination</b>.</p>
                           
                            <p>6. Applicants will also be held personally for any false statement or omission made in their applications.</p>
                           
                            <p>7. The University does not give financial assistance to students. It is therefore the responsibility of students to arrange for their own sponsorship and maintenance during the period of study.</p>
                            </div>
                            <p>8. You are to note that the University is a secular institution and is therefore not bound by observance of any religious or sectarian practices. As much as possible the University lectures and / or examination would be scheduled to take place within normal working days, but where it is  not feasible, lectures and examination would be held on other days.</p>
                           <div id='page2'>
                            <p>9. As a policy of the University, all students shall be required to register under the National Health Insurance Scheme (NHIS) on their own to enable them access medical care whilst on campus.</p>
                           
                           
                            @if($data->RESIDENTIAL_STATUS==0) 
                            <p>10. You are affiliated to<b> {{strtoupper($data->HALL_ADMITTED)}} Hall. </b></p>
                          
                           @else
                             <p>10. You have been given Hall Accommodation at<b> {{strtoupper($data->HALL_ADMITTED)}} Hall </b>. You will be required to make payment of  GHS{{$sys->hallFees($data->HALL_ADMITTED)}} into any branch of Zenith Bank Ghana with account number  {{$sys->hallAccount($data->HALL_ADMITTED)}}. <b/>You shall report to your assigned hall of residence with the original copy of pay-in-slip
                           NOTE: Hall fees paid is not refundable.
                             @endif
                            <p>11. Any applicant who falsified results will be withdrawn from the university and will forfeits his/her fees paid.</p>
                            <p>You are required to make <b>PROVISIONAL PAYMENT</b> of all fees before or on Monday 28th August {{date('Y')}}. </p>
 
                            <p>Please, accept my congratulations on your admission to the University.</p>
                          
                           <div>
                           <table>
                           <tr>
                           <td>
                               <p>Yours faithfully</p>
                               <p><img src='{{url("public/assets/img/signature.png")}}' style="width:90px;height:auto;" /></p>
                               <p>SNR. ASSISTANT REGISTRAR(ADMISSIONS)<br/>For: REGISTRAR</p>
                               </td>
                               <td><div class="visible-print text-center"  style="margin-left:258px">
                            {!! QrCode::size(100)->generate(Request::url()); !!}

                        </div></td>
                       
                               </tr>

                               </table>
                           </div>
                            <td> <img src='{{url("public/assets/img/footer.jpg")}}' style=""  class="image-responsive"/> 

                           </div></div></div></div>
                      
                        
                        @elseif($data->ADMISSION_TYPE=='conditional')
                        
                        <div class="content" id="conditional">  <div class="watermark">
                             <div style="margin-left: 10px">
                                 <p style="text-transform: capitalize">DEAR <span style="text-transform: capitalize"> {{$data->TITLE}}.  {{$data->NAME}}</span></p>
                           
                            <div style="margin-left: 0px;text-align: justify">
                               
                                <centerd><b><p class="">OFFER OF ADMISSION(<b>CONDITIONAL</b>) - {{ strtoupper(@$data->admitedProgram->department->schools->FACULTY)}}  -  ADMISSION N<u>O </u>: {{$data->APPLICATION_NUMBER}}</p></b></center>
                                <hr>
                                     
                                  <p>We write on behalf of the Academic Board to offer you admission to Takoradi  Technical University to pursue a programme of study leading to the award of
                                    
                                     @if(0===strpos($data->PROGRAMME_ADMITTED,'B'))
                                   @elseif(0===strpos($data->PROGRAMME_ADMITTED,'C') || 0===strpos($data->PROGRAMME_ADMITTED,'A'))
                                 
                                      @elseif(0===strpos($data->PROGRAMME_ADMITTED,'H'))
                                    
                                       @elseif(0===strpos($data->PROGRAMME_ADMITTED,'D'))
                                     
                                      @else
                                      @endif<b> {{$sys->getProgram($data->PROGRAMME_ADMITTED)}}</b>. The duration of the programme is {{$sys->getProgramDuration($data->PROGRAMME_ADMITTED)}} Academic years. A change of Programme is <strong><b>NOT ALLOWED</b>.</strong></p>
                                <p><i>
                                        <b><i> Note: Your admission is conditional. Per the new requirements you are supposed to have a minimum of D7 in six subjects with at least C6 in three relevant subjects in the area of specialization. You are therefore required to rewrite to make good the deficiencies within a period of one academic year. Your eligibility to continue with the HND programme would be based on the outcome of the SSCE/WASSCE result. You would be required to present your new results in writing to the DEPUTY REGISTRAR Academic affairs</i></b>

                                    </i></p>
                                <p>1. Your admission is for the<b> {{$year}} </b>Academic year. If you fail to enroll or withdraw from the programme without prior approval of the University, you will forfeits the admission automatically.</p>
                              
                                <p>2. The<b> {{$year}} academic year</b> is scheduled to begin on <b> Monday 28th August   {{date('Y')}}</b>. You are expected to report for medical examination and registration from <b>Monday 28th August {{date('Y')}} to Friday 8th September  {{date('Y')}}</b>.You are mandated to participate in orientation programme which will run from <b>Monday 4th September to Friday 8th September {{date('Y')}}</b>.</p>
                                
                                 <p>3. You are required to make <b>PROVISIONAL PAYMENT</b> of <b>GHS{{ $data->ADMISSION_FEES}}</b> at any branch of 
                                     @if($data->admitedProgram->TYPE=="NON TERTIARY")
                                     <b> UNIBANK into Account Number   1570105703613</b>. If you do not indicate acceptance by paying the fees before <b> Monday 28th August,{{date('Y')}}</b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make <b>PROVISIONAL PAYMENT</b> of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year and non-refundable</b>.</p>
                                   @elseif(strpos($sys->getProgram($data->PROGRAMME_ADMITTED),"Evening")!==false)  
                                         <b> Ecobank into Account Number   0189104488868901</b>. If you do not indicate acceptance by paying the fees before <b> Monday 28th August,{{date('Y')}}</b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make <b>PROVISIONAL PAYMENT</b> of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year and non-refundable</b>.</p>
                                  
                                 @else
                                        <b>{{strtoupper(@$data->admitedProgram->department->schools->banks->NAME)}} into Account Number {{@$data->admitedProgram->department->schools->banks->ACCOUNT_NUMBER}}</b>. If you do not indicate acceptance by paying the fees before <b> Monday 28th August,{{date('Y')}}</b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make <b>PROVISIONAL PAYMENT</b> of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year and non-refundable</b>.</p>
                                   
                                      @endif
                            <p>4. You will be on probation for the full duration of your programme and may be dismissed at any time for unsatisfactory academic work or misconduct. You will be required to adhere to <b>ALL</b> the rules and regulations of the University as contained in the University Statutes, Examination Policy, Ethics Policy and Students' Handbook.</p>
                            
                             <p>5. You are also to note that your admission is subject to being declared medically fit to pursue the programme of study in this University. You <b>are therefore required to undergo a medical examination at the University Clinic before registration.</b> <b>You will be withdrawn from the University if you fail to do the medical examination</b>.</p>
                           
                            <p>6. Applicants will also be held personally for any false statement or omission made in their applications.</p>
                           
                            <p>7. The University does not give financial assistance to students. It is therefore the responsibility of students to arrange for their own sponsorship and maintenance during the period of study.</p>
                            </div>
                            <p>8. You are to note that the University is a secular institution and is therefore not bound by observance of any religious or sectarian practices. As much as possible the University lectures and / or examination would be scheduled to take place within normal working days, but where it is  not feasible, lectures and examination would be held on other days.</p>
                           <div id='page2'>
                            <p>9. As a policy of the University, all students shall be required to register under the National Health Insurance Scheme (NHIS) on their own to enable them access medical care whilst on campus.</p>
                           
                           
                            @if($data->RESIDENTIAL_STATUS==0) 
                            <p>10. You are affiliated to<b> {{strtoupper($data->HALL_ADMITTED)}} Hall. </b></p>
                          
                           @else
                             <p>10. You have been given Hall Accommodation at<b> {{strtoupper($data->HALL_ADMITTED)}} Hall </b>. You will be required to make payment of  GHS{{$sys->hallFees($data->HALL_ADMITTED)}} into any branch of Zenith Bank Ghana with account number  {{$sys->hallAccount($data->HALL_ADMITTED)}}. <b/>You shall report to your assigned hall of residence with the original copy of pay-in-slip
                           NOTE: Hall fees paid is not refundable.
                            
                            @endif
                            <p>11. Any applicant who falsified results will be withdrawn from the university and will forfeits his/her fees paid.</p>
                            <p>You are required to make <b>PROVISIONAL PAYMENT</b> of all fees before or on Monday 28th August {{date('Y')}}. </p>
 
                            <p>Please, accept my congratulations on your admission to the University.</p>
                          
                           <div>
                           <table>
                           <tr>
                           <td>
                               <p>Yours faithfully</p>
                               <p><img src='{{url("public/assets/img/signature.png")}}' style="width:90px;height:auto;" /></p>
                               <p>SNR. ASSISTANT REGISTRAR(ADMISSIONS)<br/>For: REGISTRAR</p>
                               </td>
                               <td><div class="visible-print text-center"  style="margin-left:258px">
                            {!! QrCode::size(100)->generate(Request::url()); !!}

                        </div></td>
                       
                               </tr>

                               </table>
                           </div>
                            <td> <img src='{{url("public/assets/img/footer.jpg")}}' style=""  class="image-responsive"/> 

                           </div></div></div></div>
                      
                        
                        
                          @elseif($data->ADMISSION_TYPE=='regular' || $data->ADMISSION_TYPE=='mature')
                      
                        <div class="content" id="regular">  <div class="watermark">
                             <div style="margin-left: 10px">
                                 <p style="text-transform: capitalize">DEAR <span style="text-transform: capitalize"> {{$data->TITLE}}.  {{$data->NAME}}</span></p>
                           
                            <div style="margin-left: 0px;text-align: justify">
                               <centerd><b><p class="">OFFER OF ADMISSION  -
                                            @if(@$data->admitedProgram->AFFILAITION=="")
                                            {{ strtoupper(@$data->admitedProgram->department->schools->FACULTY)}}  -  
                                            @else
                                             {{ strtoupper(@$data->admitedProgram->PROGRAMME ."-".@$data->admitedProgram->AFFILAITION)}}  ADMISSION N<u>O </u>: {{$data->APPLICATION_NUMBER}}</p></b></center>
                                @endif
                                            <hr>
                                <p>We write on behalf of the Academic Board to offer you admission to Takoradi  Technical University to pursue a programme of study leading to the award of
                                    
                                    @if(0===strpos($data->PROGRAMME_ADMITTED,'B'))
                                   @elseif(0===strpos($data->PROGRAMME_ADMITTED,'C') || 0===strpos($data->PROGRAMME_ADMITTED,'A'))
                                  
                                      @elseif(0===strpos($data->PROGRAMME_ADMITTED,'H'))
                                    
                                       @elseif(0===strpos($data->PROGRAMME_ADMITTED,'D'))
                                    
                                      @else
                                      @endif
                                    <b> {{$sys->getProgram($data->PROGRAMME_ADMITTED)}}</b>. The duration of the programme is {{$sys->getProgramDuration($data->PROGRAMME_ADMITTED)}} Academic years. A change of Programme is <strong><b>NOT ALLOWED</b>.</strong></p>
                                
                                <p>1. Your admission is for the<b> {{$year}} </b>Academic year. If you fail to enroll or withdraw from the programme without prior approval of the University, you will forfeits the admission automatically.</p>
                              
                                <p>2. The<b> {{$year}} academic year</b> is scheduled to begin on <b> Monday 28th August   {{date('Y')}}</b>. You are expected to report for medical examination and registration from <b>Monday 28th August {{date('Y')}} to Friday 8th September  {{date('Y')}}</b>.You are mandated to participate in orientation programme which will run from <b>Monday 4th September to Friday 8th September {{date('Y')}}</b>.</p>
                                
                                 <p>3. You are required to make <b>PROVISIONAL PAYMENT</b>
                                     of <b>GHS{{ $data->ADMISSION_FEES}}</b> at any branch of 
                                     @if($data->admitedProgram->TYPE=="NON TERTIARY")
                                     <b> UNIBANK into Account Number   1570105703613</b>. If you do not indicate acceptance by paying the fees before <b> Monday 28th August,{{date('Y')}}</b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make <b>PROVISIONAL PAYMENT</b> of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year and non-refundable</b>.</p>
                                        @elseif(0===strpos($data->PROGRAMME_ADMITTED,'B'))
                                     <b>PRUDENTIAL BANK into Account Number 0271900010010 </b>. If you do not indicate acceptance by paying the fees before <b> Monday 28th August,{{date('Y')}}</b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make <b>PROVISIONAL PAYMENT</b> of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year and non-refundable</b>.</p>
                                   @elseif(strpos($sys->getProgram($data->PROGRAMME_ADMITTED),"Evening")!==false)  
                                         <b> Ecobank into Account Number   0189104488868901</b>. If you do not indicate acceptance by paying the fees before <b> Monday 28th August,{{date('Y')}}</b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make <b>PROVISIONAL PAYMENT</b> of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year and non-refundable</b>.</p>
                                 
                                 @else
                                        <b>{{strtoupper($data->admitedProgram->department->schools->banks->NAME)}} into Account Number {{$data->admitedProgram->department->schools->banks->ACCOUNT_NUMBER}}</b>. If you do not indicate acceptance by paying the fees before <b> Monday 28th August,{{date('Y')}}</b> your place will be offered to another applicant on the waiting list. You are advised to make photocopy of the Pay-in-slip for keeps and present the original to the School Accounts Office on arrival.Indicate your admission number and programme of study on the Pay-in-slip. Any Applicant who fails to make <b>PROVISIONAL PAYMENT</b> of fees forfeits his/her admission. <b>Note: Fee payment is for an Academic Year and non-refundable</b>.</p>
                                   @endif
                            <p>4. You will be on probation for the full duration of your programme and may be dismissed at any time for unsatisfactory academic work or misconduct. You will be required to adhere to <b>ALL</b> the rules and regulations of the University as contained in the University Statutes, Examination Policy, Ethics Policy and Students' Handbook.</p>
                            
                             <p>5. You are also to note that your admission is subject to being declared medically fit to pursue the programme of study in this University. You <b>are therefore required to undergo a medical examination at the University Clinic before registration.</b> <b>You will be withdrawn from the University if you fail to do the medical examination</b>.</p>
                           
                            <p>6. Applicants will also be held personally for any false statement or omission made in their applications.</p>
                           
                            <p>7. The University does not give financial assistance to students. It is therefore the responsibility of students to arrange for their own sponsorship and maintenance during the period of study.</p>
                            </div>
                            <p>8. You are required to note that the University is a secular institution and is therefore not bound by observance of any religious or sectarian practices. As much as possible the University lectures and / or examination would be scheduled to take place within normal working days, but where it is  not feasible, lectures and examination would be held on other days.</p>
                           <div id='page2'>
                            <p>9. As a policy of the University, all students shall be required to register under the National Health Insurance Scheme (NHIS) on their own to enable them access medical care whilst on campus.</p>
                           
                           @if($data->RESIDENTIAL_STATUS==0) 
                            <p>10. You are affiliated to<b> {{strtoupper($data->HALL_ADMITTED)}} Hall. </b></p>
                          
                           @else
                           
                             <p>10. You have been given Hall Accommodation at<b> {{strtoupper($data->HALL_ADMITTED)}} Hall </b>. You will be required to make payment of  GHS{{$sys->hallFees($data->HALL_ADMITTED)}} into any branch of Zenith Bank Ghana with account number  {{$sys->hallAccount($data->HALL_ADMITTED)}}. <b/>You shall report to your assigned hall of residence with the original copy of pay-in-slip
                           NOTE: Hall fees paid is not refundable.
                           
                             @endif
                            <p>11. Any applicant who falsified results will be withdrawn from the university and will forfeits his/her fees paid.</p>
                            <p>You are required to make <b>PROVISIONAL PAYMENT</b> of all fees before or on Monday 28th August {{date('Y')}}. </p>
 
                            <p>Please, accept my congratulations on your admission to the University.</p>
                          
                           <div>
                           <table>
                           <tr>
                           <td>
                               <p>Yours faithfully</p>
                               <p><img src='{{url("public/assets/img/signature.png")}}' style="width:90px;height:auto;" /></p>
                               <p>SNR. ASSISTANT REGISTRAR(ADMISSIONS)<br/>For: REGISTRAR</p>
                               </td>
                               <td><div class="visible-print text-center"  style="margin-left:258px">
                            {!! QrCode::size(100)->generate(Request::url()); !!}

                        </div></td>
                       
                               </tr>

                               </table>
                           </div>
                            <td> <img src='{{url("public/assets/img/footer.jpg")}}' style=""  class="image-responsive"/> 

                           </div></div></div></div>
                      
                       
                        
                        @endif
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        </div>
                             </div></div></div>
                        @else
                        <p>Letter not ready yet. come back later</p>
                        @endif

                        </div>
                    </div>	
                </div>
            </div>
        </div>
     
 

@endsection
  @section('js')
  <script>
    $(document).ready(function(){
        // Wrap each tr and td's content within a div
        // (todo: add logic so we only do this when printing)
        $("table tbody th, table tbody td").wrapInner("<div></div>");
    })
</script>
                    <script language="javascript" type="text/javascript">
                        function printDiv(divID) {
                            //Get the HTML of div
                            var divElements = document.getElementById(divID).innerHTML;
                            //Get the HTML of whole page
                            var oldPage = document.body.innerHTML;

                            //Reset the page's HTML with div's HTML only
                            document.body.innerHTML =
                                    "<html><head><title></title></head><body>" +
                                    divElements + "</body>";

                            //Print Page
                            window.print();

                            //Restore orignal HTML
                            document.body.innerHTML = oldPage;


                        }
                    </script>

