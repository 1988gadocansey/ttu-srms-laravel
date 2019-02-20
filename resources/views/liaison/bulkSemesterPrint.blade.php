@extends('layouts.printlayout')
@section('style')
    <style>
        @page {
            size: A4;

            margin: 0.5cm;

        }
        body{
            background-image:url("{{url('public/assets/img/background.jpgs')}}");
            background-repeat: no-repeat;
            background-attachment: fixed;
            font-size:13px;
        }
        .address{
            font-weight: bold;
        }

        @media print {

            .uk-grid, to {display: inline !important}
            #page1	{page-break-before:always;}
            .condition	{page-break-before:always;}
            #page2	{page-break-before:always;}
            .school	{page-break-before:always;}
            .page9	{page-break-inside:avoid; page-break-after:auto}
            a,
            a:visited {
                text-decoration: underline;
            }


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
@endsection
@section('content')
    @inject('help', 'App\Http\Controllers\SystemController')
    <div align="" style="margin-left: 12px">

        <div>

            @foreach($datas as $data)
            <div id="page1">


                <table border='0' style="margin-top: -78px">
                    <tr>
                        <td><img style="width:1000px;height: auto" src='{{url("public/assets/img/liaison.png")}}'
                                 style="" class="image-responsive"/>


                    </tr>

                </table>

                <div class="invoice-address" style="margin-top: -45px">
                    <div class="row">
                        <div class="col-md-5 col-sm-5">
                            <div style='pluck; text-align:right;'>

                                <?php
                                //variable declaraions
                                $programmename = @$data->studentDetials->PROGRAMMECODE;
                                $level = substr($data->level,0,1);
                                $studentLevel =  substr($data->level,0,1) ;
                                if($studentLevel==1){
                                    $studentLevel="I";
                                }
                                elseif($studentLevel==2){
                                    $studentLevel="II";
                                }
                                elseif($studentLevel==3){
                                    $studentLevel="III";
                                }
                                elseif($studentLevel==4){
                                    $studentLevel="IV";
                                }


                                ?>

                            </div>
                            <br>
                            <div align="left" style="margin-left:0; font-size:14px; font-weight:bold;">
                                <?php   //echo $help->get_letter_code(substr($data->studentDetials->PROGRAMMECODE, 0, 1), $data->level);  ?>

                                TTU/ILO/IAP/VOL.2/12
                            </div>
                            <div style='float:right;text-align:right; pluck;'><b><?php echo  strtoupper(date('jS F, Y'));  ?> </b></div>
                            <!-- <br> -->

                            <p class="address"><?php echo strtoupper(@$data->addressDetails->addresses); ?>

                                <br/><?php echo strtoupper($data->company_name);?>
                                <br/><?php echo strtoupper($data->company_address);?>

                                <?php echo strtoupper($data->company_location);?>

                            </p>



                        </div>

                    </div>
                </div>
            </div>

            <div class="body">


                <div id='letter'><br><div style='text-align:left; margin-top: -27px'>Dear Sir/Madam,</div>
                    <!-- begin main Letter -->



                <!--  BEGIN THE REST HERE  -->

                    <center> <p style=";" class=" "><h5 >PRACTICAL INDUSTRIAL TRAINING PROGRAMME FOR STUDENTS</h5> </center>
                    <p style='text-align:justify; pluck;'>Students of Takoradi Technical University pursuing Higher National Diploma (HND)<?php //echo $help->getProgram($person[PROGRAMME]);?> are expected to undergo practical industrial training in industry as part of the requirements for the award of their certificate.</p>

                    <p style='text-align:justify; pluck;'>
                        It is believed that the attachment programme would bring positive industrial exposure to students. This exercise would enable students to put theory into practice and acquaint themselves with current technological development in industry and commerce.</p>
                    <p style='text-align:justify; pluck;'>The University would, therefore, be grateful if you could consider the under-mentioned student to undertake his/her industrial attachment programme in your organization from <b> 28th January – 20th April, 2019</b>.</p>
                    <p style='text-align:justify; pluck;'> The student's particulars are as follows: </p>


                    <p><b>REGISTRATION NUMBER:</b>  <?php echo $data->studentDetials->INDEXNO;?> <br/>
                        <b> NAME:</b>  <?php echo strtoupper($data->studentDetials->NAME);?> <br/>
                        <b>PROGRAMME: </b> <?php echo strtoupper($data->studentDetials->program->PROGRAMME). "  ". $studentLevel;?><br/>
                        <b>CONTACT NUMBER:</b>  <?php echo $data->studentDetials->TELEPHONENO;?><br/></p>



                    <p style='text-align:justify;  '>We request that the student should be made to familiarize him/herself with all the related sections available in your organization.
                        For your information, all students at the University are covered by Group Personal Accident Insurance policy.</p>
                    <p style='text-align:justify; pluck;'>We count on your usual cooperation.</p>




                    <div style='float:left;font-weight: normal;'>
                        <p style=''>Yours faithfully,
                        </p>
                        <!-- <br />-->

                        <p style='font-size:16px; font-weight:bold;'>Joseph Eshun<br />
                            Head, Industrial Liaison Office </p>



                        <small><b>NB: DO NOT ACCEPT THIS LETTER IF IT DOES NOT BEAR THE ORIGINAL SIGNATURE AND STAMP</b></small>
                    </div>

                </div>

                <div class="footer">
                    <img style="width:1000px;height: auto" src='{{url("public/assets/img/footer.jpg")}}' style=""
                         class="image-responsive"/>


                </div>



            </div>

            @endforeach
        </div>

    </div>


@endsection

@section('js')
    <script type="text/javascript">

        window.print();

        window.close();


    </script>

@endsection