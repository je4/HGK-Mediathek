<?php
  include( '../init.inc.php' );

if( !$session->isLoggedIn()) {
  header( 'Location: https://mediathek.hgk.fhnw.ch/auth?target='.urlencode($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']) );
  exit;
}

?><!DOCTYPE html>
<html>
  <head>
    <title>Peter von Arx</title>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.38/pdfmake.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.38/vfs_fonts.js"></script>
  </head>
  <body>
    <script>
     var docDefinition = { content: 'This is an sample PDF printed with pdfMake' };
     // open the PDF in a new window
    pdfMake.createPdf(docDefinition).open();

    // print the PDF
    pdfMake.createPdf(docDefinition).print();

    // download the PDF
    pdfMake.createPdf(docDefinition).download('optionalName.pdf');
    </script>
  </body>
  </html>
