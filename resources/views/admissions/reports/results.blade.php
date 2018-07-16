@extends('layouts.app')


@section('style')

@endsection
@section('content')

    <div class="md-card-content">

        @if($messages=Session::get("success"))

            <div class="uk-form-row">
                <div style="text-align: center" class="uk-alert uk-alert-success" data-uk-alert="">

                    <ul>
                        @foreach ($messages as $message)
                            <li> {!!  $message  !!} </li>
                        @endforeach
                    </ul>
                </div>
            </div>
    </div>
    @endif


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
        <h5 class="heading_c uk-margin-bottom">REGULAR ADMISSIONS EXAM RESULTS</h5>

        <div style="">
            <div class="uk-margin-bottom" style="margin-left:1000px" >
                <i title="click to print" onclick="javascript:printDiv('print')" class="material-icons md-36 uk-text-success"   >print</i>

                <div class="uk-button-dropdown" data-uk-dropdown="{mode:'click'}">
                    <button class="md-btn md-btn-small md-btn-success"> show/hide columns <i class="uk-icon-caret-down"></i></button>
                    <div class="uk-dropdown">
                        <ul class="uk-nav uk-nav-dropdown" id="columnSelector"></ul>
                    </div>
                </div>
                <div class="uk-button-dropdown" data-uk-dropdown="{mode:'click'}">
                    <button class="md-btn md-btn-small md-btn-success uk-margin-right">Export <i class="uk-icon-caret-down"></i></button>
                    <div class="uk-dropdown">
                        <ul class="uk-nav uk-nav-dropdown">
                            <li><a href="#" onClick ="$('#regular').tableExport({type:'csv',escape:'false'});"><img src='{!! url("public/assets/icons/csv.png")!!}' width="24"/> CSV</a></li>

                          <li class="uk-nav-divider"></li>
                                            <li><a href="#" onClick ="$('#ts_pager_filter').tableExport({type:'excel',escape:'false'});"><img src='{!! url("public/assets/icons/xls.png")!!}' width="24"/> XLS</a></li>
                                            <li><a href="#" onClick ="$('#ts_pager_filter').tableExport({type:'doc',escape:'false'});"><img src='{!! url("public/assets/icons/word.png")!!}' width="24"/> Word</a></li>
                                            <li><a href="#" onClick ="$('#ts_pager_filter').tableExport({type:'powerpoint',escape:'false'});"><img src='{!! url("public/assets/icons/ppt.png")!!}' width="24"/> PowerPoint</a></li>
                                            <li class="uk-nav-divider"></li>
                                           
                        </ul>
                    </div>
                </div>
            </div>

        </div>
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





                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- filters here -->
        @inject('fee', 'App\Http\Controllers\FeeController')
        @inject('sys', 'App\Http\Controllers\SystemController')


        <!-- end filters -->
        <div class="uk-width-xLarge-1-1">
            <div class="md-card">
                <div class="md-card-content">


                    <div class="uk-overflow-container" id='print'>
                        <center><span class="uk-text-success uk-text-bold">{!! $data->total()!!} Records</span></center>
                        <table border="0" class="uk-table uk-table-hover uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id="ts_pager_filter">
                            <thead>
                            <tr><!--
                                <th>NO</th>
                                <th class="uk-width-1-10">PERSONAL DETAILS</th>

                                <th>1ST SITTING </th>
                                <th>2ND SITTING </th>
                                <th>THIRD SITTING </th>
                                <!--                                            <th>TOTAL </th>-->
                            </tr>
                            </thead>
                            <tbody>

                            @foreach($data as $index=> $row)
                                <tr>
                                    <td> {{ $data->perPage()*($data->currentPage()-1)+($index+1) }} </td>

                                    
                                                <td>{{ $row->NAME }}</td>
                                                <td>{{ $row->APPLICATION_NUMBER }}</td>
                                                <td><b>{{ $row->admitedProgram->PROGRAMME }}</b></td>
                                </tr>          
                                <tr>
                                	<td></td>
                                    <td class=' '><?php  print_r($sys->getSittingGrade($row->APPLICATION_NUMBER,'FIRST SITTING'))?></td>
                                    <td class=' '><?php if(!empty($sys->getSittingGrade($row->APPLICATION_NUMBER,'SECOND SITTING'))){ print_r($sys->getSittingGrade($row->APPLICATION_NUMBER,'SECOND SITTING'));}?></td>
                                    <td class=' '><?php print_r ($sys->getSittingGrade($row->APPLICATION_NUMBER,'THIRD SITTING'))?> </td>
                                </tr>
                                </tr>
                                	<td></td>
                                    <td></td>
                                </tr>
                            @endforeach



                            </tbody>

                        </table>
                        <table>

                        </table>

                        {!! (new Landish\Pagination\UIKit($data->appends(old())))->render() !!}
                    </div>


                </div>
            </div></div>
@endsection
@section('js')
    <script type="text/javascript">

        $(document).ready(function(){

            $(".parent").on('change',function(e){

                $("#group").submit();

            });
        });

    </script>
    <script src="{!! url('public/assets/js/select2.full.min.js') !!}"></script>
    <script>
        $(document).ready(function(){
            $('select').select2({ width: "resolve" });


        });


    </script>

    <!--  notifications functions -->
    <script src="public/assets/js/components_notifications.min.js"></script>
@endsection