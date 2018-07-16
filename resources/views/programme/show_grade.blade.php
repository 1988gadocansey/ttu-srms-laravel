@extends('layouts.app')

 
@section('style')
 <style>
    .marks{
         
height: auto;
margin: 0;
padding: 4px;
line-height: 24px;
border: 1px solid rgba(0,0,0,.12);
color: #212121;
box-sizing: border-box;
-webkit-transition: height .1s ease;
transition: height .1s ease;
border-radius: 0;
-webkit-appearance: none;
    }
</style>
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
 
 <h5>Grade System for {{$type}} programs</h5>
 <div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content">
      <form  action='{{url("/update_grades")}}'  id="form" accept-charset="utf-8" method="POST" name="applicationForm"  v-form>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}"> 
          
                <div class="uk-overflow-container">
                    <center><span class="uk-text-success uk-text-bold">{!! $data->total()!!} Records</span></center>

                    <table class="uk-table uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id="ts_pager_filter">                     
                        <thead>
                            <tr>

                                <th>NO</th>
                                <th>Grade</th> 

                                <th>Lower Limit</th>
                                <th>Upper Limit</th>
                                <th>Value</th>
                                <th style="text-align: center">Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $count = 0; ?>
                            @foreach($data as $index=> $row) 
                            <?php $count++; ?>
                            <tr align="">

                                <td> {{ $data->perPage()*($data->currentPage()-1)+($index+1) }} </td>
                        <input type="hidden" name="key[]" value="{{$row->id}}"/>
                         <input type="hidden" name="type" value="{{$row->type}}"/>


                        <td>
                            {!! Form::select('grade[]', 
                                (['' => 'select grade'] +$grade ), 
                                  old("grade",""),
                                    ['class' => 'md-input gad','style'=>'width:120px','required'=>'','v-model'=>'grade','v-form-ctrl'=>'','v-select'=>''] )  !!}
	 
                        </td>
                        <td><input style="text-align: center;width:87px"name="lower[]" maxlength="4" class="marks" type="text" value="{{@$row->lower}}"/></td>
                        <td><input style="text-align: center;width:87px"name="upperLimit[]" maxlength="4"class="marks" type="text" value="{{@$row->upper}}"/></td>

                        <td><input style="text-align: center;width:87px"name="value[]"class="marks" maxlength="4" type="text" value="{{@$row->value}}"/></td>
                        <td style="text-align: center">{{@$row->type}}</td>


                        <input type="hidden" name="gradeOld[]" value="{{$row->grade}}"/>
                        <input type="hidden" name="lowerOld[]" value="{{$row->lower}}"/>
                        <input type="hidden" name="upperOld[]" value="{{$row->upper}}"/>
                        <input type="hidden" name="valueOld[]" value="{{$row->value}}"/>


                        </tr>
                        @endforeach
                        </tbody>

                    </table>

                    {!! (new Landish\Pagination\UIKit($data->appends(old())))->render() !!}
                </div>
                <center><div style="position: fixed;  bottom: 0px;left: 45%  ">
                        <p>
                            <input type="hidden" name="upper" value="{{$count++}}" id="upper" />

                            <button type="submit"  class="md-btn md-btn-success md-btn-small"><i class="fa fa-save" ></i>Update</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;


                        </p>
                    </div></center>
  </form>
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
 
<script src="{!! url('public/assets/js/select2.full.min.js') !!}"></script>
<script>
$(document).ready(function(){
  $('select').select2({ width: "resolve" });

  
});


</script>
 
@endsection