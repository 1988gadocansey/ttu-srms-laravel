
<!DOCTYPE html>
<html lang="en">
    
<!-- Mirrored from steelcoders.com/alpha/pattern-lock-screen.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 10 Aug 2016 23:01:34 GMT -->
<head>
        
        <!-- Title -->
        <title>Alpha | Responsive Admin Dashboard Template</title>
        
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta charset="UTF-8">
        <meta name="description" content="Responsive Admin Dashboard Template" />
        <meta name="keywords" content="admin,dashboard" />
        <meta name="author" content="Steelcoders" />
        
        <!-- Styles -->
        <link type="text/css" rel="stylesheet" href="{!!url('public/pattern/plugins/materialize/css/materialize.min.css')!!}"/>
         <link href="{!! url('public/pattern/plugins/material-preloader/css/materialPreloader.min.css')!!}" rel="stylesheet">    
        <link href="{!! url('public/pattern/plugins/patternlock-master/patternLock.css')!!}" rel="stylesheet">    

        	
        <!-- Theme Styles -->
        <link href="{!!url('public/pattern/css/alpha.min.css')!!}" rel="stylesheet" type="text/css"/>
        <link href="{!!url('public/pattern/css/custom.css')!!}" rel="stylesheet" type="text/css"/>
        
         
    </head>
    <body class="signin-page pattern-lock-screen">
             
                <div class="spinner-layer spinner-blue">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div><div class="gap-patch">
                    <div class="circle"></div>
                    </div><div class="circle-clipper right">
                    <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div><div class="gap-patch">
                    <div class="circle"></div>
                    </div><div class="circle-clipper right">
                    <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-yellow">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div><div class="gap-patch">
                    <div class="circle"></div>
                    </div><div class="circle-clipper right">
                    <div class="circle"></div>
                    </div>
                </div>
                <div class="spinner-layer spinner-green">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div><div class="gap-patch">
                    <div class="circle"></div>
                    </div><div class="circle-clipper right">
                    <div class="circle"></div>
                    </div>
                </div>
            </div>
        
        <div class="mn-content valign-wrapper">
            <main class="mn-inner container ">
                <div class="valign">
                    <div class="row">
                        <div class="col s12 m6 l4 offset-l4 offset-m3 center">
                            <h3 id="time" class="white-text"></h3>
                            <h5 id="date" class="white-text"></h5>
                            <div id="patternContainer" style="margin: 0 auto;"></div>
                            <small class="white-text">Draw 'G' shape to unlock, start from top right corner</small>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        
        <!-- Javascripts -->
        <script src="{!!url('public/pattern/plugins/jquery/jquery-2.2.0.min.js')!!}"></script>
        <script src="{!!url('public/pattern/plugins/materialize/js/materialize.min.js')!!}"></script>
        <script src="{!!url('public/pattern/plugins/material-preloader/js/materialPreloader.min.js')!!}"></script>
          <script src="{!!url('public/pattern/plugins/patternlock-master/patternLock.min.js')!!}"></script>
        <script src="{!! url('public/pattern/js/alpha.min.js')!!}"></script>
        <script src="{!!url('public/pattern/js/pages/pattern-lock-screen.js')!!}"></script>
        
    </body>

 </html>