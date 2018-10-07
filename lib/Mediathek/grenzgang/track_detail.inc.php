<!-- START map_detail.inc.php -->
<link rel="stylesheet" href="https://openlayers.org/en/v4.0.1/css/ol.css" type="text/css">
				<div style="">
				<span style="; font-weight: bold;">Aktuelles Element</span><br />
					<div class="facet" style="">
						<div class="marker" style=""></div>
						<div style="width:99%; height:600px" id="map" class="map"></div>
						<select id="layer-select">
							<option value="Aerial">Luftbild</option>
							<option value="AerialWithLabels" selected>Luftbild mit Beschriftung</option>
							<option value="Road">Strasse</option>
						</select>
						<div id="info"> </div>
						<p><hr /></p>
						<a href="<?php echo $data['meta:raw']; ?>" target="_blank">Download File</a>
					</div>
				</div>
		<!-- <script src="https://www.openlayers.org/api/OpenLayers.js"></script> -->
    <!-- The line below is only needed for old environments like Internet Explorer and Android 4.x -->
    <script src="https://cdn.polyfill.io/v2/polyfill.min.js?features=requestAnimationFrame,Element.prototype.classList,URL"></script>
    <script src="https://openlayers.org/en/v4.0.1/build/ol-debug.js"></script>
		<script>
		// Startposition auf der Karte
		var lat=47.5615288876
		var lon=7.598510552
		var zoom=13


			var map;

			function initGrenzgang() {

				var styles = [
	        'Road',
	        'Aerial',
	        'AerialWithLabels',
	        //'collinsBart',
	        //'ordnanceSurvey'
	      ];

				var layers = [];
	       var i, ii;
	       for (i = 0, ii = styles.length; i < ii; ++i) {
	         layers.push(new ol.layer.Tile({
	           visible: false,
	           preload: Infinity,
	           source: new ol.source.BingMaps({
	             key: 'Ai6CjweCL0TvwNZsI_ehEScQ4BilSeAsP_ye4Zr73M26GQVcGuMe5V_kiBozvXuC',
	             imagerySet: styles[i]
	             // use maxZoom 19 to see stretched tiles instead of the BingMaps
	             // "no photos at this zoom level" tiles
	             // maxZoom: 19
	           })
	         }));
	       }
/*
				 var raster = new ol.layer.Tile({
	        source: new ol.source.BingMaps({
	          imagerySet: 'Aerial',
	          key: 'Ai6CjweCL0TvwNZsI_ehEScQ4BilSeAsP_ye4Zr73M26GQVcGuMe5V_kiBozvXuC'
	        })
	      });
*/
	      var style = {

	        'Point': new ol.style.Style({
	          image: new ol.style.Circle({
	            fill: new ol.style.Fill({
	              color: 'rgba(255,100,100,0.4)'
	            }),
	            radius: 3,
	            stroke: new ol.style.Stroke({
	              color: '#f77',
	              width: 1
	            })
	          })
	        }),

	        'LineString': new ol.style.Style({
	          stroke: new ol.style.Stroke({
	            color: '#f00',
	            width: 3
	          })
	        }),
	        'MultiLineString': new ol.style.Style({
	          stroke: new ol.style.Stroke({
	            color: '#0f0',
	            width: 3
	          })
	        })
	      };

	      var vector = new ol.layer.Vector({
	        source: new ol.source.Vector({
	          url: '<?php echo $data['meta:gpx']; ?>',
	          format: new ol.format.GPX()
	        }),
	        style: function(feature) {
	          return style[feature.getGeometry().getType()];
	        }
	      });

				layers.push( vector );

	      map = new ol.Map({
	        layers: layers, //[raster, vector],
	        target: document.getElementById('map'),
	        view: new ol.View({
	          center: [lon, lat],
						projection: ol.proj.get('EPSG:4326'),
	          zoom: 13
	        })
	      });

				var select = document.getElementById('layer-select');
	      function onChange() {
	        var style = select.value;
	        for (var i = 0, ii = layers.length-1; i < ii; ++i) {
	          layers[i].setVisible(styles[i] === style);
	        }
	      }
	      select.addEventListener('change', onChange);
	      onChange();

	      var displayFeatureInfo = function(pixel) {
	        var features = [];
	        map.forEachFeatureAtPixel(pixel, function(feature) {
	          features.push(feature);
	        });
	        if (features.length > 0) {
	          var info = [];
	          var i, ii;
	          for (i = 0, ii = features.length; i < ii; ++i) {
	            info.push(features[i].get('desc'));
	          }
	          document.getElementById('info').innerHTML = info.join(', ') || '(unknown)';
	          map.getTarget().style.cursor = 'pointer';
	        } else {
	          document.getElementById('info').innerHTML = '&nbsp;';
	          map.getTarget().style.cursor = '';
	        }
	      };

	      map.on('pointermove', function(evt) {
	        if (evt.dragging) {
	          return;
	        }
	        var pixel = map.getEventPixel(evt.originalEvent);
	        displayFeatureInfo(pixel);
	      });

	      map.on('click', function(evt) {
	        displayFeatureInfo(evt.pixel);
	      });
			}
		</script>
<!-- END map_detail.inc.php -->
