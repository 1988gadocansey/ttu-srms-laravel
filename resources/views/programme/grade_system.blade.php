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
 
 <h5>Grading System Categories</h5>
 <div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content">
      
             <div class="uk-overflow-container">
           <center><span class="uk-text-success uk-text-bold">{!! $data->total()!!} Records</span></center>
       
           <table class="uk-table uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id="ts_pager_filter">                     <thead>
               <thead>
                   <tr>

                       <th>NO</th>
                       <th>NAME</th> 
 
                       <th>ACTION</th>
 
                   </tr>
               </thead>
               <tbody>

                   @foreach($data as $index=> $row) 

                   <tr align="">

                       <td> {{ $data->perPage()*($data->currentPage()-1)+($index+1) }} </td>

                       <td> {{ @$row->type }}</td> 

                       <td>


<!--                           {!!Form::open(['action' =>['GradeController@destroy', 'id'=>$row->id], 'method' => 'DELETE','name'=>'myform' ,'style' => 'display: inline;'])  !!}

                           <button type="submit" onclick="return confirm('Are you sure you want to delete this fee component??')" class="md-btn  md-btn-danger md-btn-small   md-btn-wave-light waves-effect waves-button waves-light" ><i  class="sidebar-menu-icon material-icons md-18">delete</i></button>

                           {!! Form::close() !!}-->

                            <a href=""><i class="md-icon material-icons">&#xE254;</i></a>
                            <a href='{{url("/grade_system/$row->type/slug")}}'><i class="md-icon material-icons">&#xE88F;</i></a>

                       </td>

                   </tr>
                   @endforeach
               </tbody>

           </table>
           
          {!! (new Landish\Pagination\UIKit($data->appends(old())))->render() !!}
     </div>
<div class="md-fab-wrapper">
        <a class="md-fab md-fab-small md-fab-accent md-fab-wave" href="{!! url('/create_grade') !!}">
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
            url:  "{!! route('programmes.data') !!}"
             
        },
        columns: [
        {data: 'ID', name: 'ID'},
            
            {data: 'DEPTCODE', name: 'DEPTCODE'},
            {data: 'PROGRAMMECODE', name: 'PROGRAMMECODE'},
            {data: 'PROGRAMME', name: 'PROGRAMME'},
            {data: 'AFFILAITION', name: 'AFFILAITION'},
            {data: 'DURATION', name: 'DURATION'},
          
              {data: 'MINCREDITS', name: 'MINCREDITS'},
               {data: 'MAXI_CREDIT', name: 'MAXI_CREDIT'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
    

    
</script>
 
@endsection