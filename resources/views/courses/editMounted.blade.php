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

  

  @inject('sys', 'App\Http\Controllers\SystemController')

  

 <h5 class="heading_c">Edit Mounted Courses</h5>

 <div class="uk-width-xLarge-1-1">

    <div class="md-card">

        <div class="md-card-content">

    <form  action=""  id="form" accept-charset="utf-8" method="POST" name="applicationForm"  v-form>

                <input type="hidden" name="_token" value="{!! csrf_token() !!}"> 

  

   <div class="uk-overflow-container" id='print'>

         <center><span class="uk-text-success uk-text-bold">{!! $data->total()!!} Records</span></center>

                <table class="uk-table uk-table-hover uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id="ts_pager_filter"> 

               <thead>

                 <tr>

                     <th class="filter-false remove sorter-false" data-priority="6">NO</th>

                      <th>COURSE</th>

                      

                     <th>PROGRAM</th> 

                     <th style="">CREDIT</th>



                     <th style="">LEVEL</th>

                     <th style="">SEMESTER</th>

                      

                      <th style="">LECTURER</th>

                      <th style="">TYPE</th>

                </tr>

             </thead>

      <tbody> <?php $count=0;?>

                                        

                                         @foreach($data as $index=> $row) 

                                           <input name="key" value="{{$ID}}" id="" />

                            

                                          <?php $count++;?>

                                        <tr align="">

                                            <td> {{ $data->perPage()*($data->currentPage()-1)+($index+1) }} </td>

                                              <td>

                                                  

                                               {{$row->course->COURSE_NAME}}  - {{$row->course->COURSE_CODE}}

                                              

                                              </td>

                                              

                                            <td> {{$sys->getProgram($row->PROGRAMME)}} </td>

                                             

                                            

                                            

                                             <td>

                                                  <select placeholder='select credit' class="form-control" style="" name="credit[]" required="required" class= 'md-input'v-model='course' v-form-ctrl='' v-select=''>

                                                     

                                                   



                                                   <option  value="1" <?php

                                                                            if ($row->COURSE_CREDIT ==1) {

                                                                                echo "selected='selected'";

                                                                            }

                                                                            ?>     >1</option>

                                                  <option value="2" <?php

                                                                            if ($row->COURSE_CREDIT ==2) {

                                                                                echo "selected='selected'";

                                                                            }

                                                                            ?>  >2</option>

                                                   <option value="3"<?php

                                                                            if ($row->COURSE_CREDIT ==3) {

                                                                                echo "selected='selected'";

                                                                            }

                                                                            ?>  >3</option>

                                                    <option value="4" <?php

                                                                            if ($row->COURSE_CREDIT ==4) {

                                                                                echo "selected='selected'";

                                                                            }

                                                                            ?> >4</option>

                                                     

                                                    </select>

                                             </td>

                                            

                                                              <td>

                                                  <select placeholder='select year' class="form-control" style="" name="level[]" required="required" class= 'md-input'v-model='course' v-form-ctrl='' v-select=''>

                                                      <option  <?php

                                                                            if ($row->COURSE_LEVEL =="100H") {

                                                                                echo "selected='selected'";

                                                                            }

                                                                            ?>   value="100H">100H</option>

                                                    <option  <?php

                                                                            if ($row->COURSE_LEVEL =="200H") {

                                                                                echo "selected='selected'";

                                                                            }

                                                                            ?>   value="200H">200H</option>



                                                   <option  <?php

                                                                            if ($row->COURSE_LEVEL =="300H") {

                                                                                echo "selected='selected'";

                                                                            }

                                                                            ?>   value="300H">300H</option>

                                                  <option <?php

                                                                            if ($row->COURSE_LEVEL =="100NT") {

                                                                                echo "selected='selected'";

                                                                            }

                                                                            ?> value="100NT">100NT</option>

                                                   <option<?php

                                                                            if ($row->COURSE_LEVEL =="200NT") {

                                                                                echo "selected='selected'";

                                                                            }

                                                                            ?>  value="200NT">200NT</option>

                                                    <option value="100BTT" <?php

                                                                            if ($row->COURSE_LEVEL =="100BTT") {

                                                                                echo "selected='selected'";

                                                                            }

                                                                            ?> >100BTT</option>

                                                      <option value="200BTT" <?php

                                                                            if ($row->COURSE_LEVEL =="200BTT") {

                                                                                echo "selected='selected'";

                                                                            }

                                                                            ?> >200BTT</option>

                                                    <option value="500MT" <?php

                                                                            if ($row->COURSE_LEVEL =="500MT") {

                                                                                echo "selected='selected'";

                                                                            }

                                                                            ?> >500MT</option>

                                                    <option value="600MT" <?php

                                                                            if ($row->COURSE_LEVEL =="600MT") {

                                                                                echo "selected='selected'";

                                                                            }

                                                                            ?> >600MT</option>

                                                    </select>

                                             </td>

                                            

                                            

                                            

                                           <td>

                                               <select placeholder='select semester' class="form-control" style="" name="semester[]" required="required" class= 'md-input'v-model='course' v-form-ctrl='' v-select=''>

                                           

                                                    <option value="1" <?php

                                                                            if ($row->COURSE_SEMESTER =="1") {

                                                                                echo "selected='selected'";

                                                                            }

                                                                            ?> >1</option>

                                                    <option value="2" <?php

                                                                            if ($row->COURSE_SEMESTER =="2") {

                                                                                echo "selected='selected'";

                                                                            }

                                                                            ?> >2</option>

                                                    <option value="3" <?php

                                                                            if ($row->COURSE_SEMESTER =="3") {

                                                                                echo "selected='selected'";

                                                                            }

                                                                            ?> >3</option>

                                                </select>

                                           </td>

                                            <td> {!! Form::select('lecturer[]',

                                            (['' => 'Select lecturer'] + $lecturer), 

                                                null, 

                                                ["required"=>"required",'class' => 'md-input','v-model'=>'lecturer','v-form-ctrl'=>'','v-select'=>''] )  !!}

                                              </td>

                                             

                                              

                                                 <td>

                                               <select placeholder='select course type' class="form-control" style="" name="type[]" required="required" class= 'md-input'v-model='course' v-form-ctrl='' v-select=''>

                                           

                                                    <option value="Core" <?php

                                                                            if ($row->COURSE_TYPE =="Core") {

                                                                                echo "selected='selected'";

                                                                            }

                                                                            ?> >Core</option>

                                                    <option value="Elective" <?php

                                                                            if ($row->COURSE_TYPE =="Elective") {

                                                                                echo "selected='selected'";

                                                                            }

                                                                            ?> >Elective</option>

                                                     

                                                </select>

                                           </td>

                                              

                                              

                                        </tr>

                                      

                                         @endforeach

                                    </tbody>

                                    

                             </table>

           {!! (new Landish\Pagination\UIKit($data->appends(old())))->render() !!}

     </div>

                

        <center><div style="position: fixed;  bottom: 0px;left: 45%  ">

                        <p>

                             <input type="hidden" name="upper" value="{{$count++}}" id="upper" />

                             

                                  <button type="submit"  class="md-btn md-btn-success md-btn-small"><i class="fa fa-save" ></i>Save</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                            

                                   

                        </p>

                    </div></center> 

     </div>

 

 </div>

</div>

  

@endsection

@section('js')

 <script type="text/javascript">

      

$(document).ready(function(){

 

$(".parent").on('change',function(e){

 

   $("#group").submit();

 

});

});



</script>

 <script src="{!! url('public/assets/js/select2.full.min.js') !!}"></script>

<script>

$(document).ready(function(){

  $('select').select2({ width: "resolve" });



  

});





</script>

 <!--  notifications functions -->

    <script src="public/assets/js/components_notifications.min.js"></script>

@endsection