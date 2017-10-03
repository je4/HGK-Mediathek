<html>
    <head>
        <title>Testing123</title>

        <link href="css/animate.css" rel="stylesheet">

        <style type="text/css">
            body {
                margin: 0px;
                padding: 0px;
            }
        </style>

        <script type="text/javascript">
        function init() {

            screens = 4;

            width = $(window).width();
            height = $(window).height();
            $('body').prepend( '<canvas id="main" width="'+width+'" height="'+height+'"></canvas>');

            quarter = Math.floor( width / screens );

            colors = [ "red", "green", "blue", "cyan" ];

            for( i = 0; i < screens; i++ ) {
                $('canvas').drawRect({
                    strokeStyle: colors[i],
                    strokeWidth: 1,
                    x: (i*quarter), y: 0,
                    width: quarter-1,
                    height: height,
                    cornerRadius: 20,
                    fromCenter: false,
                  });

                $('canvas').drawLine({
                    strokeStyle: '#000',
                    strokeWidth: 1,
                    x1: i*quarter, y1: 0,
                    x2: (i+1)*quarter, y2: height,
                  });
               $('canvas').drawLine({
                    strokeStyle: '#000',
                    strokeWidth: 1,
                    x1: i*quarter, y1: height,
                    x2: (i+1)*quarter, y2: 0,
                  });

               $('canvas').drawText({
                    layer: false,
                    name: 'size',
                    fillStyle: '#36c',
                    strokeWidth: 2,
                    x: (i+0.5)*quarter, y: height/2,
                    fontSize: '36pt',
                    fontFamily: 'Verdana, sans-serif',
                    text: quarter+'x'+height,
                    fromCenter: true,
                  });

            }
          $('canvas').drawText({
                    layer: false,
                    name: 'size',
                    fillStyle: 'red',
                    strokeWidth: 2,
                    x: 0.5*width, y: height/4,
                    fontSize: '48pt',
                    fontFamily: 'Verdana, sans-serif',
                    text: width+'x'+height,
                    fromCenter: true,
                  });


        }

        function doInit() {
          $('.tlt').textillate( {
            initialDelay: 10000
          });
        }

        function init2() {
          window.setTimeout( doInit, 10000 );
        }
        </script>
    </head>
    <body onload="doInit();">
        <h1 style="font-size: 220pt; visibility:hidden;" class="tlt">Center for Digital Matter</h1>

        <script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
        <script type="text/javascript" src="js/jcanvas.min.js"></script>
        <script type="text/javascript" src="js/jquery.lettering.js"></script>
        <script type="text/javascript" src="js/jquery.fittext.js"></script>
        <script type="text/javascript" src="js/jquery.textillate.js"></script>
    </body>
</html>
