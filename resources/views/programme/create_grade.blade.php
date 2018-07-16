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

            <h5 class=" ">Create Grade System here</h5>
            <form    id="form" accept-charset="utf-8" method="POST" name="applicationForm"  v-form>
                <input type="hidden" name="_token" value="{!! csrf_token() !!}"> 
                <div class="parsley-row">
                    <div class="uk-input-group">

                        <label for="wizard_phone">Programme Categories<span class="req uk-text-danger">*</span></label>
                        <p></p>
                                             {!!  Form::select('type', array('HND'=>'HND','BTECH'=>'BTECH','DBS' => 'DBS','NON-T'=>'NON-TERTIARY' ), null, ['placeholder' => 'Select category','id'=>'parent','class'=>'md-input','required'=>'required','v-model'=>'type','v-form-ctrl'=>'','v-select'=>'','style'=>'width:200px'],old("type","")); !!}
                            
                    </div>
                </div>
          <table id="paymentTable" class="uk-table"border="0" style="font-weight:bold">
	  <tr id="paymentRow" payment_row="payment_row"><td valign="top"> 
          
              <td valign="top">Lower Boundary &nbsp;<input type="text"  v-model='lower[]' v-form-ctrl=''   class="md-input md-input"  name="lower[]" style="width:auto;"></td>

    
              <td valign="top">Upper Boundary &nbsp;<input type="text"   class="md-input md-input" required=""   v-model='upper[]' v-form-ctrl='' name="upper[]" style="width:auto;"></td>

          <td valign="top">Grade &nbsp;
             {!! Form::select('grade[]', 
                                (['' => 'select grade'] +$grade ), 
                                  old("grade",""),
                                    ['class' => 'md-input gad','style'=>'width:200px','required'=>'','v-model'=>'grade','v-form-ctrl'=>'','v-select'=>''] )  !!}
	 
          </td>

          
          <td valign="top">Value &nbsp;<input type="text"    class="md-input md-input" required="" v-model='value[]' v-form-ctrl=''  name="value[]" style="width:auto;"></td>

          
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

        
         