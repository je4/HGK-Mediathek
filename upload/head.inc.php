<!-- ########## START: HEAD PANEL ########## -->
<div class="br-header">
  <div class="br-header-left">
    <div class="navicon-left hidden-md-down"><a id="btnLeftMenu" href=""><i class="icon ion-navicon-round"></i></a></div>
    <div class="navicon-left hidden-lg-up"><a id="btnLeftMenuMobile" href=""><i class="icon ion-navicon-round"></i></a></div>
    <div class="input-group hidden-xs-down wd-170 transition">
      <input id="searchbox" type="text" class="form-control" placeholder="Search">
      <span class="input-group-btn">
        <button class="btn btn-secondary" type="button"><i class="fa fa-search"></i></button>
      </span>
    </div><!-- input-group -->
  </div><!-- br-header-left -->
  <div class="br-header-right">
    <div class="dropdown">
                <a href="" class="nav-link nav-link-profile" data-toggle="dropdown">
                  <span class="logged-name hidden-md-down"><?php echo htmlspecialchars( $session->shibGetGivenName()); ?></span>
<?php
$logo = $session->shibGetLogoLarge();
if( $logo && strlen( $logo )) {
?>
<?php echo str_replace( '/>', ' class="wd-32 rounded-circle" />', $logo ); ?>
<span class="square-10 bg-success"></span>
<?php
} else {
?>
<img src="https://via.placeholder.com/500x500" class="wd-32 rounded-circle" alt="">
<span class="square-10 bg-success"></span>
<?php
}

 ?>
                  <!--
                  <img src="http://via.placeholder.com/500x500" class="wd-32 rounded-circle" alt="">
                  <span class="square-10 bg-success"></span>
                -->
                </a>
                <div class="dropdown-menu dropdown-menu-header wd-250">
                  <div class="tx-center">
                    <a href="<?php echo $session->shibGetOrganizationURL(); ?>">
<?php
if( $logo && strlen( $logo )) {
?>
<?php echo str_replace( '/>', ' class="wd-80 rounded-circle" />', $logo ); ?>
<?php
} else {
?>
<img src="https://via.placeholder.com/500x500" class="wd-80 rounded-circle" alt="">
<?php
}
?>
                    </a>
                    <h6 class="logged-fullname"><?php echo htmlspecialchars( $session->shibGetUsername()); ?></h6>
                    <p><?php echo htmlspecialchars( $session->shibGetMail()); ?></p>
                    <p><?php echo htmlspecialchars( $session->shibDepartement()); ?></p>
                  </div>
                  <hr>
                  <div class="tx-center">
                    <p>
<?php
  foreach( $session->getGroups()  as $grp ) {
    ?>
    <?php echo htmlspecialchars( $grp ); ?><br />
    <?php
  }
?>
                    </p>
                  </div>
<!--
                  <hr>
                  <div class="tx-center">
                    <span class="profile-earning-label">Earnings After Taxes</span>
                    <h3 class="profile-earning-amount">$13,230 <i class="icon ion-ios-arrow-thin-up tx-success"></i></h3>
                    <span class="profile-earning-text">Based on list price.</span>
                  </div>
                  <hr>
                  <ul class="list-unstyled user-profile-nav">
                    <li><a href=""><i class="icon ion-ios-person"></i> Edit Profile</a></li>
                    <li><a href=""><i class="icon ion-ios-gear"></i> Settings</a></li>
                    <li><a href=""><i class="icon ion-ios-download"></i> Downloads</a></li>
                    <li><a href=""><i class="icon ion-ios-star"></i> Favorites</a></li>
                    <li><a href=""><i class="icon ion-ios-folder"></i> Collections</a></li>
                    <li><a href=""><i class="icon ion-power"></i> Sign Out</a></li>
                  </ul>
-->
                </div><!-- dropdown-menu -->
              </div><!-- dropdown -->
  </div><!-- br-header-right -->
</div><!-- br-header -->
<!-- ########## END: HEAD PANEL ########## -->
