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
    @if(Session::has('success'))
        <div style="text-align: center" class="uk-alert uk-alert-success" data-uk-alert="">
            {!! Session::get('success') !!}
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
    <div class="uk-width-xLarge-1-10">
        <div class="md-card">
            <div class="md-card-content" style="">

                <h5 class=" ">Create Address to</h5>
                <form    id="form" accept-charset="utf-8" method="POST" name="applicationForm"  v-form>
                    <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                    <table id="paymentTable" class="uk-table"border="0" style="font-weight:bold">
                        <tr id="paymentRow" payment_row="payment_row">
                            <td valign="top">Addressed to  &nbsp;<input type="text"   class="md-input md-input" required=""  placeholder="e.g. Director"  v-model='names[]' v-form-ctrl='' name="names[]"  ></td>







                            <td valign="top" id="insertPaymentCell"><button  type="button" id="insertPaymentRow" class="md-btn md-btn-primary md-btn-small " title='click to add more ' ><i class="sidebar-menu-icon material-icons">add</i></button></td></tr>

                    </table>
                    <table align="center">

                        <tr><td><input type="submit" value="Save" id='save'v-show="applicationForm.$valid"  class="md-btn   md-btn-success uk-margin-small-top">
                                <input type="reset" value="Cancel" class="md-btn   md-btn-default uk-margin-small-top">
                            </td></tr></table>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')

        
         