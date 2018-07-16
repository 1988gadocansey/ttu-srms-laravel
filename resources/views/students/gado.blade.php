@extends('layouts.app')
@section('content')
 <div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Custom Filter [Case Sensitive]</h3>
    </div>
    <div class="panel-body">
        <form method="POST" id="search-form" class="form-inline" role="form">

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="search name">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" class="form-control" name="email" id="email" placeholder="search email">
            </div>

           <div class="uk-width-1-1">
                                <button type="submit" class="md-btn md-btn-primary"><i class="fa fa-save" ></i>Save</button>
                            </div>
        </form>
    </div>
</div>
     
             
                <table class="uk-table uk-table-nowrap uk-table-hover" id="users-table"> 
        <thead>
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
        </thead>
    </table>
 
@endsection
@section('js')
 
<script>
    
 
 var oTable = $('#users-table').DataTable({
        
        processing: true,
        serverSide: true,
        ajax: {
            url:  "{!! route('datatables.data') !!}",
            data: function (d) {
                d.name = $('input[name=name]').val();
                d.email = $('input[name=email]').val();
            }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'created_at', name: 'created_at'},
            {data: 'updated_at', name: 'updated_at'}
        ]
    });

    $('#search-form').on('submit', function(e) {
        oTable.draw();
        e.preventDefault();
    });
</script>
@endsection