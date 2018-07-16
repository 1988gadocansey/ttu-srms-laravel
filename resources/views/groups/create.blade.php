@extends('layouts.app')


@section('style')
<style>
    table td{
     border:none;   
    }
    
</style>
  
@endsection
@section('content')
 @inject('sys', 'App\Http\Controllers\SystemController')
  
<div class="uk-width-xLarge-1-10">
  <h2 class="heading_b uk-margin-bottom">Create Class Groupings</h2>
<!-- <table class="uk-table uk-table-hover uk-table-align-vertical uk-table-nowrap " id=""> 
                                  <thead>
                                        <tr>
                                             <th class="uk-width-1-10">PROGRAMS</th>
                                             
                                            <th>LEVEL 100 </th>
                                            <th>LEVEL 200 </th>
                                            <th>LEVEL 300 </th>
                                            <th>LEVEL 400 </th>
                                            <th>BTECH TOPUP LEVEL 100  </th>
                                            <th>BTECH TOPUP LEVEL 200   </th>
 
                                        </tr>
                                    </thead>
                                    <tbody>
                                         @foreach($data as $index=> $row) 
                                     <tr>

                                         <td>{{$row->PROGRAMME}}</td>
                                         <td class=''>{{@$sys->getStudentsTotalPerProgramLevel($row->PROGRAMMECODE,'1')}}</td>
                                         <td class=''>{{@$sys->getStudentsTotalPerProgramLevel($row->PROGRAMMECODE,'2')}}</td>
                                         <td class=''>{{@$sys->getStudentsTotalPerProgramLevel($row->PROGRAMMECODE,'3')}}</td>
                                         <td class=''>{{@$sys->getStudentsTotalPerProgramLevel($row->PROGRAMMECODE,'4')}}</td>
                                        <td class=''>{{@$sys->getStudentsTotalPerProgramLevel($row->PROGRAMMECODE,'400/1')}}</td>
                                        <td class=''><?php echo $sys->getStudentsTotalPerProgramLevel($row->PROGRAMMECODE,'400/2')?></td>
                                        
                                         
                                     </tr>
                                        @endforeach
                                    </tbody>
                        </table>-->
            <div class="md-card">
                <div class="md-card-content">
                    <div id="wizard_vertical">

                        <h3>Create Groups</h3>
                        <section>
                            <h2 class="heading_b">
                                Get started
                                <span class="sub-heading">Create the name of the groups  </span>
                            </h2>
                            <hr class="md-hr">
                              
                            <form   id="form" name="applicationForm"   accept-charset="utf-8" method="POST"  v-form>
                                <input type="hidden" name="_token" value="{!! csrf_token() !!}"> 
                                <table id="paymentTable" class=""border="0" style="font-weight:bold">
                                    <tr id="paymentRow" payment_row="payment_row"> 
                                        <td>Program &nbsp;
                                            {!! Form::select('program[]', 
                                            (['' => 'select program'] +$program ), 
                                            old("program",""),
                                            ['class' => 'md-input gad program','style'=>'width:200px','v-model'=>'program','v-form-ctrl'=>'','v-select'=>''] )  !!}
                                        </td>
                                        <td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</td>
                                        <td>Level &nbsp;
                                             {!! Form::select('level[]', 
                                            (['' => 'select level'] +$level ), 
                                            old("level",""),
                                            ['class' => 'md-input gad level','style'=>'width:200px','v-model'=>'level','v-form-ctrl'=>'','v-select'=>''] )  !!}
                                      
                                        </td>
                                         <td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</td>
                                        <td valign="top">Group Name &nbsp;<input type="text"   class="md-input name" required=""   v-model='name' v-form-ctrl='' name="name[]" style="width:auto;"></td> 

                                        <td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</td>
                                        <td>Total Per Group &nbsp;
                                            <input type="number" required="" class="md-input" name="total[]"   value="{{ old('total') }}" />
                                   
                                        </td>
                                         <td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</td>
                                        <td valign="top" id="insertPaymentCell"><button  type="button" id="insertPaymentRow" class="md-btn md-btn-primary md-btn-small " ><i class="sidebar-menu-icon material-icons">add</i></button></td></tr>
	
                                        
                                </table>
                                <table align="center">

                                    <tr><td><input type="submit" value="Create"  v-show="applicationForm.$valid"  class="md-btn save  md-btn-success uk-margin-small-top">
                                            <input type="reset" value="Cancel" class="md-btn   md-btn-danger uk-margin-small-top">
                                        </td></tr></table>
                            </form>
                        </section>
                        <h3>View Groups and Students</h3>
                        <section>
                            <h2 class="heading_b">
                              Preview Groups
                                <span class="sub-heading">Check your groups finally before running it.</span>
                            </h2>
                            <p><a href="{{url('/groups/run')}}" onclick="return confirm('Once you run this group it will be affected on all the students in that group forever')"><button class="md-btn   md-btn-success uk-margin-small-top">Run this group</button></a></p>
                            <hr class="md-hr">
                            <table class="uk-table uk-table-hover uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id="ts_pager_filter"> 
               <thead>
                 <tr>
                     <th class="filter-false remove sorter-false" data-priority="6">No</th>
                      <th>Group Name</th>
                     <th  style="text-align:">level</th>
                     <th>Programme</th> 
                     <th style="text-align:">year</th>

                      
                     <th  class="filter-false remove sorter-false uk-text-center" colspan="2" data-priority="1">ACTION</th>   
                                     
                </tr>
             </thead>
      <tbody>
                                        
                                         @foreach($group as $index=> $row) 
                                         
                                         
                                        <tr align="">
                                            <td> {{ $data->perPage()*($data->currentPage()-1)+($index+1) }} </td>
                                            <td> {{ @$row->name }}</td>
                                            <td> {{ @$row->levels->slug	 }}</td>
                                            <td> {{ @$row->programme->PROGRAMME	 }}</td>
                                            <td> {{ @$row->year	 }}</td>
                                            
                                            <td> 
                                        
                                             {!!Form::open(['action' =>['GroupController@destroy', 'id'=>$row->id], 'method' => 'DELETE','name'=>'c' ,'style' => 'display: inline;'])  !!}

                                                      <button type="submit" onclick="return confirm('Are you sure you want to delete   {{$row->name }} for level  {{$row->level}}-{{  @$row->programme->PROGRAMME	 }}?')" class="md-btn  md-btn-danger md-btn-small   md-btn-wave-light waves-effect waves-button waves-light" ><i  class="sidebar-menu-icon material-icons md-18">delete</i></button>
                                                        
                                                     {!! Form::close() !!}
                                            </td>
                                          
                                        </tr>
                                            @endforeach
                                    </tbody>
                                    
                             </table>
                        
                        
                        </section>
                        <h3>Assign Lecturers</h3>
                        <section>
                            <h2 class="heading_b">
                               Add lecturers to your groups
                                   </h2>
                            <hr class="md-hr">
                             
                            <form action="{{url('/groups/activate')}}" id="form" accept-charset="utf-8" method="POST" name="applicationForm"  v-form>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}"> 
          
                <div class="uk-overflow-container">
                  
                    <table class="uk-table uk-table-align-vertical uk-table-nowrap tablesorter tablesorter-altair" id="ts_pager_filter">                     
                        <thead>
                            <tr>

                                <th>NO</th>
                                <th>Group Name</th> 
                                 <th>Group Level</th>
                                <th>Program</th>
                               
                                <th>Capacity</th>
                                <th>Lecturer</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $count = 0; ?>
                           @foreach($group as $index=> $row) 
                                          
                            <?php $count++; ?>
                            <tr align="">

                                <td> {{ $count }} </td>
                        <input type="hidden" name="key[]" value="{{$row->id}}"/>
                         <input type="hidden" name="name[]" value="{{$row->name}}"/>
                       
                        
                        <td> {{ @$row->name }}</td>
                        <td> {{ @$row->levels->slug	 }}</td>
                        <td> {{ @$row->programme->PROGRAMME	 }}</td>
                         <td> {{ @$row->totalStudent	 }}</td>

                         <td>
                            {!! Form::select('lecturer[]', 
                                (['' => 'select lecturer'] +$lecturers ), 
                                  old("lecturer",""),
                                    ['class' => 'md-input gad','style'=>'width:120px','required'=>'','v-model'=>'grade','v-form-ctrl'=>'','v-select'=>''] )  !!}
	 
                        </td>
                       

                        </tr>
                        @endforeach
                        </tbody>

                    </table>

                     </div>
                &nbsp; 
                <center><div style="   bottom: 0px;left: 45%  ">
                        <p>
                            <input type="hidden" name="upper" value="{{$count++}}" id="upper" />

                            <button type="submit"  class="md-btn md-btn-success md-btn-small"><i class="fa fa-save" ></i>Update</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;


                        </p>
                    </div></center>
  </form>
  
                        </section>

                    </div>
                </div>
            </div>
</div>

@endsection

@section('js')
    <script>
       </script>

 

 
@endsection