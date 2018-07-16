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
            <a  href="{{url('/report/sms')}}"  onclick="return confirm('This will send bulk grades score notification to all students')"  title="sent bulk admission notification to applicants"> <i   title="click to sent bulk admission notification to applicants"  class="material-icons md-36 uk-text-success"   >phonelink_ring</i></a>

            <a href="{{url('/report/broadsheet')}}" ><i   title="refresh this page" class="uk-icon-refresh uk-icon-medium "></i></a>



        </div>
    </div>
    <h6 class="heading_c">Nabptex Broadsheet</h6>
    <div class="uk-width-xLarge-1-1">
        <div class="md-card">
            <div class="md-card-content">

                <form action="{{url('process_broadsheet_napbtex')}}"  method="POST" accept-charset="utf-8"  >
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
                       {{-- <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                {!! Form::select('level',
                            ( $level ),
                              old("level",""),
                                ['class' => 'md-input parents','required'=>'','id'=>"parents",'placeholder'=>'select level'] )  !!}
                            </div>
                        </div>--}}


                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                {!! Form::select('year',
                          (['' => 'Select year group'] +$year ),
                            old("year",""),
                              ['class' => 'md-input parenst','id'=>"parents" ,'required'=>''] )  !!}   </div>
                        </div>



                        <div  align='center'>

                            <button class="md-btn  md-btn-small md-btn-success uk-margin-small-top" type="submit"><i class="material-icons">search</i></button>

                        </div>
                       {{-- <div class="uk-width-medium-1-5">
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
                        </div>--}}

                        <!--                         <div class="uk-width-medium-1-5">
                                                    <div class="uk-margin-small-top">
                                                        <input type="text" style=" "   name="search"  class="md-input" placeholder="search by course name or course code">
                                                    </div>
                                                </div>-->







                    </div>


                </form>
            </div>
        </div>
    </div>

    @if(Request::isMethod('post'))
        <p></p>
        <h4 class="heading_c uk-text-upper"><center>Broadsheet for {{$sys->getProgram($programs) }}    Graduating Group {{$years}}</center></h4>
        <p></p>
        <div class="uk-width-xLarge-1-1">
            <div class="md-card">
                <div class="md-card-content">
                    <div class="uk-overflow-container" id='print'>

                        <table border='1' class="uk-table uk-table-hover uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id="ts_pager_filter">
                            <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th COLSPAN="4" class="uk uk-text-bold" style="text-align:center;font-weight: bold">SEM 1</th>
                                <th COLSPAN="4" class="uk uk-text-bold" style="text-align:center;font-weight: bold">SEM 2</th>
                                <th COLSPAN="4" class="uk uk-text-bold" style="text-align:center;font-weight: bold">SEM 3</th>
                                <th COLSPAN="4" class="uk uk-text-bold" style="text-align:center;font-weight: bold">SEM 4</th>
                                <th COLSPAN="4" class="uk uk-text-bold" style="text-align:center;font-weight: bold">SEM 5</th>
                                <th COLSPAN="4" class="uk uk-text-bold" style="text-align:center;font-weight: bold">SEM 6</th>
                                <th COLSPAN="6" class="uk uk-text-bold" style="text-align:center;font-weight: bold">COMMULATIVE</th>

                            </tr>


                            <tr>
                                <th class="filter-false remove sorter-false"  >NO</th>
                                <th>CANDIDATE NO</th>
                                <th>CANDIDATE NAME</th>

                                <th>CR</th>
                                <th>GP</th>
                                <th>GPA</th>
                                <th>CGPA</th>

                                <th>CR</th>
                                <th>GP</th>
                                <th>GPA</th>
                                <th>CGPA</th>

                                <th>CR</th>
                                <th>GP</th>
                                <th>GPA</th>
                                <th>CGPA</th>

                                <th>CR</th>
                                <th>GP</th>
                                <th>GPA</th>
                                <th>CGPA</th>

                                <th>CR</th>
                                <th>GP</th>
                                <th>GPA</th>
                                <th>CGPA</th>

                                <th>CR</th>
                                <th>GP</th>
                                <th>GPA</th>
                                <th>CGPA</th>

                                <th>CR</th>
                                <th>GP</th>
                                <th>GPA</th>
                                <th>CGPA</th>


                                <th>AWARDS</th>
                                <th>TRAILS</th>

                            </tr>
                            </thead>
                            <tbody>




                            <?php $count=0;?>
                            @foreach($student as $stud=> $pupil)  <?php  $count++;?>

                            <tr>
                                <td><?php $students[]=$pupil->indexno;
                                    \Session::put('students', $students);echo $count?></td>
                                <td> {{  strtoupper(@$pupil->INDEXNO)	 }}</td>
                                <td> {{  strtoupper(@$pupil->NAME)	 }}</td>

                                <td> <?php $semArray11=  $sys->getCreditBySem(@$pupil->INDEXNO,1,'100H');echo @$sys->getCreditBySem(@$pupil->INDEXNO,1,'100H')	 ?></td>
                                <td> <?php $gpArray11=  $sys->getGPBySem(@$pupil->INDEXNO,1,'100H');echo @$sys->getGPBySem(@$pupil->INDEXNO,1,'100H')	 ?></td>
                                <td> <?php $gpaArray11=  $sys->getGPABySem(@$pupil->INDEXNO,1,'100H');echo @$sys->getGPABySem(@$pupil->INDEXNO,1,'100H')	 ?></td>
                                <td> <?php $sem1CGPA=  $sys->getCGPAPerSem(@$pupil->INDEXNO,1,'100H');


                                echo $sem1CGPA	 ?></td>

                                <td> <?php $semArray12=  $sys->getCreditBySem(@$pupil->INDEXNO,2,'100H');echo @$sys->getCreditBySem(@$pupil->INDEXNO,2,'100H')	 ?></td>
                                <td> <?php $gpArray12=  $sys->getGPBySem(@$pupil->INDEXNO,2,'100H');echo @$sys->getGPBySem(@$pupil->INDEXNO,2,'100H')	 ?></td>
                                <td> <?php $gpaArray12=  $sys->getGPABySem(@$pupil->INDEXNO,2,'100H');echo @$sys->getGPABySem(@$pupil->INDEXNO,2,'100H')	 ?></td>

                                <td> <?php $sem2CGPA=    @number_format( (($gpArray11+$gpArray12)/($semArray11+$semArray12)),2, '.', ',');


                                    echo $sem2CGPA	 ?></td>
                                <!-- 2nd year -->

                                <td> <?php $semArray21=  $sys->getCreditBySem(@$pupil->INDEXNO,1,'200H');echo @$sys->getCreditBySem(@$pupil->INDEXNO,1,'200H')	 ?></td>
                                <td> <?php $gpArray21=  $sys->getGPBySem(@$pupil->INDEXNO,1,'200H');echo @$sys->getGPBySem(@$pupil->INDEXNO,1,'200H')	 ?></td>
                                <td> <?php $gpaArray21=  $sys->getGPABySem(@$pupil->INDEXNO,1,'200H');echo @$sys->getGPABySem(@$pupil->INDEXNO,1,'200H')	 ?></td>
                                <td> <?php $sem3CGPA=    @number_format( (($gpArray11+$gpArray12+$gpArray21)/($semArray11+$semArray12+$semArray21)),2, '.', ',');


                                    echo $sem3CGPA	 ?></td>

                                <td> <?php $semArray22=  $sys->getCreditBySem(@$pupil->INDEXNO,2,'200H');echo @$sys->getCreditBySem(@$pupil->INDEXNO,2,'200H')	 ?></td>
                                <td> <?php $gpArray22=  $sys->getGPBySem(@$pupil->INDEXNO,2,'200H');echo @$sys->getGPBySem(@$pupil->INDEXNO,2,'200H')	 ?></td>
                                <td> <?php $gpaArray22=  $sys->getGPABySem(@$pupil->INDEXNO,2,'200H');echo @$sys->getGPABySem(@$pupil->INDEXNO,2,'200H')	 ?></td>
                                <td> <?php $sem4CGPA=    @number_format( (($gpArray11+$gpArray12+$gpArray21+$gpArray22)/($semArray11+$semArray12+$semArray21+$semArray22)),2, '.', ',');


                                    echo $sem4CGPA	 ?></td>
                                <!-- 3rd year -->

                                <td> <?php $semArray31=  $sys->getCreditBySem(@$pupil->INDEXNO,1,'300H');echo @$sys->getCreditBySem(@$pupil->INDEXNO,1,'300H')	 ?></td>
                                <td> <?php $gpArray31=  $sys->getGPBySem(@$pupil->INDEXNO,1,'300H');echo @$sys->getGPBySem(@$pupil->INDEXNO,1,'300H')	 ?></td>
                                <td> <?php $gpaArray31=  $sys->getGPABySem(@$pupil->INDEXNO,1,'300H');echo @$sys->getGPABySem(@$pupil->INDEXNO,1,'300H')	 ?></td>
                                <td> <?php $sem5CGPA=    @number_format( (($gpArray11+$gpArray12+$gpArray21+$gpArray22+$gpArray31)/($semArray11+$semArray12+$semArray21+$semArray22+$semArray31)),2, '.', ',');


                                    echo $sem5CGPA	 ?></td>




                                <td> <?php $semArray32=  $sys->getCreditBySem(@$pupil->INDEXNO,2,'300H');echo @$sys->getCreditBySem(@$pupil->INDEXNO,2,'300H')	 ?></td>
                                <td> <?php $gpArray32=  $sys->getGPBySem(@$pupil->INDEXNO,2,'300H');echo @$sys->getGPBySem(@$pupil->INDEXNO,2,'300H')	 ?></td>
                                <td> <?php $gpaArray32=  $sys->getGPABySem(@$pupil->INDEXNO,2,'300H');echo @$sys->getGPABySem(@$pupil->INDEXNO,2,'300H')	 ?></td>

                                <td> <?php @$sem6CGPA=    @number_format( (($gpArray11+$gpArray12+$gpArray21+$gpArray22+$gpArray31+$gpArray32)/($semArray11+$semArray12+$semArray21+$semArray22+$semArray31+$semArray32)),2, '.', ',');


                                    echo @$sem6CGPA	 ?></td>

                                <!-- cummulative total -->


                                <td> <?php $totalCredit=  $semArray11+$semArray12+$semArray21
                                        +$semArray22+$semArray31+$semArray32;
                                    echo  @number_format( $totalCredit,2, '.', ',');
                                    ?>

                                </td>
                                <td> <?php $totalGP= $gpArray32+$gpArray31+$gpArray22
                                        +$gpArray21+$gpArray12+$gpArray11;
                                    echo  @number_format( $totalGP,2, '.', ',');
                                    ?>

                                </td>
                                <td> <?php $totalGPA=$gpaArray32+$gpaArray31+$gpaArray22
                                        +$gpaArray21+$gpaArray12+$gpaArray11;
                                    echo @number_format( $totalGP/$totalCredit,2, '.', ',');
                                    ?>

                                </td>
                                <td> <?php $sem6CGPA=    @number_format( (($gpArray11+$gpArray12+$gpArray21+$gpArray22+$gpArray31+$gpArray32)/($semArray11+$semArray12+$semArray21+$semArray22+$semArray31+$semArray32)),2);


                                    echo @number_format($sem6CGPA	, 2, '.', ',') ?></td>
                                <td>
                                    <?php

                                        if(@number_format($sem6CGPA	, 2, '.', ',')>4){
                                            echo "First Class";
                                        }
                                    elseif(@number_format($sem6CGPA	, 2, '.', ',')>3){
                                            echo "Second Upper";
                                        }
                                        elseif(@number_format($sem6CGPA	, 2, '.', ',')>2){
                                            echo "Second Lower";
                                        }
                                        elseif(@number_format($sem6CGPA	, 2, '.', ',')>1.5){
                                            echo "Pass";
                                        }
                                        else {echo 'Fail';}



                                    ?>
                                </td>
                                <td>
                                    <?php
                                    echo $sys->getTrails(@$pupil->INDEXNO);
                                    ?>
                                </td>

                            </tr>


                            @endforeach
                            <?php  $gpArray32=0;$gpArray31=0;$gpArray22=0;$gpArray21;$gpArray12;$gpArray11;$sem2CGPA=0?>

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