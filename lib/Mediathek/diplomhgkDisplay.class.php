<?php
/**
 * This file is part of MediathekMiner.
 * MediathekMiner is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * Foobar is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details
 *
 * You should have received a copy of the GNU General Public License along with Foobar. If not, see http://www.gnu.org/licenses/.
 *
 *
 * @package     Mediathek
 * @subpackage  NEBISDisplay
 * @author      Juergen Enge (juergen@info-age.net)
 * @copyright   (C) 2016 Academy of Art and Design FHNW
 * @license     http://www.gnu.org/licenses/gpl-3.0
 * @link        http://mediathek.fhnw.ch
 *
 */

/**
 * @namespace
 */

namespace Mediathek;

class diplomhgkDisplay extends DisplayEntity {

    public function __construct( $doc, $urlparams, $db, $highlightedDoc ) {
        parent::__construct( $doc, $urlparams, $db, $highlightedDoc );

        $this->db = $db;
    }

    public function getSchema() {
		$schema = array();
		$schema['@context'] = 'http://schema.org';
		$schema['@type'] = array('Periodical');
		$schema['@id'] = $this->doc->id;
		$schema['name'] = $this->doc->title;
		if( isset( $this->doc->author_ss ) && count( $this->doc->author_ss )) {
			$schema['author'] = array();
			foreach( $this->doc->author_ss as $author ) {
				$schema['author'][] = array( '@type' => 'Person', 'name' => $author );
			}
		}

		if( $this->doc->publisher && count( $this->doc->publisher ))
			foreach( $this->doc->publisher as $publisher ) {
				$schema['publisher'][] = array( '@type' => 'Organization', 'legalName' => $publisher );
			}
		$schema['url'] = array( 'https://mediathek.hgk.fhnw.ch/detail.php?id='.urlencode( $this->doc->id ));
		if( is_array( $this->doc->url )) foreach( $this->doc->url as $u ) {
			$us = explode( ':', $u );
			if( substr( $us[1], 0, 4 ) == 'http' ) {
				$url = substr( $u, strlen( $us[0])+1 );
				$schema['url'][]= $url;
			}
		}
		if( $this->doc->cluster_ss )
			$schema['keywords'] = implode( '; ', $this->doc->cluster_ss );

		$schema['license'] = implode( '; ', $this->doc->license );

		return $schema;
	}

	public function detailView() {
    global $config, $pagesize, $solrclient, $db, $urlparams;
    $html = '';

    $entity = new diplomhgkEntity($this->db);
    $meta = json_decode( $this->data, true );
    $entity->loadFromArray( $meta );


    $authors = $entity->getAuthors();
    ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);

?>
<div class="row">
  <div class="col-md-3">
    <div style="">
    <span style="; font-weight: bold;">Diplom</span><br />
      <div class="facet" style="">
        <div class="marker" style=""></div>
        <b><?php echo htmlspecialchars( str_replace( '>>', '', str_replace( '<<', '', strip_tags( $entity->getTitle())))); ?></b><br />
          <?php
          $i = 0;
          foreach( $authors as $author ) {
            if( $i > 0) echo "; "; ?>
              <a href="javascript:doSearchFull('author:&quot;<?php echo trim( $author ); ?>&quot;', '', [], {'catalog':[<?php echo $this->getCatalogList(); ?>]}, 0, <?php echo $pagesize; ?> );">
                <?php echo htmlspecialchars( $author ); ?>
              </a>
            <?php
            $i++;
          }
          ?>

          <br />
          <?php echo htmlspecialchars( str_replace( '>>', '', str_replace( '<<', '', strip_tags( $meta['anlassbezeichnung'])))); ?><br />
<?php
          $publishers = $entity->getPublisher();
          $city = $entity->getCity();
          $year = $entity->getYear();
          if( $city ) echo htmlspecialchars( $city ).': ';
          if( $publishers ) {
            foreach( $publishers as $publisher ) { ?>
              <a href="javascript:doSearchFull('publisher:&quot;<?php echo trim( $publisher ); ?>&quot;', '', [], {'catalog':[<?php echo $this->getCatalogList(); ?>]}, 0, <?php echo $pagesize; ?> );">
                <?php echo htmlspecialchars( $publisher ); ?>
              </a>
          <?php
            }
          }

          if( $year ) echo htmlspecialchars( $year ).'.';
          if( $city || $year || $publisher ) echo "<br />\n";

          $codes = $entity->getCodes();
          if( is_array( $codes )) foreach( $codes as $code )
            echo htmlspecialchars( str_replace( ':', ': ', $code ))."<br />\n";

?>
      </div>
    </div>


  </div>
  <div class="col-md-6">
<?php
    $abstract = $entity->getAbstract();
    if( strlen( $abstract ) || true 	) {
?>
    <div style="">
    <span style="; font-weight: bold;">Abstract</span><br />
      <div class="facet" style="">
        <div class="marker" style=""></div>
        <?php echo $abstract."<p />&nbsp;<br />\n"; ?>

        <div style="text-align: center;">
        <?php
        foreach( $meta['files'] as $file ) {
          if( preg_match( '/^image\//', $file['mimetype'] )) {
            if( strlen( trim( $file['webname'] )))
              echo "<img style=\"text-align: center; max-width: 80%;\" src=\"/diplomhgk/{$file['webname']}\" /><br />";
          }
        }
        ?>
      </div>
      <?php

          if( is_array( $this->doc->url ))
          echo "<b>Referenzen</b><br />\n";
            if( is_array( $this->doc->url )) foreach( $this->doc->url as $url ) {
              if( preg_match( '/^([a-z]+):(.*)$/', $url, $matches )) {
                echo "{$matches[1]}:<a href=\"".$matches[2]."\" target=\"_blank\">{$matches[2]}</a><br />";
              }
          }

        ?>
      </div>
    </div>
<?php
    }
?>
  </div>
  <div class="col-md-3">
<?php 	if( is_array( $this->doc->cluster_ss ))  { ?>
    <div style="">
    <span style="; font-weight: bold;">Themen</span><br />
      <div class="facet" style="">
        <div class="marker" style=""></div>
<?php
        foreach( $this->doc->cluster_ss as $cl ) {
?>
            <label>
              <a href="javascript:doSearchFull('', '', [], {'catalog':[<?php echo $this->getCatalogList(); ?>], cluster: ['<?php echo htmlspecialchars( $cl ); ?>']}, 0, <?php echo $pagesize; ?> );"><?php echo htmlspecialchars( $cl ); ?></a>
            </label><br />
<?php
        }
?>
      </div>
    </div>
        <?php  } ?>
</div>
<div class="row">
  <div class="col-md-9">
    <div style="">
<?php
$issues = array();
foreach( $entity->getIssues() as $issue ) {
if( preg_match( '/^(.*,) [^,]+$/', $issue, $matches ))
{
  $issues[] = $matches[1];
}
}
?>
    <span style="; font-weight: bold;"><?php echo htmlspecialchars( "{$meta['anlassbezeichnung']} (Diplom {$meta['year']})"); ?></span><br />
      <div class="facet" style="">
        <div class="marker" style=""></div>
<?php

$squery = $solrclient->createSelect();
$helper = $squery->getHelper();
$squery->setRows( 500 );
$squery->setStart( 0 );
$phrase = 'source:'.$helper->escapePhrase( 'diplomhgk' )
  .' AND year:'.$helper->escapePhrase( $meta['year'] )
  .' AND cluster:'.$helper->escapePhrase( $meta['anlassbezeichnung'] );

// Issue: 452º F : Revista de Teoría de la Literatura y Literatura Comparada, Iss 2, Pp 127-136 (2010)
$qstr = $phrase . ' AND -id:'.$helper->escapeTerm( $this->doc->id );
$squery->setQuery( $qstr );
$rs = $solrclient->select( $squery );
$numResults = $rs->getNumFound();
$numPages = floor( $numResults / 500 );
if( $numResults % 500 > 0 ) $numPages++;

echo "<!-- ".$qstr." (Documents: {$numResults} // Page ".(1)." of {$numPages}) -->\n";

$res = new DesktopResult( $rs, 0, 500, $db, $urlparams );
echo $res->getResult();

?>
      </div>
    </div>
<?php
if( DEBUG ) {
?>
    <div style="">
    <span style="; font-weight: bold;">RAW</span><br />
      <div class="facet" style="padding: 0px;">
        <div class="marker" style=""></div>
        <div>
          <pre>
<?php
echo print_r( $meta, true);
?>
          </pre>
        </div>
      </div>
    </div>
    <div style="">
    <span style="; font-weight: bold;">Document</span><br />
      <div class="facet" style="padding: 0px;">
        <div class="marker" style=""></div>
        <div>
          <pre>
<?php
var_dump( $this->doc->getFields());
?>
          </pre>
        </div>
      </div>
    </div>

<?php
}
?>

  </div>

  <div class="col-md-3">
  </div>
</div>


</div>
<script>
  function initdiplomhgk() {


  }
</script><?php
    $html .= ob_get_contents();
    ob_end_clean();
    return $html;	}

    public function desktopList() {
        global $config, $googleservice, $googleclient;
        $html = '';

		$entity = new diplomhgkEntity($this->db);
    $meta = json_decode( $this->data, true );
		$entity->loadFromArray( $meta );

		$t = strtolower( $this->doc->type );
		$icon = array_key_exists( $t, $config['icon'] ) ? $config['icon'][$t] : $config['icon']['default'];


        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_REMOVABLE);
?>
        <tr>
            <td rowspan="2" class="list" style="text-align: right; width: 25%;">
                <a class="entity" href="#coll_<?php echo $this->doc->id; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo $this->doc->id; ?>">
                  <?php echo htmlspecialchars( count( $entity->getAuthors() ) ? $entity->getAuthors()[0] : "" ); ?>
                </a>
            </td>
            <td class="list" style="width: 5%;"><i class="<?php echo $icon; ?>"></i></td>
            <td class="list" style="width: 70%;">
                <a class="entity" href="#coll_<?php echo $this->doc->id; ?>" data-toggle="collapse" aria-expanded="false" aria-controls="coll_<?php echo $this->doc->id; ?>">
                    <?php echo htmlspecialchars( $entity->getTitle() ); ?>
                </a>
            </td>
        </tr>
        <tr>
            <td colspan="1" class="detail">
            </td>
            <td class="detail">
                <div class="collapse" id="coll_<?php echo $this->doc->id; ?>">

<?php
        echo "Diplom: ".$meta['year']."<br />\n";
        echo $meta['anlassbezeichnung']."<br />\n";
				if( is_array( $this->doc->url )) foreach( $this->doc->url as $u ) {
					$us = explode( ':', $u );
					if( substr( $us[1], 0, 4 ) == 'http' ) {
						$url = substr( $u, strlen( $us[0])+1 );
						echo ($us[0] == 'unknown' ? '' : $us[0].':')."<i class=\"fa fa-external-link\" aria-hidden=\"true\"></i><a href=\"".$url."\" target=\"blank\">{$url}</a><br />\n";
					}
				}

				echo "ID: ".$this->doc->id."<br />\n";
        ?>
        <a href="detail.php?<?php echo "id=".urlencode( $this->doc->id ); foreach( $this->urlparams as $key=>$val ) echo '&'.$key.'='.urlencode($val); ?>"><i class="fa fa-folder-open" aria-hidden="true"></i> Details</a><br />
                </div>
            </td>
        </tr>
<?php
        $html .= ob_get_contents();
        ob_end_clean();
        return $html;
    }
}
