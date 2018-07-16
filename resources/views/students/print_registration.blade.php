@extends('layouts.printlayout')

@section('content')
@inject('help', 'App\Http\Controllers\SystemController')
<div align="" style="margin-left: 12px">
<style>
    td{
        font-size: 13px
    }
        </style>
    <div class="md-card">
        <div   class="uk-grid" data-uk-grid-margin>

                <body>
                <div id="print">

                    <table  border="0" cellspacing="0" align="center">
                        <tr></tr>
                        <tr>
                            <th height="341" valign="top" class="bod" scope="row"><table width="100%" border="0">
                            <tr>
                                <th align="center" valign="middle" scope="row"><table width="882" height="113" border="0">
                                <tr>
                                    <th align="center" valign="middle" scope="row"><table style="" width="882" height="113" border="0" align="left">
                                    <tr>
                                        <td><img src='{{url("Assets/img/printout.png")}}' style="width:581px;height:153px;margin-left: -5%;" /> </td

                                    </tr>
                                </table>
                                <table>
                                    <tr>
                                        <td align="center"><div class="" style="margin-left:-62%">Department: {{ strtoupper($help->getDepartmentName($help->getProgramDepartment($student->PROGRAMMECODE))) }}</div></td>
                                    </tr>
                                     <tr>
                                        <td align="center"><div class="" style="margin-left:-54.5%;text-align:">School: {{ strtoupper($help->getSchoolName($help->getSchoolCode($help->getProgramDepartment($student->PROGRAMMECODE)))) }}</div></td>
                                    </tr>
                                    <tr>
                                        <td  ><div style="margin-left:11%;width: 890px"><hr></hr></div></td>
                                    </tr>

                                </table>
                                </tr>


                            </table>
                            <div align="center">

                                <table border='0' align="center"  width='900px'>
                                    <tr>
                                        <td width="" style="width:69%">
                                            <div class="table-responsive" style="margin-left:15.5%">
                                                <table border='0' class="uk-table uk-table-nowrap uk-table-no-border" width=""  style="margin-left:-1%" >
                                                    <tbody><tr>
                                                            <td>NAME</td> <td style="padding-right: 36px;">{{$student->NAME}}</td>
                                                        </tr>

                                                        <tr>
                                                            <td style="padding-right: px;">INDEX NO</td> <td style="padding-right: 93px;">{{$student->INDEXNO}}</td>
                                                        </tr>

                                                        <tr>
                                                            <td>LEVEL</td> <td style="padding-right: 203px;">{{$student->LEVEL}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>YEAR</td> <td style="padding-right: 203px;">{{$year}}    SEMESTER: {{$sem}}</td>
                                                        </tr>

                                                        <tr>
                                                            <td>PROGRAMME</td> <td style="padding-right: 177px;"> {{$student->programme->PROGRAMME}}</td>
                                                        </tr>



                                                    </tbody></table> </div>
                                        </td>
                                        <td width="15">&nbsp;						  </td>
                                        <td width="237" align="left" valign="top"><table width="237" border="0" bordercolor="#D3E5FA" style="margin-left:-13%;">
                                                <tr>
                                                    <td width="202" ><div style="float:right;"><img style="width:130px;height:auto"  class=" " style=" margin-left: 26%" {!! $help->picture('{{url("albums/students/$student->INDEXNO")}}',210) !!} src='{{url("albums/students/$student->INDEXNO".'.jpg')}}' alt=" Picture of Student Here" /></div>
                                                        <p align="center">&nbsp;</p></td>
                                                </tr>
                                            </table></td>
                                    </tr>
                                    </tr>
                                </table> <!-- end basic infos -->


                                <table class="uk-table uk-table-nowrap uk-table-hover" id=""> 
                                    <thead>
                                        <tr>

                                            <th >NO</th>

                                            <th >COURSE</th>
                                            <th  style="text-align:">CODE</th>

                                            <th style="text-align:center">CREDIT</th>





                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach($course as $courseindex=> $rows) 


                                        <?php $total[] = $rows->credits ?>

                                        <tr align="">



                                            <td> {{ $course->perPage()*($course->currentPage()-1)+($courseindex+1) }} </td>
                                            <td> {{ strtoupper(@$rows->courseMount->course->COURSE_NAME) }}</td>
                                            <td> {{ strtoupper(@$rows->courseMount->course->COURSE_CODE)	 }}</td>

                                            <td class="uk-text-center"> {{ @$rows->credits }}</td>



                                        </tr>

                                        @endforeach


                                    </tbody>

                                </table>
                                 
                                <div style="margin-left:736px">
                                    <span class="uk-text-bold uk-text-success uk-text-large">Total {!! @array_sum($total)!!}</span>
                                </div>
                                <p>&nbsp;</p>
                                <table width="809" height="90" border="0">
                                    <tr>
                                        <td width="362"><p>.................................................................</p>
                                            <p align='center'>Student's Signature</p></td>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td width="362"><p>.................................................................</p>
                                            <p align='center'>Registration's Officer Signature</p></td>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td width="431"><p align="">.................................................................</p>
                                            <p align="center">Faculty Officer Signature</p></td>
                                    </tr>
                                </table>
                                 <div class="visible-print text-center" align='center'>
                                    {!! QrCode::size(100)->generate(Request::url()); !!}

                                </div>
                               
                            </div>
                              
</div>
                            </tr>
                        </table></th>
                        </tr>
                        <tr></tr>
                    </table>
                    
                    <div>
                    </div>
                </div>

        </div>


        @endsection

        @section('js')
        <script type="text/javascript">

         window.print();
 

        </script>

        @endsection