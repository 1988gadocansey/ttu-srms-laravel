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
 
 <h5 class="uk-heading_c">Classing System for all Programmes</h5>
 <div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content">
      
             <div class="uk-overflow-container">
           <center><span class="uk-text-success uk-text-bold">{!! $data->total()!!} Records</span></center>
       
           <table class="uk-table uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id="ts_pager_filter">                     
               <thead>
                   <tr>

                       <th>No</th>
                       <th>Lower Boundary</th> 
 
                       <th>Upper Boundary</th>
                       <th> Class</th>
 
                   </tr>
               </thead>
               <tbody>

                   @foreach($data as $index=> $row) 

                   <tr align="">

                       <td> {{ $data->perPage()*($data->currentPage()-1)+($index+1) }} </td>

                       <td> {{ @$row->lowerBoundary }}</td> 
                       <td> {{ @$row->upperBoundary }}</td> 
                       <td> {{ @$row->class }}</td> 
                        
                        

                   </tr>
                   @endforeach
               </tbody>

           </table>
           
          {!! (new Landish\Pagination\UIKit($data->appends(old())))->render() !!}
     </div>
<div class="md-fab-wrapper">
        <a class="md-fab md-fab-small md-fab-accent md-fab-wave" title="create class" href="{!! url('/classes/create') !!}">
            <i class="material-icons md-18">&#xE145;</i>
        </a>
    </div>
 </div>
    </div>
 </div>
@endsection
@section('js')
 
 
 
@endsection