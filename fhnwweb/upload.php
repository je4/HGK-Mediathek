<?php
namespace Mediathek;

include( '../init.inc.php' );

$md5 = $session->GetID();

 ?><html>
<head>
  <base href="<?php echo dirname( $_SERVER['SCRIPT_NAME']); ?>/">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
  <link href="fine-uploader/fine-uploader-gallery.min.css" rel="stylesheet">
  <style>
  .card-deck .card {
      width: initial;
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
    <div class="container">
      <div class="row">
        <div class="col-sm-8 py-4">
          <h4 class="text-white">About</h4>
          <p class="text-muted">
            Erstelle aus Personenfotos Formate, welche für dir FHNW Webseite passend sind.
            Was nicht passt wird passend gemacht...
          </p>
        </div>
        <div class="col-sm-4 py-4">
          <h4 class="text-white">Contact</h4>
          <ul class="list-unstyled">
            <li><a href="#" class="text-white">Follow on Twitter</a></li>
            <li><a href="#" class="text-white">Like on Facebook</a></li>
            <li><a href="#" class="text-white">Email me</a></li>
          </ul>
        </div>
      </div>
    </div>
    </div>
      <div class="navbar navbar-inverse bg-inverse">
        <div class="container d-flex justify-content-between">
          <a href="#" class="navbar-brand">PersonPics FHNW</a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>
      </div>

      <section class="jumbotron text-center">
        <div class="container">
          <h1 class="jumbotron-heading">Personal Picture Resizer</h1>
        </div>
      </section>

      <div id="uploader"> </div>

    <div class="container-fluid">
      <p>
      </p>
      <div id="filecontent">
      </div>
    </div>

<hr />
<footer class="text-muted">
  <div class="container">
    <p class="float-right">
      <a href="#">Back to top</a>
    </p>
    <p>(c) Copyright Center for Digital Matter HGK FHNW</p>
  </div>
</footer>


 <script
   src="https://code.jquery.com/jquery-3.2.1.min.js"
   integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
   crossorigin="anonymous"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
 <script src="fine-uploader/jquery.fine-uploader.js"></script>

 <script language="javascript">
  $(document).ready( function () {

    $("#uploader").fineUploader({
        debug: true,
        request: {
            endpoint: 'endpoint.php?form=<?php echo $md5; ?>'
        },
        chunking: {
          enabled: true,
          success: {endpoint: 'endpoint.php?done&form=<?php echo $md5; ?>'},
          concurrent: {enabled: true}
        }
    }).on('complete', function (event, id, filename, responseJSON) {
        var path = responseJSON.uuid+"/"+filename;
        $( "#filecontent" ).load( "pics.load.php?form=<?php echo $md5; ?>" );

        //alert( path );
    });

    $( "#filecontent" ).load( "pics.load.php?form=<?php echo $md5; ?>" );

  });
 </script>
</body>
</html>
