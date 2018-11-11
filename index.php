<html>
    <head>
        <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.css"/>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
        <style>
        #mapid{
            height: 480px;
        }
        #download {
            position:absolute;
            top:10px;
            right:10px;
            z-index:1000;
        }
        
        #cover {
            display: none;
            text-align: center;
            padding-top: 200px;
            background: #CCC;
            opacity: 0.5;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10000;
        }
        
        #cover.active{
            display: block;
        }
        </style>
    </head>
    
    <body>
        <div id="cover">Generating PDF</div>

        <div id="mapid"></div>
        <button id="download">Download</button>

        <script>L_PREFER_CANVAS = true;</script>
        <script src="http://cdn.leafletjs.com/leaflet/v0.7.7/leaflet.js"></script>
        <script src='http://api.tiles.mapbox.com/mapbox.js/plugins/leaflet-image/v0.0.4/leaflet-image.js'></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.2.61/jspdf.debug.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
        <script>
            var map = L.map('mapid', {preferCanvas: true}).setView([51.5, -0.12], 13);
            var cover = document.getElementById('cover');
            L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
                drawControl: true,
                preferCanvas: true
            }).addTo(map);
            
            var drawnItems = new L.FeatureGroup();
             map.addLayer(drawnItems);
             var drawControl = new L.Control.Draw({
                 edit: {
                     featureGroup: drawnItems
                 }
             });
             map.addControl(drawControl);
            
            map.on(L.Draw.Event.CREATED, function (e) {
                var type = e.layerType,
                   layer = e.layer;
                map.addLayer(layer);                
            });
            
            // Image overlay
            var imageUrl = 'images/image.jpg';
            var imageBounds = [[51.400, -0.10], [51.500, 0.05]];
            L.imageOverlay(imageUrl, imageBounds, { opacity: 0.5 }).addTo(map);
            
            function getColor(d) {
                return d > 1000 ? '#800026' :
                       d > 500  ? '#BD0026' :
                       d > 200  ? '#E31A1C' :
                       d > 100  ? '#FC4E2A' :
                       d > 50   ? '#FD8D3C' :
                       d > 20   ? '#FEB24C' :
                       d > 10   ? '#FED976' :
                                  '#FFEDA0';
            }
            function style(feature) {
                return {
                    fillColor: getColor(feature.properties.density),
                    weight: 2,
                    opacity: 1,
                    color: 'white',
                    dashArray: '3',
                    fillOpacity: 0.7
                };
            }
            
            document.getElementById('download').addEventListener('click', function() {
                cover.className = 'active';
                leafletImage(map, downloadMap);
            });
            function downloadMap(err, canvas) {
                var imgData = canvas.toDataURL("image/svg+xml", 1.0);
                var dimensions = map.getSize();
                
                var pdf = new jsPDF('l', 'pt', 'letter');
                pdf.addImage(imgData, 'PNG', 10, 10, dimensions.x * 0.5, dimensions.y * 0.5);
                
                cover.className = '';
                pdf.save("download.pdf");
            };
        </script>
    </body>
</html>