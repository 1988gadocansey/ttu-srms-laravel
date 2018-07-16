@extends('layouts.app')


@section('style')
<style>
    .md-card{
        width: auto;

    }
    
</style>
 <script src="{!! url('public/assets/js/jquery.min.js') !!}"></script>
 
        <script src="{!! url('public/assets/js/jquery-ui.min.js') !!}"></script>
 
@endsection
@section('content')
<div class="uk-width-xLarge-1-10">
    <div class="md-card">
        <div class="md-card-content" style="">

            <h5 class=" ">Create Programmes here</h5>
            <form    id="form" accept-charset="utf-8" method="POST" name="applicationForm"  v-form>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}"> 
                 <table id="paymentTable" class="uk-table"border="0" style="font-weight:bold">
	  <tr id="paymentRow" payment_row="payment_row"> 
              <td>Department &nbsp;
	   {!! Form::select('department[]', 
                                (['' => 'select department'] +$department ), 
                                  old("department",""),
                                    ['class' => 'md-input gad','style'=>'width:200px','v-model'=>'department','v-form-ctrl'=>'','v-select'=>''] )  !!}
	  </td>
	  <td valign="top">Programme Code &nbsp;<input type="text"  v-model='code[]' v-form-ctrl=''   class="md-input md-input"  name="code[]" style="width:auto;"></td>

    
          <td valign="top">Programme Name &nbsp;<input type="text"   class="md-input md-input" required=""   v-model='name[]' v-form-ctrl='' name="name[]" style="width:auto;"></td>

          <td valign="top">Programme duration &nbsp;<input type="number"   class="md-input md-input" required="" v-model='duration[]' v-form-ctrl=''  name="duration[]" style="width:auto;"></td>

          
          <td valign="top">Minimum Credit &nbsp;<input type="number"    class="md-input md-input" required="" v-model='credit[]' v-form-ctrl=''  name="credit[]" style="width:auto;"></td>
           <td>Grade &nbsp;
	   {!! Form::select('grade[]', 
                                (['' => 'select grade system'] +$grade ), 
                                  old("grade[]",""),
                                    ['class' => 'md-input gad','style'=>'width:200px','v-model'=>'grade','v-form-ctrl'=>'','v-select'=>''] )  !!}
	  </td>
          
	  <td valign="top" id="insertPaymentCell"><button  type="button" id="insertPaymentRow" class="md-btn md-btn-primary md-btn-small " title='click to add more ' ><i class="sidebar-menu-icon material-icons">add</i></button></td></tr>
	   
      </table>
      <table align="center">
       
        <tr><td><input type="submit" value="Save" id='save'v-show="applicationForm.$valid"  class="md-btn   md-btn-success uk-margin-small-top">
      <input type="reset" value="Cancel" class="md-btn   md-btn-default uk-margin-small-top">
    </td></tr></table>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')

        
         