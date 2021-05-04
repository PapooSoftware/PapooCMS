'use strict';

/**
 * Extracts all addresses from the given jquery object
 *
 * @param $addressesObject
 *
 * @return Array
 */
function parseAddresses($addressesObject) {
    var addressesArray = [];

    $addressesObject.each(function(index, $singleAddress) {
        addressesArray[index] = $singleAddress.innerHTML;
    });

    return addressesArray;
}

$(document).on('DOMContentLoaded', function () {
    var addressesSelector = 'address',      // address selector
        $addresses = $(addressesSelector),  // all address tags
        addressTexts;                       // addresses texts
    // osm_map_height, osm_map_width and
    // osm_map_zoom_level have been initialized previously

    // get addresses texts
    addressTexts = parseAddresses($addresses);
    $addresses.each(function(index, addressTag) {
        var tempDiv = document.createElement("div");
        tempDiv.id = 'map-' + index;
        $(tempDiv).css({
            height: osm_map_height,
            width: osm_map_width,
        });
        $(tempDiv).insertAfter(addressTag);
    });

    // get nominatim results (api or cache)
    addressTexts.forEach(function(address, index) {
        $.ajax({
            url: location.protocol + '//' + location.host + papooWebRoot + 'plugin.php',
            method: 'GET',
            dataType: 'json',
            data: {
                template: 'osm_map_plugin/templates/getAddress.json',
                address: address
            },
            success: function(response) {
                var $currentmap = $('#map-' + index);
                if (response.success === true) {
                    var width = $($addresses[index]).attr('data-osm-map-width') || null,
                        height = $($addresses[index]).attr('data-osm-map-height') || null,
                        zoom = $($addresses[index]).attr('data-osm-map-zoom') || null,
                        m = [],
                        widthHeightRegex =
                            /^(\d*(p[xtc]|[cm]m|r?em|ex|ch|v(w|h|min|max)|\%)|auto|inherit|initial|unset)(?:\s+(?:border|content)-box)?$/gmi;
                    // override settings if data-osm-map-[width|height|zoom] is set
                    if (width !== null) {
                        while ((m = widthHeightRegex.exec(width)) !== null) {
                            if (m.index === widthHeightRegex.lastIndex) {
                                widthHeightRegex.lastIndex++;
                            }
                            if (typeof m[1] === 'string') {
                                $currentmap.css({width: m[1]});
                            }
                        }
                    }
                    m = [];
                    if (height !== null) {
                        while ((m = widthHeightRegex.exec(height)) !== null) {
                            if (m.index === widthHeightRegex.lastIndex) {
                                widthHeightRegex.lastIndex++;
                            }
                            if (typeof m[1] === 'string') {
                                $currentmap.css({height: m[1]});
                            }
                        }
                    }
                    if (zoom !== null) {
                        osm_map_zoom_level = (parseInt(zoom) < 0 ? 0 : (parseInt(zoom) > 19 ? 19 : parseInt(zoom)));
                    }

                    // create map
                    var tempMap = L.map('map-' + index).setView([response.lat, response.lon], osm_map_zoom_level);
                    // create map layer
                    L.tileLayer(
                        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                        {
                            maxZoom: 19,
                            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                        }
                    ).addTo(tempMap);
                    // add marker
                    var marker = L.marker(
                        [response.lat, response.lon],
                        {color: 'red'}
                    ).addTo(tempMap);
                    // and add a popup
                    marker.bindPopup(address).openPopup();

                    // Quick Fix Z-Index
                    $currentmap.css({zIndex: '0'});
                }
                else {
                    console.warn('Papoo OSM Plugin: Nominatim API call failed!', response.exception);
                }
            }
        });
    });
});