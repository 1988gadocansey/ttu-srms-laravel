<!DOCTYPE html>
<html>
    <head>
        <title>Laravel Autocomplete</title>
        <meta charset="utf-8">
        <link rel="stylesheet"href="//codeorigin.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" />
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script src="//codeorigin.jquery.com/ui/1.10.2/jquery-ui.min.js"></script>
    </head>
    <body>
        <h2>Laravel Autocomplete</h2>

    
 <form method="post" action="{{ url('gad') }}">
       {!!  csrf_field()  !!}
  <input id="q" placeholder="Search users" name="q" type="text" value="">
  <input class="btn btn-default" type="submit" value="Search">
</form>


</div>
   <script type="text/javascript">
           //Javascript
$(function()
{
	 $( "#q" ).autocomplete({
	  source: "{{ url('search/autocomplete') }}",
	  minLength: 3,
	  select: function(event, ui) {
	  	$('#q').val(ui.item.value);
	  }
	});
});
        </script>
@section('js')
 
 

      
        @endsection
    </body>
</html>