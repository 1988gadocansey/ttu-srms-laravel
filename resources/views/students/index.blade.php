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


            <div class="uk-alert uk-alert-danger  uk-alert-close" style="background-color: red;color: white"
                 data-uk-alert="">

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
                <h4 class="uk-modal-title">Send sms here</h4>
            </div>
            <center><p>Insert the following placeholders into the message [NAME] [FIRSTNAME] [SURNAME] [INDEXNO] [CGPA]
                    [BILLS] <br/>[BILL_OWING] [PROGRAMME] [PASSWORD]</p></center>
            <form action="{!! url('/sms')!!}" method="POST">
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">


                <textarea cols="30" rows="4" name="message" class="md-input" required=""></textarea>


                <div class="uk-modal-footer uk-text-right">
                    <button type="submit" class="md-btn md-btn-flat md-btn-flat-primary md-btn-wave"
                            id="snippet_new_save"><i class="material-icons">smartphone</i>Send
                    </button>
                    <button type="button" class="md-btn md-btn-flat uk-modal-close md-btn-wave">Close</button>
                </div>
            </form>
        </div>
    </div>
    <h3 class="heading_b uk-margin-bottom">Students List</h3>
    <div style="" class="">
        <!--    <div class="uk-margin-bottom" style="margin-left:910px" >-->
        <div class="uk-margin-bottom" style="">
            @if(@\Auth::user()->department=="Tpmid" || @\Auth::user()->department=="Tptop")
                <a href="#new_task" data-uk-modal="{ center:true }"> <i title="click to send sms to students"
                                                                        class="material-icons md-36 uk-text-success">phonelink_ring
                        message</i></a>
            @endif

            <a href="#" class="md-btn md-btn-small md-btn-success uk-margin-right" id="printTable">Print Table</a>
            <div class="uk-button-dropdown" data-uk-dropdown="{mode:'click'}">
                <button class="md-btn md-btn-small md-btn-success"> columns <i class="uk-icon-caret-down"></i></button>
                <div class="uk-dropdown">
                    <ul class="uk-nav uk-nav-dropdown" id="columnSelector"></ul>
                </div>
            </div>


            <div style="margin-top: -5px" class="uk-button-dropdown" data-uk-dropdown="{mode:'click'}">
                <button class="md-btn md-btn-small md-btn-success uk-margin-small-top">Export <i
                            class="uk-icon-caret-down"></i></button>
                <div class="uk-dropdown">
                    <ul class="uk-nav uk-nav-dropdown">

                        <li class="uk-nav-divider"></li>
                        <li><a href="#" onClick="$('#ts_pager_filter').tableExport({type:'excel',escape:'false'});"><img
                                        src='{!! url("public/assets/icons/xls.png")!!}' width="24"/> Excel</a></li>
                        <li class="uk-nav-divider"></li>

                    </ul>
                </div>
            </div>


            <i title="click to print" onclick="javascript:printDiv('print')"
               class="material-icons md-36 uk-text-success">print</i>


        </div>
    </div>
    <!-- filters here -->
    @inject('fee', 'App\Http\Controllers\FeeController')
    @inject('sys', 'App\Http\Controllers\SystemController')
    <div class="uk-width-xLarge-1-1">
        <div class="md-card">
            <div class="md-card-content">

                <form action=" " method="get" accept-charset="utf-8" novalidate id="group">
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
                                {!! Form::select('level',
                                (['' => 'by levels'] +$level  ),
                                old("level",""),
                                ['class' => 'md-input parent','id'=>"parent"] )  !!}
                            </div>
                        </div>
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">

                                {!!  Form::select('gender', array('Male'=>'Male','Female' => 'Female'), null, ['placeholder' => 'select gender','id'=>'parent','class'=>'md-input parent'],old("level","")); !!}

                            </div>
                        </div>
                        @if(@\Auth::user()->role=='Dean' || @\Auth::user()->department=="Tpmid" || @\Auth::user()->department=="Tptop" || @\Auth::user()->department=="Finance" || @\Auth::user()->department=="Rector" || @\Auth::user()->role=="Rector" || @\Auth::user()->department=="Registrar" || @\Auth::user()->department=="Admissions" ||  @\Auth::user()->department=="Planning"  || @\Auth::user()->role=="Accountant" || @\Auth::user()->department == 'Examination' || @\Auth::user()->role == 'Admin' || @\Auth::user()->department == 'top')
                            <div class="uk-width-medium-1-5">
                                <div class="uk-margin-small-top">
                                    {!! Form::select('school',
                                    (['' => 'by schools'] +$school  ),
                                    old("school",""),
                                    ['class' => 'md-input parent','id'=>"parent"] )  !!}
                                </div>
                            </div>
                        @endif
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

                                {!!  Form::select('status', array('Admitted'=>'Admitted','In school'=>'In school','Alumni' => 'Completed','Deferred' => 'Deferred','Dead' => 'Dead','Rusticated' => 'Rusticated','Withdrawn' => 'Withdrawn','Unknown' => 'Unknown'), null, ['placeholder' => 'select status of student','id'=>'parent','value'=>'In school','class'=>'md-input parent'],old("level","")); !!}

                            </div>
                        </div>
                        @if(@\Auth::user()->role=='Dean' || @\Auth::user()->department=="Tpmid" || @\Auth::user()->department=="Tptop" || @\Auth::user()->department=="Finance" || @\Auth::user()->department=="Rector" || @\Auth::user()->role=="Rector" || @\Auth::user()->department=="Registrar" || @\Auth::user()->department=="Admissions" ||  @\Auth::user()->department=="Planning"  || @\Auth::user()->role=="Accountant" || @\Auth::user()->department == 'Examination' || @\Auth::user()->role == 'Admin' || @\Auth::user()->department == 'top')
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
                            @if(@\Auth::user()->role!='FO')
                                <div class="uk-width-medium-1-5">
                                    <div class="uk-margin-small-top">
                                        {!! Form::select('religion',
                                        (['' => 'Search by Religions'] +$religion  ),
                                        old("religion",""),
                                        ['class' => 'md-input parent','id'=>"parent"] )  !!}
                                    </div>
                                </div>
                            @endif
                        @endif
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                {!! Form::select('group',
                                (['' => 'by graduating group'] +$year  ),
                                old("group",""),
                                ['class' => 'md-input parent','id'=>"parent"] )  !!}
                            </div>
                        </div>
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                {!! Form::select('type',
                                (['' => 'by programme types'] +$type  ),
                                old("type",""),
                                ['class' => 'md-input parent','id'=>"parent"] )  !!}
                            </div>
                        </div>
                        @if(@\Auth::user()->isSupperAdmin=='1')
                            <div class="uk-width-medium-1-5">
                                <div class="uk-margin-small-top">

                                    {!!  Form::select('sms', array(''=>'- select status -','1'=>'SMS Sent','0'=>'SMS not sent'), null, ['placeholder' => 'select sms status of student','id'=>'parent','class'=>'md-input parent'],old("level","")); !!}

                                </div>
                            </div>
                        @endif
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">

                                {!!  Form::select('register', array('1'=>'Registered students','0' => 'Unregistered students'), null, ['placeholder' => 'Select Registration Status','id'=>'parent','class'=>'md-input parent'],old("action","")); !!}

                            </div>
                        </div>
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">

                                {!!  Form::select('qa', array('1'=>'Students who have access their lecturers','0' => 'Students yet to access'), null, ['placeholder' => 'Quality Assurance Status','id'=>'parent','class'=>'md-input parent'],old("qa","")); !!}

                            </div>
                        </div>
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">

                                {!!  Form::select('by', array('INDEXNO'=>'Search by Index Number','STNO'=>'Search by Admission Number','NAME'=>'Search by Name','required'=>''), null, ['placeholder' => 'select search type','class'=>'md-input'], old("","")); !!}
                            </div>
                        </div>
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                <input type="text" style=" " required="" name="search" class="md-input"
                                    placeholder="search student by index number or name">
                            </div>
                        </div>
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                <input type="text" style=" " required="" name="pay" class="md-input"
                                    placeholder="search student by payment">
                            </div>
                        </div>


                    </div>
                    <center>
                        <div class="uk-width-medium-1-10" style=" ">
                            <div class="uk-margin-small-top">

                                <button class="md-btn  md-btn-small md-btn-success uk-margin-small-top" type="submit">
                                    <i class="material-icons">search</i></button>
                            </div>
                        </div>
                    </center>
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
                    <table class="uk-table uk-table-hover uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair"
                           id="ts_pager_filter">
                        <thead>
                        <tr>
                            <th class="filter-false remove sorter-false">NO</th>

                            <th data-priority="6">NAME</th>
                            <th>PHOTO</th>
                            <th>INDEX N<u>O</u></th>
                            <th>STUDENT N<u>O</u></th>


                            <th>PROGRAM</th>

                            <th>LEVEL</th>

                            <th>GENDER</th>


                            <th>AGE</th>


                            @if(@\Auth::user()->role=='Dean' || @\Auth::user()->department=="Tpmid" || @\Auth::user()->department=="Tptop" || @\Auth::user()->department=="Rector" || @\Auth::user()->role=="Rector" || @\Auth::user()->role=="Accountant"  || @\Auth::user()->department == 'top')
                                <th>PHONE</th>
                            @endif
                            <th>NATIONALITY</th>
                            <th>YEAR BILLS</th>
                            @if(@\Auth::user()->role=='Dean' || @\Auth::user()->department=="Tpmid" || @\Auth::user()->department=="Tptop" || @\Auth::user()->department=="Finance" || @\Auth::user()->department=="Planning" || @\Auth::user()->department=="Rector" || @\Auth::user()->role=="Rector" || @\Auth::user()->role=="Accountant"  || @\Auth::user()->department == 'top')
                                <th>PAID</th>
                                <th>OWINGS</th>
                                <th>PASSWORD</th>
                            @endif
                            <th>YEAR GROUP</th>
                            <th>QUALITY ASSURANCE</th>


                            <th>STATUS</th>
                            @if(@\Auth::user()->department=='Tptop' || @\Auth::user()->department=='Tpmid')


                                <th colspan="2" class="filter-false remove sorter-false uk-text-center"
                                    data-priority="1">ACTION
                                </th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($data as $index=> $row)




                            <tr align="">
                                <td> {{ $data->perPage()*($data->currentPage()-1)+($index+1) }} </td>
                                <td> {{ strtoupper(@$row->NAME) }}</td>
                                <td>

                                    @if(substr($row->LEVEL, 0, 3 ) === "100" || substr($row->LEVEL, 0, 3 ) === "500"  )
                                        <img style="width:90px;height:auto;margin-left:-5px" <?php
                                        $pic = $row->STNO;
                                        echo $sys->picture("{!! url(\"public/albums/applicants/$pic.jpg\") !!}", 50)
                                        ?>  src="http://www.ttuportal.com/admissions/public/albums/thumbnails/{{$pic}}.jpg"
                                             alt="photo"/>






                        @else
                                        
                    


                         <?php
                          $pic = $row->INDEXNO;
                          $filename = url("public/albums/students/$pic.JPG");

                         //for 2 weeks Gad couldn't write the code below. i did i one sunday morning
                            ?>

                             <a onclick="return MM_openBrWindow('{{url("/student_show/$row->ID/id")}}', 'mark', 'width=800,height=500')"><img  style="width:90px;height: auto;" src='{{url("public/albums/students/$pic.JPG")}}' onerror="this.onerror=function my(){return this.src='{{url("public/albums/students/USER.JPG")}}';};this.src='{{url("public/albums/students/$pic.jpg")}}';" /></a>

                                   
                                   

                        @endif
                </div>



                </td>
                <td> {{ @$row->INDEXNO }}</td>
                <td> {{ @$row->STNO }}</td>

                <td>{!! strtoupper(@$row->program->PROGRAMME) !!}</td>
                <td> {{ strtoupper(@$row->levels->slug) }}</td>

                <td> {{ strtoupper(@$row->SEX) }}</td>
                <td> {{ @$row->AGE }}yrs</td>

                @if(@\Auth::user()->role=='Dean' || @\Auth::user()->department=="Tpmid" || @\Auth::user()->department=="Tptop" || @\Auth::user()->department=="Rector" || @\Auth::user()->role=="Rector" || @\Auth::user()->role=="Accountant"  || @\Auth::user()->department == 'top')

                    <td> {{ @$row->TELEPHONENO }}</td>
                @endif
                <td> {{ strtoupper(@$row->COUNTRY) }}</td>

                <td>GHC {{ @$row->BILLS }}</td>
                @if(@\Auth::user()->role=='Dean' || @\Auth::user()->department=="Tpmid" || @\Auth::user()->department=="Tptop" || @\Auth::user()->department=="Finance" || @\Auth::user()->department=="Rector" || @\Auth::user()->role=="Rector" || @\Auth::user()->role=="Accountant"  || @\Auth::user()->department=="Planning" || @\Auth::user()->department == 'top')
                    <td>GHC {{ @$row->PAID }}</td>
                    <td>GHC {{ @$row->BILL_OWING }}</td>


                    <td> {{ @$sys->getStudentPassword(@$row->INDEXNO) }}</td>
                @endif
                <td> {{ @$row->GRADUATING_GROUP }}</td>
                <td>
                    @if($row->QUALITY_ASSURANCE=='1')
                        Yes
                    @else
                        No
                    @endif

                </td>


                <td> {{ strtoupper(@$row->STATUS) }}</td>
                @if( @\Auth::user()->department=="Tptop"|| @\Auth::user()->department=="Tpmid")

                    <td>
                        <a href='{{url("edit_student/$row->ID/id")}}'>Edit</a>
                        <a onclick="return MM_openBrWindow('{{url("/student_show/$row->ID/id")}}', 'mark', 'width=800,height=500')">View</a>

                    </td>
                    @endif

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
    <script>
        $(document).ready(function () {
            $('select').select2({width: "resolve"});
        });</script>

@endsection