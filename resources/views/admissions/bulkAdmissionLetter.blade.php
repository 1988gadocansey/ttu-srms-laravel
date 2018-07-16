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
    <h3 class="heading_b uk-margin-bottom">Bulk Admission Letter Printing</h3>
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











        </div>
    </div>
    <!-- filters here -->
    @inject('fee', 'App\Http\Controllers\FeeController')
    @inject('sys', 'App\Http\Controllers\SystemController')
    <div class="uk-width-xLarge-1-1">
        <div class="md-card">
            <div class="md-card-content">

                <form action="{{url('/admissions/letter/bulk/process')}}"  method="post" accept-charset="utf-8" novalidate id="group">
                    {!!  csrf_field()  !!}
                    <div class="uk-grid" data-uk-grid-margin="">

                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                {!! Form::select('program',
                                (['' => 'All programs'] + $data ),
                                old("program",""),
                                ['class' => 'md-input parent','id'=>"parent",'placeholder'=>'select program','required'=>''] )  !!}
                            </div>
                        </div>





                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">

                                <button class="md-btn  md-btn-small md-btn-success uk-margin-small-top" type="submit"><i class="material-icons">search</i></button>
                            </div>
                        </div>



                    </div>

                </form>
            </div>
        </div>

    </div>

    <!-- end filters -->
    <div class="uk-width-xLarge-1-1">
        <div class="md-card">
            <div class="md-card-content">


                <div class="uk-overflow-container" id='print'>

                    @if(Request::isMethod('post'))
                            gg
                    @endif
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