<?php
function navbar( $current, $area ) {
	global $session;
	
	$areas = array( 'online'=>'Online',
					'books'=>'Books',
					);
	
	ob_start();
?>
<nav class="navbar navbar-static-top navbar-dark bg-inverse">
  <button class="navbar-toggler hidden-sm-up" type="button" data-toggle="collapse" data-target="#exCollapsingNavbar2">
    &#9776;
  </button>
  <div class="collapse navbar-toggleable-xs" id="exCollapsingNavbar2">
    <a class="navbar-brand" style="padding-top: 0px; padding-bottom: 0px; padding-right: 10px;" title="FHNW Hochschule für Gestaltung und Kuhnst" target="_top" href="http://www.fhnw.ch/hgk">
		<img src="img/FHNW_HGK_40px_inverse.jpg" width="275" height="40" border="0" alt="FHNW HGK Logo">
	</a>
    <ul class="nav navbar-nav">
      <li class="nav-item<?php if( $current == 'home' ) echo ' active'; ?>">
        <a class="nav-link" href="index.php">Willkommen<?php if( $current == 'home' ) echo ' <span class="sr-only">(current)</span>'; ?></a>
      </li>
      <li class="nav-item<?php if( $current == 'search' ) echo ' active'; ?>">
        <a class="nav-link" href="search.php">Suche<?php if( $current == 'search' ) echo ' <span class="sr-only">(current)</span>'; ?></a>
      </li>
      <li class="nav-item<?php if( $current == 'kiste' ) echo ' active'; ?>">
        <a class="nav-link" href="kistentool.php">Standortsuche<?php if( $current == 'kiste' ) echo ' <span class="sr-only">(current)</span>'; ?></a>
      </li>
      <li class="nav-item active">
        BETA<span class="sr-only">(current)</span>
      </li>
	<?php if( $current != 'search' ) { ?>
	<!--
	<li class="nav-item">
		<form class="form-inline">
		<input class="form-control" type="text" placeholder="Suche">
		<button class="btn bw" type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
		</form>
	</li>
	-->
	<?php  } ?>
</ul>
<ul class="nav navbar-nav pull-xs-right">
	<?php if( !$session->isLoggedIn() ) { ?>
	<li class="nav-item<?php if( $current == 'login' ) echo ' active'; ?>">
		<a class="nav-link" href="auth/?target=<?php echo urlencode( $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']); ?>">Login<?php if( $current == 'login' ) echo ' <span class="sr-only">(current)</span>'; ?></a>
    </li>
	<?php } else { ?>
	<li class="nav-item<?php if( $current == 'login' ) echo ' active'; ?>">
		<a style="padding: 0px;" href="#" class="nav-link" data-toggle="modal" data-target="#MTModal" data-user="<?php echo htmlentities( $session->shibGetUsername()); ?>" >
			<?php echo htmlentities( $session->shibGetUsername()); ?><?php if( $current == 'login' ) echo ' <span class="sr-only">(current)</span>'; ?>
		</a>
    </li>
	<?php } ?>
	</li>	
</ul>
  </div>
</nav>
<?php
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}
?>