@extends('layouts.printlayout')
@section('style')
    <style>
        body{
            font-size:16px;
        }
        .gad{
            padding:2px;
        }
    </style>
    <style>
        html, body, #page3,  #page4, #page5 { float: none; }

        @media print
        {
            table {float: none !important; }
            div { float: none !important; }
            #page1  { page-break-inside: avoid; page-break-before: always; }
            #page2  { page-break-inside: avoid; page-break-before: always; }
            #page3  { page-break-inside: avoid; page-break-before: always; }
        }

        @page {
            size: A4;
        }

        table, figure {
            page-break-inside: avoid;
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


    </style>

@endsection
@section('content')
    @inject('help', 'App\Http\Controllers\SystemController')

        <?php
        $comprehensive_outline ='comprehensive_outline';
        $outline_based_on_sylla= 'outline_based_on_sylla';   //to be removed
        $outline_recommended_books='outline_recommended_books';
        $lecturer_person_details ='lecturer_person_details';
        $course_objective_spelt='course_objective_spelt';
        $course_material_list ='course_material_list';

        $class_start_week='class_start_week';
        $class_met_regularly='class_met_regularly';
        $lecturer_punctual='lecturer_punctual';
        $lecturer_missed_reason='lecturer_missed_reason';

        $lecturer_stays_period = 'lecturer_stays_period';    //recently added

        $demonstrate_knowledge='demonstrate_knowledge';
        $well_organised_delivery='well_organised_delivery';
        $communicate_effectively='communicate_effectively';
        $class_time_prom_learn='class_time_prom_learn';
        $varying_teaching_meth = 'varying_teaching_meth';
        $encourage_stud_participation='encourage_stud_participation';
        $encourage_problem_solving='encourage_problem_solving';
        $respond_to_stud_concerns='respond_to_stud_concerns';
        $other_media_delivery='other_media_delivery';
        $room_for_question='room_for_question';
        $adequate_assignment='adequate_assignment';
        $state_feedback_time='state_feedback_time';
        $mark_assignment='mark_assignment';
        $discuss_in_class='discuss_in_class';
        $stud_progress_concern='stud_progress_concern';
        $stud_responsibility='stud_responsibility';
        $deadline_assignment='deadline_assignment';
        $disclose_marks ='disclose_marks';
        $late_submission_policy='late_submission_policy';
        $variety_assignment_used = 'variety_assignment_used';
        $course_objective_achieved ='course_objective_achieved';
        $expectations_communicated ='expectations_communicated';
        $sold_handout='sold_handout';
        $created_friendly_atmosphere='created_friendly_atmosphere';
        $programmecode='programmecode';

        ?>
@if($data!="")

            {{--<a  style="float:left"onclick="javascript:printDiv('print')" class="md-btn   md-btn-success">Click to print form</a>--}}

            <div id="print">

                <div id="page1">
                <table>
                    <tr>
                        <td><img style="width:1000px;height: auto" src='{{url("public/assets/img/qualityassurance.jpg")}}'
                                 style="" class="image-responsive"/>
                        </td>
                    </tr>
                </table>
                    <p></p>

                    <?php
                        $arr=$help->getCourseName($course);
                    ?>
                    <div style='text-align:justify;  '>NAME OF LECTURER: &nbsp; &nbsp; <b>  <?php echo $help->getLectureName($lecturer);?></b></div>
                    <div style="text-align:justify;  ">ACADEMIC YEAR: &nbsp; &nbsp; <b><?php echo @$year;?> </b></div>
                    <div style='text-align:justify;  '>NUMBER OF STUDENTS:&nbsp; &nbsp; <b> <?php echo @count(@$data); ?></b> </div>
                    <div style='text-align:justify;  '>COURSE:&nbsp; &nbsp; <b>  <?php echo @$arr[0]->COURSE_NAME;?></b></div>
                    <div style='text-align:justify;  '>DEPARTMENT:&nbsp; &nbsp; <b>  <?php echo  @$help->getDepartmentProgramme(@$arr[0]->PROGRAMME);?></b></div>

                    <div>
                        <p><center><b><h5><center>ASSESSMENT REPORT</center></h5></b></center></p>


                        <p style='text-align:justify'>






                                <table  class="uk-table gad"  border="1">
                                    <tr>
                                        <th width="20" style="text-align: left">COURSE CONTENT</th>
                                        <th width="100">1</th>
                                        <th width="150">2</th>
                                        <th width="78">3</th>
                                        <th width="71">4</th>
                                        <th width="71">5</th>
                                        <th width="71">6</th>
                                        <th width="71">7</th>
                                        <th width="71">SCORE</th>
                                        <th width="71">%SCORE</th>
                                        <th width="71">REMARKS</th>
                                    </tr>
                                    <tbody>
                                    <tr>
                                        <td style="width: 280px">1)The lecturer provided a comprehensive outline of the course  at the beginning of the semester</td>
                                        <td><?php echo $comprehensive_outline_1= @@$help->selectcount($comprehensive_outline, '1', $lecturer,  @$arr[0]->COURSE_CODE, @$sem, @$year);  ?></td>
                                        <td><?php echo $comprehensive_outline_2= @@$help->selectcount($comprehensive_outline, '2', $lecturer,  $arr[0]->COURSE_CODE, $sem, $year);  ?></td>
                                        <td><?php echo $comprehensive_outline_3= @@$help->selectcount($comprehensive_outline, '3', $lecturer,  $arr[0]->COURSE_CODE, $sem, $year);  ?></td>
                                        <td><?php echo $comprehensive_outline_4= @@$help->selectcount($comprehensive_outline, '4', $lecturer,  $arr[0]->COURSE_CODE, $sem, $year);  ?></td>
                                        <td><?php echo $comprehensive_outline_5= @@$help->selectcount($comprehensive_outline, '5', $lecturer,  $arr[0]->COURSE_CODE, $sem, $year);  ?></td>
                                        <td><?php echo $comprehensive_outline_6= @@$help->selectcount($comprehensive_outline, '6', $lecturer,  $arr[0]->COURSE_CODE, $sem, $year);  ?></td>
                                        <td><?php echo $comprehensive_outline_7= @@$help->selectcount($comprehensive_outline, '7', $lecturer,  $arr[0]->COURSE_CODE, $sem, $year);  ?></td>

                                        <td><?php  //echo    number_format($comprehensive_outline_yp /100,4);
                                            @$comprehensive_outline_total =@$comprehensive_outline_1 + @$comprehensive_outline_2  +  @$comprehensive_outline_3 + @$comprehensive_outline_4 + @$comprehensive_outline_5 + @$comprehensive_outline_6 + @$comprehensive_outline_7;

                                            @$comprehensive_outline_score =((@$comprehensive_outline_1*1)+(@$comprehensive_outline_2*2) + (@$comprehensive_outline_3*3) + (@$comprehensive_outline_4 * 4) + (@$comprehensive_outline_5 *5) + (@$comprehensive_outline_6 * 6) + (@$comprehensive_outline_7 * 7) )/(@$comprehensive_outline_total *7);
                                            echo  number_format(@$comprehensive_outline_score ,4);  ?></td>
                                        <td><?php
                                            echo  @$comprehensive_outline_percentage = @number_format($comprehensive_outline_score ,4) * 100;  ?></td>
                                        <td><?php   echo $comprehensive_outline_remark= @$help->remark($comprehensive_outline_percentage); $no_poor_1 = @$help->count_poor($comprehensive_outline_remark,1);  ?></td>

                                    </tr>
                                    <tr>
                                        <td style="width: 280px">2)A list of recommended textbooks was provided in the course outline</td>
                                        <td><?php echo $outline_recommended_books_1 = @@$help->selectcount($outline_recommended_books, '1', $lecturer,  $arr[0]->COURSE_CODE, $sem, $year);  ?></td>
                                        <td><?php echo $outline_recommended_books_2 = @@$help->selectcount($outline_recommended_books, '2', $lecturer,  $arr[0]->COURSE_CODE, $sem, $year);  ?></td>
                                        <td><?php echo $outline_recommended_books_3 = @$help->selectcount($outline_recommended_books, '3', $lecturer,  $arr[0]->COURSE_CODE, $sem, $year);  ?></td>
                                        <td><?php echo $outline_recommended_books_4 = @$help->selectcount($outline_recommended_books, '4', $lecturer,  $arr[0]->COURSE_CODE, $sem, $year);  ?></td>
                                        <td><?php echo $outline_recommended_books_5 = @$help->selectcount($outline_recommended_books, '5', $lecturer,  $arr[0]->COURSE_CODE, $sem, $year);  ?></td>
                                        <td><?php echo $outline_recommended_books_6 = @$help->selectcount($outline_recommended_books, '6', $lecturer,  $arr[0]->COURSE_CODE, $sem, $year);  ?></td>
                                        <td><?php echo $outline_recommended_books_7 = @$help->selectcount($outline_recommended_books, '7', $lecturer,  $arr[0]->COURSE_CODE, $sem, $year);  ?></td>
                                        <td><?php @$outline_recommended_books_total =@$outline_recommended_books_1 + @$outline_recommended_books_2  +  @$outline_recommended_books_3 + @$outline_recommended_books_4 + @$outline_recommended_books_5 + @$outline_recommended_books_6 + @$outline_recommended_books_7;
                                            @$outline_recommended_books_score  =@(@($outline_recommended_books_1*1)+(@$outline_recommended_books_2*2) + (@$outline_recommended_books_3*3) + (@$outline_recommended_books_4 * 4) + (@$outline_recommended_books_5 *5) + (@$outline_recommended_books_6 * 6) + (@$outline_recommended_books_7 * 7) )/(@$outline_recommended_books_total *7);
                                            echo @ number_format(@$outline_recommended_books_score ,4);
                                            ?></td>
                                        <td><?php
                                            echo  @$outline_recommended_books_percentage = @number_format(@$outline_recommended_books_score ,4) * 100;  ?></td>
                                        <td><?php  echo @$outline_recommended_books_remark=  @$help->remark(@$outline_recommended_books_percentage); $no_poor_2 = $help->count_poor(@$outline_recommended_books_remark,2);?></td>


                                    </tr>

                                    <tr>
                                        <td style="width: 280px">3)Lecturer provided his/her email, phone number, office hours and professional background</td>
                                        <td><?php echo $lecturer_person_details_1 = @$help->selectcount($lecturer_person_details, '1', $lecturer, $arr[0]->COURSE_CODE, $sem, $year);  ?></td>
                                        <td><?php echo $lecturer_person_details_2 = @$help->selectcount($lecturer_person_details, '2', $lecturer, $arr[0]->COURSE_CODE, $sem, $year);  ?></td>
                                        <td><?php echo $lecturer_person_details_3 = @$help->selectcount($lecturer_person_details, '3', $lecturer, $arr[0]->COURSE_CODE, $sem, $year);  ?></td>
                                        <td><?php echo $lecturer_person_details_4 = @$help->selectcount($lecturer_person_details, '4', $lecturer, $arr[0]->COURSE_CODE, $sem, $year);  ?></td>
                                        <td><?php echo $lecturer_person_details_5 = @$help->selectcount($lecturer_person_details, '5', $lecturer, $arr[0]->COURSE_CODE, $sem, $year);  ?></td>
                                        <td><?php echo $lecturer_person_details_6 = @$help->selectcount($lecturer_person_details, '6', $lecturer, $arr[0]->COURSE_CODE, $sem, $year);  ?></td>
                                        <td><?php echo $lecturer_person_details_7 = @$help->selectcount($lecturer_person_details, '7', $lecturer, $arr[0]->COURSE_CODE, $sem, $year);  ?></td>
                                        <td><?php @$lecturer_person_details_total =@$lecturer_person_details_1 + $lecturer_person_details_2  +  $lecturer_person_details_3 + $lecturer_person_details_4 + $lecturer_person_details_5 + $lecturer_person_details_6 + $lecturer_person_details_7;
                                            $lecturer_person_details_score  =@(@($lecturer_person_details_1*1)+($lecturer_person_details_2*2) + ($lecturer_person_details_3*3) + ($lecturer_person_details_4 * 4) + ($lecturer_person_details_5 *5) + ($lecturer_person_details_6 * 6) + ($lecturer_person_details_7 * 7) )/($lecturer_person_details_total *7);
                                            echo  @number_format(@$lecturer_person_details_score ,4);
                                            ?></td>
                                        <td><?php
                                            echo  @$lecturer_person_details_percentage = @number_format($lecturer_person_details_score ,4) * 100;  ?></td>
                                        <td><?php   echo $lecturer_person_details_remark= @$help->remark($lecturer_person_details_percentage); $no_poor_3 = $help->count_poor($lecturer_person_details_remark,3);  ?></td>


                                    </tr>
                                    <tr>
                                        <td style="width: 280px"> 4)The course objectives and learning outcomes are clearly spelt out in the course outline</td>
                                        <td><?php echo $course_objective_spelt_1 = @$help->selectcount($course_objective_spelt, '1', $lecturer, $arr[0]->COURSE_CODE,$sem, $year);  ?></td>
                                        <td><?php echo $course_objective_spelt_2 = @$help->selectcount($course_objective_spelt, '2', $lecturer, $arr[0]->COURSE_CODE,$sem, $year);  ?></td>
                                        <td><?php echo $course_objective_spelt_3 = @$help->selectcount($course_objective_spelt, '3', $lecturer, $arr[0]->COURSE_CODE,$sem, $year);  ?></td>
                                        <td><?php echo $course_objective_spelt_4 = @$help->selectcount($course_objective_spelt, '4', $lecturer, $arr[0]->COURSE_CODE,$sem, $year);  ?></td>
                                        <td><?php echo $course_objective_spelt_5 = @$help->selectcount($course_objective_spelt, '5', $lecturer, $arr[0]->COURSE_CODE,$sem, $year);  ?></td>
                                        <td><?php echo $course_objective_spelt_6 = @$help->selectcount($course_objective_spelt, '6', $lecturer, $arr[0]->COURSE_CODE,$sem, $year);  ?></td>
                                        <td><?php echo $course_objective_spelt_7 = @$help->selectcount($course_objective_spelt, '7', $lecturer, $arr[0]->COURSE_CODE,$sem, $year);  ?></td>
                                        <td><?php @$course_objective_spelt_total =@$course_objective_spelt_1 + $course_objective_spelt_2  +  $course_objective_spelt_3 + $course_objective_spelt_4 + $course_objective_spelt_5 + $course_objective_spelt_6 + $course_objective_spelt_7;
                                            $course_objective_spelt_score  =@(@($course_objective_spelt_1*1)+($course_objective_spelt_2*2) + ($course_objective_spelt_3*3) + ($course_objective_spelt_4 * 4) + ($course_objective_spelt_5 *5) + ($course_objective_spelt_6 * 6) + ($course_objective_spelt_7 * 7) )/($course_objective_spelt_total *7);
                                            echo  @number_format($course_objective_spelt_score ,4);
                                            ?></td>
                                        <td><?php
                                            echo  $course_objective_spelt_percentage = @number_format($course_objective_spelt_score ,4) * 100;  ?></td>
                                        <td><?php   echo @$course_objective_spelt_remark= @$help->remark($course_objective_spelt_percentage); $no_poor_4 = $help->count_poor($course_objective_spelt_remark,4); ?></td>


                                    </tr>
                                    <tr>
                                        <td style="width: 280px"> 5)Lecturer provided a list of course materials needed for the course</td>
                                        <td><?php echo $course_material_list_1 = @$help->selectcount($course_material_list , '1', $lecturer, $arr[0]->COURSE_CODE,$sem, $year);  ?></td>
                                        <td><?php echo $course_material_list_2 = @$help->selectcount($course_material_list , '2', $lecturer, $arr[0]->COURSE_CODE,$sem, $year);  ?></td>
                                        <td><?php echo $course_material_list_3 = @$help->selectcount($course_material_list , '3', $lecturer, $arr[0]->COURSE_CODE,$sem, $year);  ?></td>
                                        <td><?php echo $course_material_list_4 = @$help->selectcount($course_material_list , '4', $lecturer, $arr[0]->COURSE_CODE,$sem, $year);  ?></td>
                                        <td><?php echo $course_material_list_5 = @$help->selectcount($course_material_list , '5', $lecturer, $arr[0]->COURSE_CODE,$sem, $year);  ?></td>
                                        <td><?php echo $course_material_list_6 = @$help->selectcount($course_material_list , '6', $lecturer, $arr[0]->COURSE_CODE,$sem, $year);  ?></td>
                                        <td><?php echo $course_material_list_7 = @$help->selectcount($course_material_list , '7', $lecturer, $arr[0]->COURSE_CODE,$sem, $year);  ?></td>
                                        <td><?php $course_material_list_total =$course_material_list_1 + $course_material_list_2  +  $course_material_list_3 + $course_material_list_4 + $course_material_list_5 + $course_material_list_6 + $course_material_list_7;
                                            $course_material_list_score  =@(@($course_material_list_1*1)+($course_material_list_2*2) + ($course_material_list_3*3) + ($course_material_list_4 * 4) + ($course_material_list_5 *5) + ($course_material_list_6 * 6) + ($course_material_list_7 * 7) )/($course_material_list_total *7);
                                            echo  @number_format(@$course_material_list_score ,4);
                                            ?></td>
                                        <td><?php
                                            echo  @$course_material_list_percentage = @number_format($course_material_list_score ,4) * 100;  ?></td>
                                        <td><?php  echo $course_material_list_remark=  @$help->remark($course_material_list_percentage); $no_poor_5 = $help->count_poor($course_material_list_remark, 5); ?></td>


                                    </tr>
                                    <tr>
                                        <td style="width: 280px"> SUB TOTAL</td>
                                        <td><?php //$course_content_subtotal_n = ($comprehensive_outline_n + $outline_based_on_sylla_n + $outline_recommended_books_n + $lecturer_person_details_n + $course_objective_spelt_n + $course_material_list_n);  echo number_format($course_content_subtotal_n,0);  ?></td>
                                        <td><?php //$course_content_subtotal_n = ($comprehensive_outline_n + $outline_based_on_sylla_n + $outline_recommended_books_n + $lecturer_person_details_n + $course_objective_spelt_n + $course_material_list_n);  echo number_format($course_content_subtotal_n,0);  ?></td>
                                        <td><?php //$course_content_subtotal_n = ($comprehensive_outline_n + $outline_based_on_sylla_n + $outline_recommended_books_n + $lecturer_person_details_n + $course_objective_spelt_n + $course_material_list_n);  echo number_format($course_content_subtotal_n,0);  ?></td>
                                        <td><?php //$course_content_subtotal_n = ($comprehensive_outline_n + $outline_based_on_sylla_n + $outline_recommended_books_n + $lecturer_person_details_n + $course_objective_spelt_n + $course_material_list_n);  echo number_format($course_content_subtotal_n,0);  ?></td>
                                        <td><?php //$course_content_subtotal_n = ($comprehensive_outline_n + $outline_based_on_sylla_n + $outline_recommended_books_n + $lecturer_person_details_n + $course_objective_spelt_n + $course_material_list_n);  echo number_format($course_content_subtotal_n,0);  ?></td>
                                        <td><?php //$course_content_subtotal_n = ($comprehensive_outline_n + $outline_based_on_sylla_n + $outline_recommended_books_n + $lecturer_person_details_n + $course_objective_spelt_n + $course_material_list_n);  echo number_format($course_content_subtotal_n,0);  ?></td>
                                        <td><?php //$course_content_subtotal_n = ($comprehensive_outline_n + $outline_based_on_sylla_n + $outline_recommended_books_n + $lecturer_person_details_n + $course_objective_spelt_n + $course_material_list_n);  echo number_format($course_content_subtotal_n,0);  ?></td>
                                        <td><?php @$course_content_subtotal = (@$comprehensive_outline_score + $outline_recommended_books_score + $lecturer_person_details_score + $course_objective_spelt_score + $course_material_list_score);  echo number_format($course_content_subtotal,4); ?></td>
                                        <td><?php
                                            //echo  $comprehensive_outline_percentage = number_format($comprehensive_outline_score ,4) * 100;  ?></td>
                                        <td>&nbsp;</td>


                                    </tr>
                                    </tbody>
                                </table>
                                <p></p>
                                <table  class="uk-table gad"  border="1">
                                    <tr>
                                        <th width="20" style="text-align: left">ATTENDANCE</th>
                                        <th width="100">1</th>
                                        <th width="150">2</th>
                                        <th width="78">3</th>
                                        <th width="71">4</th>
                                        <th width="71">5</th>
                                        <th width="71">6</th>
                                        <th width="71">7</th>
                                        <th width="71">SCORE</th>
                                        <th width="71">%SCORE</th>
                                        <th width="71">REMARKS</th>
                                    </tr>
                                    <tbody>
                                    <tr>
                                        <?php
                                        $coursecode=$arr[0]->COURSE_CODE;
                                        $semester=$sem;
                                        $academic_year=$year;


                                        ?>
                                        <td style="width: 280px"> 6) Lecturer started classes in the week it was supposed to begin</td>
                                        <td><?php echo $class_start_week_1 = @$help->selectcount($class_start_week, '1', $lecturer, $arr[0]->COURSE_CODE,$sem, $year);  ?></td>
                                        <td><?php echo $class_start_week_2 = @$help->selectcount($class_start_week, '2', $lecturer, $arr[0]->COURSE_CODE,$sem, $year);  ?></td>
                                        <td><?php echo $class_start_week_3 = @$help->selectcount($class_start_week, '3',$lecturer, $arr[0]->COURSE_CODE,$sem, $year);  ?></td>
                                        <td><?php echo $class_start_week_4 = @$help->selectcount($class_start_week, '4', $lecturer, $arr[0]->COURSE_CODE,$sem, $year);  ?></td>
                                        <td><?php echo $class_start_week_5 = @$help->selectcount($class_start_week, '5', $lecturer, $arr[0]->COURSE_CODE,$sem, $year);  ?></td>
                                        <td><?php echo $class_start_week_6 = @$help->selectcount($class_start_week, '6', $lecturer, $arr[0]->COURSE_CODE,$sem, $year);  ?></td>
                                        <td><?php echo $class_start_week_7 = @$help->selectcount($class_start_week, '7', $lecturer, $arr[0]->COURSE_CODE,$sem, $year);  ?></td>
                                        <td><?php $class_start_week_total =$class_start_week_1 + $class_start_week_2  +  $class_start_week_3 + $class_start_week_4 + $class_start_week_5 + $class_start_week_6 + $class_start_week_7;
                                            $class_start_week_score  =(($class_start_week_1*1)+($class_start_week_2*2) + ($class_start_week_3*3) + ($class_start_week_4 * 4) + ($class_start_week_5 *5) + ($class_start_week_6 * 6) + ($class_start_week_7 * 7) )/($class_start_week_total *7);
                                            echo  number_format($class_start_week_score ,4);
                                            ?></td>
                                        <td><?php
                                            echo  $class_start_week_percentage = number_format($class_start_week_score ,4) * 100;  ?></td>
                                        <td><?php  echo  $class_start_week_remark = $help->remark($class_start_week_percentage);  $no_poor_6 = $help->count_poor($class_start_week_remark, 6); ?></td>


                                    </tr>
                                    <tr>
                                        <td style="width: 280px">7) Lecturer met class regularly</td>
                                        <td><?php echo $class_met_regularly_1 = @$help->selectcount($class_met_regularly , '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $class_met_regularly_2 = @$help->selectcount($class_met_regularly , '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $class_met_regularly_3 = @$help->selectcount($class_met_regularly , '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $class_met_regularly_4 = @$help->selectcount($class_met_regularly , '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $class_met_regularly_5 = @$help->selectcount($class_met_regularly , '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $class_met_regularly_6 = @$help->selectcount($class_met_regularly , '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $class_met_regularly_7 = @$help->selectcount($class_met_regularly , '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php $class_met_regularly_total =$class_met_regularly_1 + $class_met_regularly_2  +  $class_met_regularly_3 + $class_met_regularly_4 + $class_met_regularly_5 + $class_met_regularly_6 + $class_met_regularly_7;
                                            $class_met_regularly_score  =(($class_met_regularly_1*1)+($class_met_regularly_2*2) + ($class_met_regularly_3*3) + ($class_met_regularly_4 * 4) + ($class_met_regularly_5 *5) + ($class_met_regularly_6 * 6) + ($class_met_regularly_7 * 7) )/($class_met_regularly_total *7);
                                            echo  number_format($class_met_regularly_score ,4);
                                            ?></td>
                                        <td><?php
                                            echo $class_met_regularly_percentage = number_format($class_met_regularly_score ,4) * 100;  ?></td>
                                        <td><?php  echo $class_met_regularly_remark = $help->remark($class_met_regularly_percentage);   $no_poor_7 = $help->count_poor($class_met_regularly_remark, 7); ?></td>


                                    </tr>

                                    <tr>
                                        <td style="width: 280px">8)	Lecturer was punctual to class</td>
                                        <td><?php echo $lecturer_punctual_1 = @$help->selectcount($lecturer_punctual , '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $lecturer_punctual_2 = @$help->selectcount($lecturer_punctual , '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $lecturer_punctual_3 = @$help->selectcount($lecturer_punctual , '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $lecturer_punctual_4 = @$help->selectcount($lecturer_punctual , '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $lecturer_punctual_5 = @$help->selectcount($lecturer_punctual , '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $lecturer_punctual_6 = @$help->selectcount($lecturer_punctual , '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $lecturer_punctual_7 = @$help->selectcount($lecturer_punctual , '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php $lecturer_punctual_total =$lecturer_punctual_1 + $lecturer_punctual_2  +  $lecturer_punctual_3 + $lecturer_punctual_4 + $lecturer_punctual_5 + $lecturer_punctual_6 + $lecturer_punctual_7;
                                            $lecturer_punctual_score  =(($lecturer_punctual_1*1)+($lecturer_punctual_2*2) + ($lecturer_punctual_3*3) + ($lecturer_punctual_4 * 4) + ($lecturer_punctual_5 *5) + ($lecturer_punctual_6 * 6) + ($lecturer_punctual_7 * 7) )/($lecturer_punctual_total *7);
                                            echo  number_format($lecturer_punctual_score ,4);
                                            ?></td>
                                        <td><?php
                                            echo  $lecturer_punctual_percentage = number_format($lecturer_punctual_score ,4) * 100;  ?></td>
                                        <td><?php  echo $lecturer_punctual_remark= $help->remark($lecturer_punctual_percentage); $no_poor_8 = $help->count_poor($lecturer_punctual_remark, 8); ?></td>


                                    </tr>
                                    <tr>
                                        <td style="width: 280px">  9) When lecturer misses class for a good reason, he/she reschedules it</td>
                                        <td><?php echo $lecturer_missed_reason_1 = @$help->selectcount($lecturer_missed_reason, '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $lecturer_missed_reason_2 = @$help->selectcount($lecturer_missed_reason, '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $lecturer_missed_reason_3 = @$help->selectcount($lecturer_missed_reason, '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $lecturer_missed_reason_4 = @$help->selectcount($lecturer_missed_reason, '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $lecturer_missed_reason_5 = @$help->selectcount($lecturer_missed_reason, '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $lecturer_missed_reason_6 = @$help->selectcount($lecturer_missed_reason, '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $lecturer_missed_reason_7 = @$help->selectcount($lecturer_missed_reason, '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php $lecturer_missed_reason_total =$lecturer_missed_reason_1 + $lecturer_missed_reason_2  +  $lecturer_missed_reason_3 + $lecturer_missed_reason_4 + $lecturer_missed_reason_5 + $lecturer_missed_reason_6 + $lecturer_missed_reason_7;
                                            $lecturer_missed_reason_score  =(($lecturer_missed_reason_1*1)+($lecturer_missed_reason_2*2) + ($lecturer_missed_reason_3*3) + ($lecturer_missed_reason_4 * 4) + ($lecturer_missed_reason_5 *5) + ($lecturer_missed_reason_6 * 6) + ($lecturer_missed_reason_7 * 7) )/($lecturer_missed_reason_total *7);
                                            echo  number_format($lecturer_missed_reason_score ,4);
                                            ?></td>
                                        <td><?php
                                            echo  $lecturer_missed_reason_percentage = number_format($lecturer_missed_reason_score ,4) * 100;  ?></td>
                                        <td><?php  echo  $lecturer_missed_reason_remark= $help->remark($lecturer_missed_reason_percentage); $no_poor_9 = $help->count_poor($lecturer_missed_reason_remark, 9); ?></td>


                                    </tr>
                                    <tr>
                                        <td style="width: 280px">10) The lecturer usually stays throughout the entire period.</td>
                                        <td><?php echo $lecturer_stays_period_1 = @$help->selectcount($lecturer_stays_period, '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $lecturer_stays_period_2 = @$help->selectcount($lecturer_stays_period, '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $lecturer_stays_period_3 = @$help->selectcount($lecturer_stays_period, '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $lecturer_stays_period_4 = @$help->selectcount($lecturer_stays_period, '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $lecturer_stays_period_5 = @$help->selectcount($lecturer_stays_period, '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $lecturer_stays_period_6 = @$help->selectcount($lecturer_stays_period, '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php echo $lecturer_stays_period_7 = @$help->selectcount($lecturer_stays_period, '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                        <td><?php  $lecturer_stays_period_total =$lecturer_stays_period_1 + $lecturer_stays_period_2  +  $lecturer_stays_period_3 + $lecturer_stays_period_4 + $lecturer_stays_period_5 + $lecturer_stays_period_6 + $lecturer_stays_period_7;
                                            $lecturer_stays_period_score  =(($lecturer_stays_period_1*1)+($lecturer_stays_period_2*2) + ($lecturer_stays_period_3*3) + ($lecturer_stays_period_4 * 4) + ($lecturer_stays_period_5 *5) + ($lecturer_stays_period_6 * 6) + ($lecturer_stays_period_7 * 7) )/($lecturer_stays_period_total *7);
                                            echo  number_format($lecturer_stays_period_score ,4);
                                            ?></td>
                                        <td><?php
                                            echo  $lecturer_stays_period_percentage = number_format($lecturer_stays_period_score ,4) * 100;  ?></td>
                                        <td><?php  echo  $lecturer_stays_period_remark= $help->remark($lecturer_stays_period_percentage); $no_poor_10 = $help->count_poor($lecturer_stays_period_remark, 10); ?></td>


                                    </tr>
                                    <tr>
                                        <td style="width: 280px"> SUB TOTAL</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td><?php  $attendance_subtotal = ($class_start_week_score + $class_met_regularly_score + $lecturer_punctual_score + $lecturer_missed_reason_score + $lecturer_stays_period_score);
                                            echo number_format($attendance_subtotal,4);   ?></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>

                                    </tr>
                                    </tbody>
                                </table>

                        <p></p>

                </div>

                        <div id="page2">
                        <table  class="uk-table gad"  border="1">
                            <tr>
                                <th width="20" style="text-align: left">MODE OF DELIVERY</th>
                                <th width="100">1</th>
                                <th width="150">2</th>
                                <th width="78">3</th>
                                <th width="71">4</th>
                                <th width="71">5</th>
                                <th width="71">6</th>
                                <th width="71">7</th>
                                <th width="71">SCORE</th>
                                <th width="71">%SCORE</th>
                                <th width="71">REMARKS</th>
                            </tr>
                            <tbody>
                            <tr>
                                <td style="width: 280px">11) The Lecturer demonstrated knowledge of the subject matter</td>
                                <td><?php echo $demonstrate_knowledge_1 = @$help->selectcount($demonstrate_knowledge, '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $demonstrate_knowledge_2 = @$help->selectcount($demonstrate_knowledge, '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $demonstrate_knowledge_3 = @$help->selectcount($demonstrate_knowledge, '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $demonstrate_knowledge_4 = @$help->selectcount($demonstrate_knowledge, '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $demonstrate_knowledge_5 = @$help->selectcount($demonstrate_knowledge, '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $demonstrate_knowledge_6 = @$help->selectcount($demonstrate_knowledge, '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $demonstrate_knowledge_7 = @$help->selectcount($demonstrate_knowledge, '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php $demonstrate_knowledge_total =$demonstrate_knowledge_1 + $demonstrate_knowledge_2  +  $demonstrate_knowledge_3 + $demonstrate_knowledge_4 + $demonstrate_knowledge_5 + $demonstrate_knowledge_6 + $demonstrate_knowledge_7;
                                    $demonstrate_knowledge_score  =(($demonstrate_knowledge_1*1)+($demonstrate_knowledge_2*2) + ($demonstrate_knowledge_3*3) + ($demonstrate_knowledge_4 * 4) + ($demonstrate_knowledge_5 *5) + ($demonstrate_knowledge_6 * 6) + ($demonstrate_knowledge_7 * 7) )/($demonstrate_knowledge_total *7);
                                    echo  number_format($demonstrate_knowledge_score ,4);
                                    ?></td>
                                <td><?php
                                    echo  $demonstrate_knowledge_percentage = number_format($demonstrate_knowledge_score ,4) * 100;  ?></td>
                                <td><?php  echo  $demonstrate_knowledge_remark = $help->remark($demonstrate_knowledge_percentage); $no_poor_11 = $help->count_poor($demonstrate_knowledge_remark, 11);?></td>


                            </tr>
                            <tr>
                                <td style="width: 280px">12) The Lecturer&rsquo;s delivery was well organized and systematic</td>
                                <td><?php echo $well_organised_delivery_1 = @$help->selectcount( $well_organised_delivery  , '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $well_organised_delivery_2 = @$help->selectcount( $well_organised_delivery  , '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $well_organised_delivery_3 = @$help->selectcount( $well_organised_delivery  , '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $well_organised_delivery_4 = @$help->selectcount( $well_organised_delivery  , '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $well_organised_delivery_5 = @$help->selectcount( $well_organised_delivery  , '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $well_organised_delivery_6 = @$help->selectcount( $well_organised_delivery  , '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $well_organised_delivery_7 = @$help->selectcount( $well_organised_delivery  , '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php $well_organised_delivery_total =$well_organised_delivery_1 + $well_organised_delivery_2  +  $well_organised_delivery_3 + $well_organised_delivery_4 + $well_organised_delivery_5 + $well_organised_delivery_6 + $well_organised_delivery_7;
                                    $well_organised_delivery_score  =(($well_organised_delivery_1*1)+($well_organised_delivery_2*2) + ($well_organised_delivery_3*3) + ($well_organised_delivery_4 * 4) + ($well_organised_delivery_5 *5) + ($well_organised_delivery_6 * 6) + ($well_organised_delivery_7 * 7) )/($well_organised_delivery_total *7);
                                    echo  number_format($well_organised_delivery_score ,4);
                                    ?></td>
                                <td><?php
                                    echo  $well_organised_delivery_percentage = number_format($well_organised_delivery_score ,4) * 100;  ?></td>
                                <td><?php   echo $well_organised_delivery_remark = $help->remark($well_organised_delivery_percentage);  $no_poor_12 = $help->count_poor($well_organised_delivery_remark, 12); ?></td>

                            </tr>

                            <tr>
                                <td style="width: 280px">13) The Lecturer effectively communicated  what he/she was teaching</td>
                                <td><?php echo $communicate_effectively_1 =  @$help->selectcount( $communicate_effectively , '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $communicate_effectively_2 =  @$help->selectcount( $communicate_effectively , '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $communicate_effectively_3 =  @$help->selectcount( $communicate_effectively , '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $communicate_effectively_4 =  @$help->selectcount( $communicate_effectively , '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $communicate_effectively_5 =  @$help->selectcount( $communicate_effectively , '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $communicate_effectively_6 =  @$help->selectcount( $communicate_effectively , '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $communicate_effectively_7 =  @$help->selectcount( $communicate_effectively , '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php $communicate_effectively_total =$communicate_effectively_1 + $communicate_effectively_2  +  $communicate_effectively_3 + $communicate_effectively_4 + $communicate_effectively_5 + $communicate_effectively_6 + $communicate_effectively_7;
                                    $communicate_effectively_score  =(($communicate_effectively_1*1)+($communicate_effectively_2*2) + ($communicate_effectively_3*3) + ($communicate_effectively_4 * 4) + ($communicate_effectively_5 *5) + ($communicate_effectively_6 * 6) + ($communicate_effectively_7 * 7) )/($communicate_effectively_total *7);
                                    echo  number_format($communicate_effectively_score ,4);
                                    ?></td>
                                <td><?php
                                    echo $communicate_effectively_percentage = number_format($communicate_effectively_score ,4) * 100;  ?></td>
                                <td><?php  echo $communicate_effectively_remark=   $help->remark($communicate_effectively_percentage); $no_poor_13 =  $help->count_poor($communicate_effectively_remark, 13); ?></td>


                            </tr>
                            <tr>
                                <td style="width: 280px">14) The Lecturer used class time to fully promote learning.</td>
                                <td><?php echo $class_time_prom_learn_1 = @$help->selectcount($class_time_prom_learn , '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $class_time_prom_learn_2 = @$help->selectcount($class_time_prom_learn , '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $class_time_prom_learn_3 = @$help->selectcount($class_time_prom_learn , '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $class_time_prom_learn_4 = @$help->selectcount($class_time_prom_learn , '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $class_time_prom_learn_5 = @$help->selectcount($class_time_prom_learn , '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $class_time_prom_learn_6 = @$help->selectcount($class_time_prom_learn , '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $class_time_prom_learn_7 = @$help->selectcount($class_time_prom_learn , '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php $class_time_prom_learn_total =$class_time_prom_learn_1 + $class_time_prom_learn_2  +  $class_time_prom_learn_3 + $class_time_prom_learn_4 + $class_time_prom_learn_5 + $class_time_prom_learn_6 + $class_time_prom_learn_7;
                                    $class_time_prom_learn_score  =(($class_time_prom_learn_1*1)+($class_time_prom_learn_2*2) + ($class_time_prom_learn_3*3) + ($class_time_prom_learn_4 * 4) + ($class_time_prom_learn_5 *5) + ($class_time_prom_learn_6 * 6) + ($class_time_prom_learn_7 * 7) )/($class_time_prom_learn_total *7);
                                    echo  number_format($class_time_prom_learn_score ,4);
                                    ?></td>
                                <td><?php
                                    echo $class_time_prom_learn_percentage = number_format($class_time_prom_learn_score ,4) * 100;  ?></td>
                                <td><?php  echo $class_time_prom_learn_remark= $help->remark($class_time_prom_learn_percentage);  $no_poor_14 = $help->count_poor($class_time_prom_learn_remark, 14);?></td>


                            </tr>
                            <tr>
                                <td style="width: 280px">15) The Lecturer used varying teaching methodology (Lecturers, demonstrations, presentations etc.</td>
                                <td><?php echo $varying_teaching_meth_1 = @$help->selectcount($varying_teaching_meth, '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $varying_teaching_meth_2 = @$help->selectcount($varying_teaching_meth, '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $varying_teaching_meth_3 = @$help->selectcount($varying_teaching_meth, '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $varying_teaching_meth_4 = @$help->selectcount($varying_teaching_meth, '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $varying_teaching_meth_5 = @$help->selectcount($varying_teaching_meth, '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $varying_teaching_meth_6 = @$help->selectcount($varying_teaching_meth, '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $varying_teaching_meth_7 = @$help->selectcount($varying_teaching_meth, '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php $varying_teaching_meth_total =$varying_teaching_meth_1 + $varying_teaching_meth_2  +  $varying_teaching_meth_3 + $varying_teaching_meth_4 + $varying_teaching_meth_5 + $varying_teaching_meth_6 + $varying_teaching_meth_7;
                                    $varying_teaching_meth_score  =(($varying_teaching_meth_1*1)+($varying_teaching_meth_2*2) + ($varying_teaching_meth_3*3) + ($varying_teaching_meth_4 * 4) + ($varying_teaching_meth_5 *5) + ($varying_teaching_meth_6 * 6) + ($varying_teaching_meth_7 * 7) )/($varying_teaching_meth_total *7);
                                    echo  number_format($varying_teaching_meth_score ,4);
                                    ?></td>
                                <td><?php
                                    echo  $varying_teaching_meth_percentage = number_format($varying_teaching_meth_score ,4) * 100;  ?></td>
                                <td><?php   echo $varying_teaching_meth_remark = $help->remark($varying_teaching_meth_percentage); $no_poor_15 = $help->count_poor($varying_teaching_meth_remark, 15); ?></td>


                            </tr>
                            <tr>
                                <td style="width: 280px">16) The Lecturer encouraged students participation.</td>
                                <td><?php echo $encourage_stud_participation_1 = @$help->selectcount($encourage_stud_participation, '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $encourage_stud_participation_2 = @$help->selectcount($encourage_stud_participation, '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $encourage_stud_participation_3 = @$help->selectcount($encourage_stud_participation, '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $encourage_stud_participation_4 = @$help->selectcount($encourage_stud_participation, '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $encourage_stud_participation_5 = @$help->selectcount($encourage_stud_participation, '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $encourage_stud_participation_6 = @$help->selectcount($encourage_stud_participation, '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $encourage_stud_participation_7 = @$help->selectcount($encourage_stud_participation, '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php $encourage_stud_participation_total =$encourage_stud_participation_1 + $encourage_stud_participation_2  +  $encourage_stud_participation_3 + $encourage_stud_participation_4 + $encourage_stud_participation_5 + $encourage_stud_participation_6 + $encourage_stud_participation_7;
                                    $encourage_stud_participation_score  =(($encourage_stud_participation_1*1)+($encourage_stud_participation_2*2) + ($encourage_stud_participation_3*3) + ($encourage_stud_participation_4 * 4) + ($encourage_stud_participation_5 *5) + ($encourage_stud_participation_6 * 6) + ($encourage_stud_participation_7 * 7) )/($encourage_stud_participation_total *7);
                                    echo  number_format($encourage_stud_participation_score ,4);
                                    ?></td>
                                <td><?php
                                    echo  $encourage_stud_participation_percentage = number_format($encourage_stud_participation_score,4) * 100;  ?></td>
                                <td><?php  echo $encourage_stud_participation_remark = $help->remark($encourage_stud_participation_percentage); $no_poor_16 = $help->count_poor($encourage_stud_participation_remark, 16); ?></td>


                            </tr>
                            <tr>
                                <td style="width: 280px">17) The Lecture encouraged problem solving.</td>
                                <td><?php echo $encourage_problem_solving_1 = @$help->selectcount($encourage_problem_solving, '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $encourage_problem_solving_2 = @$help->selectcount($encourage_problem_solving, '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $encourage_problem_solving_3 = @$help->selectcount($encourage_problem_solving, '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $encourage_problem_solving_4 = @$help->selectcount($encourage_problem_solving, '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $encourage_problem_solving_5 = @$help->selectcount($encourage_problem_solving, '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $encourage_problem_solving_6 = @$help->selectcount($encourage_problem_solving, '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $encourage_problem_solving_7 = @$help->selectcount($encourage_problem_solving, '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php $encourage_problem_solving_total =$encourage_problem_solving_1 + $encourage_problem_solving_2  +  $encourage_problem_solving_3 + $encourage_problem_solving_4 + $encourage_problem_solving_5 + $encourage_problem_solving_6 + $encourage_problem_solving_7;
                                    $encourage_problem_solving_score  =(($encourage_problem_solving_1*1)+($encourage_problem_solving_2*2) + ($encourage_problem_solving_3*3) + ($encourage_problem_solving_4 * 4) + ($encourage_problem_solving_5 *5) + ($encourage_problem_solving_6 * 6) + ($encourage_problem_solving_7 * 7) )/($encourage_problem_solving_total *7);
                                    echo  number_format($encourage_problem_solving_score ,4);

                                    ?></td>
                                <td><?php
                                    echo $encourage_problem_solving_percentage = number_format($encourage_problem_solving_score ,4) * 100;  ?></td>
                                <td><?php   echo $encourage_problem_solving_remark = $help->remark($encourage_problem_solving_percentage);  $no_poor_17 = $help->count_poor($encourage_problem_solving_remark, 17); ?></td>


                            </tr>
                            <tr>
                                <td style="width: 280px">18) The Lecturer was responsive to student&rsquo;s questions and concerns.</td>
                                <td><?php echo $respond_to_stud_concerns_1 = @$help->selectcount($respond_to_stud_concerns, '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $respond_to_stud_concerns_2 = @$help->selectcount($respond_to_stud_concerns, '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $respond_to_stud_concerns_3 = @$help->selectcount($respond_to_stud_concerns, '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $respond_to_stud_concerns_4 = @$help->selectcount($respond_to_stud_concerns, '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $respond_to_stud_concerns_5 = @$help->selectcount($respond_to_stud_concerns, '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $respond_to_stud_concerns_6 = @$help->selectcount($respond_to_stud_concerns, '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $respond_to_stud_concerns_7 = @$help->selectcount($respond_to_stud_concerns, '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php $respond_to_stud_concerns_total =$respond_to_stud_concerns_1 + $respond_to_stud_concerns_2  +  $respond_to_stud_concerns_3 + $respond_to_stud_concerns_4 + $respond_to_stud_concerns_5 + $respond_to_stud_concerns_6 + $respond_to_stud_concerns_7;
                                    $respond_to_stud_concerns_score  =(($respond_to_stud_concerns_1*1)+($respond_to_stud_concerns_2*2) + ($respond_to_stud_concerns_3*3) + ($respond_to_stud_concerns_4 * 4) + ($respond_to_stud_concerns_5 *5) + ($respond_to_stud_concerns_6 * 6) + ($respond_to_stud_concerns_7 * 7) )/($respond_to_stud_concerns_total *7);
                                    echo  number_format($respond_to_stud_concerns_score ,4);
                                    ?></td>
                                <td><?php
                                    echo  $respond_to_stud_concerns_percentage = number_format($respond_to_stud_concerns_score ,4) * 100;  ?></td>
                                <td><?php   echo $respond_to_stud_concerns_remark = $help->remark($respond_to_stud_concerns_percentage); $no_poor_18 = $help->count_poor($respond_to_stud_concerns_remark, 18); ?></td>


                            </tr>
                            <tr>
                                <td style="width: 280px">19) Lecturer used  any other media to deliver lectures(e.g. flipchart, teaching/learning aids e.t.c.</td>
                                <td><?php echo $other_media_delivery_1= @$help->selectcount($other_media_delivery, '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $other_media_delivery_2= @$help->selectcount($other_media_delivery, '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $other_media_delivery_3= @$help->selectcount($other_media_delivery, '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $other_media_delivery_4= @$help->selectcount($other_media_delivery, '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $other_media_delivery_5= @$help->selectcount($other_media_delivery, '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $other_media_delivery_6= @$help->selectcount($other_media_delivery, '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $other_media_delivery_7= @$help->selectcount($other_media_delivery, '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php $other_media_delivery_total =$other_media_delivery_1 + $other_media_delivery_2  +  $other_media_delivery_3 + $other_media_delivery_4 + $other_media_delivery_5 + $other_media_delivery_6 + $other_media_delivery_7;
                                    $other_media_delivery_score  =(($other_media_delivery_1*1)+($other_media_delivery_2*2) + ($other_media_delivery_3*3) + ($other_media_delivery_4 * 4) + ($other_media_delivery_5 *5) + ($other_media_delivery_6 * 6) + ($other_media_delivery_7 * 7) )/($other_media_delivery_total *7);
                                    echo  number_format($other_media_delivery_score ,4);
                                    ?></td>
                                <td><?php
                                    echo $other_media_delivery_percentage = number_format($other_media_delivery_score ,4) * 100;  ?></td>
                                <td><?php  echo $other_media_delivery_remark =  $help->remark($other_media_delivery_percentage); $no_poor_19 = $help->count_poor($other_media_delivery_remark, 19); ?></td>


                            </tr>
                            <tr>
                                <td style="width: 280px">20) The Lecturer made room for questions and expression opinions.</td>
                                <td><?php echo $room_for_question_1 = @$help->selectcount($room_for_question, '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $room_for_question_2 = @$help->selectcount($room_for_question, '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $room_for_question_3 = @$help->selectcount($room_for_question, '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $room_for_question_4 = @$help->selectcount($room_for_question, '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $room_for_question_5 = @$help->selectcount($room_for_question, '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $room_for_question_6 = @$help->selectcount($room_for_question, '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $room_for_question_7 = @$help->selectcount($room_for_question, '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php $room_for_question_total =$room_for_question_1 + $room_for_question_2  +  $room_for_question_3 + $room_for_question_4 + $room_for_question_5 + $room_for_question_6 + $room_for_question_7;
                                    $room_for_question_score  =(($room_for_question_1*1)+($room_for_question_2*2) + ($room_for_question_3*3) + ($room_for_question_4 * 4) + ($room_for_question_5 *5) + ($room_for_question_6 * 6) + ($room_for_question_7 * 7) )/($room_for_question_total *7);
                                    echo  number_format($room_for_question_score ,4);
                                    ?></td>
                                <td><?php
                                    echo  $room_for_question_percentage = number_format($room_for_question_score ,4) * 100;  ?></td>
                                <td><?php  echo $room_for_question_remark = $help->remark($room_for_question_percentage); $no_poor_20 = $help->count_poor($room_for_question_remark, 20); ?></td>


                            </tr>
                            <tr>
                                <td style="width: 280px"> SUB TOTAL</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td><?php
                                    $mode_of_delivery_subtotal=($demonstrate_knowledge_score + $well_organised_delivery_score + $communicate_effectively_score + $class_time_prom_learn_score + $varying_teaching_meth_score + $encourage_stud_participation_score +  $encourage_problem_solving_score + $respond_to_stud_concerns_score + $other_media_delivery_score + $room_for_question_score);
                                    echo number_format($mode_of_delivery_subtotal,4);
                                    ?></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>

                            </tr>
                            </tbody>
                        </table>

                        <p></p>
                        <table  class="uk-table gad"  border="1">
                            <tr>
                                <th width="20" style="text-align: left">ASSESSMENT</th>
                                <th width="100">1</th>
                                <th width="150">2</th>
                                <th width="78">3</th>
                                <th width="71">4</th>
                                <th width="71">5</th>
                                <th width="71">6</th>
                                <th width="71">7</th>
                                <th width="71">SCORE</th>
                                <th width="71">%SCORE</th>
                                <th width="71">REMARKS</th>
                            </tr>
                            <tbody>
                            <tr>
                                <td style="width: 280px">21) The Lecturer gave adequate assignments/quizzes(minimum of 2).</td>
                                <td><?php echo $adequate_assignment_1 = @$help->selectcount($adequate_assignment, '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $adequate_assignment_2 = @$help->selectcount($adequate_assignment, '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $adequate_assignment_3 = @$help->selectcount($adequate_assignment, '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $adequate_assignment_4 = @$help->selectcount($adequate_assignment, '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $adequate_assignment_5 = @$help->selectcount($adequate_assignment, '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $adequate_assignment_6 = @$help->selectcount($adequate_assignment, '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $adequate_assignment_7 = @$help->selectcount($adequate_assignment, '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php $adequate_assignment_total =$adequate_assignment_1 + $adequate_assignment_2  +  $adequate_assignment_3 + $adequate_assignment_4 + $adequate_assignment_5 + $adequate_assignment_6 + $adequate_assignment_7;
                                    $adequate_assignment_score  =(($adequate_assignment_1*1)+($adequate_assignment_2*2) + ($adequate_assignment_3*3) + ($adequate_assignment_4 * 4) + ($adequate_assignment_5 *5) + ($adequate_assignment_6 * 6) + ($adequate_assignment_7 * 7) )/($adequate_assignment_total *7);
                                    echo  number_format($adequate_assignment_score ,4);
                                    ?></td>
                                <td><?php
                                    echo  $adequate_assignment_percentage = number_format($adequate_assignment_score ,4) * 100;  ?></td>
                                <td><?php  echo $adequate_assignment_remark = $help->remark($adequate_assignment_percentage);  $no_poor_21 = $help->count_poor($adequate_assignment_remark, 21); ?></td>


                            </tr>
                            <tr>
                                <td style="width: 280px">22) Expected time for student to receive feedback on assignments or
                                    discussions is stated.</td>
                                <td><?php echo $state_feedback_time_1 = @$help->selectcount($state_feedback_time, '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $state_feedback_time_2 = @$help->selectcount($state_feedback_time, '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $state_feedback_time_3 = @$help->selectcount($state_feedback_time, '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $state_feedback_time_4 = @$help->selectcount($state_feedback_time, '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $state_feedback_time_5 = @$help->selectcount($state_feedback_time, '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $state_feedback_time_6 = @$help->selectcount($state_feedback_time, '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $state_feedback_time_7 = @$help->selectcount($state_feedback_time, '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php $state_feedback_time_total =$state_feedback_time_1 + $state_feedback_time_2  +  $state_feedback_time_3 + $state_feedback_time_4 + $state_feedback_time_5 + $state_feedback_time_6 + $state_feedback_time_7;
                                    $state_feedback_time_score  =(($state_feedback_time_1*1)+($state_feedback_time_2*2) + ($state_feedback_time_3*3) + ($state_feedback_time_4 * 4) + ($state_feedback_time_5 *5) + ($state_feedback_time_6 * 6) + ($state_feedback_time_7 * 7) )/($state_feedback_time_total *7);
                                    echo  number_format($state_feedback_time_score ,4);
                                    ?></td>
                                <td><?php
                                    echo $state_feedback_time_percentage = number_format($state_feedback_time_score ,4) * 100;  ?></td>
                                <td><?php  echo $state_feedback_time_remark = $help->remark($state_feedback_time_percentage); $no_poor_22 = $help->count_poor($state_feedback_time_remark, 22); ?></td>


                            </tr>

                            <tr>
                                <td style="width: 280px">23) Marked assignment/quizzes were returned on time.</td>
                                <td><?php echo $mark_assignment_1 = @$help->selectcount($mark_assignment, '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $mark_assignment_2 = @$help->selectcount($mark_assignment, '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $mark_assignment_3 = @$help->selectcount($mark_assignment, '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $mark_assignment_4 = @$help->selectcount($mark_assignment, '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $mark_assignment_5 = @$help->selectcount($mark_assignment, '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $mark_assignment_6 = @$help->selectcount($mark_assignment, '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $mark_assignment_7 = @$help->selectcount($mark_assignment, '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php  $mark_assignment_total =$mark_assignment_1 + $mark_assignment_2  +  $mark_assignment_3 + $mark_assignment_4 + $mark_assignment_5 + $mark_assignment_6 + $mark_assignment_7;
                                    $mark_assignment_score  =(($mark_assignment_1*1)+($mark_assignment_2*2) + ($mark_assignment_3*3) + ($mark_assignment_4 * 4) + ($mark_assignment_5 *5) + ($mark_assignment_6 * 6) + ($mark_assignment_7 * 7) )/($mark_assignment_total *7);
                                    echo  number_format($mark_assignment_score ,4);
                                    ?></td>
                                <td><?php
                                    echo  $mark_assignment_percentage = number_format($mark_assignment_score ,4) * 100;  ?></td>
                                <td><?php  echo $mark_assignment_remark = $help->remark($mark_assignment_percentage); $no_poor_23 = $help->count_poor($mark_assignment_remark, 23); ?></td>


                            </tr>
                            <tr>
                                <td style="width: 280px">24) Assignments or Quizzes were subsequently discussed in class or at tutorials</td>
                                <td><?php echo $discuss_in_class_1 = @$help->selectcount($discuss_in_class, '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $discuss_in_class_2 = @$help->selectcount($discuss_in_class, '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $discuss_in_class_3 = @$help->selectcount($discuss_in_class, '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $discuss_in_class_4 = @$help->selectcount($discuss_in_class, '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $discuss_in_class_5 = @$help->selectcount($discuss_in_class, '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $discuss_in_class_6 = @$help->selectcount($discuss_in_class, '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $discuss_in_class_7 = @$help->selectcount($discuss_in_class, '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php  $discuss_in_class_total =$discuss_in_class_1 + $discuss_in_class_2  +  $discuss_in_class_3 + $discuss_in_class_4 + $discuss_in_class_5 + $discuss_in_class_6 + $discuss_in_class_7;
                                    $discuss_in_class_score  =(($discuss_in_class_1*1)+($discuss_in_class_2*2) + ($discuss_in_class_3*3) + ($discuss_in_class_4 * 4) + ($discuss_in_class_5 *5) + ($discuss_in_class_6 * 6) + ($discuss_in_class_7 * 7) )/($discuss_in_class_total *7);
                                    echo  number_format($discuss_in_class_score ,4);
                                    ?></td>
                                <td><?php
                                    echo  $discuss_in_class_percentage = number_format($discuss_in_class_score ,4) * 100;  ?></td>
                                <td><?php  echo $discuss_in_class_remark = $help->remark($discuss_in_class_percentage); $no_poor_24 = $help->count_poor($discuss_in_class_remark, 24); ?></td>


                            </tr>
                            <tr>
                                <td style="width: 280px">25) The Lecturer was genuinely concerned with students&rsquo; progress.</td>
                                <td><?php echo $stud_progress_concern_1 = @$help->selectcount($stud_progress_concern, '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $stud_progress_concern_2 = @$help->selectcount($stud_progress_concern, '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $stud_progress_concern_3 = @$help->selectcount($stud_progress_concern, '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $stud_progress_concern_4 = @$help->selectcount($stud_progress_concern, '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $stud_progress_concern_5 = @$help->selectcount($stud_progress_concern, '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $stud_progress_concern_6 = @$help->selectcount($stud_progress_concern, '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $stud_progress_concern_7 = @$help->selectcount($stud_progress_concern, '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php  $stud_progress_concern_total =$stud_progress_concern_1 + $stud_progress_concern_2  +  $stud_progress_concern_3 + $stud_progress_concern_4 + $stud_progress_concern_5 + $stud_progress_concern_6 + $stud_progress_concern_7;
                                    $stud_progress_concern_score  =(($stud_progress_concern_1*1)+($stud_progress_concern_2*2) + ($stud_progress_concern_3*3) + ($stud_progress_concern_4 * 4) + ($stud_progress_concern_5 *5) + ($stud_progress_concern_6 * 6) + ($stud_progress_concern_7 * 7) )/($stud_progress_concern_total *7);
                                    echo  number_format($stud_progress_concern_score ,4);
                                    ?></td>
                                <td><?php
                                    echo $stud_progress_concern_percentage = number_format($stud_progress_concern_score ,4) * 100;  ?></td>
                                <td><?php   echo $stud_progress_concern_remark = $help->remark($stud_progress_concern_percentage); $no_poor_25 = $help->count_poor($stud_progress_concern_remark, 25); ?></td>


                            </tr>
                            <tr>
                                <td style="width: 280px">26) Expectations of student&rsquo; responsibilities are stated eg. Attending classes regularly, early etc</td>
                                <td><?php echo $stud_responsibility_1 = @$help->selectcount($stud_responsibility, '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $stud_responsibility_2 = @$help->selectcount($stud_responsibility, '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $stud_responsibility_3 = @$help->selectcount($stud_responsibility, '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $stud_responsibility_4 = @$help->selectcount($stud_responsibility, '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $stud_responsibility_5 = @$help->selectcount($stud_responsibility, '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $stud_responsibility_6 = @$help->selectcount($stud_responsibility, '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $stud_responsibility_7 = @$help->selectcount($stud_responsibility, '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php $stud_responsibility_total =$stud_responsibility_1 + $stud_responsibility_2  +  $stud_responsibility_3 + $stud_responsibility_4 + $stud_responsibility_5 + $stud_responsibility_6 + $stud_responsibility_7;
                                    $stud_responsibility_score  =(($stud_responsibility_1*1)+($stud_responsibility_2*2) + ($stud_responsibility_3*3) + ($stud_responsibility_4 * 4) + ($stud_responsibility_5 *5) + ($stud_responsibility_6 * 6) + ($stud_responsibility_7 * 7) )/($stud_responsibility_total *7);
                                    echo  number_format($stud_responsibility_score ,4);
                                    ?></td>
                                <td><?php
                                    echo $stud_responsibility_percentage = number_format($stud_responsibility_score ,4) * 100;  ?></td>
                                <td><?php  echo $stud_responsibility_remark =  $help->remark($stud_responsibility_percentage); $no_poor_26 = $help->count_poor($stud_responsibility_remark, 26); ?></td>


                            </tr>
                            <tr>
                                <td style="width: 280px">27) Deadlines for assignments, projects, quizzes, exams etc are specified</td>
                                <td><?php echo $deadline_assignment_1 =  @$help->selectcount($deadline_assignment, '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $deadline_assignment_2 =  @$help->selectcount($deadline_assignment, '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $deadline_assignment_3 =  @$help->selectcount($deadline_assignment, '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $deadline_assignment_4 =  @$help->selectcount($deadline_assignment, '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $deadline_assignment_5 =  @$help->selectcount($deadline_assignment, '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $deadline_assignment_6 = @$help->selectcount($deadline_assignment, '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $deadline_assignment_7 =  @$help->selectcount($deadline_assignment, '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php $deadline_assignment_total =$deadline_assignment_1 + $deadline_assignment_2  +  $deadline_assignment_3 + $deadline_assignment_4 + $deadline_assignment_5 + $deadline_assignment_6 + $deadline_assignment_7;
                                    $deadline_assignment_score  =(($deadline_assignment_1*1)+($deadline_assignment_2*2) + ($deadline_assignment_3*3) + ($deadline_assignment_4 * 4) + ($deadline_assignment_5 *5) + ($deadline_assignment_6 * 6) + ($deadline_assignment_7 * 7) )/($deadline_assignment_total *7);
                                    echo  number_format($deadline_assignment_score ,4);
                                    ?></td>
                                <td><?php
                                    echo  $deadline_assignment_percentage = number_format($deadline_assignment_score ,4) * 100;  ?></td>
                                <td><?php  echo $deadline_assignment_remark=  $help->remark($deadline_assignment_percentage); $no_poor_27 =  $help->count_poor($deadline_assignment_remark, 27); ?></td>

                            </tr>
                            <tr>
                                <td style="width: 280px">28) The marks for each assignment and final course grading scale is disclosed</td>
                                <td><?php echo $disclose_marks_1 = @$help->selectcount($disclose_marks, '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $disclose_marks_2 = @$help->selectcount($disclose_marks, '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $disclose_marks_3 = @$help->selectcount($disclose_marks, '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $disclose_marks_4 = @$help->selectcount($disclose_marks, '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $disclose_marks_5 = @$help->selectcount($disclose_marks, '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $disclose_marks_6 = @$help->selectcount($disclose_marks, '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $disclose_marks_7 = @$help->selectcount($disclose_marks, '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php $disclose_marks_total =$disclose_marks_1 + $disclose_marks_2  +  $disclose_marks_3 + $disclose_marks_4 + $disclose_marks_5 + $disclose_marks_6 + $disclose_marks_7;
                                    $disclose_marks_score  =(($disclose_marks_1*1)+($disclose_marks_2*2) + ($disclose_marks_3*3) + ($disclose_marks_4 * 4) + ($disclose_marks_5 *5) + ($disclose_marks_6 * 6) + ($disclose_marks_7 * 7) )/($disclose_marks_total *7);
                                    echo  number_format($disclose_marks_score ,4);
                                    ?></td>
                                <td><?php
                                    echo  $disclose_marks_percentage = number_format($disclose_marks_score ,4) * 100;  ?></td>
                                <td><?php  echo $disclose_marks_remarks=  $help->remark($disclose_marks_percentage); $no_poor_28 = $help->count_poor($disclose_marks_remarks, 28); ?></td>


                            </tr>
                            <tr>
                                <td style="width: 280px">29) Lecturer's policies on late submission of assignments are explained
                                    In class</td>
                                <td><?php echo $late_submission_policy_1 =  @$help->selectcount($late_submission_policy , '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $late_submission_policy_2 =  @$help->selectcount($late_submission_policy , '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $late_submission_policy_3 =  @$help->selectcount($late_submission_policy , '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $late_submission_policy_4 =  @$help->selectcount($late_submission_policy , '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $late_submission_policy_5 =  @$help->selectcount($late_submission_policy , '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $late_submission_policy_6 =  @$help->selectcount($late_submission_policy , '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $late_submission_policy_7 =  @$help->selectcount($late_submission_policy , '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php $late_submission_policy_total =$late_submission_policy_1 + $late_submission_policy_2  +  $late_submission_policy_3 + $late_submission_policy_4 + $late_submission_policy_5 + $late_submission_policy_6 + $late_submission_policy_7;
                                    $late_submission_policy_score  =(($late_submission_policy_1*1)+($late_submission_policy_2*2) + ($late_submission_policy_3*3) + ($late_submission_policy_4 * 4) + ($late_submission_policy_5 *5) + ($late_submission_policy_6 * 6) + ($late_submission_policy_7 * 7) )/($late_submission_policy_total *7);
                                    echo  number_format($late_submission_policy_score ,4);
                                    ?></td>
                                <td><?php
                                    echo $late_submission_policy_percentage = number_format($late_submission_policy_score ,4) * 100;  ?></td>
                                <td><?php   echo $late_submission_policy_remark=  $help->remark($late_submission_policy_percentage);  $no_poor_29 =  $help->count_poor($late_submission_policy_remark, 29); ?></td>


                            </tr>
                            <tr>
                                <td style="width: 280px">30) A variety of assessment methods are used in class (Class test, quiz, practicals, group assignments, presentation etc.</td>
                                <td><?php echo $variety_assignment_used_1 =  @$help->selectcount($variety_assignment_used , '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $variety_assignment_used_2 =  @$help->selectcount($variety_assignment_used , '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $variety_assignment_used_3 =  @$help->selectcount($variety_assignment_used , '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $variety_assignment_used_4 =  @$help->selectcount($variety_assignment_used , '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $variety_assignment_used_5 =  @$help->selectcount($variety_assignment_used , '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $variety_assignment_used_6 =  @$help->selectcount($variety_assignment_used , '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $variety_assignment_used_7 =  @$help->selectcount($variety_assignment_used , '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php $variety_assignment_used_total =$variety_assignment_used_1 + $variety_assignment_used_2  +  $variety_assignment_used_3 + $variety_assignment_used_4 + $variety_assignment_used_5 + $variety_assignment_used_6 + $variety_assignment_used_7;
                                    $variety_assignment_used_score  =(($variety_assignment_used_1*1)+($variety_assignment_used_2*2) + ($variety_assignment_used_3*3) + ($variety_assignment_used_4 * 4) + ($variety_assignment_used_5 *5) + ($variety_assignment_used_6 * 6) + ($variety_assignment_used_7 * 7) )/($variety_assignment_used_total *7);
                                    echo  number_format($variety_assignment_used_score ,4);
                                    ?></td>
                                <td><?php
                                    echo  $variety_assignment_percentage = number_format($variety_assignment_used_score ,4) * 100;  ?></td>
                                <td><?php   echo $variety_assignment_used_remark =  $help->remark($variety_assignment_percentage);  $no_poor_30 =  $help->count_poor($variety_assignment_used_remark, 30); ?></td>

                            </tr>
                            <tr>
                                <td style="width: 280px">31) Assessment methods and learning activities help to achieve course
                                    Objectives and learning outcomes</td>
                                <td><?php echo $course_objective_achieved_1 = @$help->selectcount($course_objective_achieved , '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $course_objective_achieved_2 = @$help->selectcount($course_objective_achieved , '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $course_objective_achieved_3 = @$help->selectcount($course_objective_achieved , '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $course_objective_achieved_4 = @$help->selectcount($course_objective_achieved , '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $course_objective_achieved_5 = @$help->selectcount($course_objective_achieved , '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $course_objective_achieved_6 = @$help->selectcount($course_objective_achieved , '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $course_objective_achieved_7 = @$help->selectcount($course_objective_achieved , '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php $course_objective_achieved_total =$course_objective_achieved_1 + $course_objective_achieved_2  +  $course_objective_achieved_3 + $course_objective_achieved_4 + $course_objective_achieved_5 + $course_objective_achieved_6 + $course_objective_achieved_7;
                                    $course_objective_achieved_score  =(($course_objective_achieved_1*1)+($course_objective_achieved_2*2) + ($course_objective_achieved_3*3) + ($course_objective_achieved_4 * 4) + ($course_objective_achieved_5 *5) + ($course_objective_achieved_6 * 6) + ($course_objective_achieved_7 * 7) )/($course_objective_achieved_total *7);
                                    echo  number_format($course_objective_achieved_score ,4);
                                    ?></td>
                                <td><?php
                                    echo $course_objective_achieved_percentage = number_format($course_objective_achieved_score ,4) * 100;  ?></td>
                                <td><?php  echo $course_objective_achieved_remark = $help->remark($course_objective_achieved_percentage); $no_poor_31 = $help->count_poor($course_objective_achieved_remark, 31); ?></td>


                            </tr>
                            <tr>
                                <td style="width: 280px">32) What is expected of students regarding assignments, quizzes,
                                    Presentations and projects are clearly communicated to them</td>
                                <td><?php echo $expectations_communicated_1 = @$help->selectcount($expectations_communicated, '1' , $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $expectations_communicated_2 = @$help->selectcount($expectations_communicated, '2' , $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $expectations_communicated_3 = @$help->selectcount($expectations_communicated, '3' , $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $expectations_communicated_4 = @$help->selectcount($expectations_communicated, '4' , $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $expectations_communicated_5 = @$help->selectcount($expectations_communicated, '5' , $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $expectations_communicated_6 = @$help->selectcount($expectations_communicated, '6' , $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $expectations_communicated_7 = @$help->selectcount($expectations_communicated, '7' , $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php $expectations_communicated_total =$expectations_communicated_1 + $expectations_communicated_2  +  $expectations_communicated_3 + $expectations_communicated_4 + $expectations_communicated_5 + $expectations_communicated_6 + $expectations_communicated_7;
                                    $expectations_communicated_score  =(($expectations_communicated_1*1)+($expectations_communicated_2*2) + ($expectations_communicated_3*3) + ($expectations_communicated_4 * 4) + ($expectations_communicated_5 *5) + ($expectations_communicated_6 * 6) + ($expectations_communicated_7 * 7) )/($expectations_communicated_total *7);
                                    echo  number_format($expectations_communicated_score ,4);
                                    ?></td>
                                <td><?php
                                    echo $expectations_communicated_percentage = number_format($expectations_communicated_score ,4) * 100;  ?></td>
                                <td><?php  echo $expectations_communicated_remark=  $help->remark($expectations_communicated_percentage);  $no_poor_32 = $help->count_poor($expectations_communicated_remark, 32); ?></td>


                            </tr>
                            <tr>
                                <td style="width: 280px"> SUB TOTAL</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td><?php $assessment_subtotal = ($adequate_assignment_score + $state_feedback_time_score + $mark_assignment_score + $discuss_in_class_score + $stud_progress_concern_score +  $stud_responsibility_score + $deadline_assignment_score + $disclose_marks_score + $late_submission_policy_score +  $variety_assignment_used_score + $course_objective_achieved_score  + $expectations_communicated_score);
                                    echo number_format($assessment_subtotal,4);
                                    ?></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>

                            </tr>
                            </tbody>
                        </table>
                        </div>

                        <p></p>
                            <div id="page3">
                        <table  class="uk-table gad"  border="1">
                            <tr>
                                <th width="20" style="text-align: left">HANDOUT/COURSE MATERIALS</th>
                                <th width="100">1</th>
                                <th width="150">2</th>
                                <th width="78">3</th>
                                <th width="71">4</th>
                                <th width="71">5</th>
                                <th width="71">6</th>
                                <th width="71">7</th>
                                <th width="71">SCORE</th>
                                <th width="71">%SCORE</th>
                                <th width="71">REMARKS</th>
                            </tr>
                            <tbody>
                            <tr>
                                <td style="width: 280px">33) The Lecturer sold hand-outs to students(not authored books).</td>
                                <td><?php echo $sold_handout_y = @$help->selectcount($sold_handout, 'Yes', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $sold_handout_n = @$help->selectcount($sold_handout, 'No', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td><?php  $sold_handout_percentage = ($sold_handout_n /(($sold_handout_y+$sold_handout_n))* 7);
                                    echo number_format($sold_handout_percentage, 4);
                                    ?></td>
                                <td><?php
                                    //echo  $comprehensive_outline_percentage = number_format($comprehensive_outline_score ,4) * 100;  ?>
                                    <?php  echo $sold_handout_np = number_format(($sold_handout_n /(($sold_handout_y+$sold_handout_n))* 100),2); ?></td>
                                <td><?php   echo $sold_handout_remark = $help->remark($sold_handout_np);  $no_poor_33 = $help->count_poor($sold_handout_remark, 33); ?></td>


                            </tr>

                            <tr>
                                <td style="width: 280px"> SUB TOTAL</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td><?php $handout_total = $sold_handout_percentage; echo number_format($handout_total, 4); ?></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            </tbody>
                        </table>
                        <p></p>
                        <table  class="uk-table gad"  border="1">
                            <tr>
                                <th width="20" style="text-align: left">GENERAL ATMOSPHERE IN CLASS</th>
                                <th width="100">1</th>
                                <th width="150">2</th>
                                <th width="78">3</th>
                                <th width="71">4</th>
                                <th width="71">5</th>
                                <th width="71">6</th>
                                <th width="71">7</th>
                                <th width="71">SCORE</th>
                                <th width="71">%SCORE</th>
                                <th width="71">REMARKS</th>
                            </tr>
                            <tbody>
                            <tr>
                                <td style="width: 280px">34) The Lecturer created friendly atmosphere whenever he/she came to class</td>
                                <td><?php echo $created_friendly_atmosphere_1 = @$help->selectcount($created_friendly_atmosphere, '1', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $created_friendly_atmosphere_2 = @$help->selectcount($created_friendly_atmosphere, '2', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $created_friendly_atmosphere_3 = @$help->selectcount($created_friendly_atmosphere, '3', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $created_friendly_atmosphere_4 = @$help->selectcount($created_friendly_atmosphere, '4', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $created_friendly_atmosphere_5 = @$help->selectcount($created_friendly_atmosphere, '5', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $created_friendly_atmosphere_6 = @$help->selectcount($created_friendly_atmosphere, '6', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php echo $created_friendly_atmosphere_7 = @$help->selectcount($created_friendly_atmosphere, '7', $lecturer, $coursecode, $semester, $academic_year);  ?></td>
                                <td><?php  //echo number_format($created_friendly_atmosphere_yp / 100,4);
                                    $created_friendly_atmosphere_total =$created_friendly_atmosphere_1 + $created_friendly_atmosphere_2  +  $created_friendly_atmosphere_3 + $created_friendly_atmosphere_4 + $created_friendly_atmosphere_5 + $created_friendly_atmosphere_6 + $created_friendly_atmosphere_7;
                                    $created_friendly_atmosphere_score  =(($created_friendly_atmosphere_1*1)+($created_friendly_atmosphere_2*2) + ($created_friendly_atmosphere_3*3) + ($created_friendly_atmosphere_4 * 4) + ($created_friendly_atmosphere_5 *5) + ($created_friendly_atmosphere_6 * 6) + ($created_friendly_atmosphere_7 * 7) )/($created_friendly_atmosphere_total *7);
                                    echo  number_format($created_friendly_atmosphere_score ,4);

                                    ?></td>
                                <td> <?php echo $created_friendly_atmosphere_percentage = number_format($created_friendly_atmosphere_score ,4) * 100; ?></td>
                                <td><?php  echo $created_friendly_atmosphere_remark = $help->remark($created_friendly_atmosphere_percentage); $no_poor_34 = $help->count_poor($created_friendly_atmosphere_remark, 34); ?></td>


                            </tr>

                            <tr>
                                <td style="width: 280px"> SUB TOTAL</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td><?php $atmosphere_total = ($created_friendly_atmosphere_score); echo number_format($atmosphere_total,4);  ?></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>

                            </tr>
                            <tr  style="border-left-color:#FFFFFF; border-right-color:#FFFFFF">
                                <td  style="border-left-color:#FFFFFF; border-right-color:#FFFFFF"><div style='text-align:justify; font-size:16px; font-weight:bold'>
                                        <?php  //echo $course_content_subtotal. "  ". $attendance_subtotal. "  ". $mode_of_delivery_subtotal . "  ". $assessment_subtotal . "  ".$handout_total . "  ".$atmosphere_total; ?>
                                        <!--TOTAL MARKS =&nbsp; -->
                                        <?php
                                        $total_marks_obtained= $course_content_subtotal + $attendance_subtotal +  $mode_of_delivery_subtotal + $assessment_subtotal + $handout_total + $atmosphere_total;

                                        //echo number_format($total_marks_obtained,4);
                                        $calc_total_marks_obtained = $total_marks_obtained/2;
                                        $calc_total_marks_obtained_approximate = number_format($calc_total_marks_obtained, 2);
                                        echo "Total marks obtained = " . number_format($total_marks_obtained, 4) . " / ". " 2 = ". $calc_total_marks_obtained_approximate;

                                        ?>
                                    </div></td>
                                <td  style="border-left-color:#FFFFFF; border-right-color:#FFFFFF">&nbsp;</td>
                                <td  style="border-left-color:#FFFFFF; border-right-color:#FFFFFF">&nbsp;</td>
                                <td  style="border-left-color:#FFFFFF; border-right-color:#FFFFFF">&nbsp;</td>
                                <td  style="border-left-color:#FFFFFF; border-right-color:#FFFFFF">&nbsp;</td>
                                <td  style="border-left-color:#FFFFFF; border-right-color:#FFFFFF">&nbsp;</td>
                                <td  style="border-left-color:#FFFFFF; border-right-color:#FFFFFF">&nbsp;</td>
                                <td  style="border-left-color:#FFFFFF; border-right-color:#FFFFFF">&nbsp;</td>
                                <td  style="border-left-color:#FFFFFF; border-right-color:#FFFFFF">&nbsp;</td>
                                <td  style="border-left-color:#FFFFFF; border-right-color:#FFFFFF">&nbsp;</td>
                                <td  style="border-left-color:#FFFFFF; border-right-color:#FFFFFF">&nbsp;</td>
                            </tr>
                            </tbody>
                        </table>
                        <?php

                        $poor=array();
                        $all_poor= "$no_poor_1,$no_poor_2,$no_poor_3,$no_poor_4,$no_poor_5,$no_poor_6,$no_poor_7,$no_poor_8,$no_poor_9,$no_poor_10,$no_poor_11,$no_poor_12,$no_poor_13,$no_poor_14,$no_poor_15,$no_poor_16,$no_poor_17,$no_poor_18,$no_poor_19,$no_poor_20,$no_poor_21,$no_poor_22,$no_poor_23,$no_poor_24,$no_poor_25,$no_poor_26,$no_poor_27,$no_poor_28,$no_poor_29,$no_poor_30,$no_poor_31,$no_poor_32,$no_poor_33,$no_poor_34";
                        $poor[]=$all_poor;

                        $all_poor1= "$no_poor_1,$no_poor_2,$no_poor_3,$no_poor_4,$no_poor_5,$no_poor_6,$no_poor_7,$no_poor_8,$no_poor_9,$no_poor_10,$no_poor_11,$no_poor_12,$no_poor_13,$no_poor_14,$no_poor_15,$no_poor_16,$no_poor_17,$no_poor_18,$no_poor_19,$no_poor_20,$no_poor_21,$no_poor_22,$no_poor_23,$no_poor_24,$no_poor_25,$no_poor_26,$no_poor_27,$no_poor_28,$no_poor_29,$no_poor_30,$no_poor_31,$no_poor_32,$no_poor_33,$no_poor_34";

                        ?>
                        <p>CRITERIA</p>
                        <p>	 80% -100% agree with item -Excellent<br />
                            70% - 79% agree with item Very Good<br />
                            60% - 69% agree with item - Good<br />
                            50% -59% agree with item - Satisfactory<br />
                            Below 50% agree with item - Poor</p>
                        <p>NB: FOR ITEM 33, THE &quot;NO&quot; RESPONSES ARE USED IN THE CALCULATION OF THE SCORES OBTAINED.<br />
                            COMMENTS:<br />

                            The Lecturer's overall performance was &nbsp; &nbsp;&nbsp;<font size="+1"><b><?php  echo $help->total_mark_remark($calc_total_marks_obtained);?></b></font> </b>
                            <br />
                            <?php
                            //the function below checks if there is an integer in the array. if no return 0, if yes return 1
                            function isNumericArray(array $array)
                            {
                            foreach ($array as $a => $b) { //open foreach
                            if (!is_int($a)) {    // open if  ---if it is not a number
                            return false;

                            }//close if
                            }//close foreach
                            return true;
                            }//close isNumericArray function

                            ?>

                            <?php
                            $boolvalue_true_false= isNumericArray($poor);
                            //echo $boolvalue_true_false;
                            if ( $boolvalue_true_false==1)
                            {
                               // dd($all_poor);
                            echo "However, lecturer should pay particular attention  to items number ";
                            $pieces_poor = explode(",", $all_poor); //exploses the _allpoor1 into an arrary separated by comma

                            for ($i=0; $i < 33; $i++)
                            {
                            echo '<b>'. $pieces_poor [$i].'</b>'. ",";
                            }// end of for loop
                            echo "( places  where the Lecturer's performance was poor)";
                            }// boolvalue
                            else
                            {
                            // do nothing
                            } // end of if boolean value is true or not
                                ?>


                        <br>
                        ...................................
                        <p>DR. EMMANUEL MENSAH BAAH<br>
                            Dean, Quality Assurance Office
                            <br>

                        </p>
                        <p><b>NB: Assessment form must be signed  and stamped by the Dean or his representative at the Academic Quality Assurance Office. </b></p>




                    </div>

                    <div class="footer">
                        <img style="width:1000px;height: auto" src='{{url("public/assets/img/footer.jpg")}}' style=""
                             class="image-responsive"/>


                    </div>



                </div>

        @else
            <p>No report available for lecturer</p>

         @endif
            </div>


            @endsection

        @section('js')
            <script type="text/javascript">

                 window.print();


            </script>

@endsection