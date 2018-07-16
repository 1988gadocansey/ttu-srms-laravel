@extends('layouts.app')


@section('style')
<style>
    input{
        text-transform: uppercase
    }
    
</style>
 <script src="{!! url('public/assets/js/jquery.min.js') !!}"></script>
 
        <script src="{!! url('public/assets/js/jquery-ui.min.js') !!}"></script>
 
 
@endsection
@section('content')
<div class="md-card-content">
<div style="text-align: center;display: none" class="uk-alert uk-alert-success" data-uk-alert="">

    </div>



    <div style="text-align: center;display: none" class="uk-alert uk-alert-danger" data-uk-alert="">

    </div>

    @if (count($errors) > 0)


    <div class="uk-alert uk-alert-danger  uk-alert-close" style="background-color: red;color: white" data-uk-alert="">

        <ul>
            @foreach ($errors->all() as $error)
            <li>{!!$error  !!} </li>
            @endforeach
        </ul>
    </div>

    @endif


</div>
<h5 class="heading_b uk-margin-bottom">Add Staff  here</h5>
<div class="uk-width-xLarge-1-10">
    <div class="md-card">
        <div class="md-card-content" style="">

             
            
        <form  novalidate id="wizard_advanced_form" class="uk-form-stacked"  action="{{url('add_staff')}}"  method="post" accept-charset="utf-8"  name="memberForm"  v-form>

                {!!  csrf_field() !!}
            <p><input type="text" required="" name="name" placeholder="Name"></p></p>
            <p><input type="text" required="" name="staff" placeholder="Staff ID"></p></p>
            <p><input type="text" required="" name="phone"placeholder="Phone"></p></p>

            <p>{!!   Form::select('department',$department ,array("class"=>"md-input")   )  !!}</p>

            <p><input type="submit" class="uk uk-btn btn-primary" value="Save"/> </p>
        </form>


      </section>

       </div>

</form>

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

<script>


//code for ensuring vuejs can work with select2 select boxes
Vue.directive('select', {
  twoWay: true,
  priority: 1000,
  params: [ 'options'],
  bind: function () {
    var self = this
    $(this.el)
      .select2({
        data: this.params.options,
         width: "resolve"
      })
      .on('change', function () {
        self.vm.$set(this.name,this.value)
        Vue.set(self.vm.$data,this.name,this.value)
      })
  },
  update: function (newValue,oldValue) {
    $(this.el).val(newValue).trigger('change')
  },
  unbind: function () {
    $(this.el).off().select2('destroy')
  }
})


var vm = new Vue({
  el: "body",
  ready : function() {
  },
 data : {
   
    
 options: [      
    ],
    in_payment_section : false,
  },
  methods : {
    go_to_payment_section : function (event){
    UIkit.modal.confirm(vm.$els.confirm_modal.innerHTML, function(){
        
      vm.$data.in_payment_section=true
})

    },
    
    go_to_fill_form_section : function (event){    
      vm.$data.in_payment_section=false
    }
  }
})

</script>
<script>
                    $(document).ready(function(){
            $('.client').on('click', function(e){


            var year = $('.year').val();
                   
                    UIkit.modal.confirm("Are you sure every data is accurate?? "
                            , function(){
                            modal = UIkit.modal.blockUI("<div class='uk-text-center'>Saving data <br/><img class='uk-thumbnail uk-margin-top' src='{!! url('public/assets/img/spinners/spinner.gif')  !!}' /></div>");
                                    //setTimeout(function(){ modal.hide() }, 500) })()            
                                    $.ajax({
                                     
                                            type: "POST",
                                            url:"{{url('add_staff')}}",
                                            data: $('#wizard_advanced_form').serialize(), //your form data to post goes 
                                            dataType: "json",
                                    }).done(function(data){
                //  var objData = jQuery.parseJSON(data);
                modal.hide();
                        //                                    
                        //                                     UIkit.modal.alert("Action completed successfully");

                        //alert(data.status + data.data);
                        if (data.status == 'success'){
                $(".uk-alert-success").show();
                        $(".uk-alert-success").text(data.status + " " + data.message);
                        $(".uk-alert-success").fadeOut(4000);
                         window.location.href="{{url('/staff')}}";
                }
                else{
                $(".uk-alert-danger").show();
                        $(".uk-alert-danger").text(data.status + " " + data.message);
                        $(".uk-alert-danger").fadeOut(4000);
                }


                });
                            }
                    );
            });
            
             
            });</script>

        
@endsection         