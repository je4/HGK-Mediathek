<?php
namespace Mediathek;
include( '../init.inc.php' );

$mail = $session->shibGetMail();

$func = array_key_exists( 'func', $_REQUEST ) ? $_REQUEST['func'] : null;
$param = array_key_exists( 'param', $_REQUEST ) ? $_REQUEST['param'] : array();

?><html>
<head>
  <title>Active Directory</title></head>
  <style>
    table {
      border: 1px solid black;
      border-collapse: collapse;
    }
    td { border: 1px solid black; }
  </style>
<body>
  <form method="get">
    <input type="hidden" name="func" value="groups" />
    <input name="param[]" type="text" size="40" /><input type="submit">
  </form>
<?php
  switch( $func ) {
    case 'members':
      include( 'ad_members.inc.php' );
      break;
    case 'groups':
      include( 'ad_groups.inc.php' );
      break;
    case 'start':
    default:
      include( 'ad_start.inc.php' );
      break;
  }
 ?>
</body>
