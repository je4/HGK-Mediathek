<?php

include( '../init.inc.php' );

$mail = $session->shibGetMail();
$number = $session->shibGetEmployeenumber();

$id = @intval( $_REQUEST['id']);

$submit = array_key_exists( 'submit', $_REQUEST );

if( $submit && $id>0 ) {
  $data = $_REQUEST['data'];

  foreach( $data  as $key=>$val ){
    $sql = "REPLACE INTO source_diplom2017_data( idperson, name, value ) VALUES(
        {$id}
      , ".$db->qstr( $key )."
      , ".$db->qstr( $val )."
    )";
    $db->Execute( $sql );
  }

  $sql = "UPDATE source_diplom2017 SET done=1 WHERE idperson=".$id;
  $db->Execute( $sql );
}

$sql = "SELECT * FROM source_diplom2017 WHERE idperson=".$id;
$user = $db->GetRow( $sql );

function formString( $id, $label, $descr, $required ) {
?>
  <div class="form-group row">
    <label for="<?php echo $id; ?>" class="col-sm-2 col-form-label"><?php echo htmlspecialchars( $label ); if( $required ) echo '<a class="required" href="#" data-toggle="tooltip" title="Pflichtfeld">*</a>'; ?></label>
    <div class="col-sm-10">
      <input name="data[<?php echo $id; ?>]" type="text" class="form-control" id="<?php echo $id; ?>" aria-describedby="<?php echo $id; ?>Help" placeholder="" <?php echo $required ? 'required' : ''; ?>>
      <small id="<?php echo $id; ?>Help" class="form-text text-muted"><?php echo htmlspecialchars( $descr ); ?></small>
    </div>
  </div>
<?php
}

function formText( $id, $label, $descr, $required, $lines=3 ) {
?>
<div class="form-group row">
  <label for="<?php echo $id; ?>" class="col-sm-2 col-form-label"><?php echo htmlspecialchars( $label ); if( $required ) echo '<a class="required" href="#" data-toggle="tooltip" title="Pflichtfeld">*</a>'; ?></label>
  <div class="col-sm-10">
    <textarea rows=6 name="data[<?php echo $id; ?>]" type="text" class="form-control" id="<?php echo $id; ?>" aria-describedby="<?php echo $id; ?>Help" placeholder="" <?php echo $required ? 'required' : ''; ?>></textarea>
    <small id="<?php echo $id; ?>Help" class="form-text text-muted"><?php echo nl2br( htmlspecialchars( $descr )); ?></small>
  </div>
</div>
<?php
}

function formSelect( $id, $label, $descr, $required, $sel ) {
?>
<div class="form-group row">
  <label for="<?php echo $id; ?>" class="col-sm-2 col-form-label"><?php echo htmlspecialchars( $label ); if( $required ) echo '<a class="required" href="#" data-toggle="tooltip" title="Pflichtfeld">*</a>'; ?></label>
  <div class="col-sm-10">
  <select name="data[<?php echo $id; ?>]" class="custom-select" id="<?php echo $id; ?>" aria-describedby="<?php echo $id; ?>Help" placeholder="" <?php echo $required ? 'required' : ''; ?>>
   <option value="" selected>Bitte auswählen</option>
<?php
  foreach( $sel as $s ) {
?>
   <option><?php echo htmlspecialchars( $s ); ?></option>
<?php
  }
?>
  </select>
  <small id="<?php echo $id; ?>Help" class="form-text text-muted"><?php echo htmlspecialchars( $descr ); ?></small>
</div>
</div>
<?php
}
 ?><html>
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
  <link href="fine-uploader/fine-uploader-gallery.min.css" rel="stylesheet">

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
  <script type="text/template" id="qq-template">
      <div class="qq-uploader-selector qq-uploader qq-gallery" qq-drop-area-text="Dateien hier ablegen">
          <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
              <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
          </div>
          <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
              <span class="qq-upload-drop-area-text-selector"></span>
          </div>
          <div class="qq-upload-button-selector qq-upload-button">
              <div>Datei hochladen</div>
          </div>
          <span class="qq-drop-processing-selector qq-drop-processing">
              <span>Verarbeite Dateien...</span>
              <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
          </span>
          <ul class="qq-upload-list-selector qq-upload-list" role="region" aria-live="polite" aria-relevant="additions removals">
              <li>
                  <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
                  <div class="qq-progress-bar-container-selector qq-progress-bar-container">
                      <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
                  </div>
                  <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
                  <div class="qq-thumbnail-wrapper">
                      <img class="qq-thumbnail-selector" qq-max-size="120" qq-server-scale>
                  </div>
                  <button type="button" class="qq-upload-cancel-selector qq-upload-cancel">X</button>
                  <button type="button" class="qq-upload-retry-selector qq-upload-retry">
                      <span class="qq-btn qq-retry-icon" aria-label="Retry"></span>
                      Retry
                  </button>

                  <div class="qq-file-info">
                      <div class="qq-file-name">
                          <span class="qq-upload-file-selector qq-upload-file"></span>
                          <span class="qq-edit-filename-icon-selector qq-btn qq-edit-filename-icon" aria-label="Edit filename"></span>
                      </div>
                      <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
                      <span class="qq-upload-size-selector qq-upload-size"></span>
                      <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">
                          <span class="qq-btn qq-delete-icon" aria-label="Delete"></span>
                      </button>
                      <button type="button" class="qq-btn qq-upload-pause-selector qq-upload-pause">
                          <span class="qq-btn qq-pause-icon" aria-label="Pause"></span>
                      </button>
                      <button type="button" class="qq-btn qq-upload-continue-selector qq-upload-continue">
                          <span class="qq-btn qq-continue-icon" aria-label="Continue"></span>
                      </button>
                  </div>
              </li>
          </ul>

          <dialog class="qq-alert-dialog-selector">
              <div class="qq-dialog-message-selector"></div>
              <div class="qq-dialog-buttons">
                  <button type="button" class="qq-cancel-button-selector">Schliessen</button>
              </div>
          </dialog>

          <dialog class="qq-confirm-dialog-selector">
              <div class="qq-dialog-message-selector"></div>
              <div class="qq-dialog-buttons">
                  <button type="button" class="qq-cancel-button-selector">Nein</button>
                  <button type="button" class="qq-ok-button-selector">Ja</button>
              </div>
          </dialog>

          <dialog class="qq-prompt-dialog-selector">
              <div class="qq-dialog-message-selector"></div>
              <input type="text">
              <div class="qq-dialog-buttons">
                  <button type="button" class="qq-cancel-button-selector">Abbrechen</button>
                  <button type="button" class="qq-ok-button-selector">Ok</button>
              </div>
          </dialog>
      </div>
  </script>


</head>
<body>
  <div class="collapse bg-inverse" id="navbarHeader">
    <?php include( 'header.inc.php' );  ?>
  </div>
      <div class="navbar navbar-inverse bg-inverse">
        <div class="container d-flex justify-content-between">
          <a href="#" class="navbar-brand">Diplom HGK 2017 - Upload</a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>
      </div>

      <section class="jumbotron text-center">
        <div class="container">
          <h1 class="jumbotron-heading">Diplom 2017 - Upload</h1>
          <p class="lead text-muted">
            Please fill out the following form to upload your text and images describing your DIPLOM work 2017.
            </p>
            <noscript>
              <h2>
                Diese Seite benötigt Javascript. Bitte verwenden Sie einen Webbrowser, der Javascript unterstützt.
              </h2>
            </noscript>


        </div>
      </section>

    <div class="container">
      <form method="POST" action="form.php" role="form" id="apply">
        <input type="hidden" name="submit" value="yes" />
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <div class="error alert alert-danger" role="alert">
          <span>&nbsp;</span>
        </div>

            <p>

<?php
if( $id == 0 || count($user) == 0 ) {
?>
  <div class="alert alert-danger" role="alert">
    <h2>invalid user (#<?php echo $id; ?>)</h2>
  </div>
<?php
}
elseif( $user['done'] == 1 ) {
  echo '<h2>'.htmlentities( "{$user['Vornamen']} {$user['Nachname']} ({$user['IDPerson']})" ).'</h2>';
  echo '<h3>'.htmlentities( "{$user['Anlassbezeichnung']}" ).'</h3>';

?>
<div class="alert alert-info" role="alert">Upload abgeschlossen</div>
<?php
$sql = "SELECT * FROM source_diplom2017_data WHERE idperson=".$id;
$rs = $db->Execute( $sql );
foreach( $rs as $row ) {
  echo nl2br(htmlentities( "{$row['name']}: {$row['value']}" ))."<br />";
}
?>
<div style="margin-top: 100px;" id="filecontent"></div>
<?php
}
else {
  echo '<h2>'.htmlentities( "{$user['Vornamen']} {$user['Nachname']} ({$user['IDPerson']})" ).'</h2>';
  echo '<h3>'.htmlentities( "{$user['Anlassbezeichnung']}" ).'</h3>';
echo "<p />";
formString( 'email', 'Email Adresse', 'Emailadresse für die DiplomHGK Seite', false );
formString( 'web1', 'Webseite #1', 'Kontakt Webseite (persönliche Website)', false );
formString( 'web2', 'Webseite #2', 'Zusätzliche Webseite (Projektseite etc...)', false );
formString( 'titel', 'Titel (Diplomarbeit)', 'Bsp: Complexity', true );
formString( 'untertitel', 'Untertitel (Diplomarbeit)', 'Bsp: Der Reichtum der Unterschiede', true );
formText( 'beschreibung', 'Beschreibung', 'Projektbeschrieb (Diplomarbeit)', true );
formText( 'webmedia', 'Webadressen', 'Webadressen für Video, Audio und Pdf: Vimeo, YouTube, Issuu, Soundcloud
Z.B.:
https://vimeo.com/39825378
https://youtu.be/Yyl1xxdatn8
http://issuu.com/interiordesignandscenography/docs/iis_yearbook2013/1
https://soundcloud.com/fhnw-hgk-iku/mah02448mp4-dance', false );
 ?>
<p>
  <div id="uploader"> </div>
</p>
<p>
            <div class="form-group row">
              <button type="submit" value="Validate!" class="btn btn-primary btn-block" style="height: 100px; font-size: 36px;">Fertig</button>
            </div>
          </p>
    </form>
    <div style="margin-top: 100px;" id="filecontent">
    </div>
<?php } ?>
<hr />
<?php include( 'footer.inc.php' ); ?>

<script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
 <script src="fine-uploader/jquery.fine-uploader.js"></script>
 <script src="js/jquery.validate.js"></script>
 <script language="javascript">
  $(document).ready( function () {


        $("div.error").hide();


    $("#uploader").fineUploader({
        debug: true,
        request: {
            endpoint: 'endpoint.php?id=<?php echo $id; ?>'
        },
        chunking: {
          enabled: true,
          success: {endpoint: 'endpoint.php?done&id=<?php echo $id; ?>'},
          concurrent: {enabled: true}
        }
    }).on('complete', function (event, id, filename, responseJSON) {
        var path = responseJSON.uuid+"/"+filename;
        $( "#filecontent" ).load( "file_cards.load.php?id=<?php echo $id; ?>" );
        $('#submitButton').attr("disabled", false);
        //alert( path );
    }).on('submit', function (event, id, filename, responseJSON) {
      $('#submitButton').attr("disabled", true);

    });

    $( "#filecontent" ).load( "file_cards.load.php?id=<?php echo $id; ?>" );

    $('#bpers').on( 'click', function( event ) {
      $('#awerk').tab('show');
    });

    $('.required').tooltip();

    $( '#apply' ).validate( {
       ignore: '.ignore',
       rules: {
          // at least 15€ when bonus material is included
          "data[rechtesumme]": {
            required: true,
            min: {
              // min needs a parameter passed to it
              minlength: 2,
            }
          },
          "data[lizenz]": {
            required: true,
            min: {
              // min needs a parameter passed to it
              minlength: 2,
            }
          },
          /*
          "data[rechte]": {
            required: true,
            min: {
              // min needs a parameter passed to it
              minlength: 2,
            }
          }
          */
        },
       errorPlacement: function(error, element) {
          // error.appendTo( element.parent("td").next("td") );
          // <div class="form-control-feedback">Sorry, that username's taken. Try another?</div>
          //element.insertAfter( '<div class="form-control-feedback">Sorry, that username is taken. Try another?</div>' )
          element.closest( '.form-group').addClass( 'has-danger' );
          element.addClass( 'form-control-danger' );
          var id = element.closest( '.tab-pane' ).attr( 'id' );
          if( id == 'pers' ) {
            $( '#apers').tab( 'show' );
          }
        },
        unhighlight: function(element, errorClass, validClass) {
          var el = element.closest( '.form-group');
          if( el ) $(el).removeClass( 'has-danger' );
          $(element).removeClass( 'form-control-danger' );
          $("div.error").hide();
        },
       invalidHandler: function(event, validator) {
         var errors = validator.numberOfInvalids();
          if (errors) {
            var message = errors == 1
              ? '1 Feld wurde nicht korrekt Ausgefüllt. Es wurde markiert'
              : errors + ' Felder wurden nicht korrekt ausgefüllt. Sie wurden markiert';
            $("div.error span").html(message);
            $("div.error").show();
          } else {
            $("div.error").hide();
          }
     }
    });

  });
 </script>
</body>
</html>