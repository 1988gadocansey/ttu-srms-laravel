@extends('layouts.app')

 
   
@section('style')
 
        <script src="{!! url('public/assets/js/jquery.min.js') !!}"></script>
 
        <script src="{!! url('public/assets/js/jquery-ui.min.js') !!}"></script>
 
    <link rel="stylesheet" href="{!! url('public/assets/css/jquery-ui.css') !!}" media="all">
        
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
 @if (!empty($errorP)&& count($errorP) > 0)


    <div class="uk-alert uk-alert-danger  uk-alert-close" style="background-color: red;color: white" data-uk-alert="">

        <ul>
            @foreach ($errorP->all() as $error)
            <li>{!!$error  !!} </li>
            @endforeach
        </ul>
    </div>

    @endif
    @if (!empty($errorC) && count($errorC) > 0)


    <div class="uk-alert uk-alert-danger  uk-alert-close" style="background-color: red;color: white" data-uk-alert="">

        <ul>
            @foreach ($errorC->all() as $error)
            <li>{!!$error  !!} </li>
            @endforeach
        </ul>
    </div>

    @endif
 
 </div>
 
 
 <div align="center">
      <a href="{{ url('public/uploads/Mounted_course.xlsx') }}" download="Mounted_course.xlsx">   <button class="md-btn md-btn-small md-btn-primary uk-margin-small-top">Download mounted course template</button></a>
                                  
     <p></p>
     <form action="{{url('/upload_mounted')}}" method="post" enctype="multipart/form-data"  accept-charset="utf-8" id="form" name="applicationForm"  v-form>
         <input type="hidden" name="_token" value="{!! csrf_token() !!}"> 

         <div class="uk-width-small-1-2">
              
                 <div class="md-card">
                     <div class="md-card-content">
                         <h3 class="heading_a uk-margin-small-bottom">
                            Upload Mounted Courses   here  Max size (10MB)
                         </h3>
                         <input type="file" id="input-file-e" required="" name="file" v-model="file" v-form-ctrl="" class="dropify" data-max-file-size="200000K" />
                     </div>
                 </div>
                 <div class="uk-grid"  >
                 <div class="uk-width-1-1">
                     <input type="submit"  v-show="applicationForm.$valid" class="md-btn md-btn-primary" value="upload"  />
                 </div>
            
             </div>
             
         </div>
        
     </form>
      
 </div>
 
   
   
 @endsection
 
@section('js')
 <script>
        $(document).ready(function(){
            $("#formm").on("submit",function(event){
                event.preventDefault();
       UIkit.modal.alert('uploading mounted courses...');
         $(event.target).unbind("submit").submit();
    
                        
            });
            
    
                    
    
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
   
   
 options: [    ]  
    
  },
   
})

</script>
@endsection