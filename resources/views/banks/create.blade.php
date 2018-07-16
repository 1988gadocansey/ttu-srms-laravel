@extends('layouts.app')

 
@section('style')
<style>
    .md-card{
        width: auto;
         
    
    
    
    }
</style>
@endsection
 @section('content')
 <div class="uk-width-large-8-10">
 <div class="md-card">
 <div class="md-card-content" style="">
     
                    <h5 class=" ">Create Banks for Fee Payment here</h5>
                    <form action="create_bank" method="POST">
                    <input type="hidden" name="_token" value="{!! csrf_token() !!}"> 
                    <div id="inn">
                    <div id="clonedInput1" class="clonedInput">
                  
                    <div class="uk-grid" data-uk-grid-margin>
                        <div class="uk-width-medium-1-2">
                            <div class="uk-form-row">
                                <div class="uk-grid" data-uk-grid-margin>
                                    <div class="uk-width-medium-1-2">
                                        <label>Bank Name</label>
                                        <input type="text" class="md-input md-input-success" required="" name="bank[]"/>
                                    </div>
                                    <div class="uk-width-medium-1-2">
                                        <label>Account Number</label>
                                        <input type="text" class="md-input md-input-success" required="" name="account[]"/>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="uk-width-medium-1-2">
                            <div class="uk-form-row" style="margin-top:25px">
                                <div class="uk-grid" data-uk-grid-margin>
                                    <div class="uk-width-medium-1-2">
                                        <button type="button"class="md-btn md-btn-primary md-btn-small clone"  >Add More</button>


                                        <button  type="button"  class="md-btn md-btn-danger md-btn-small remove"  >Remove</button>

                                    </div>
                                </div>
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
                </div>
 </div>
 </div>
 @endsection
@section('js')
 
  <script type="text/javascript">
           //Javascript
var regex = /^(.+?)(\d+)$/i;
var cloneIndex = $(".clonedInput").length;

function clone(){
    $(this).parents(".clonedInput").clone()
        .appendTo("#inn")
        .attr("id", "clonedInput" +  cloneIndex)
        .find("*")
        .each(function() {
            var id = this.id || "";
            var match = id.match(regex) || [];
            if (match.length == 3) {
                this.id = match[1] + (cloneIndex);
            }
        })
        .on('click', 'button.clone', clone)
        .on('click', 'button.remove', remove);
    cloneIndex++;
}
function remove(){
    $(this).parents(".clonedInput").remove();
}
$("button.clone").on("click", clone);

$("button.remove").on("click", remove);
        </script>
@endsection