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
 <div class="uk-modal" id="new_task">
        <div class="uk-modal-dialog">
            <div class="uk-modal-header">
                <h4 class="uk-modal-title">Create Banks for Fee Payment here</h4>
            </div>
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
                                        <button class="md-btn md-btn-primary md-btn-small clone"  >Add More</button>
                                    </div>
                                   

                                        <button   class="md-btn md-btn-danger md-btn-small remove"  >Remove</button>

                                    </div>
                                 
                            </div>

                        </div>
                    </div>
                    </div>
                    </div>
                    
                <div class="uk-modal-footer uk-text-right">
                    <button type="submit" class="md-btn md-btn-flat md-btn-flat-primary md-btn-wave" id="snippet_new_save">Add Bank</button>    
                    <button type="button" class="md-btn md-btn-flat uk-modal-close md-btn-wave">Close</button>
                </div>
            </form>
        </div>
    </div>
 <h5>Banks</h5>
 <div class="uk-width-xLarge-1-1">
    <div class="md-card">
        <div class="md-card-content">
     <div class="uk-overflow-container">
         <table class="uk-table uk-table-hover uk-table-align-vertical uk-table-nowrap " id="gad"> 
             <thead>
                 <tr>
                     <th>N<u>O</u></th>
                <th>Bank</th>
                <th >Account N<u>O</u></th>

                <th>ACTION</th>


                </tr>
             </thead>

         </table>
     </div>
<div class="md-fab-wrapper">
        <a class="md-fab md-fab-small md-fab-accent md-fab-wave" href="#new_task" data-uk-modal="{ center:true }">
            <i class="material-icons md-18">&#xE145;</i>
        </a>
    </div>
 </div>
    </div>
 </div>
@endsection
@section('js')
 
<script>
    
 
 var oTable = $('#gad').DataTable({
     
        
        processing: true,
        serverSide: true,
        ajax: {
            url:  "{!! route('banks.data') !!}"
             
        },
        columns: [
        {data: 'ID', name: 'ID'},
            
            {data: 'NAME', name: 'NAME'},
            {data: 'ACCOUNT_NUMBER', name: 'ACCOUNT_NUMBER'},
             
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
    

    
</script>
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