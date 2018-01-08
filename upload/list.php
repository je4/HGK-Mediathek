<?php
namespace Mediathek;

  include( '../init.inc.php' );

if( !$session->isLoggedIn()) {
  header( 'Location: https://mediathek.hgk.fhnw.ch/auth?target='.urlencode($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']) );
  exit;
}

$archive = trim( $_REQUEST['group'] );
$library = null;
$sql = "SELECT data FROM zotero.groups WHERE id=".intval( $archive );
$data = $db->getOne( $sql );
if( $data ) {
  $library = new Zotero\Library( json_decode(gzdecode( $data ), true));
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
  </head>

  <body class="">

    <?php include( 'left.inc.php' ); ?>
    <?php include( 'head.inc.php' ); ?>

    <!-- ########## START: MAIN PANEL ########## -->
        <div class="br-mainpanel">
          <div class="br-pageheader">
            <nav class="breadcrumb pd-0 mg-0 tx-12">
              <a class="breadcrumb-item" href="index.php">Mediathek HGK</a>
              <a class="breadcrumb-item" href="archives.php">Archive</a>
              <a class="breadcrumb-item" href="#"><?php echo htmlspecialchars($library ? $library->getName() : $archive); ?></a>
            </nav>
          </div><!-- br-pageheader -->
          <div class="br-pagetitle">
            <i class="icon icon ion-ios-box-outline"></i>
            <div>
              <h4><?php echo htmlspecialchars($library ? $library->getName() : $archive); ?></h4>
              <p class="mg-b-0"><?php echo htmlspecialchars($library ? $library->getDescription() : ''); ?></p>
            </div>
          </div><!-- d-flex -->

          <div class="br-pagebody">
            <div class="br-section-wrapper">
<?php
  if( !$library ) {
?>
    <div class="alert alert-danger alert-bordered" role="alert">
      <strong class="d-block d-sm-inline-block-force">Sammlung "<?php echo $archive; ?>" nicht gefunden!</strong> Die ausgewählte Sammlung exisitiert leider nicht.
    </div><!-- alert -->
<?php
  }
  else {
    $acls = $library->getACL( 'acl_upload' );
    if( !$session->inAnyGroup( $acls )) {
?>
<div class="alert alert-danger alert-bordered" role="alert">
  <strong class="d-block d-sm-inline-block-force"></strong> You successfully read this important alert message.
</div><!-- alert -->
<?php
  }
  else {
?>
    <div class="table-wrapper">
            <div id="datatable2_wrapper" class="dataTables_wrapper no-footer">
              <table id="datatable2" class="table display responsive nowrap dataTable no-footer dtr-inline" role="grid" aria-describedby="datatable2_info" style="width: 1082px;">
              <thead>
                <tr role="row"><th class="wd-15p sorting_asc" tabindex="0" aria-controls="datatable2" rowspan="1" colspan="1" style="width: 139px;" aria-sort="ascending" aria-label="First name: activate to sort column descending">First name</th><th class="wd-15p sorting" tabindex="0" aria-controls="datatable2" rowspan="1" colspan="1" style="width: 139px;" aria-label="Last name: activate to sort column ascending">Last name</th><th class="wd-20p sorting" tabindex="0" aria-controls="datatable2" rowspan="1" colspan="1" style="width: 192px;" aria-label="Position: activate to sort column ascending">Position</th><th class="wd-15p sorting" tabindex="0" aria-controls="datatable2" rowspan="1" colspan="1" style="width: 138px;" aria-label="Start date: activate to sort column ascending">Start date</th><th class="wd-10p sorting" tabindex="0" aria-controls="datatable2" rowspan="1" colspan="1" style="width: 84px;" aria-label="Salary: activate to sort column ascending">Salary</th><th class="wd-25p sorting" tabindex="0" aria-controls="datatable2" rowspan="1" colspan="1" style="width: 246px;" aria-label="E-mail: activate to sort column ascending">E-mail</th></tr>
              </thead>
            </table>

          </div>
          <?php
  }
}
?>

              <h6 class="br-section-label">Archive</h6>
              <!-- <p class="br-section-text">Using the most basic table markup.</p> -->

              <div class="list-group">

                <?php
                  $sql = "SELECT data FROM zotero.groups";
                  $rs = $db->Execute( $sql );
                  foreach( $rs as $row ) {
                    $library = new Zotero\Library( json_decode(gzdecode( $row['data'] ), true));
                    $acls = $library->getACL( 'acl_upload' );
                    if( !$session->inAnyGroup( $acls )) continue;
                 ?>

                <div class="list-group-item pd-y-15 pd-x-20 d-xs-flex align-items-center justify-content-start">
                  <img src="<?php echo $library->hasImage() ? "https://s3.amazonaws.com/zotero.org/images/settings/group/".$library->getID()."_squarethumb.png": "https://via.placeholder.com/280x280"; ?>" class="wd-48 rounded-circle" alt="">
                  <div class="mg-xs-l-15 mg-t-10 mg-xs-t-0 mg-r-auto">
                    <p class="mg-b-0 tx-inverse tx-medium"><?php echo htmlspecialchars( $library->getName()); ?></p>
                    <span class="d-block tx-13"><?php echo nl2br(htmlspecialchars( $library->getDescription())); ?><!-- <br />
                      <pre><?php echo gzdecode( $row['data'] ); ?></pre> --></span>
                  </div>
                  <div class="d-flex align-items-center mg-t-10 mg-xs-t-0">
                    <div class="dropdown">
                      <a href="" class="btn btn-outline-light btn-icon" data-toggle="dropdown">
                        <div class="tx-20"><i class="icon ion-ios-plus-outline"></i><i class="fa fa-angle-down mg-l-5"></i></div>
                      </a>
                      <div class="dropdown-menu pd-10 wd-200">
                        <nav class="nav nav-style-1 flex-column">
                          <a href="upload.php?type=book&group=<?php echo $library->getId(); ?>" class="nav-link"><i class="icon ion-ios-book-outline"></i> Book</a>
                          <a href="upload.php?type=video&group=<?php echo $library->getId(); ?>" class="nav-link"><i class="icon ion-ios-videocam-outline"></i> Video</a>
                          <a href="upload.php?type=audio&group=<?php echo $library->getId(); ?>" class="nav-link"><i class="icon ion-ios-mic-outline"></i> Audio</a>
                          <a href="upload.php?type=music&group=<?php echo $library->getId(); ?>" class="nav-link"><i class="icon ion-ios-musical-notes"></i> Music</a>
                        </nav>
                      </div><!-- dropdown-menu -->
                    </div>
                    <a href="list.php?group=<?php echo $library->getId(); ?>" class="btn btn-outline-light btn-icon mg-l-5">
                      <div class="tx-20"><i class="icon ion-ios-list-outline"></i></div>
                    </a>
                  </div>
                </div><!-- list-group-item -->

<?php } ?>

              </div><!-- list-group -->



            </div><!-- br-section-wrapper -->
          </div><!-- br-pagebody -->
          <footer class="br-footer">
            <div class="footer-left">
              <div class="mg-b-2">Copyright &copy; 2018. Mediathek HGK. All Rights Reserved.</div>
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
    <script src="lib/datatables/jquery.dataTables.js"></script>
    <script src="lib/datatables-responsive/dataTables.responsive.js"></script>

    <script src="js/bracket.js"></script>
    <script>
      $(function(){
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

        $('#datatable2').DataTable({
          bLengthChange: false,
          searching: false,
          responsive: true
        });
      });
    </script>
  </body>
</html>
