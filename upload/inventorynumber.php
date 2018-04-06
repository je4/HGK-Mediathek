<?php
include( '../init.inc.php' );

if( !$session->isLoggedIn()) {
    header( 'Location: https://mediathek.hgk.fhnw.ch/auth?target='.urlencode($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']) );
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
        <i class="icon icon ion-pricetags-outline"></i>
        <div>
            <h4>Inventarnummern</h4>
            <p class="mg-b-0">Hier können eindeutige Inventarnummern bezogen werden</p>
        </div>
    </div><!-- d-flex -->

    <div class="br-pagebody">
        <div class="card pd-30 shadow-base bd-0 mg-t-20">
            <div class="card-title">Nummergenerierung</div>
            <div class="justify-content-lg-between align-items-lg-center">
                <div class="mg-b-20 mg-lg-b-0">
                    <h4 id="newInventoryNo" class="tx-normal tx-roboto tx-inverse d-inline mg-b-5 mg-r-20">YXXXXXXXXXX</h4> <button type="button" class="btn btn-icon btn-outline-primary mg-b-5" id="btnCopyToClipboard" data-toggle="tooltip-primary" data-placement="top" title="In die Zwischenablage"><div><i class="fa fa-clipboard"></i></div></button>
                    <p class="tx-13 mg-b-0">Mit einem Klick auf den entsprechenden Button, kann eine Single oder Bundle-ID erzeugt werden.</p>
                </div>
                <div class="mg-b-20 mg-lg-b-0 mg-t-20">
                    <button class="btn btn-outline-primary" id="btnCreateSingle"><i class="fa fa-tag mg-r-10"></i> Single</button>
                    <button class="btn btn-outline-primary" id="btnCreateBundle"><i class="fa fa-tags mg-r-10"></i> Bundle</button>
                </div>
            </div><!-- row -->
        </div>
        <div class="card pd-30 shadow-base bd-0 mg-t-20">
            <div class="card-title">Nummerinfos</div>
            <div class="justify-content-lg-between align-items-lg-center">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Suche Inventarnummer" id="searchTerm">
                    <span class="input-group-btn">
                  <button class="btn bd bg-white tx-gray-600" type="button" id="btnSearch"><i class="fa fa-search"></i></button>
                </span>
                </div>
                <p id="searchError" class="tx-danger"></p>
                <h6 id="searchTitle"></h6>
                <p id="searchCreated"></p>
            </div><!-- row -->
        </div>
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

        $('#btnCreateSingle').on('click', function (e) {
            $('#newInventoryNo').text("- - - - - - - - - -");
                $.post("inventorynumber_gen.php", {type: "S"})
                .done(function(msg){
                    $('#newInventoryNo').text(msg);
                }).fail(function(xhr, status, error) {
                    if(error==="Unauthorized")
                        $('#newInventoryNo').text('Fehler: Nicht eingeloggt');
                    else
                        $('#newInventoryNo').text('Fehler beim Erzeugen');
                });
        });
        $('#btnCreateBundle').on('click', function (e) {
            $('#newInventoryNo').text("- - - - - - - - - -");
            $.post("inventorynumber_gen.php", {type: "B"})
                .done(function(msg){
                    $('#newInventoryNo').text(msg);
                }).fail(function(xhr, status, error) {
                if(error==="Unauthorized")
                    $('#newInventoryNo').text('Fehler: Nicht eingeloggt');
                else
                    $('#newInventoryNo').text('Fehler beim Erzeugen');
            });
        });

        $('#btnCopyToClipboard').on('click', function (e) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val($('#newInventoryNo').text()).select();
            document.execCommand("copy");

            $temp.remove();
        });

        $('#btnSearch').on('click', function (e) {
            $('#searchError').text('');
            $('#searchTitle').text('');
            $('#searchCreated').text('');
            $.get("inventorynumber_info.php", {search: $('#searchTerm').val()})
                .done(function(msg){
                    var resp = JSON.parse(msg);
                    //alert(msg);
                    $('#searchTitle').text(resp.type + pad(resp.id,10));
                    $('#searchCreated').text('Erstellt von '+resp.created_by+' am '+ resp.created_at);
                }).fail(function(xhr, status, error) {
                if(error==="Unauthorized")
                    $('#searchError').text('Nicht eingeloggt');
                else if(error==="Not Found")
                    $('#searchError').text('Nicht gefunden');
                else if(error==="Bad Request")
                    $('#searchError').text('Ungültige Eingabe');
                else
                    $('#searchError').text('Allg. Fehler');
            });
        });

        function pad (str, max) {
            str = str.toString();
            return str.length < max ? pad("0" + str, max) : str;
        }
    });
</script>
</body>
</html>
