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

            <h5 class=" ">Create Classes here</h5>
            <form    id="form" accept-charset="utf-8" method="POST" name="applicationForm"  v-form>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}"> 
                
          <table id="paymentTable" class="uk-table"border="0" style="font-weight:bold">
	  <tr id="paymentRow" payment_row="payment_row"><td valign="top"> 
          
              <td valign="top">Lower Boundary &nbsp;<input type="text"  v-model='lower[]' v-form-ctrl=''   class="md-input md-input"  name="lower[]" style="width:auto;"></td>

    
              <td valign="top">Upper Boundary &nbsp;<input type="text"   class="md-input md-input" required=""   v-model='upper[]' v-form-ctrl='' name="upper[]" style="width:auto;"></td>

          <td valign="top">Class &nbsp;
             
                                 {!!  Form::select('class[]', array('1st Class'=>'First Class','2nd Class(Upper Division)'=>'Second Class Upper(Upper Division)','2nd Class(Lower Division)'=>'Second Class Upper(Lower Division)', '3rd Class'=>'Third Class','Pass'=>'Pass','Failed'=>'Failed'), null, ['placeholder' => 'select class','required'=>'required','class'=>'md-input parent'],old("class","")); !!}
          </td>

          
         
          
	  <td valign="top" id="insertPaymentCell"><button  type="button" id="insertPaymentRow" class="md-btn md-btn-primary md-btn-small " ><i class="sidebar-menu-icon material-icons">add</i></button></td></tr>
	   
      </table>
      <table align="center">
       
        <tr><td><input type="submit" value="Save" id='save'  class="md-btn   md-btn-success uk-margin-small-top">
      <input type="reset" value="Cancel" class="md-btn   md-btn-default uk-margin-small-top">
    </td></tr></table>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')

        
         