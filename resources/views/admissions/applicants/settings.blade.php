@extends('layouts.app')



@section('style')

<script src="{!! url('public/assets/js/jquery.min.js') !!}"></script>

<script src="{!! url('public/assets/js/jquery-ui.min.js') !!}"></script>

<style>

</style>
@endsection
@section('content')
<div class="md-card-content">
    <div style="text-align: center;display: none" class="uk-alert uk-alert-success" data-uk-alert="">

    </div>



    <div style="text-align: center;display: none" class="uk-alert uk-alert-danger" data-uk-alert="">

    </div>

</div>
@inject('sys', 'App\Http\Controllers\SystemController')

<div align="center">
    <h4 class="heading_b uk-margin-bottom">Applicant Admission Form Settings</h4>

    <h5 class="uk-text-upper"> Form settings for {{$data[0]->NAME}}</h5>
    <hr>

    <form id='form' method="POST" action="{{ url('/admissions/applicant/fire') }}" accept-charset="utf-8"  name="applicationForm"  v-form>
        <input type="hidden" name="_token" value="{!! csrf_token() !!}"> 


        <div class="uk-grid" data-uk-grid-margin data-uk-grid-match="{target:'.md-card-content'}">
            <div class="uk-width-medium-1-2">
                <div class="md-card">
                    <div class="md-card-content">
                        <div class="uk-overflow-container">
                            <table>
                                <tr>
                                    <td  align=""> <div  align="right" class=" ">Block / Unblock Admission Form</div></td>
                                    <td>
                                        <select name="action" required="" class="md-input" v-form-ctrl='' v-model='action' v-select=''>
                                            <option>Select action type</option>
                                            <option value="0">Unblock</option>

                                            <option value="1">Block</option>

                                        </select>
                                        <p class="uk-text-danger uk-text-small"  v-if="applicationForm.action.$error.required" >Action   is required</p>


                                    </td>
                                </tr>

                            </table>
                            <p></p>

                            <center>

                                <button  v-show="applicationForm.$valid" type="button" class="md-btn md-btn-primary action"><i class="fa fa-save" ></i>Submit</button>


                            </center>
                        </div>
                    </div>
                </div>



            </div>
            <div class="uk-width-medium-1-2">
                <div class="md-card">
                    <div class="md-card-content">
                        <table>
                            <tr>
                                <td>
                                    <table>


                                        <tr>
                                            <td  align=""> <div  align="right" >Admission Number:</div></td>
                                            <td>
                                                {{ $data[0]->APPLICATION_NUMBER}}
                                                <input type="hidden" name="student" id="student" value="{{ $data[0]->APPLICATION_NUMBER}}" />

                                            </td>
                                        </tr>

                                        <tr>
                                            <td  align=""> <div  align="right" >Full Name:</div></td>
                                            <td>
                                                {{ $data[0]->NAME}}
                                                <input type="hidden" name="name" id="name" value="{{ $data[0]->NAME}}" />

                                            </td>
                                        </tr>

                                        <tr>
                                            <td  align=""> <div  align="right" >First Choice:</div></td>
                                            <td>
                                                {{ $sys->getProgram($data[0]->FIRST_CHOICE)}}
                                            </td>
                                        </tr>




                                        <tr>
                                            <td align=""> <div  align="right" class="uk-text-success">Phone N<u>o</u>:</div></td>
                                            <td>

                                                {{ $data[0]->PHONE}}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td valign="top">
                                    <img   style="width:150px;height: auto;"  <?php
                                           $pic = $data[0]->APPLICATION_NUMBER;
                                           ?>   src="http://application.ttuportal.com/public/uploads/photos/{{$data[0]->APPLICATION_NUMBER}}.jpg" alt="  Affix student picture here"    />
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

            </div>




    </form>
    @endsection

    @section('js')

    <script>
        $(document).ready(function(){
$('.action').on('click', function(e){


var applicant = $('#name').val();
       
        UIkit.modal.confirm("Are you sure of what you are about? "
                , function(){
                modal = UIkit.modal.blockUI("<div class='uk-text-center'>Executing action on " + applicant + "<br/><img class='uk-thumbnail uk-margin-top' src='{!! url('public/assets/img/spinners/spinner.gif')  !!}' /></div>");
                        //setTimeout(function(){ modal.hide() }, 500) })()            
                        $.ajax({

                        type: "POST",
                                url:"{{url('/admissions/applicant/fire')}}",
                                data: $('#form').serialize(), //your form data to post goes 
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
    <script src="{!! url('public/assets/js/select2.full.min.js') !!}"></script>
    <script>
        $(document).ready(function(){
$('select').select2({ width: "resolve" });
});    </script>
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
                                self.vm.$set(this.name, this.value)
                                        Vue.set(self.vm.$data, this.name, this.value)
                                })
                        },
                        update: function (newValue, oldValue) {
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

</div>

@endsection

 