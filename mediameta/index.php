<?php
  include( '../init.inc.php' );

if( !$session->isLoggedIn()) {
  header( 'Location: https://mediathek.hgk.fhnw.ch/auth?target='.urlencode($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']) );
  exit;
}

if( !$session->isAdmin()) {
  die( 'admin rights required' );
}


?><!DOCTYPE html>
<html>
  <head>
    <title>DIGMA Mediaserver</title>
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
          idSrc:  'masterid',
          fields: [ {
              label: "Masterid",
              name: "masterid",
              type: "readonly",
            },{
              label: "Type",
              name: "type",
              type: "readonly",
            },{
              label: "Collection",
              name: "collectionname",
              type: "readonly",
            },{
              label: "Signature",
              name: "signature",
              type: "readonly",
            },{
              label: "Copyright",
              name: "copyright",
            },{
              label: "License",
              name: "license",
            },{
              label: "Access",
              name: "access",
            },{
              label: "Reference",
              name: "reference",
            },{
              label: "Embargo",
              name: "embargo",
              type:   'datetime',
              def:    function () { return new Date(); },
              format: 'YYYY-MM-DD',
            },{
              label: "End of life",
              name: "endoflife",
              type:   'datetime',
              def:    function () { return new Date(); },
              format: 'YYYY-MM-DD',
            }]
      } );

      $('#thetable').on( 'click', 'tbody td:nth-child(n+4)', function (e) {
            editor.bubble( this );
        } );
        table = $('#thetable').DataTable( {
            dom: 'Blfrtip',
            order: [6, 'asc'],
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
                editor:  editor,
                columns: ':nth-child(n+4 )'
            },
            buttons: [
              { extend: 'edit',   editor: editor },
              // { extend: 'pdf' },
            ],
            columns: [
              { data: "thumb" },
              { data: "fullscreen" },
              { data: "masterid" },
              { data: "type" },
              { data: "collectionid" },
              { data: "collectionname" },
              { data: "signature" },
              { data: "copyright" },
              { data: "license" },
              { data: "access" },
              { data: "reference" },
              { data: "embargo" },
              { data: "endoflife" },
            ],
            columnDefs: [
            { // thumbnail
                render: function ( data, type, row ) {
                  if( row.type == 'image' )
                      return '<center><a href="'+row.fullscreen+'" target="_blank"><img style="max-height: 110px;" src="'+data+'" /></a></center>';
                  if( row.type == 'pdf' )
                      return '<center><a href="'+row.fullscreen+'" target="_blank">pdf</a></center>';
                  if( row.type == 'video' )
                      return '<center><a href="'+row.fullscreen+'" target="_blank"><img style="max-height: 110px;" src="'+data+'" /></a><br />video</center>';
                  return row.type;
                },
               visible: true,
               searchable: false,
               targets: 0,
            },
            { // fullscreen
               visible: false,
               searchable: false,
               targets: 1,
            },
            { // masterid
              visible: false,
              searchable: false,
              targets: 2,
            },
            { // type
              visible: false,
              searchable: false,
              targets: 3,
            },
            {  // collectionid
               visible: false,
               searchable: true,
               targets: 4,
            },
            {  // collectionname
               visible: true,
               searchable: true,
               targets: 5,
            },
          ]
        } );

        $( '#formCollection' ).change( function () {
          table.column( 4 )
            .search( $(this).val())
            .draw();
        })
    } );
    </script>
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <a class="navbar-brand" href="#">DIGMA Mediaserver</a>
    </nav>
    <div class="container-fluid">
      <p/>
      <form>
      <div class="row">
        <div class="col">
          <label for="formCollection">Collection</label>
          <select class="form-control" id="formCollection">
            <option></option>
<?php
  $sql = "SELECT DISTINCT collectionname, collectionid FROM mediaserver.fullmeta ORDER BY collectionname ASC";
  $rs = $db->Execute( $sql );
  foreach( $rs as $row ) {
    echo "    <option value=\"{$row['collectionid']}\">".htmlspecialchars( $row['collectionname'])."</option>\n";
  }
?>
          </select>
        </div>
      </div>
      </form>
      <p />
        <table id="thetable" class="display" style="width:100%">
          <thead>
              <tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>Collection</th>
                <th>Signature</th>
                <th>Copyright</th>
                <th>License</th>
                <th>Access</th>
                <th>Reference</th>
                <th>Embargo</th>
                <th>End of life</th>
              </tr>
          </thead>
          <tfoot>
              <tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>Collection</th>
                <th>Signature</th>
                <th>Copyright</th>
                <th>License</th>
                <th>Access</th>
                <th>Reference</th>
                <th>Embargo</th>
                <th>End of life</th>
              </tr>
          </tfoot>
      </table>
    </div>
  </body>
  </html>
