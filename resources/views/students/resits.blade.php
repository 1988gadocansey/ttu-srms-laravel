@extends('layouts.app')


@section('style')

@endsection
@section('content')
    @inject('strCtrl', 'App\Http\Controllers\StudentController')
    @inject('sys', 'App\Http\Controllers\SystemController')
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

    <div style="">
        <div class="uk-margin-bottom" style="margin-left:900px" >


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

            <a href="{{url('/student/resit')}}" ><i   title="refresh this page" class="uk-icon-refresh uk-icon-medium "></i></a>



        </div>
    </div>
    <h6 class="heading_c">Resits Courses</h6>
    <div class="uk-width-xLarge-1-1">
        <div class="md-card">
            <div class="md-card-content">

                <form action="{{url('student_resit_process')}}"  method="POST" accept-charset="utf-8"  >
                    {!!  csrf_field()  !!}
                    <div class="uk-grid" data-uk-grid-margin="">

                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                {!! Form::select('program',
                            ($program ),
                              old("program",""),
                                ['class' => 'md-input parents','id'=>"parents",'required'=>'','placeholder'=>'select program']  )  !!}
                            </div>
                        </div>
                         <div class="uk-width-medium-1-5">
                             <div class="uk-margin-small-top">
                                 {!! Form::select('level',
                             ( $level ),
                               old("level",""),
                                 ['class' => 'md-input parents','required'=>'','id'=>"parents",'placeholder'=>'select level'] )  !!}
                             </div>
                         </div>
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">

                                {!!  Form::select('semester', array('1'=>'1st sem','2'=>'2nd sem','3' => '3rd sem'), null, ['placeholder' => 'select semester','id'=>'parents','class'=>'md-input parents','required'=>''],old("semester","")); !!}

                            </div>
                        </div>

                       


                        <div  align='center'>

                            <button class="md-btn  md-btn-small md-btn-success uk-margin-small-top" type="submit"><i class="material-icons">search</i></button>

                        </div>





                    </div>


                </form>
            </div>
        </div>
    </div>

    @if(Request::isMethod('post'))
        <p></p>
        <h4 class="heading_c uk-text-upper"><center>Resit for {{$sys->getProgram($programs) }},  Semester {{$term}}, Level {{$levels}}</center></h4>
        <p></p>
        <div class="uk-width-xLarge-1-1">
            <div class="md-card">
                <div class="md-card-content">
                    <div class="uk-overflow-container" id='print'>

                        <table border='1' class="uk-table uk-table-hover uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id="ts_pager_filter">
                            <thead>
                            <tr>

                                <th>No</th>

                                <th COLSPAN="0" class="uk uk-text-bold" style="text-align:center;font-weight: bold">Index Number</th>
                                <th COLSPAN="0" class="uk uk-text-bold" style="text-align:center;font-weight: bold">Name</th>
                               
                                <th COLSPAN="0" class="uk uk-text-bold" style="text-align:center;font-weight: bold">Course</th>

                            </tr>



                            </thead>
                            <tbody>




                            <?php $count=0;?>
                            @foreach($data as $row)  <?php  $code[]=$row->code;$count++;?>

                            <tr>
                                <td><?php  echo $count?></td>
                                <td> {{  @$row->indexno	 }}</td>
                                <td> {{  strtoupper(@$strCtrl->getStudentName($row->indexno))	 }}</td>
                                
                                <td> {{  @$strCtrl->getTrails(@$row->indexno,$term,$levels,$programs)	 }}</td>

                            </tr>
                                @endforeach
                            </tbody>

                        </table>


                        <table class="uk-table">
                            <tr>
                                <td>NO</td>
                                <td>CODE</td>
                                <td>COURSE NAME</td>
                                <td>TOTAL TRAILS</td>

                            </tr>
                            <tbody>
                            <?php $n=0;?>
                            @foreach(array_unique($code) as  $key)
                                <?php $n++; $courseDetail=$sys->getCourseByCodeObject($key)?>
                                <tr>
                                    <td>{{$n}}</td>

                                    <td>{{ $key }} </td>
                                    <td>{{ strtoupper(@$courseDetail[0]->COURSE_NAME)}} </td>
                                    <td>{{@$strCtrl->countTrails($key,$term,$levels,$programs)}}</td>

                                </tr>
                                @endforeach


                                </tbody>
                        </table>



                    </div>
                </div>

            </div>
        </div>
    @endif
@endsection
@section('js')

    <script type="text/javascript">



    </script>
    <script src="{!! url('public/assets/js/select2.full.min.js') !!}"></script>
    <script>
        $(document).ready(function () {
            $('select').select2({width: "resolve"});
        });</script>



@endsection