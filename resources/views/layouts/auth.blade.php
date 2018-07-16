<!DOCTYPE html>
<html lang="en">
    <head>
        <title>SRMS | Takoradi Technical University</title>

          <link media="all" type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">
          <link media="all" type="text/css" rel="stylesheet" href='{{url("public/assets/js/bootstrap.css")}}'>


    </head>

    <body>
        <div class="container">
            <nav class="navbar navbar-default">
                 <div class="collapse navbar-collapse" id="frontend-navbar-collapse">
            <ul class="nav navbar-nav">
                <li><img src='{{url("public/assets/img/logo.png")}}' style="width:65px;height: auto" alt=""/>Students Records Management System</li>
                
            </ul>

            <ul class="nav navbar-nav navbar-right">
                
 
                    <li><a href='{{url("/login")}}'>Login</a></li>
                    <li><a href='{{url("/register")}}'>Register</a></li>
                 
            </ul>
        </div><!--navbar-collapse-->
            </nav>
        </div>

        @yield('content')
        <small class="footer uk-text-small"><center><?php date("Y");?> All Rights Reserved | Takoradi Technical University - Powered by Tpconnect </center></small>
      
    </body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>

          <script src='{{url("public/assets/js/bootstrap.min.js")}}'></script>

</html>