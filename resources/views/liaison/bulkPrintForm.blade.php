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
    <h5 class="heading_c">Liaison Office Bulk Printing Page</h5>
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
                                ['class' => 'md-input year','id'=>"parent",'placeholder'=>'select academic year','required'=>"required",'v-model'=>'year','v-form-ctrl'=>'','v-select'=>''] )  !!}
                                <p class="uk-text-danger uk-text-small"  v-if="applicationForm.year.$error.required" >Academic year is required</p>

                            </div>
                        </div>
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">

                                {!!  Form::select('semester', array('1'=>'1st sem','2'=>'2nd sem','3' => '3rd sem'), null, ['placeholder' => 'select semester','id'=>'parent','class'=>'md-input semester','required'=>"required",'v-model'=>'semester','v-form-ctrl'=>'','v-select'=>''],old("semester","")); !!}

                                <p class="uk-text-danger uk-text-small"  v-if="applicationForm.semester.$error.required" >Semester is required</p>

                            </div>
                        </div>

                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                {!! Form::select('level',
                           (['' => 'Select level'] +$levels ),
                             old("level",""),
                               ['class' => 'md-input parent level','id'=>"parent",'placeholder'=>'select level','required'=>"required",'v-model'=>'level','v-form-ctrl'=>'','v-select'=>''] )  !!}

                                <p class="uk-text-danger uk-text-small"  v-if="applicationForm.level.$error.required" >Level is required</p>

                            </div>
                        </div>






                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                {!! Form::select('program',
                              (['' => 'Select program'] + $program),
                                  null,
                                  ["required"=>"required",'class' => 'md-input program','v-model'=>'program','v-form-ctrl'=>'','v-select'=>''] )  !!}

                                <p class="uk-text-danger uk-text-small"  v-if="applicationForm.program.$error.required" >Program is required</p>

                            </div>
                        </div>

                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">

                                <select name="type" required="" class="'md-input type" v-model="type" v-form-ctrl="" v-select="">
                                    <option value="">Select Print type</option>
                                    <option value="1">Attachment Letter</option>
                                    <option value="2">Assumption of Duty</option>
                                    <option value="3">Semester Out</option>
                                </select>

                                <p class="uk-text-danger uk-text-small"  v-if="applicationForm.type.$error.required" >Print type is required</p>

                            </div>
                        </div>









                    </div>
                    <center>
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">

                                <button  v-show="applicationForm.$valid"  class="md-btn   md-btn-small md-btn-primary uk-margin-small-top actions" type="submit">Print</button>

                            </div>
                        </div>
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