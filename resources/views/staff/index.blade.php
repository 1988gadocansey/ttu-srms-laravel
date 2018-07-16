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

<div class="heading_c">Staff</div>
<div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content">
            <div class="uk-overflow-container">
                <table class="uk-table uk-table-hover uk-table-align-vertical uk-table-nowrap " id="gad"> 
                    <thead>
                        <tr>
                            <th>N<u>O</u></th>
                    <th>Name</th>
                    <th >Staff N<u>O</u></th>

                    <th>Designation</th>
                    <th>Department</th>
                    <th>Action</th>

                    </tr>
                    </thead>

                </table>
            </div>
            <div class="md-fab-wrapper">
                <a class="md-fab md-fab-small md-fab-accent md-fab-wave" href="#new_task" data-uk-modal="{ center:true }">
                    <i class="material-icons md-18">&#xE145;</i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')

<script>


    var oTable = $('#gad').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{!! route('staff.data') !!}"

        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'fullName', name: 'fullName'},
            {data: 'staffID', name: 'staffID'},
            {data: 'designation', name: 'designation'},
             {data: 'department', name: 'department'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });



</script>

@endsection