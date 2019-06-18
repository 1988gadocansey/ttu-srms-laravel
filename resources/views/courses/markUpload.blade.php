@extends('layouts.app')


@section('style')
<style>
    .md-card{
        width: auto;

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
 
    @if(Session::has('error'))
            <div style="text-align: center" class="uk-alert uk-alert-danger" data-uk-alert="">
                {!! Session::get('error') !!}
            </div>
 @endif
  <h3 class="heading_c uk-margin-bottom">Upload   Grades (Excel file format only)  </h3>
        
<div class="uk-width-xLarge-1-1">
    
    <div class="md-card">
        <div class="md-card-content" style="">

             <form  action="{{url('/upload_marks')}}" enctype="multipart/form-data" id="form" accept-charset="utf-8" method="POST" name="applicationForm"  v-form>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
               <div class="uk-grid" data-uk-grid-margin>
                                  <div class="uk-width-medium-1-6">
                                    <div class="uk-form-row">
                                        <label class="">Level</label>
                                        <p></p>
                   <!-- {{ Form::select('sem', array(''=>'select semester',$sem=>$sem), null, ["required"=>"required",'class' => 'md-input label-fixed','v-model'=>'semester','v-form-ctrl'=>'','v-select'=>'']) }} -->
                                            <select name="level" id="level" required>
                                                <option value="" disabled selected>Select Level</option>
                                                <option value="100">Level 100</option>
                                                <option value="200">Level 200</option>
                                                <option value="300">Level 300</option>
                                                <option value="400">Level 400</option>
                                             </select>
                                    </div>
                                  </div>
                                  <div class="uk-width-medium-1-3">
                                    <div class="uk-form-row">
                                      <table align="left">
                                                <tr><td><label>Masters</label><br/><p></p>
                                                &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="combine" name="combine" value="MT" required>
                                                </td>
                                                <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                                <td><label>Btech (4yrs)</label><br/><p></p>
                                                &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="combine" name="combine" value="BT">
                                                </td>
                                                <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                                <td><label>Btech top up</label><br/><p></p>
                                                &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="combine" name="combine" value="BTT">
                                                </td>
                                                <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                                <td><label>HND</label><br/><p></p>
                                                <input type="radio" id="combine" name="combine" value="H">
                                                </td>
                                                <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                                <td><label>Diploma</label><br/><p></p>
                                                &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="combine" name="combine" value="NT">
                                                </td>
                                                </tr>

                                            </table>
                                        <!-- <label>Academic year</label>
                                        <p></p> -->
                     <!-- {{ Form::select('year', array(''=>'select year',$year=>$year), null, ["required"=>"required",'class' => 'md-input label-fixed','v-model'=>'year','v-form-ctrl'=>'','v-select'=>'']) }} -->
                                    </div>
                                  </div>

                           <!-- <div class="uk-width-medium-1-4">
                                <label>Level</label>
                                <p></p>
                               {!! Form::select('level',
                                ($level ),
                                  old("level",""),
                                    ['class' => 'md-input parent','required'=>"required",'placeholder'=>'select level'] )  !!}

                            </div> -->




                            <div class="uk-width-medium-1-6">
                            <div class="uk-form-row">

                                <label>Excel files only</label>
                                 <p></p>
                               <input type="file"  class="md-input   md-input-success " required=""  name="file"/>
                            </div>

                        </div>


                        <div class="uk-width-medium-2-5">
                            <div class="uk-form-row">

                                <label>Course</label>
                                <p></p>
                                  <select placeholder='type courses' class="form-control" style="width:490px" name="course" required="required" class= 'md-input'v-model='course' v-form-ctrl='' v-select=''>

                                           <option value="" disabled selected hidden>select course</option>


                                           @foreach($courses as $item)



                                          <option value="{{$item->COURSE_CODE}}">{{$item->COURSE_NAME}} - {{ $item->COURSE_CODE }} - Sem{{ $item->COURSE_SEMESTER}}</option>

                                        @endforeach

                                      </select>
                            </div>
                            </div>
                        <div class="uk-width-medium-1-5">
                            <div class="uk-form-row">
                              <p></p><p></p><p></p><p></p>
                                <table align="center">

        <tr><td><input type="submit" value="Upload" id='savess'   class="md-btn   md-btn-success uk-margin-small-top">
      <input type="reset" value="Clear" class="md-btn   md-btn-default uk-margin-small-top">
    </td></tr></table>
                            </div>

                        </div>
                    </div>
                    <p></p>

            </form>
            <progress id="js-progressbar" class="uk-progress" value="0" max="100" hidden></progress>

        </div>

    </div>
</div>
<!-- <div class="iframe" style="left:0; width:50%; height:50%;" >
        <iframe src="{{URL::to('/transcript')}}" style="margin-top: -100" width="100%" height="300"></iframe>
        </div> -->
@endsection

@section('js')
<script>
        $(document).ready(function(){
            $("#form").on("submit",function(event){
                event.preventDefault();


                var bar = document.getElementById('js-progressbar');

                UIkit.upload('.js-upload', {

                    //url: '/echo/json/',
                    'url': '/echo/htmdl/',
                    'data-type': 'json',
                    'name': 'json',
                    'multiple': false,
                    'params': {
                        'json': '{"name":"John","age":30,"car":null}',
                        'html': '<p>this is html</p>'
                    },

                    beforeSend: function() {
                        console.log('beforeSend', arguments);
                    },
                    beforeAll: function() {
                        console.log('beforeAll', arguments);
                    },
                    load: function() {
                        console.log('load', arguments);
                    },
                    error: function() {
                        console.log('error', arguments);
                    },
                    complete: function() {
                        console.log('complete', arguments);
                    },

                    loadStart: function(e) {
                        console.log('loadStart', arguments);

                        bar.removeAttribute('hidden');
                        bar.max = e.total;
                        bar.value = e.loaded;
                    },

                    progress: function(e) {
                        console.log('progress', arguments);

                        bar.max = e.total;
                        bar.value = e.loaded;
                    },

                    loadEnd: function(e) {
                        console.log('loadEnd', arguments);

                        bar.max = e.total;
                        bar.value = e.loaded;
                    },

                    completeAll: function(HttpRequest) {
                        console.log('completeAll', arguments);

                        setTimeout(function() {
                            bar.setAttribute('hidden', 'hidden');
                        }, 1000);

                        console.log('Upload Completed');
                        console.log('Response', HttpRequest.response);

                    }

                });




            });




    });
</script>
<script src="{!! url('public/assets/js/select2.full.min.js') !!}"></script>
  <script>
$(document).ready(function(){
  $('select').select2({ width: "resolve" });


});


</script>
 
@endsection    