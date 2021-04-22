(function() {

	function osm_overlay(div)
	{
		if (!this) { throw "Cannot call constructor without `new`!"; }
		
		var map, popupOverlay;
		var lon = parseFloat(div.getAttribute('data-lon'));
		var lat = parseFloat(div.getAttribute('data-lat'));
		var geojsonUrl = div.getAttribute('data-source');
		this.zoom = parseInt(div.getAttribute('data-zoom'));
		this.searchZoom = parseInt(div.getAttribute('data-search-zoom'));

		// Layer für Popups bauen
		var popupOverlayElement = div.parentNode.getElementsByClassName('osm-overlay-popup')[0];
		console.log(popupOverlayElement);
		this.popupOverlay = popupOverlay = new ol.Overlay({
			element: popupOverlayElement,
			autoPan: true,
			autoPanAnimation: {
				duration: 250
			}
		});
		popupOverlayElement.getElementsByClassName('ol-popup-closer')[0].onclick = function () {
			popupOverlay.setPosition(undefined);
			this.blur();
			return false;
		}
		
		// Vektor-Layer für Marker bauen
		this.featureLayer = new ol.layer.Vector({
			source: new ol.source.Vector({
				url: geojsonUrl,
				format: new ol.format.GeoJSON(),
				useSpatialIndex: true
			}),
			style: function (feature, resolution) {
				var icon_url = feature.get('icon');
				var icon = new ol.style.Icon({
					src: icon_url,
					anchor: [0.5,0.5]
					//scale, etc.
				});
				return new ol.style.Style({
					image: icon
				});
			},
		});

		// Karte bauen
		this.map = map = new ol.Map({
			layers: [
				new ol.layer.Tile({
					preload: 3,
					source: new ol.source.OSM()
				}),
				this.featureLayer
			],
			overlays: [this.popupOverlay],
			target: div,
			view: new ol.View({
				center: ol.proj.fromLonLat([lon, lat]),
				zoom: this.zoom,
				maxZoom: this.maxZoom,
				minZoom: this.minZoom,
				enableRotation: false,
			}),
			interactions: ol.interaction.defaults({altShiftDragRotate:false, pinchRotate:false, doubleClickZoom:true}),
			loadTilesWhileInteracting: true,
			moveTolerance: 3,
		});
		
		/**
		 * Click handler: display popup
		 */
		this.map.on('singleclick', function(evt) {
			var feature = map.forEachFeatureAtPixel(evt.pixel, function(feature, layer) { return feature; });

			var title = popupOverlayElement.getElementsByClassName('popup-title')[0];
			title.innerHTML = feature.get('title');
			var desc = popupOverlayElement.getElementsByClassName('popup-description')[0];
			desc.innerHTML = feature.get('description').replace(/\n/g, '<br />');
			popupOverlay.setPosition(feature.getGeometry().getCoordinates());
		});
	}
	osm_overlay.prototype = {
		zoom: null,
		searchZoom: null,
		minZoom: 3,
		maxZoom: 19,
		countrycodes: 'de',
		featureLayer: null,
		popupOverlay: null,
		map: null,
		$ajax: undefined,
 
		search: function ()
		{
			var osm_overlay = this;
			if (this.$ajax != undefined)
				this.search_abort();
			
			this.$ajax = $.ajax({
				url: 'https://nominatim.openstreetmap.org/search',
				complete: function(xhr, status) {
				},
				data: {
					format: 'json',
					postalcode: $('#osm_overlay_search').val(),
					countrycodes: this.countrycodes
				},
				success: function(data, status) {
					osm_overlay.search_success(data);
				},
				error: function(xhr, desc, err) {
					osm_overlay.search_abort();
				}
			});
			this.spinner();
		},
 
		spinner: function()
		{
			var img = document.createElement('img');
			img.classList.add('spinner');
			img.setAttribute('src', '/plugins/osm_overlay/css/ajax-loader.gif');
			img.id = 'osm_overlay_loading';
			$('#osm_overlay_search').parent().append(img);
		},

		search_abort: function()
		{
			$('#osm_overlay_loading').remove();
			$('#osm_overlay_search').removeClass('error');
			if (this.$ajax != undefined) {
				this.$ajax.abort();
				this.$ajax = undefined;
			}
		},
 
		search_success: function (data) {
			if (data.length == 0)
				return this.search_error();
			var result = [];
			for (var i in data) {
				var row = data[i];
				result.push({
					lon: parseFloat(row['lon']),
					lat: parseFloat(row['lat'])
				});
			}
			this.map.getView().animate(
				{center: ol.proj.fromLonLat([result[0].lon, result[0].lat])},
				{zoom: this.searchZoom}
			);
			this.$ajax = undefined;
			this.search_abort();
		},
 
		search_error: function() {
			this.search_abort();
			$('#osm_overlay_search').addClass('error');
		},
 
	};
	
	/*
	$.fn.osm_overlay = {
		lon: 7.094,
		lat: 50.739,
		countrycodes: 'de',
		zoom: 6,
		searchZoom: 17,
		jsonType: 'json',
		jsonURL: '/templates_c',
		textUrl: null,
		markerUrl: 'http://www.openlayers.org/dev/img/marker.png',
		activeMarker: 'http://www.openlayers.org/dev/img/marker.png',
		vectorStyle: OpenLayers.Util.extend(OpenLayers.Feature.Vector.style['default'], {
			graphicOpacity: 1.0,
			graphicHeight: 32,
			graphicYOffset: -32,
			externalGraphic: 'http://www.openlayers.org/dev/img/marker.png',
			strokeWidth: 7,
			cursor: "pointer"
		}),
		data: null,
		map: null,
		EPSG4326: new OpenLayers.Projection("EPSG:4326"),
		EPSG900913: new OpenLayers.Projection("EPSG:900913"),

		abort: function() {
			$('#osm_overlay_loading').remove();
			$('#osm_overlay_search').removeClass('error');
			if (this.$ajax != undefined) {
				this.$ajax.abort();
				this.$ajax = undefined;
			}
		},

		center: function(lon, lat) {
			var lonlat = new OpenLayers.LonLat(lon, lat);
			this.map.setCenter(lonlat.transform(this.EPSG4326, this.EPSG900913), this.zoom);
		},

		check: function() {
			$('#osm_overlay_search').focus();
			return $('#osm_overlay_search').is(':invalid') ? false : true;
		},

		error: function() {
			$().osm_overlay.abort();
			$('#osm_overlay_search').addClass('error');
		},

		panTo: function(lon, lat, zoom) {
			var lonlat = new OpenLayers.LonLat(lon, lat);
			this.map.panTo(lonlat.transform(this.EPSG4326, this.EPSG900913));
			this.map.zoomTo(zoom);
		},

		init: function(div) {
			$(document).keypress(function(e){
				if(e.which !== 13)
					return;
				$().osm_overlay.search();
			});
			this.div = div;
			OpenLayers.Lang.setCode('de');
			OpenLayers.Layer.Text.prototype.markerClick = function(evt) {
				var sameMarkerClicked = (this == this.layer.selectedFeature);
				this.layer.selectedFeature = (!sameMarkerClicked) ? this : null;
				for(var i=0, len=this.layer.map.popups.length; i<len; i++) {
					this.layer.map.removePopup(this.layer.map.popups[i]);
				}
				if (!sameMarkerClicked) {
					this.layer.map.addPopup(this.createPopup(true));
				}
				OpenLayers.Event.stop(evt);
			}
			this.map = new OpenLayers.Map(div[0].id, {
				controls: [
					new OpenLayers.Control.Navigation(),
					new OpenLayers.Control.PinchZoom(),
					new OpenLayers.Control.Zoom(),
					new OpenLayers.Control.Attribution({template: 'Karte: ${layers}'}),
//                     new OpenLayers.Control.LayerSwitcher()
				],
				displayProjection: this.EPSG4326,
				maxExtent: new OpenLayers.Bounds(-20037508.34, -20037508.34, 20037508.34, 20037508.34),
				numZoomLevels: 18,
				maxResolution: 156543,
				units: 'meters'
			});
			this.map.addLayer(new OpenLayers.Layer.OSM.Mapnik("OpenStreetMap (Mapnik)"));
			var text = new OpenLayers.Layer.Text("text", {
				location: this.textUrl
			});
			this.map.addLayer(text);
			this.center(this.lon, this.lat);
		},

		load: function() {
			$.ajax({
				dataType: this.jsonType,
				url: this.jsonURL,
				error: function (xhr, textStatus, error) {
					if ('console' in window)
						console.log('AJAX error in osm_overlay plugin:', error, textStatus, xhr);
				},
				success: function(data) {
					$().osm_overlay.loaded(data);
				}
			});
		},

		loaded: function(data) {
			this.data = data;
		},

		search: function() {
			if (this.$ajax != undefined)
				this.abort();
			if (!this.check())
				return;
			this.$ajax = $.ajax({
				url: 'http://nominatim.openstreetmap.org/search',
				complete: function(xhr, status) {
				},
				data: {
					format: 'json',
//                      q: this.searchQuery + " " + ,
						postalcode: $('#osm_overlay_search').val(),
						countrycodes: this.countrycodes
				},
				success: function(data, status) {
					$().osm_overlay.success(data);
				},
				error: function(xhr, desc, err) {
					$().osm_overlay.abort();
//                  console.log(xhr);
//                  console.log("Details: " + desc + "\nError:" + err);
				}
			});
			this.spinner();
		},

		success: function (data) {
			if (data.length == 0)
				return this.error();
			var result = [];
			for (var i in data) {
				var row = data[i];
				result.push({
					lon: row['lon'],
					lat: row['lat']
				});
			}
			this.panTo(result[0].lon, result[0].lat, this.searchZoom);
			this.$ajax = undefined;
			this.abort();
		},

		spinner: function() {
			var img = document.createElement('img');
			img.classList.add('spinner');
			img.setAttribute('src', 'http://' + window.location.hostname + '/plugins/osm_overlay/css/ajax-loader.gif');
			img.id = 'osm_overlay_loading';
			$('#osm_overlay_search').parent().append(img);
		},
	}
	*/
	
	$('.osm-overlay-map').each(function(){
		if (!this.hasAttribute('data-lon'))
			return;
		var overlay = new osm_overlay(this);
		
		$('.osm-overlay-search').on('submit', function (evt) {
			evt.preventDefault();
			overlay.search();
			return false;
		})
	});
}(jQuery));