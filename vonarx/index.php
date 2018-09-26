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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous"/>
    <!-- link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.css"/ -->

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/autofill/2.3.0/css/autoFill.bootstrap4.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.bootstrap4.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/colreorder/1.5.0/css/colReorder.bootstrap4.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/keytable/2.4.0/css/keyTable.bootstrap4.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/scroller/1.5.0/css/scroller.bootstrap4.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.2.6/css/select.bootstrap4.css"/>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/jquery.dataTables.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/autofill/2.3.0/css/autoFill.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedcolumns/3.2.5/css/fixedColumns.dataTables.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.4/css/fixedHeader.dataTables.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.2/css/responsive.dataTables.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/scroller/1.5.0/css/scroller.dataTables.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.2.6/css/select.dataTables.css"/>

    <link rel="stylesheet" type="text/css" href="vendor/DataTables/Editor-1.7.4/css/editor.dataTables.css">

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/autofill/2.3.0/js/dataTables.autoFill.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/fixedcolumns/3.2.5/js/dataTables.fixedColumns.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/fixedheader/3.1.4/js/dataTables.fixedHeader.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.2/js/dataTables.responsive.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/scroller/1.5.0/js/dataTables.scroller.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/select/1.2.6/js/dataTables.select.js"></script>
    <script type="text/javascript" src="vendor/DataTables/Editor-1.7.4/js/dataTables.editor.js"></script>
    <!-- script type="text/javascript" src="vendor/DataTables/Editor-1.7.4/js/editor.bootstrap4.js"></script -->

    <!--
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

    <script type="text/javascript" class="init">
    var table;
    var editor;
    $(document).ready(function() {
      editor = new $.fn.dataTable.Editor( {
          ajax: "writer.php",
          table: "#thetable",
          idSrc:  'ObjectNr',
          fields: [
            {
              name: "Box" ,
              label: "Box"
            },
            {
              name: "ObjectNr" ,
              label: "ObjectNr"
            },
            {
              name: "Title" ,
              label: "Title"
            },
            {
              name: "Object" ,
              label: "Object"
            },
            {
              name: "Description" ,
              label: "Description"
            },
            {
              name: "Date" ,
              label: "Date"
            },
            {
              name: "Client" ,
              label: "Client"
            },
            {
              name: "Media" ,
              label: "Media"
            },
            {
              name: "Size" ,
              label: "Size"
            },
            {
              name: "Author" ,
              label: "Author"
            },
            {
              name: "Print" ,
              label: "Print"
            },
            {
              name: "Copyright" ,
              label: "Copyright"
            },
            {
              name: "Location" ,
              label: "Location"
            },
            {
              name: "Digital" ,
              label: "Digital"
            },
          ]
      } );

      $('#thetable').on( 'click', 'tbody td:not(:first-child)', function (e) {
            editor.bubble( this );
        } );
        table = $('#thetable').DataTable( {
            dom: 'Blfrtip',
            order: [4, 'asc'],
            serverSide: true,
            paging: true,
            processing: true,
            lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
            pageLength: 10,
            ajax: "reader.php",
            type: "POST",
            select: {
                style:    'os',
                //selector: 'td:first-child',
                blurable: true
            },
            autoFill: {
                editor:  editor
            },
            buttons: [
              { extend: 'create', editor: editor },
              { extend: 'edit',   editor: editor },
              { extend: 'remove', editor: editor },
              // { extend: 'pdf' },
            ],
            columns: [
              {
                  data: null,
                  defaultContent: '',
                  className: 'select-checkbox',
                  orderable: false
              },
              { data: "Thumb",
                orderable: false
              },
              { data: "Fullscreen",
                orderable: false
              },
              { data: "Box" },
              { data: "ObjectNr" },
              { data: "Title" },
              { data: "Object" },
              { data: "Description" },
              { data: "Date" },
              { data: "Client" },
              { data: "Media" },
              { data: "Size" },
              { data: "Author" },
              { data: "Print" },
              { data: "Copyright" },
              { data: "Location" },
              { data: "Digital" },
            ],
            columnDefs: [
              {
                  searchable: false,
                  targets: 0
              },
              {
                  render: function ( data, type, row ) {
                      return '<center><a href="'+row.Fullscreen+'" target="_blank"><img style="max-height: 110px;" src="'+data+'" /></a></center>';
                  },
                  searchable: false,
                  targets: 1
              },
              {
                 visible: false,
                 searchable: false,
                 targets: 2,
           },
          ]
        } );
    } );
    </script>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <a class="navbar-brand" href="#">Peter von Arx Archive</a>
    </nav>
    <div class="container-fluid">
      <p/>

        <table id="thetable" class="display" style="width:100%">
          <thead>
              <tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>Fullscreen</th>
                <th>Box</th>
                  <th>ObjectNr</th>
                  <th>Title</th>
                  <th>Object</th>
                  <th>Description</th>
                  <th>Date</th>
                  <th>Client</th>
                  <th>Media</th>
                  <th>Size</th>
                  <th>Author</th>
                  <th>Print</th>
                  <th>Copyright</th>
                  <th>Location</th>
                  <th>Digital</th>
              </tr>
          </thead>
          <tfoot>
              <tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>Fullscreen</th>
                <th>Box</th>
                <th>ObjectNr</th>
                <th>Title</th>
                <th>Object</th>
                <th>Description</th>
                <th>Date</th>
                <th>Client</th>
                <th>Media</th>
                <th>Size</th>
                <th>Author</th>
                <th>Print</th>
                <th>Copyright</th>
                <th>Location</th>
                <th>Digital</th>
              </tr>
          </tfoot>
      </table>
    </div>
  </body>
  </html>
