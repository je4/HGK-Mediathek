<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
  <style type="text/css">
  .form-control, .form-control:focus {
    border-radius: 0px;
    font-family: 'Raleway', sans-serif;
    border-color: black;
  }
  .custom-select, .custom-select:hover, .custom-select:focus {
    border-radius: 0px;
    font-family: 'Raleway', sans-serif;
    border-color: black;
  }
  .btn {
    border-radius: 0px;
    font-family: 'Raleway', sans-serif;
  }
  .btn-primary, .btn-primary:focus, .btn-primary:hover {
    color: white;
    background-color: black;
    border-color: black;
  }

  </style>
</head>
<body>
  <form class="form-inline" method=GET action="../search.php" target="_blank">

    <input type="text" class="form-control mb-2 mr-sm-2 mb-sm-0" id="inlineFormInput" placeholder="Suchbegriff" name="search">
    <select class="custom-select mb-2 mr-sm-2 mb-sm-0" id="inlineFormCustomSelect" name="facets[catalog][]">
      <option value="HGK" selected>Mediathek HGK</option>
      <option value="FHNW-Bib">Alle FHNW Bibliotheken</option>
      <option value="Kunsthochschul-Bibs">Bibliotheken Kunsthochschulen CH</option>
      <option value="openaccess_journals">Open Access Zeitschriften</option>
      <option value="NATIONALLICENCE">Nationallizenzen</option>
    </select>


    <button type="submit" class="btn btn-primary">Suchen</button>
  </form>
  <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
</body>
</html>
