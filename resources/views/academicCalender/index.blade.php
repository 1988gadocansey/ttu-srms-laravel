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
 
 <form  action=""  id="form" accept-charset="utf-8" method="POST" name="applicationForm"  v-form>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}"> 
 <h5>Academic Calender</h5>
 <div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content">
            <div class="uk-overflow-container" id='print'>
                <center><span class="uk-text-success uk-text-bold">{!! $data->total()!!} Records</span></center>
                    <table class="uk-table uk-table-hover uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id="ts_pager_filter"> 
                        <thead>
                            <tr>
                                <th class="filter-false remove sorter-false">NO</th>
                                <th>Year</th>
                                <th>Sem</th>
                                <th>Register</th>
                                <th>Exam</th>
                                <th>Exam</th>
                                <th>Attachment</th>
                                <th>Assesment</th>
                                <th>Assesment</th>
                                <th>Result View</th>
                
                                <th  class="filter-false remove sorter-false uk-text-center">ACTION</th>   
                     
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($data as $index=>$row) 
 
                            <tr align="">
                                <td> {{ $data->perPage()*($data->currentPage()-1)+($index+1) }} </td>
                                <td> 
                                    <div class="uk-input-group">

                                        <div class="md-input-wrapper md-input-filled"><input type="text" id="year" name="year" v-form-ctrl  class="md-input uk-text-primary uk-text-bold"    v-model="year" value="{{ @$row->YEAR}}"/><span class="md-input-bar"></span>
                                        </div>         

                                    </div>
                                </td>
                                <td> <div class="uk-input-group col-md-4">

                                        <input type="text" id="sem" name="sem" v-form-ctrl  class="md-input uk-text-primary uk-text-bold col-md-4"    v-model="sem" value="{{ @$row->SEMESTER}}"/>         

                                    </div>
                                </td>
                        
                                <td class="uk-text-center">
                                    @if($row->STATUS==1)<span class="uk-badge uk-badge-success">Opened</span>
                                    <span> <a href='{{url("fireCalender/$row->ID/id/closeReg/action")}}' ><i title='Click to close online registration' onclick="return confirm('Are you sure you want to close online registration?' );" class="md-icon material-icons uk-text-danger">power_settings_new</i></span>
                                    @else <span class="uk-badge uk-badge-danger">Closed</span><span> <a href='{{url("fireCalender/$row->ID/id/openReg/action")}}' ><i title='Click to open online registration' onclick="return confirm('Are you sure you want to open online registration?' );" class="md-icon material-icons uk-text-success">power_settings_new</i></span> @endif
                                </td>
                     
                                <td>
                                    <div class="uk-input-group col-md-4">

                                        <input type="text" id="upload" name="upload" v-form-ctrl  class="md-input uk-text-primary uk-text-bold col-md-4"    v-model="upload" value="{{ @$row->RESULT_DATE}}"/>         

                                    </div>
                                </td>


                                <td class="uk-text-center">@if($row->ENTER_RESULT==1)<span class="uk-badge uk-badge-success">Opened</span><span> <a href='{{url("fireCalender/$row->ID/id/closeMark/action")}}' ><i title='Click to close entering of marks' onclick="return confirm('Are you sure you want to close entering of marks?' );" class="md-icon material-icons uk-text-danger">power_settings_new</i></span> @else <span class="uk-badge uk-badge-danger">Closed</span><span> <a href='{{url("fireCalender/$row->ID/id/openMark/action")}}' ><i onclick="return confirm('Are you sure you want to open entering of marks?' );" title='Click to open online registration'  class="md-icon material-icons uk-text-success">power_settings_new</i></span> @endif
                                </td>

                                <td class="uk-text-center">@if($row->LIAISON==1)<span class="uk-badge uk-badge-success">Opened</span><span> <a href='{{url("fireCalender/$row->ID/id/closeLia/action")}}' ><i title='Click to close registration for attachment' onclick="return confirm('Are you sure you want to close registration for attachment?' );" class="md-icon material-icons uk-text-danger">power_settings_new</i></span> 
                                @else <span class="uk-badge uk-badge-danger">Closed</span><span> <a href='{{url("fireCalender/$row->ID/id/openLia/action")}}' ><i onclick="return confirm('Are you sure you want to open registration for attachment?' );" title='Click to open registration for attachment'  class="md-icon material-icons uk-text-success">power_settings_new</i></span> @endif
                                </td>

                                <td> 
                                    <div class="uk-input-group col-md-4">

                                        <input type="text" id="qa" name="qa" v-form-ctrl  class="md-input uk-text-primary uk-text-bold col-md-4"    v-model="qa" value="{{ @$row->QA}}"/>         

                                    </div>
                                </td>

                                <td class="uk-text-center">@if($row->QAOPEN==1)<span class="uk-badge uk-badge-success">Opened</span><span> <a href='{{url("fireCalender/$row->ID/id/closeQa/action")}}' ><i title='Click to close lecturer assesment' onclick="return confirm('Are you sure you want to close lecturer assesment?' );" class="md-icon material-icons uk-text-danger">power_settings_new</i></span> 
                                @else <span class="uk-badge uk-badge-danger">Closed</span><span> <a href='{{url("fireCalender/$row->ID/id/openQa/action")}}' ><i onclick="return confirm('Are you sure you want to open lecturer assesment?' );" title='Click to open lecturer assesment'  class="md-icon material-icons uk-text-success">power_settings_new</i></span> @endif
                                </td>

                                <td> 
                                    <div class="uk-input-group col-md-4">

                                        <input type="text" id="result" name="result" v-form-ctrl  class="md-input uk-text-primary uk-text-bold col-md-4"    v-model="result" value="{{ @$row->RESULT_BLOCK}}"/>         

                                    </div>
                                </td>
                                <td class="uk-text-center">
                                    <input type="submit" value="Save" id='save'v-show="applicationForm.$valid"  class="md-btn   md-btn-success uk-margin-small-top">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                                    
                    </table>
           {!! (new Landish\Pagination\UIKit($data->appends(old())))->render() !!}
         
            </div>
            
        </div>
    </div>
 </div>
@endsection
@section('js')
 
 
@endsection
</form>