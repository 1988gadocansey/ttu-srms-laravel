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

            <div class="uk-form-row">
                <div class="uk-alert uk-alert-danger" style="background-color: red;color: white">

                    <ul>
                        @foreach ($errors->all() as $error)
                            <li> {{  $error  }} </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>

    @inject('sys', 'App\Http\Controllers\SystemController')
    <h5 class="heading_c">Liaison Office - Assumption of duty excel download page</h5>
    <p></p>
    <div class="uk-width-xLarge-1-1">
        <div class="md-card">
            <div class="md-card-content">

                <form     method="post" accept-charset="utf-8" name="applicationForm"  v-form >
                    {!!  csrf_field()  !!}
                    <div class="uk-grid" data-uk-grid-margin="">

                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                {!! Form::select('year',
                            (['' => 'Select academic year'] +$years ),
                              old("year",""),
                                ['class' => 'md-input year','required'=>"required",'placeholder'=>'select academic year', 'v-model'=>'year','v-form-ctrl'=>'','v-select'=>''] )  !!}
                                <p class="uk-text-danger uk-text-small"  v-if="applicationForm.year.$error.required" >Academic year is required</p>

                            </div>
                        </div>



{{--
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                {!! Form::select('level',
                           (['' => 'Select level'] +$levels ),
                             old("level",""),
                               ['class' => 'md-input parent level','id'=>"parent",'placeholder'=>'select level', 'v-model'=>'level','v-form-ctrl'=>'','v-select'=>''] )  !!}


                            </div>
                        </div>






                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                {!! Form::select('program',
                              (['' => 'Select program'] + $program),
                                  null,
                                  [ 'class' => 'md-input program','v-model'=>'program','v-form-ctrl'=>'','v-select'=>''] )  !!}


                            </div>
                        </div>--}}
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                {!! Form::select('zone',
                           (['' => 'Select zone'] +$zone ),
                             old("zone",""),
                               ['class' => 'md-input parent zone','id'=>"parent",'placeholder'=>'select zone','required'=>"required",'v-model'=>'zone','v-form-ctrl'=>'','v-select'=>''] )  !!}

                                <p class="uk-text-danger uk-text-small"  v-if="applicationForm.zone.$error.required" >Zone is required</p>

                            </div>
                        </div>

                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                <div class="uk-input-group">
                                    <span class="uk-input-group-addon"><i class="uk-input-group-icon uk-icon-calendar"></i></span>
                                    <input type="text"  style="" data-uk-datepicker="{format:'YYYY-MM-DD'}" value="{{ old("from_date") }}" name="from_date" v-model="from_date" v-form-ctrl="" id="invoice_dp" class="md-input" placeholder="From date? " required>
                                    <p class="uk-text-danger uk-text-small"  v-if="applicationForm.from_date.$error.required" >Start date is required</p>

                                </div>
                            </div>
                        </div>

                        <div class="uk-width-medium-1-5">

                            <div class="uk-margin-small-top">
                                <div class="uk-input-group">
                                    <span class="uk-input-group-addon"><i class="uk-input-group-icon uk-icon-calendar"></i></span>
                                    <input type="text" style="" data-uk-datepicker="{format:'YYYY-MM-DD'}" value="{{ old("to_date") }}" name="to_date"  v-model="to_date" v-form-ctrl="" class="md-input" placeholder="To date?" required>
                                    <p class="uk-text-danger uk-text-small"  v-if="applicationForm.to_date.$error.required" >End date is required</p>

                                </div>
                            </div>
                        </div>


                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">

                                <button  v-show="applicationForm.$valid"  class="md-btn   md-btn-small md-btn-primary uk-margin-small-top actions" type="submit">Download Excel</button>

                            </div>
                        </div>






                    </div>
                    <center>

                    </center>

                </form>
            </div>
        </div>
    </div>


@endsection
@section('js')

    <script src="{!! url('public/assets/js/select2.full.min.js') !!}"></script>
    <script>
        $(document).ready(function(){
            $('select').select2({ width: "resolve" });


        });


    </script>
    <script>


        //code for ensuring vuejs can work with select2 select boxes
        Vue.directive('select', {
            twoWay: true,
            priority: 1000,
            params: [ 'options'],
            bind: function () {
                var self = this
                $(this.el)
                    .select2({
                        data: this.params.options,
                        width: "resolve"
                    })
                    .on('change', function () {
                        self.vm.$set(this.name,this.value)
                        Vue.set(self.vm.$data,this.name,this.value)
                    })
            },
            update: function (newValue,oldValue) {
                $(this.el).val(newValue).trigger('change')
            },
            unbind: function () {
                $(this.el).off().select2('destroy')
            }
        })


        var vm = new Vue({
            el: "body",
            ready : function() {
            },
            data : {


                options: [    ]

            },

        })

    </script>


@endsection