<!DOCTYPE html>
<html>
<head>
    <title>Custom SMS sender</title>
    <meta charset="utf-8">
    <link rel="stylesheet"href="//codeorigin.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" />
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="//codeorigin.jquery.com/ui/1.10.2/jquery-ui.min.js"></script>
    <script src="http://code.jquery.com/jquery-1.5.js"></script>
    <script>
        function countChar(val) {
            var len = val.value.length;
            if (len >= 800) {
                val.value = val.value.substring(0,800);
            } else {
                $('#charNum').text(800 - len);
            }
        };
    </script>
</head>
<body>
<h2>Upload wrong index numbers</h2>


<form enctype="multipart/form-data"  accept-charset="utf-8" method="post" action="{{ url('/indexno/load') }}"  >
    {!!  csrf_field()  !!}
    <div id="charNum"></div></p>
    <p> <input type="file"  class="md-input   md-input-success " required=""  name="file"/></p>

    <p><input class="btn btn-default" type="submit" value="upload"></p>
</form>

</div>

@section('js')




@endsection
</body>
</html>