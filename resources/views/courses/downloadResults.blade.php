@extends('layouts.app')


@section('style')
    <style>
        .md-card{
            width: auto;

        }

    </style>
    <script src="{!! url('public/assets/js/jquery.min.js') !!}"></script>

    <script src="{!! url('public/assets/js/jquery-ui.min.js') !!}"></script>

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
        <h3 class="heading_c uk-margin-bottom">Download Results</h3>

        <div class="uk-width-xLarge-1-1">

            <div class="md-card">
                <div class="md-card-content" style="">

                    <form  action="{{url('/download_results')}}" enctype="multipart/form-data" id="form" accept-charset="utf-8" method="POST" name="applicationForm"  v-form>
                        <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                        <div class="uk-grid" data-uk-grid-margin>
                                    
                                <div class="uk-width-medium-1-3">
                                    <div class="uk-form-row">
                                         <label>Year Group</label>
                                            <br/>
                                            {!! Form::select('year',
                                                       ($year ),
                                                         old("year",""),
                                                           ['class' => 'md-input parent','required'=>"required",'placeholder'=>'select year'] )  !!}

                                       

                                    </div>
                                </div>
                                <div class="uk-width-medium-1-3">
                                    <div class="uk-form-row">        
                                                <label>Program</label>
                                                <br/> 
                                                {!! Form::select('program',
                                                (['' => 'select program'] +$programme ),
                                                old("program",""),
                                                ['class' => 'md-input parent','required'=>"required",'placeholder'=>'select program'] )  !!}
                                            
                                    </div>
                                </div>
                                <div class="uk-width-medium-1-3">
                                    <div class="uk-form-row">    
                                    <table align="left">
                                        <tr><td><br/><input type="submit" value="Download" id='saves'   class="md-btn   md-btn-success uk-margin-small-top">
                                        <input type="reset" value="Clear" class="md-btn   md-btn-default uk-margin-small-top">
                                        </td></tr>
                                    </table>
                                    </div>
                                </div>
                        </div>
                            
                    </form>
                </div>
            </div>
        </div>
        @endsection

        @section('js')
            <script>

            </script>
            <script src="{!! url('public/assets/js/select2.full.min.js') !!}"></script>
            <script>
                $(document).ready(function(){
                    $('select').select2({ width: "resolve" });


                });


            </script>

@endsection    