<?php

include( '../init.inc.php' );

?><html>
<head>
 <meta http-equiv="X-UA-Compatible" content="IE=edge" />
 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
 <style>
 .required {
   color: red;
   text-decoration: none;
 }
 .required:hover {
   color: red;
   text-decoration: none;
 }
 .required:visited {
   color: red;
   text-decoration: none;
 }
 .required:active {
   color: red;
   text-decoration: none;
 }
 </style>
</head>
<body>
 <div class="collapse bg-inverse" id="navbarHeader">
   <?php include( '../header.inc.php' );  ?>
 </div>
     <div class="navbar navbar-inverse bg-inverse">
       <div class="container d-flex justify-content-between">
         <a href="#" class="navbar-brand">Ausschreibung &Sigma; Summe &ndash; VIDEO</a>
         <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
           <span class="navbar-toggler-icon"></span>
         </button>
       </div>
     </div>
     <p />
     <div class="container-fluid">
       <table class="table table-striped">
<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>Title</th>
<th>Jahr</th>
</tr>
</thead>
<tbody>
<?php
$sql = "SELECT * FROM einreichungen WHERE status='upload' ORDER BY nachname, vorname, titel";
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
?>
<tr>
  <td style="text-align: right;"><a href="detail.php?formid=<?php echo $row['formid']; ?>"><?php echo "#{$row['formid']}"; ?></a></td>
  <td><a href="detail.php?formid=<?php echo $row['formid']; ?>"><?php echo htmlspecialchars("{$row['nachname']}, {$row['vorname']}"); ?></a></td>
  <td><a href="detail.php?formid=<?php echo $row['formid']; ?>"><?php echo htmlspecialchars("{$row['titel']}"); ?></a></td>
  <td><a href="detail.php?formid=<?php echo $row['formid']; ?>"><?php echo htmlspecialchars("{$row['werkjahr']}"); ?></a></td>
</tr>
<?php
}
$rs->Close();
 ?>
 </tbody>
 </table>

</div>

 <?php include( '../footer.inc.php' ); ?>

  <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
  <script language="javascript">
   $(document).ready( function () {
   });
  </script>
 </body>
 </html>
