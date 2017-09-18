<?php
namespace Mediathek;
include( '../init.inc.php' );

$mail = $session->shibGetMail();

$func = array_key_exists( 'func', $_REQUEST ) ? $_REQUEST['func'] : null;
$param = array_key_exists( 'param', $_REQUEST ) ? $_REQUEST['param'] : array();

?><html>
<head>
  <title>Active Directory</title></head>
<body>
  <form method="get">
    <input type="hidden" name="func" value="members" />
    <input name="param[]" type="text" size="40" /><input type="submit">
  </form>
<?php
  switch( $func ) {
    case 'members':
      include( 'ad_members.inc.php' );
      break;
    case 'start':
    default:
      include( 'ad_start.inc.php' );
      break;
  }
 ?>
</body>
