@extends('layouts.app')

 
@section('style')
 
@endsection
 @section('content')
 <div class="uk-width-large-8-10">
 <div class="md-card">
 <div class="md-card-content" style="">
     
                    <h5 class=" ">Update Banks here</h5>
                    <form action="" method="POST">
                    <input type="hidden" name="_token" value="{!! csrf_token() !!}"> 
                     
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-medium-1-2">
                            <div class="uk-form-row">
                                <div class="uk-grid" data-uk-grid-margin>
                                    <div class="uk-width-medium-1-2">
                                        <label>Bank Name</label>
                                        <input type="text" class="md-input md-input-success" required="" value="{{$bank->NAME}}" name="bank"/>
                                    </div>
                                    <div class="uk-width-medium-1-2">
                                        <label>Account Number</label>
                                        <input type="text" class="md-input md-input-success" required="" value="{{$bank->ACCOUNT_NUMBER}}" name="account"/>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        
                    </div>
                    
                    <p>&nbsp;</p>
                     <div class="uk-grid" align='center'>
                            <div class="uk-width-1-1">
                                <input type="submit" class="md-btn md-btn-success" value="Save"  />
                            </div>
                        </div>
        </form>
 </div></div></div>
 @endsection
@section('js')
  
@endsection