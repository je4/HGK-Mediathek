<?php
namespace Mediathek;

  include( '../init.inc.php' );

if( !$session->isLoggedIn()) {
  header( 'Location: https://mediathek.hgk.fhnw.ch/auth?target='.urlencode($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']) );
  exit;
}
if( !$session->inAnyGroup( array( 'mab/upload' ))) {
  echo 'insufficient rights';
  exit;
}


?><!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Meta -->
    <meta name="description" content="Upload for Mediathek HGK">
    <meta name="author" content="FHNW HGK / Center for Digital Matter">

    <title>Mediathek HGK / Archive Uploader</title>

    <!-- vendor css -->
    <link href="lib/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="lib/Ionicons/css/ionicons.css" rel="stylesheet">
    <link href="lib/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
    <link href="lib/jquery-switchbutton/jquery.switchButton.css" rel="stylesheet">

    <!-- Bracket CSS -->
    <link rel="stylesheet" href="css/bracket.css">

    <!-- Fine Uploader -->
    <link href="fine-uploader/fine-uploader-gallery.min.css" rel="stylesheet">
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

  <body class="">

    <?php include( 'left.inc.php' ); ?>
    <?php include( 'head.inc.php' ); ?>

    <!-- ########## START: MAIN PANEL ########## -->
        <div class="br-mainpanel">
          <div class="br-pageheader">
            <nav class="breadcrumb pd-0 mg-0 tx-12">
              <a class="breadcrumb-item" href="index.php">Mediathek HGK</a>
              <a class="breadcrumb-item" href="#">Archive</a>
            </nav>
          </div><!-- br-pageheader -->
          <div class="br-pagetitle">
            <i class="icon icon ion-ios-cloud-upload-outline"></i>
            <div>
              <h4>PDF Upload MAB Basel</h4>
              <p class="mg-b-0">Neue PDF-Dateien in die Dropzone ziehen</p>
            </div>
          </div><!-- d-flex -->

          <div class="br-pagebody">
            <div class="br-section-wrapper">
              <h6 class="br-section-label">Upload Area</h6>
              <!-- <p class="br-section-text">Using the most basic table markup.</p> -->

                <div id="uploader"> </div>

                <div class="form-layout form-layout-5">
                  <div class="row">
                  <label class="col-sm-4 form-control-label"><span class="tx-danger">*</span> Systemnummer:</label>
                  <div class="col-sm-8 mg-t-10 mg-sm-t-0">
                    <input id="systemnummer" type="text" class="form-control" placeholder="Systemnummer eingeben">
                  </div>
                </div>
                <div class="row mg-t-30">
                  <div class="col-sm-8 mg-l-auto">
                    <div class="form-layout-footer">
                      <button id="submitButton" onmouseup="doSystemSubmit()" class="btn btn-info" disabled>Dateien eintragen</button>
                    </div><!-- form-layout-footer -->
                  </div><!-- col-8 -->
                </div>
              </div>

              <h6 class="br-section-label">MARC Code</h6>
                <div id="marc"></div>
            </div><!-- br-section-wrapper -->
          </div><!-- br-pagebody -->
          <footer class="br-footer">
            <div class="footer-left">
              <div class="mg-b-2">Copyright &copy; 2018. Center for Digital Matter HGK FHNW. All Rights Reserved.</div>
              <div>Attentively and carefully made by <a href="https://www.fhnw.ch/de/die-fhnw/hochschulen/hgk/campus-der-kuenste/center-for-digital-matter">Center for Digital Matter</a>.</div>
            </div>
          </footer>
        </div><!-- br-mainpanel -->
        <!-- ########## END: MAIN PANEL ########## -->




    <script src="lib/jquery/jquery.js"></script>
    <script src="lib/popper.js/popper.js"></script>
    <script src="lib/bootstrap/bootstrap.js"></script>
    <script src="lib/perfect-scrollbar/js/perfect-scrollbar.jquery.js"></script>
    <script src="lib/moment/moment.js"></script>
    <script src="lib/jquery-ui/jquery-ui.js"></script>
    <script src="lib/jquery-switchbutton/jquery.switchButton.js"></script>
    <script src="lib/peity/jquery.peity.js"></script>
    <script src="fine-uploader/jquery.fine-uploader.js"></script>

    <script src="js/bracket.js"></script>
    <script>
      var timestamp = Date.now();
      var uploader = null;

      function doSystemSubmit() {
        systemnummer = $( "#systemnummer" ).val();
        $('#submitButton').attr("disabled", true);
        $("#marc").load( "buildMARC.php?id="+timestamp+"&systemnummer="+systemnummer );
//        alert( systemnummer );
        $( "#systemnummer" ).val("");
        timestamp = Date.now();
        initUploader();
      }

      function initUploader() {
        $("#uploader").fineUploader({
            debug: true,
            request: {
                endpoint: 'endpoint.php?id='+timestamp
            },
            chunking: {
              enabled: true,
              success: {
                  endpoint: 'endpoint.php?done&id='+timestamp
                },
              concurrent: {enabled: true}
            }
        }).on('allComplete', function (event, succeeded, failed ) {
            //var path = responseJSON.uuid+"/"+filename;
            //$( "#filecontent" ).load( "file_cards.load.php?id="+timestamp );
            $('#submitButton').attr("disabled", false);
            //alert( path );
        }).on('submit', function (event, id, filename, responseJSON) {
          $('#submitButton').attr("disabled", true);

        });

      }

      $(document).ready( function () {
        'use strict';

        // show only the icons and hide left menu label by default
        //$('.menu-item-label,.menu-item-arrow').addClass('op-lg-0-force d-lg-none');

        $(document).on('mouseover', function(e){
          e.stopPropagation();
          if($('body').hasClass('collapsed-menu')) {
            var targ = $(e.target).closest('.br-sideleft').length;
            if(targ) {
              $('body').addClass('expand-menu');

              // show current shown sub menu that was hidden from collapsed
              $('.show-sub + .br-menu-sub').slideDown();

              var menuText = $('.menu-item-label,.menu-item-arrow');
              menuText.removeClass('d-lg-none');
              menuText.removeClass('op-lg-0-force');

            } else {
              $('body').removeClass('expand-menu');

              // hide current shown menu
              $('.show-sub + .br-menu-sub').slideUp();

              var menuText = $('.menu-item-label,.menu-item-arrow');
              menuText.addClass('op-lg-0-force');
              menuText.addClass('d-lg-none');
            }
          }
        });

        $('.br-mailbox-list').perfectScrollbar();

        $('#showMailBoxLeft').on('click', function(e){
          e.preventDefault();
          if($('body').hasClass('show-mb-left')) {
            $('body').removeClass('show-mb-left');
            $(this).find('.fa').removeClass('fa-arrow-left').addClass('fa-arrow-right');
          } else {
            $('body').addClass('show-mb-left');
            $(this).find('.fa').removeClass('fa-arrow-right').addClass('fa-arrow-left');
          }
        });

        initUploader();

      });
    </script>
  </body>
</html>
