@extends('layouts.app')





@section('style')



@endsection

@section('content')

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





            <a href="{{url('/report/broadsheet')}}" ><i   title="refresh this page" class="uk-icon-refresh uk-icon-medium "></i></a>







        </div>

    </div>



    <div class="uk-width-xLarge-1-1">

        <div class="md-card">

            <div class="md-card-content">



                <form action="{{url('/process_broadsheet')}}"  method="POST" accept-charset="utf-8"  >

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



                        <div class="uk-width-medium-1-5">

                            <div class="uk-margin-small-top">

                                {!! Form::select('year',

                          (['' => 'Select year'] +$year ),

                            old("year",""),

                              ['class' => 'md-input parenst','id'=>"parents" ,'required'=>''] )  !!}   </div>

                        </div>



                        <!--                         <div class="uk-width-medium-1-5">

                                                    <div class="uk-margin-small-top">

                                                        <input type="text" style=" "   name="search"  class="md-input" placeholder="search by course name or course code">

                                                    </div>

                                                </div>-->















                    </div>

                    <div  align='center'>



                        <button class="md-btn  md-btn-small md-btn-success uk-margin-small-top" type="submit"><i class="material-icons">search</i></button>



                    </div>



                </form>

            </div>

        </div>

    </div>



    @if(Request::isMethod('post'))
                            <p></p>

                            <h4 class="heading_c"><center>Broadsheet for Academic Board, {{$years}}, Semester {{$term}} <br/><br/>{{$sys->getProgram($programs) }},   Level {{$levels}}</center></h4>

                            <p></p>


        <div class="uk-width-xLarge-1-1">

            <div class="md-card">

                <div class="md-card-content">

                    <div class="uk-overflow-container" id='print'>


                        <table border='1' class="uk-table uk-table-hover uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id="ts_pager_filter">
                            
                            <thead>
                               
                            <tr>

                                <th class="filter-false remove sorter-false"  >NO</th>

                                <th>INDEX</th>

                                <th>STUDENT</th>

                                <?php



                                $count=0;

                                $mark=array();

                                ?>

                                @foreach($headers as $header=> $td)



                                    <th> {{  strtoupper(@$td['code'])	 }}</th>



                                @endforeach

                                <th> GPA</th>

                                <th> CGPA</th>

                            </tr>







                            </thead>

                            <tbody>










                            <?php
                            $totalCount=0;

                            $grades= array();
                            $courseCode=array();
                            $gradeArray=array("A+","A","B+","B","C+","C","D+","D","F");
                            $countGrade=array();
                            ?>
                            @foreach($student as $stud=> $pupil)  <?php  $count++;?>
                            

                            @if($pupil->grade!="E")

                                <tr>



                                    <td><?php  $students[]=@$pupil->indexno;

                                        \Session::put('students', $students);echo $count?></td>

                                    <td><?php echo $pupil->indexno;?></td>

                                    <td> {{  strtoupper(@$pupil->student->NAME)	 }}</td>



                                    <?php

                                    $a=@$pupil->student->INDEXNO;



                                    for($i=0;$i<count($course);$i++){


                                            $gradeObject=$sys->getCourseGradeNoticeBoard($course[$i],$years,$term,@$a,$pupil->level);
                                        print_r("<td>".  @round(@$gradeObject->total). "&nbsp;&nbsp;  - &nbsp;&nbsp; " .@$gradeObject->grade."</td>");


                                    }

                                    ?>



                                     <td>{{$sys->getGPABySem(@$a,$term,$pupil->level)}}</td>

                                    <td>{{$sys->getCGPA(@$a)}}</td>

                                </tr>




                            @endif



                            @endforeach


                            <tr><td colspan="<?php echo count($course) + 5; ?>" align="center">Grades Count</td></tr>

                            @foreach($gradeArray as  $col)

                                <tr>
                                    <td></td><td></td>  <td>{{$col}} </td>

                                    @foreach($course as  $item=>$needle)
                                        <td>


                                            {{@$sys->getCourseGradeCounter($needle,$term,$levels,$years,$programs,$col)}}





                                        </td>
                                    @endforeach


                                </tr>


                            @endforeach



                            <tr>
                                <td></td>
                                <td></td>
                                <td>Total</td>
                                @foreach($course as  $item=>$needle)
                                    <td>


                                    <?php echo $sys->getCourseGradeCounterTotal($needle,$term,$levels,$years,$programs,$gradeArray);?>

                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td>CPA</td>
                                @foreach($course as  $key=>$val)

                                    <td>{{@$sys->getLecturerAverage(@$sys->getCourseGradeArray($val,$term,$levels,$years,$programs))}} </td>


                                @endforeach

                            </tr>
                            <tr><td colspan="<?php echo count($course) + 5; ?>" align="center">Courses</td></tr>

                            <tr>
                            <td>NO</td>
                            <td>CODE</td>
                            <td>COURSE NAME</td>
                            

                            <?php $n=0;?>
                            @foreach($course as  $key)
                                <?php $n++; $courseDetail=$sys->getCourseByCodeObject($key)?>
                                <tr>
                                    <td>{{$n}}</td>

                                    <td>{{ $key }} </td>
                                    <td>{{ strtoupper(@$courseDetail[0]->COURSE_NAME)}} </td>

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