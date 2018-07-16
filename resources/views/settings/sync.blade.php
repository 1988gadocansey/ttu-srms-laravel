@extends('layouts.app')


@section('style')
@inject('obj', 'App\Http\Controllers\SystemController')
@endsection
@section('content')
<div class="md-card-content">
    <div style="text-align: center;display: none" class="uk-alert uk-alert-success" data-uk-alert="">

    </div>



    <div style="text-align: center;display: none" class="uk-alert uk-alert-danger" data-uk-alert="">

    </div>
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
<h4 class="heading_c uk-margin-bottom">System Settings</h4><br/>
      <h4 class="uk-text-success uk-heading_c">Fetch data from local system to online</h4>
 
<div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content">
                 
<form action="{{url('accept/bulk')}}" class="uk-form-stacked" id="page_settings" accept-charset="utf-8" method="POST" name="applicationForm" >
         <input type="hidden" name="_token" value="{!! csrf_token() !!}"> 

        

                    <div class="uk-grid" data-uk-grid-margin="">
                        <div class="uk-width-medium-1-1">
                            <div class="uk-margin-small-top">
                                {!! Form::select('table', 
                                ($data ), 
                                old("table",""),
                                ['class' => 'md-input', 'placeholder'=>'select table to sync to online portal'] )  !!}
                            </div>
                        </div>
                    </div>
         <div class="uk-grid" data-uk-grid-margin="">
                        <div class="uk-width-medium-1-5">
                            <div class="uk-margin-small-top">
                                <table align="center">

                                    <tr><td><input type="button" value="Sync data"    class="md-btn   md-btn-success uk-margin-small-top save">

                                        </td></tr></table>
                            </div>
                        </div>
                    </div>
                 
            
    


</form>
</div>
        </div>
        
@endsection
@section('js')
<script>
    $(document).ready(function () {
        $('.save').on('click', function (e) {




            UIkit.modal.confirm("Are you sure you want to send data to student portal  ?? "
                    , function () {
                        modal = UIkit.modal.blockUI("<div class='uk-text-center'>Ok synchronization has started   <br/><img class='uk-thumbnail uk-margin-top' src='{!! url('public/assets/img/spinners/spinner.gif')  !!}' /></div>");
                        //setTimeout(function(){ modal.hide() }, 500) })()            
                        $.ajax({

                            type: "POST",
                            url: "{{url('/accept/bulk')}}",
                            data: $('#page_settings').serialize(), //your form data to post goes 
                            dataType: "json"
                        }).done(function (data) {
                            //  var objData = jQuery.parseJSON(data);
                            modal.hide();
                            //                                    
                            //                                     UIkit.modal.alert("Action completed successfully");

                            //alert(data.status + data.data);
                            if (data.status == 'success') {
                                $(".uk-alert-success").show();
                                $(".uk-alert-success").text(data.status + " " + data.message);
                                $(".uk-alert-success").fadeOut(4000);
                                //window.location.href="{{url('/teachers/subject/allocation')}}";
                            } else {
                                $(".uk-alert-danger").show();
                                $(".uk-alert-danger").text(data.status + " " + data.message);
                                $(".uk-alert-danger").fadeOut(4000);
                            }


                        });
                    }
            );
        });


    });</script>
<!--  settings functions -->
 <script src="{!! url('public/assets/js/select2.full.min.js') !!}"></script>
<script>
$(document).ready(function(){
  $('select').select2({ width: "resolve" });

  
});


</script>

@endsection