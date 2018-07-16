@extends('layouts.printlayout')


@section('content')
    @inject('sys', 'App\Http\Controllers\SystemController')

    <div class="containers">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">


                    <div class="panel-body" id='gad'>
                        <a onclick="javascript:printDiv('print')" class="md-btn md-btn-flat md-btn-flat-primary md-btn-wave">Click
                            to print form</a>
                        <div id='print'>
                            {{$data}}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection
<script>
    window.print();
</script>