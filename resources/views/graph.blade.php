@extends('layouts.app')

 
@section('style')
  <!-- additional styles for plugins -->
        <!-- weather icons -->
        <link rel="stylesheet" href="public/assets/plugins/weather-icons/css/weather-icons.min.css" media="all">
        <!-- metrics graphics (charts) -->
        <link rel="stylesheet" href="public/assets/plugins/metrics-graphics/dist/metricsgraphics.css">
        <!-- chartist -->
        <link rel="stylesheet" href="public/assets/plugins/chartist/dist/chartist.min.css">
  
@endsection
 @section('content')
 <div class="uk-grid">
     <div class="uk-width-1-1">



         <div class="uk-grid uk-grid-small">

             <canvas id="canvas" height="280" width="600"></canvas>
             <div id="pop-div" style="width:800px;border:1px solid black"></div>
<?= $lava->render('GeoChart', 'Popularity', 'pop-div') ?>
         </div>
     </div></div>
 @endsection
@section('js')
 <script src="https://raw.githubusercontent.com/nnnick/Chart.js/master/dist/Chart.bundle.js"></script>

  <!-- d3 -->
           <!-- chartist (charts) -->
        <script src="public/assets/plugins/chartist/dist/chartist.min.js"></script>
         

        <!--  dashbord functions -->
        <script src="public/assets/js/pages/dashboard.min.js"></script>
 
@endsection