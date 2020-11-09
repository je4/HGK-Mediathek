<?php
namespace Mediathek;


include( '../init.inc.php' );

function personRow( $row ) {
static $badge = array(
  -1=>array('type'=>'default', 'title'=>'gespeichert'),
  0=>array('type'=>'warning', 'title'=>'neu'),
  1=>array('type'=>'success', 'title'=>'erledigt'),
);

  $id = intval( $row['IDPerson']);
  if( $id > 0 ) {
?>
<tr>
<td><a href="form.php?id=<?php echo $id; ?>"><?php echo htmlspecialchars( $row['Anlassbezeichnung']); ?></a></td>
<td><a href="form.php?id=<?php echo $id; ?>"><?php echo htmlspecialchars( $row['Nachname']); ?></a></td>
<td><a href="form.php?id=<?php echo $id; ?>"><?php echo htmlspecialchars( $row['Vornamen']); ?></a></td>
<td><span class="badge badge-<?php echo $badge[$row['done']]['type']; ?>"><?php echo $badge[$row['done']]['title']; ?></span></td>
</tr>
<?php
  } // $id > 0
  else {
?>
<tr>
<td><?php echo htmlspecialchars( $row['Anlassbezeichnung']); ?></td>
<td><?php echo htmlspecialchars( $row['Nachname']); ?></td>
<td><?php echo htmlspecialchars( $row['Vornamen']); ?></td>
<td><span class="badge badge-<?php echo $badge[$row['done']]['type']; ?>"><?php echo $badge[$row['done']]['title']; ?></span></td>
</tr>
<?php

  }

}


$mail = $session->shibGetMail();
//$sql = "SELECT IDPerson FROM source_diplomhgk_map WHERE `Partition`=".$db->qstr( 'edu' )." AND sam=".$db->qstr( $session->shibGetUID());
//$number = intval($db->GetOne( $sql ));
$number = intval($session->shibGetEmployeenumber());
$username = $session->shibGetUsername();

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
    <?php include( 'header.inc.php' );  ?>
  </div>
      <div class="navbar navbar-inverse bg-inverse">
        <div class="container d-flex justify-content-between">
          <a href="#" class="navbar-brand">Diplom HGK <?php echo $year; ?> - Upload</a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>
      </div>

      <section class="jumbotron text-center">
        <div class="container">
          <h1 class="jumbotron-heading">Diplom  <?php echo $year; ?> - Upload</h1>
          <p class="lead text-muted">
            Select student(s) for upload
          </p>
          <p>
            <?php echo htmlspecialchars($username); ?>
          </p>
            <noscript>
              <h2>
                Diese Seite benötigt Javascript. Bitte verwenden Sie einen Webbrowser, der Javascript unterstützt.
              </h2>
            </noscript>

            <table class="table table-striped">
<thead>
<tr>
<th>Diplom</th>
<th>Name</th>
<th>Vorname</th>
<th>Status</th>
</tr>
</thead>
<tbody>
<?php

if( array_key_exists( $mail, $auth )) {
  foreach( $auth[$mail] as $anlass ) {
    $sql = "SELECT * FROM source_diplomhgk WHERE year={$year} AND Anlassnummer=".$db->qstr( $anlass )." ORDER BY Nachname, Vornamen";
    echo "<!-- $sql -->\n";
    $rs = $db->Execute( $sql );
    $num = $rs->RecordCount();
    foreach( $rs as $row ) {
      personRow( $row );
    } // foreach
    $rs->Close();
  } // foreach
} // if auth
else {
    $sql = "SELECT * FROM source_diplomhgk WHERE year={$year} AND IDPerson=".$db->qstr($number);
    echo "<!-- $sql -->\n";
    $rs = $db->Execute( $sql );
    $num = $rs->RecordCount();
    foreach( $rs as $row ) {
      personRow( $row );
    }
    $rs->Close();
}
 ?>
</tbody>
</table>
<?php
if( $num == 0 ) {
?>
  <div class="alert alert-danger" role="alert">
    keinen Diplomeintrag für <?php echo htmlspecialchars( $session->shibGetUsername()); ?> gefunden.
  </div>
<?php
}
 ?>
        </div>
      </section>

    <div class="container">
      <form method="POST" action="submit.php" role="form" id="apply">
        <div class="error alert alert-danger" role="alert">
          <span>&nbsp;</span>
        </div>
<?php

 ?>


      </form>
    </div>

<hr />
<?php include( 'footer.inc.php' ); ?>

 <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
 <script src="js/jquery.validate.js"></script>
 <script language="javascript">
  $(document).ready( function () {
    $("div.error").hide();
  });
 </script>
</body>
</html>
