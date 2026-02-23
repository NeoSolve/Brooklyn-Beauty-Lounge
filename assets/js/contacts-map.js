"use strict";

(function () {
	if (typeof window.maplibregl === "undefined") return;

	var mapNodes = document.querySelectorAll(".js-bb-contacts-map");
	if (!mapNodes.length) return;

	mapNodes.forEach(function (mapNode) {
		var lat = parseFloat(mapNode.dataset.lat || "");
		var lng = parseFloat(mapNode.dataset.lng || "");
		var zoom = parseInt(mapNode.dataset.zoom || "15", 10);

		if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

		var map = new window.maplibregl.Map({
			container: mapNode,
			style: "https://basemaps.cartocdn.com/gl/dark-matter-gl-style/style.json",
			center: [lng, lat],
			zoom: Number.isFinite(zoom) ? zoom : 17,
			attributionControl: true,
		});

		map.scrollZoom.disable();
		map.addControl(new window.maplibregl.NavigationControl(), "top-right");

		map.on("load", function () {
			var style = map.getStyle();
			var layers = style && Array.isArray(style.layers) ? style.layers : [];

			layers.forEach(function (layer) {
				var layerId = String(layer.id || "").toLowerCase();

				if (layer.type === "background") {
					map.setPaintProperty(layer.id, "background-color", "#202020");
				}

				if (layerId.indexOf("road") !== -1) {
					if (layer.type === "line") {
						map.setPaintProperty(layer.id, "line-color", "#363636");
					}
					if (layer.type === "fill") {
						map.setPaintProperty(layer.id, "fill-color", "#363636");
					}
				}

				if (layer.type === "symbol") {
					var textField = map.getLayoutProperty(layer.id, "text-field");
					if (typeof textField !== "undefined") {
						map.setPaintProperty(layer.id, "text-color", "#7C7C7C");
						map.setPaintProperty(layer.id, "text-halo-color", "#202020");
						map.setPaintProperty(layer.id, "text-halo-width", 1);
					}
				}
			});

			var markerNode = document.createElement("span");
			markerNode.className = "bb-contacts-map-pin__shape";
			markerNode.setAttribute("aria-hidden", "true");

			var markerWrap = document.createElement("span");
			markerWrap.className = "bb-contacts-map-pin";
			markerWrap.appendChild(markerNode);

			new window.maplibregl.Marker({ element: markerWrap, anchor: "bottom" }).setLngLat([lng, lat]).addTo(map);
		});
	});
})();
