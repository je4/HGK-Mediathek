<?php

include( 'init.inc.php' );

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
    <textarea rows=3 name="data[<?php echo $id; ?>]" type="text" class="form-control" id="<?php echo $id; ?>" aria-describedby="<?php echo $id; ?>Help" placeholder="" <?php echo $required ? 'required' : ''; ?>></textarea>
    <small id="<?php echo $id; ?>Help" class="form-text text-muted"><?php echo htmlspecialchars( $descr ); ?></small>
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
          <a href="#" class="navbar-brand">Ausschreibung &Sigma; Summe &ndash; VIDEO</a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>
      </div>

      <section class="jumbotron text-center">
        <div class="container">
          <h1 class="jumbotron-heading">Ausschreibung für Video-Beiträge zur &Sigma; Summe &ndash; VIDEO<br />
            4. – 19. November 2017</h1>
          <p class="lead text-muted">
              Unter dem Zeichen &Sigma; &ndash; für Summe &ndash; versammeln sich unabhängige Projekträume
              aus dem Raum Basel: Wir laden diesesmal zur Videowerkschau ein! Die verschiedenen Räume kuratieren
              eigenständige Ausstellungen und Programme und zeigen im Monat November audiovisuelle
              Arbeiten aus dem aktuellen Videoschaffen.
            </p>


        </div>
      </section>

    <div class="container">
      <form method="POST" action="submit.php" role="form" id="apply">
        <div class="error alert alert-danger" role="alert">
          <span>&nbsp;</span>
        </div>

        <ul class="nav nav-tabs">

          <li class="nav-item">
            <a class="nav-link active" id="apers" data-toggle="tab" href="#pers" role="tab">Persönliche Daten</a>
          </li>

          <li class="nav-item">
          <a class="nav-link" id="awerk" data-toggle="tab" href="#werk" role="tab">Video- und Filmwerk</a>
          </li>

        </ul>

        <div class="tab-content">
          <div class="tab-pane fade show active" id="pers" role="tabpanel">
            <p>

<?php
formString( 'email', 'Email Adresse', 'Wir benötigen Ihre Emailadresse um Ihre Einreichung zu bearbeiten.', true );
formString( 'nachname', 'Nachname', 'Name der Künstlerin / des Künstlers.', true );
formString( 'vorname', 'Vorname', 'Vorname der Künstlerin / des Künstlers.', true );
formString( 'jahrgang', 'Jahrgang', 'Geburtsjahr der Künstlerin / des Künstlers.', true );
formText( 'adresse', 'Adresse', 'Adresse der Künstlerin / des Künstlers.', true );
formString( 'tel', 'Telefon/Mobile', 'Telefonnummer der Künstlerin / des Künstlers.', true );
formString( 'web', 'Webseite', 'Webseite der Künstlerin / des Künstlers oder des Werkes.', false );
 ?>



            <button id="bpers" type="button" class="btn btn-secondary btn-block">Weiter</button>
          </p>
          </div>
          <div class="tab-pane fade" id="werk" role="tabpanel">
            <p>
<?php
formString( 'titel', 'Titel der Arbeit', 'Titel der Arbeit / des Werkes.', true );
formString( 'werkjahr', 'Erscheinungsjahr', 'Erstellungs- oder Veröffentlichungsjahr der Arbeit.', true );
formSelect( 'medium', 'Medium', 'Medium der Arbeit.', true,
  array(  'Video (16:9)'
        , 'Video (4:3)'
        , '16mm Film'
        , '8mm Film'
        , 'Mehrkanal'
        , 'Installation'
      ) );
formString( 'anderesformat', 'Anderes Format', 'Bitte bei speziellen Formaten ausfüllen.', false );
formString( 'dauer', 'Dauer', 'Dauer des Werkes (z.B. 75:30 für 1h 15min 30sec).', true );
formString( 'auflage', 'Auflage', 'Auflage des Werkes.', false );
formSelect( 'ton', 'Ton', 'Ton.', true,
  array(  'ohne Ton'
        , 'Mono'
        , 'Stereo'
        , 'Mehrkanal'
      ) );
formString( 'sprache', 'Sprache', 'Verwendete Sprachen: Gesprochen und/ oder Untertitel.', false );
formSelect( 'art', 'Art/Kategorie', 'Einordnung der Arbeit.', true,
  array( 'Animation'
        , 'Dokumentation/Dokumentarfilm'
        , 'Essayfilm'
        , 'Fiktion'
        , 'Handy Video'
        , 'Installation'
        , 'Kunstvideo'
        , 'Medienexperiment'
        , 'Musikclip'
        , 'Netzkunst'
        , 'sonstiges'
      ) );
formText( 'descr', 'Kurzbeschreibung (600-900 Zeichen max.)', 'Beschreibung der Arbeit.', true, 8 );
formSelect( 'bezug', 'Bezug zu Basel', 'In welchem Bezug steht die Arbeit zu Basel.', true,
  array(  'persönlicher Bezug der / des  Kunstschaffenden zur Region Basel (Wohn- oder Geburtsort / Studium etc.)'
        , 'Werk hat einen direkten Bezug zur Region Basel  ( Drehort, Mitwirkende, Themen etc.)'
        , 'kein direkter Bezug zur Region Basel'
      ) );
?>
<hr />

<div class="form-group row">
  <label for="lizenz" class="col-sm-2 col-form-label">Lizenz(en)<a class="required" href="#" data-toggle="tooltip" title="Pflichtfeld">*</a></label>
  <div class="col-sm-10">
    <label class="custom-control custom-radio">
      <input id="lizenz1" type="radio" class="custom-control-input" value="cc-by" name="data[lizenz]">
      <span class="custom-control-indicator"></span>
      <span class="custom-control-description">
        Es bestehen keine expliziten Lizenzbedingungen
      </span>
    </label>
    <br />
    <label class="custom-control custom-radio">
      <input id="lizenz2" type="radio" class="custom-control-input" value="cc-by-nc" name="data[lizenz]">
      <span class="custom-control-indicator"></span>
      <span class="custom-control-description">
        Das eingereichte Werk ist lizenzrechtlich geschützt. Eine Kopie der Lizenzbedingungen wird beigefügt (siehe Upload).
      </span>
    </label>
    <!--
    <br />
    <label class="custom-control custom-radio">
      <input id="lizenz3" type="radio" class="custom-control-input" value="cc-by-nd" name="data[lizenz]">
      <span class="custom-control-indicator"></span>
      <span class="custom-control-description">
        Das eingereichte Werk ist urheberrechtlich geschützt.
        Es soll unter Nennung meines Namens frei zugänglich sein.
        Es darf nicht verändert werden und wird unter der <a target="_blank" href="https://creativecommons.org/licenses/by-nd/4.0/deed.de">Creative-Commons Lizenz CC - BY - ND</a>
        zugänglich gemacht.
      </span>
    </label>
    <br />
    <label class="custom-control custom-radio">
      <input id="lizenz4" type="radio" class="custom-control-input" value="cc-by-nc-nd" name="data[lizenz]">
      <span class="custom-control-indicator"></span>
      <span class="custom-control-description">
        Das eingereichte Werk ist urheberrechtlich geschützt.
        Es soll unter Nennung meines Namens für nicht kommerzielle Zwecke frei zugänglich sein.
        Es darf nicht verändert werden und wird unter der <a target="_blank" href="https://creativecommons.org/licenses/by-nc-nd/4.0/deed.de">Creative-Commons Lizenz CC - BY - NC - ND</a>
        zugänglich gemacht.
      </span>
    </label>
    <br />
    <label class="custom-control custom-radio">
      <input id="lizenz5" type="radio" class="custom-control-input" value="late" name="data[lizenz]">
      <span class="custom-control-indicator"></span>
      <span class="custom-control-description">
        Das eingereichte Werk ist urheberrechtlich geschützt.
        Die Nutzungsrechte werden nur für den Fall erteilt, dass das Werk in das Festivalprogramm aufgenommen wird.
        Die Nachnutzung wird im Abschnitt Nachnutzung geregelt.
      </span>
    </label>
    <br />
    <label class="custom-control custom-radio">
      <input id="lizenz6" type="radio" class="custom-control-input" value="hgk" name="data[lizenz]">
      <span class="custom-control-indicator"></span>
      <span class="custom-control-description">
        Das eingereichte Werk darf in den Räumlichkeiten der Mediathek HGK bis auf Widerruf gesichtet werden.
      </span>
    </label>
    <br />
    <label class="custom-control custom-radio">
      <input id="lizenz7" type="radio" class="custom-control-input" value="hgk-internet" name="data[lizenz]">
      <span class="custom-control-indicator"></span>
      <span class="custom-control-description">
        Das eingereichte Werk darf über die Website der Mediathek HGK öffentlich gesichtet werden.
      </span>
    </label>
  -->
  </div>
</div>

<hr />
<div class="form-group row">
  <label for="rechtesumme" class="col-sm-2 col-form-label">Nutzung<a class="required" href="#" data-toggle="tooltip" title="Pflichtfeld">*</a></label>
    <div class="col-sm-10">
      <p>Rechte zur Nutzung während der ∑&nbsp;&ndash;&nbsp;Summe 2017</p>
  <div class="form-check">
      <label class="custom-control custom-checkbox">
        <input id="rechtesumme" type="checkbox" class="custom-control-input" value="ok" name="data[rechtesumme]">
        <span class="custom-control-indicator"></span>
        <span class="custom-control-description">
          Hiermit übertrage ich die Nutzungsrechte für die eingereichte Arbeit an die ∑-Summe 2017.
        </span>
      </label>
    </div>
    <hr />

  </div>
</div>


<div class="form-group row">
  <label for="rechtemediathek" class="col-sm-2 col-form-label"><!-- Rechte zur Nutzung in der Mediathek HGK --></label>
  <div class="col-sm-10">
    <p>Zu folgenden Bedingungen übertrage ich die Nutzungsrechte für die eingereichte Arbeit  zusätzlich  an die Mediathek HGK.</p>

    <label class="custom-control custom-radio">
      <input id="rechte1" type="radio" class="custom-control-input" value="internet" name="data[rechte]">
      <span class="custom-control-indicator"></span>
      <span class="custom-control-description">
        Die Arbeit darf frei im Internet gezeigt werden.
      </span>
    </label>
    <br />
    <label class="custom-control custom-radio">
      <input id="rechte2" type="radio" class="custom-control-input" value="hochschulnetz" name="data[rechte]">
      <span class="custom-control-indicator"></span>
      <span class="custom-control-description">
        Die Arbeit darf im Schweizer Hochschulnetz in voller Länge gezeigt werden. Im Internet dürfen nur Vorschaubilder gezeigt werden.
      </span>
    </label>
    <br />
    <label class="custom-control custom-radio">
      <input id="rechte3" type="radio" class="custom-control-input" value="hgk" name="data[rechte]">
      <span class="custom-control-indicator"></span>
      <span class="custom-control-description">
        Die Arbeit darf nur in der HGK in voller Länge gezeigt werden. Im Internet dürfen nur Vorschaubilder gezeigt werden.
      </span>
    </label>
    <br />
    <label class="custom-control custom-radio">
      <input id="rechte4" type="radio" class="custom-control-input" value="mediathek" name="data[rechte]">
      <span class="custom-control-indicator"></span>
      <span class="custom-control-description">
        Die Arbeit darf nur in der Mediathek in voller Länge gezeigt werden. Im Internet dürfen nur Vorschaubilder gezeigt werden.
      </span>
    </label>
    <br />
    <label class="custom-control custom-radio">
      <input id="rechte5" type="radio" class="custom-control-input" value="sichtungsstation" name="data[rechte]">
      <span class="custom-control-indicator"></span>
      <span class="custom-control-description">
        Die Arbeit darf nur an einer Sichtungsstation in der Mediathek in voller Länge gezeigt werden. Im Internet dürfen nur Vorschaubilder gezeigt werden.
      </span>
    </label>
    <br />
    <label class="custom-control custom-radio">
      <input id="rechte6" type="radio" class="custom-control-input" value="nocoll" name="data[nocoll]">
      <span class="custom-control-indicator"></span>
      <span class="custom-control-description">
        Die Arbeit soll nicht in die Sammlungen der Mediathek aufgenommen werden.
      </span>
    </label>
  </div>
</div>


<p />
            <div class="form-group row">
              <button type="submit" value="Validate!" class="btn btn-secondary btn-block">Absenden</button>
            </div>
          </p>
          </div>
        </div>
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
              ? 'You missed 1 field. It has been highlighted'
              : 'You missed ' + errors + ' fields. They have been highlighted';
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
