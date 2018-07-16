@extends('layouts.app')

 
@section('style')
    <style>
     
   table td { word-wrap:break-word;}
   </style>
@endsection
@section('content')

<div class="md-card-content">
<div style="text-align: center;display: none" class="uk-alert uk-alert-success" data-uk-alert="">

    </div>



    <div style="text-align: center;display: none" class="uk-alert uk-alert-danger" data-uk-alert="">

    </div>

    @if (count($errors) > 0)


    <div class="uk-alert uk-alert-danger  uk-alert-close" style="background-color: red;color: white" data-uk-alert="">

        <ul>
            @foreach ($errors->all() as $error)
            <li>{!!$error  !!} </li>
            @endforeach
        </ul>
    </div>

    @endif


</div>
<div class="uk-modal" id="new_task">
    <div class="uk-modal-dialog">
        <div class="uk-modal-header">
            <h4 class="uk-modal-title">Send sms  here</h4>
        </div>
        <center> <p>Insert the following placeholders into the message [NAME] [FIRSTNAME] [SURNAME] [APPLICATION_NUMBER] [HALL_ADMITTED] [ADMISSION_FEES] <br/>[PROGRAMME_ADMITTED]</p></center>
        <form action="{!! url('/sms')!!}" method="POST">
            <input type="hidden" name="_token" value="{!! csrf_token() !!}"> 


            <textarea cols="30" rows="4" name="message"class="md-input" required=""></textarea>


            <div class="uk-modal-footer uk-text-right">
                <button type="submit" class="md-btn md-btn-flat md-btn-flat-primary md-btn-wave" id="snippet_new_save"><i   class="material-icons"   >smartphone</i>Send</button>    
                <button type="button" class="md-btn md-btn-flat uk-modal-close md-btn-wave">Close</button>
            </div>
        </form>
    </div>
</div>
<h3 class="heading_b uk-margin-bottom">Applicants List</h3>
<div style="" class="">

    <!--    <div class="uk-margin-bottom" style="margin-left:910px" >-->
    <div class="uk-margin-bottom" style="" >
        <a  href="#new_task" data-uk-modal="{ center:true }"> <i title="click to send sms to students"   class="material-icons md-36 uk-text-success"   > message</i></a>

        <a href="#" class="md-btn md-btn-small md-btn-success uk-margin-right" id="printTable">Print Table</a>
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
                    <li><a href="#" onClick ="$('#ts_pager_filter').tableExport({type: 'csv', escape: 'false'});"><img src='{!! url("assets/icons/csv.png")!!}' width="24"/> CSV</a></li>

                    <li class="uk-nav-divider"></li>
                    <li><a href="#" onClick ="$('#ts_pager_filter').tableExport({type: 'excel', escape: 'false'});"><img src='{!! url("assets/icons/xls.png")!!}' width="24"/> XLS</a></li>
                    <li><a href="#" onClick ="$('#ts_pager_filter').tableExport({type: 'doc', escape: 'false'});"><img src='{!! url("assets/icons/word.png")!!}' width="24"/> Word</a></li>
                    <li><a href="#" onClick ="$('#ts_pager_filter').tableExport({type: 'powerpoint', escape: 'false'});"><img src='{!! url("assets/icons/ppt.png")!!}' width="24"/> PowerPoint</a></li>
                    <li class="uk-nav-divider"></li>

                </ul>
            </div>
        </div>




        <i title="click to print" onclick="javascript:printDiv('print')" class="material-icons md-36 uk-text-success"   >print</i>

            <a  href="{{url('/applicant/auto/sms')}}"  onclick="return alert('This will send bulk admission notification to all applicants')"  title="sent bulk admission notification to applicants"> <i   title="click to sent bulk admission notification to applicants"  class="material-icons md-36 uk-text-success"   >phonelink_ring</i></a>


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
                            (['' => 'All programs'] + $programme ), 
                            old("program",""),
                            ['class' => 'md-input parent','id'=>"parent",'placeholder'=>'select program'] )  !!}
                        </div>
                    </div>

                    <div class="uk-width-medium-1-5">
                        <div class="uk-margin-small-top">

                            {!!  Form::select('gender', array('MALE'=>'Male','FEMALE' => 'Female'), null, ['placeholder' => 'select gender','id'=>'parent','class'=>'md-input parent'],old("level","")); !!}

                        </div>
                    </div>
                    <div class="uk-width-medium-1-5">
                        <div class="uk-margin-small-top">
                            {!! Form::select('school', 
                            (['' => 'by schools'] +$school  ), 
                            old("school",""),
                            ['class' => 'md-input parent','id'=>"parent"] )  !!}
                        </div>
                    </div>

                    <div class="uk-width-medium-1-5">
                        <div class="uk-margin-small-top">
                            {!! Form::select('department', 
                            (['' => 'departments'] +$department  ), 
                            old("department",""),
                            ['class' => 'md-input parent','id'=>"parent"] )  !!}
                        </div>
                    </div>

                    <div class="uk-width-medium-1-5">
                        <div class="uk-margin-small-top">

                            {!!  Form::select('status', array('Admitted'=>'Admitted','APPLICANT' => 'Applicants','In School'=>'In school'), null, ['placeholder' => 'select status of applicant','id'=>'parent','class'=>'md-input parent'],old("level","")); !!}

                        </div>
                    </div>
                    <div class="uk-width-medium-1-5">
                        <div class="uk-margin-small-top">
                            {!! Form::select('hall', 
                            (['' => 'Search by Halls'] +$halls  ), 
                            old("hall",""),
                            ['class' => 'md-input parent','id'=>"parent"] )  !!}
                        </div>
                    </div>
                    <div class="uk-width-medium-1-5">
                        <div class="uk-margin-small-top">
                            {!! Form::select('nationality', 
                            (['' => 'Nationality'] +$nationality  ), 
                            old("nationality",""),
                            ['class' => 'md-input parent','id'=>"parent"] )  !!}
                        </div>
                    </div>

                    <div class="uk-width-medium-1-5">
                        <div class="uk-margin-small-top">
                            {!! Form::select('region', 
                            (['' => 'Search by Regions'] +$region  ), 
                            old("region",""),
                            ['class' => 'md-input parent','id'=>"parent"] )  !!}
                        </div>
                    </div>

                    <div class="uk-width-medium-1-5">
                        <div class="uk-margin-small-top">
                            {!! Form::select('religion', 
                            (['' => 'Search by Religions'] +$religion  ), 
                            old("religion",""),
                            ['class' => 'md-input parent','id'=>"parent"] )  !!}
                        </div>
                    </div>

                    <div class="uk-width-medium-1-5">
                        <div class="uk-margin-small-top">
                            {!! Form::select('group', 
                            (['' => 'by admission year'] +$year  ), 
                            old("group",""),
                            ['class' => 'md-input parent','id'=>"parent"] )  !!}
                        </div>
                    </div>
                    <div class="uk-width-medium-1-5">
                        <div class="uk-margin-small-top">
                            {!! Form::select('type', 
                            (['' => 'by form types'] +$type  ), 
                            old("type",""),
                            ['class' => 'md-input parent','id'=>"parent"] )  !!}
                        </div>
                    </div>

                    <div class="uk-width-medium-1-5">
                        <div class="uk-margin-small-top">

                            {!!  Form::select('completed', array(''=>'- select status -','1'=>'Form Completed','0'=>'Not Completed'), null, ['placeholder' => 'select form status','id'=>'parent','class'=>'md-input parent'],old("level","")); !!}

                        </div>
                    </div>

                    <div class="uk-width-medium-1-5">
                        <div class="uk-margin-small-top">

                            {!!  Form::select('by', array('APPLICATION_NUMBER'=>'Search by Index Number','NAME'=>'Search by Name','required'=>''), null, ['placeholder' => 'select search type','class'=>'md-input'], old("","")); !!}
                        </div>
                    </div>

                    <div class="uk-width-medium-1-5">
                        <div class="uk-margin-small-top">                            
                            <input type="text" style=" " required=""  name="search"  class="md-input" placeholder="search student by index number or name">
                        </div>
                    </div>




                </div>
                <center>   <div class="uk-width-medium-1-10" style=" ">
                        <div class="uk-margin-small-top">                            

                            <button class="md-btn  md-btn-small md-btn-success uk-margin-small-top" type="submit"><i class="material-icons">search</i></button> 
                        </div>
                    </div></center>
            </form> 
        </div>
    </div>
<!--    <div class="md-card">
        <div class="md-card-content">
             <form   method="GET" accept-charset="utf8">
                                    {!!  csrf_field() !!}
                                    <center>   <div class="uk-width-medium-1-10" style=" ">
                        <div class="uk-margin-small-top">                            

                        
                         <a  href="{{url('applicants/sms')}}" > <i title="send sms to admitted applicants" onclick="return confirm('This action will send sms to all the admitted applicants. It is the final stage of the admission and you cannot revoke the applicant admission status again.')"  class="material-icons md-36 uk-text-success"   >phonelink_ring</i></a>

                        </div>
                    </div></center>
             </form>
        </div>
    </div>-->
</div>

<!-- end filters -->
<div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content">


            <div class="uk-overflow-container" id='print'>
                <center><span class="uk-text-success uk-text-bold">{!! $data->total()!!} Records</span></center>
                <table class="uk-table uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id="ts_pager_filter"> 
                    <thead>
                        <tr>
                            <th class="filter-false remove sorter-false" >NO</th>

                            <th data-priority="6">NAME</th>
                            <th>CODE</th>
                            <th>PHOTO</th>


                            <th>ADMISSION N<u>O</u></th>

                    <th>GENDER</th>

                    <th>AGE</th>

                    <th>PHONE <u>NO</u></th>

                    <th>FIRST CHOICE</th>
                    <th>SECOND CHOICE</th>
                    <th>THIRD CHOICE</th>
                    <th>QUALIFICATION</th>
                    
                    <th>ELIGIBILTY</th>
                    <th>BEST AGGREGATE</th>
                    <th>RESULTS</th>
                    <th>QUALIFY?</th>
                    <th>PROGRAMME ADMITTED</th>
                    <th>ADMISSION FEES</th>
                    <th>HALL ADMITTED</th>
                    <th>STATUS</th>
                   
                    <th>ADMISSION TYPE</th>
                    <th>SMS SENT</th>
                   
                    <th colspan="4" class="filter-false remove sorter-false uk-text-center" data-priority="1">STEP 1 - SELECT HALL</th>   
                    <th colspan="4" class="filter-false remove sorter-false uk-text-center" data-priority="1">STEP 2 - SELECT PROGRAM</th>
                    <th colspan="4" class="filter-false remove sorter-false uk-text-center" data-priority="1">STEP 3- SELECT ADMISSION TYPE</th>   
                     <th>STEP4-RESIDENTIAL STATUS</th>
                    </tr>
                    </thead>
                    <tbody>

                        @foreach($data as $index=> $row) 




                        <tr align="">

                            <td> {{ $data->perPage()*($data->currentPage()-1)+($index+1) }} </td>
                            <td> {{ @$row->NAME }}</td>
                            <td>PIN CODE: {{ @$row->formDetails->PIN }}  &nbsp;SERIAL: {{@$row->formDetails->serial}}</td>
                            <td> <img class="" style="width:77px;height: auto" src="http://application.ttuportal.com/public/uploads/photos/{{$row->APPLICATION_NUMBER}}.jpg"alt="photo"    /></td> 


                            <td> {{ @$row->APPLICATION_NUMBER }}</td>

                            <td> {{ @strtoupper($row->GENDER) }}</td>

                            <td> {{ @$row->AGE }}yrs</td>

                            <td> {{ @$row->PHONE }}</td>

                            <td> {{ strtoupper(@$sys->getProgram($row->FIRST_CHOICE)) }}</td>
                            <td> {{  strtoupper(@$sys->getProgram(@$row->SECOND_CHOICE)) }}</td>
                            <td> {{ strtoupper(@$sys->getProgram(@$row->THIRD_CHOICE)) }}</td>
                            <td> {{ @$row->ENTRY_QUALIFICATION }}</td>
                            
                            <td class="uk-text-primary  uk-text-bold uk-text-nowrap"> {!! nl2br(e(@$row->ELIGIBILTY)) !!}</td>
                            <td class="uk-text-success  uk-text-bold"> {{ @$row->GRADE }}</td>
                            <td class="uk-text-success  uk-text-bold"  >{!!wordwrap(@$row->GRADES, 60, "<br />\n")!!}</td>
                            <td class="uk-text-primary  uk-text-bold"> {{ @$row->QUALIFY }}</td>
                            <td> {{ strtoupper(@$sys->getProgram($row->PROGRAMME_ADMITTED)) }}</td>
                            <td>GHC {{ @$row->ADMISSION_FEES }}</td>
                            <td> {{ @$row->HALL_ADMITTED }}</td>
                             <td> {{ @$row->STATUS }}</td>
                             <td class="uk-tex-successt"> {{ strtoupper(@$row->ADMISSION_TYPE) }}</td>
                            <td class="uk-tex-successt">{!!wordwrap($sys->getMessage($row->APPLICATION_NUMBER), 60, "<br />\n")!!}</td>
                           
                         
                                <form id="gads" method="Post" accept-charset="utf8">
                                    {!!  csrf_field() !!} 
                                    <td style="width:300px">
                                        {!! Form::select('halls', 
                                        (['' => 'Search by Halls'] +$halls  ), 
                                        old("hall",""),
                                        ['class' => 'md-input halls', 'style'=>"width:300px"] )  !!}
                                    </td>
                                    <td style="width:300px">

                                        {!! Form::select('programs', 
                                        (['' => 'select program to admit'] + $programme ), 
                                        old("",""),
                                        ['class' => 'md-input programs','style'=>"width:300px",'placeholder'=>'select program'] )  !!}

                                        <input type="hidden" name="<?php echo @$row->APPLICATION_NUMBER ?>" id="<?php echo @$row->APPLICATION_NUMBER ?>" value="<?php echo @$row->APPLICATION_NUMBER ?>" class="app"/>

                                    </td>
                                    <td>
                                        <select name="type" required="" class="type">
                                            <option>select admission type</option>
                                            <option value="regular">Regular</option>
                                             <option value="conditional">Conditional</option>
                                             <option value="mature">Mature</option>
                                             <option value="provisional">Provisional</option>
                                              <option value="technical">Technical</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="resident" required="" class="resident">
                                            <option value="">select residential status</option>
                                            <option value="1">Resident</option>
                                             <option value="0">Affiliated</option>
                                              
                                        </select>
                                    </td>
                                    <td>
                                        <input title="click to admit" type="checkbox" <?php
                                       
                                        ?> class="admit" value="admit" db_id="<?php echo @$row->APPLICATION_NUMBER ?>" name="<?php echo @$row->APPLICATION_NUMBER ?>" id="<?php echo @$row->APPLICATION_NUMBER ?>" class="admit"  />
                                        
                               
                                    
                                    </td>   


                                </form>      



                            <td>
                                <a onclick="return MM_openBrWindow('{{url("/applicant_show/$row->APPLICATION_NUMBER/id")}}', 'mark', 'width=800,height=500')" ><i title='Click to view applicant .. please allow popups on browser' class="md-icon material-icons">&#xE8F4;</i></a> 
                           
                                        
                                 <a onclick="return MM_openBrWindow('{{url("/applicant/letter/$row->APPLICATION_NUMBER/printout")}}', 'mark', 'width=800,height=500')" ><i title='Click to print letter .. please allow popups on browser' class="md-icon material-icons">book</i></a> 
                             
                           @if($row->SMS_SENT==0 &&$row->ADMITTED==1 )
                                 <a onclick="return confirm('This will send sms notification to this applicant. Are you sure you want to continue?')"href='{{url("/phone/$row->PHONE/receipient/$row->APPLICATION_NUMBER/type/$row->ADMISSION_TYPE/name/$row->FIRSTNAME/")}}'  ><i title='Click to send sms' class="md-icon material-icons">phonelink_ring</i></a> 
                            @endif
                            </td>

                        </tr>
                        @endforeach
                    </tbody>

                </table>
                {!! (new Landish\Pagination\UIKit($data->appends(old())))->render() !!}
            </div>
        </div>


    </div>
</div></div>
@endsection
@section('js')
<script type="text/javascript">

                    $(document).ready(function () {

            $(".parent").on('change', function (e) {

            $("#group").submit();
            });
            });</script>
<script src="{!! url('public/assets/js/select2.full.min.js') !!}"></script>
<script src="{!! url('public/assets/js/ajax.js') !!}"></script>

<script>
                    $(document).ready(function () {
            $('select').select2({width: "resolve"});
            });</script>
<script>
                    $(document).ready(function(){
            $('.admit').on('click', function(e){


            var applicant = $(this).closest('tr').find('.app').val();
                    var program = $(this).closest('tr').find('.programs').val();
                     var hall = $(this).closest('tr').find('.halls').val();
                     var admit = $(this).closest('tr').find('.admit').val();
                     var type = $(this).closest('tr').find('.type').val();
                      var resident = $(this).closest('tr').find('.resident').val();
                       //alert(resident);
             if(program=="" || type=="" || hall=="" || resident=="")
             {
                 alert("please select program,admission type,hall and residential status ");
             }
             else{
                    UIkit.modal.confirm("Are you sure you want to admit this applicant?? "
                            , function(){
                            modal = UIkit.modal.blockUI("<div class='uk-text-center'>Admitting Applicant " + applicant + " <br/><img class='uk-thumbnail uk-margin-top' src='{!! url('public/assets/img/spinners/spinner.gif')  !!}' /></div>");
                                    //setTimeout(function(){ modal.hide() }, 500) })()            
                                    $.ajax({
                                     
                                            type: "POST",
                                            url:"{{ url('/applicants/admit')}}",
                                            data: { applicant:applicant, program:program,hall:hall,admit:admit,type:type,resident:resident }, //your form data to post goes 
                                            dataType: "json",
                                    }). done(function(data){
                //  var objData = jQuery.parseJSON(data);
                modal.hide();
                        //                                    
                        //                                     UIkit.modal.alert("Action completed successfully");

                        //alert(data.status + data.data);
                        if (data.status == 'success'){
                $(".uk-alert-success").show();
                        $(".uk-alert-success").text(data.status + " " + data.message);
                        $(".uk-alert-success").fadeOut(4000);
                }
                else{
                $(".uk-alert-danger").show();
                        $(".uk-alert-danger").text(data.status + " " + data.message);
                        $(".uk-alert-danger").fadeOut(4000);
                }


                });
                            }
                    );}
            });
            
             $('.conditional').on('click', function(e){


            var student = $(this).closest('tr').find('.app').val();
                    var program = $(this).closest('tr').find('.programs').val();
                     var hall = $(this).closest('tr').find('.halls').val();
                       var conditional = $(this).closest('tr').find('.conditional').val();
                      //alert(hall);
                    UIkit.modal.confirm("Are you sure you want to give this applicant conditional?? "
                            , function(){
                            modal = UIkit.modal.blockUI("<div class='uk-text-center'>Applying conditional admission to Applicant " +  student  + " <br/><img class='uk-thumbnail uk-margin-top' src='{!! url('public/assets/img/spinners/spinner.gif')  !!}' /></div>");
                                    //setTimeout(function(){ modal.hide() }, 500) })()            
                                    $.ajax({
                                     
                                            type: "POST",
                                             url:"{{ url('/applicants/admit')}}",
                                            data: { applicant:student, program:program,hall:hall,conditional:conditional}, //your form data to post goes 
                                            dataType: "json",
                                    }).done(function(data){
                //  var objData = jQuery.parseJSON(data);
                modal.hide();
                        //                                    
                        //                                     UIkit.modal.alert("Action completed successfully");

                        //alert(data.status + data.data);
                        if (data.status == 'success'){
                $(".uk-alert-success").show();
                        $(".uk-alert-success").text(data.status + " " + data.message);
                        $(".uk-alert-success").fadeOut(4000);
                }
                else{
                $(".uk-alert-danger").show();
                        $(".uk-alert-danger").text(data.status + " " + data.message);
                        $(".uk-alert-danger").fadeOut(4000);
                }


                });
                            }
                    );
            });
            });</script>
<!--  notifications functions -->
<script src="assets/js/components_notifications.min.js"></script>
@endsection