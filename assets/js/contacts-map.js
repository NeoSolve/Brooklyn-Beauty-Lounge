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

		var directionsUrl = mapNode.dataset.directionsUrl || "";
		if (directionsUrl) {
			var DirectionsControl = function () {};
			DirectionsControl.prototype.onAdd = function () {
				var container = document.createElement("div");
				container.className = "maplibregl-ctrl bb-map-directions";

				var link = document.createElement("a");
				link.href = directionsUrl;
				link.target = "_blank";
				link.rel = "nofollow noopener noreferrer";
				link.className = "bb-map-directions__link";
				link.title = "Get directions";

				var svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
				svg.setAttribute("viewBox", "0 0 24 24");
				svg.setAttribute("width", "18");
				svg.setAttribute("height", "18");
				svg.setAttribute("fill", "none");
				svg.setAttribute("stroke", "currentColor");
				svg.setAttribute("stroke-width", "2");
				svg.setAttribute("stroke-linecap", "round");
				svg.setAttribute("stroke-linejoin", "round");
				svg.setAttribute("aria-hidden", "true");

				var path1 = document.createElementNS("http://www.w3.org/2000/svg", "path");
				path1.setAttribute("d", "M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z");

				var circle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
				circle.setAttribute("cx", "12");
				circle.setAttribute("cy", "9");
				circle.setAttribute("r", "2.5");

				svg.appendChild(path1);
				svg.appendChild(circle);

				var text = document.createElement("span");
				text.className = "bb-map-directions__text";
				text.textContent = "Directions";

				link.appendChild(svg);
				link.appendChild(text);
				container.appendChild(link);

				this._container = container;
				return container;
			};
			DirectionsControl.prototype.onRemove = function () {
				if (this._container && this._container.parentNode) {
					this._container.parentNode.removeChild(this._container);
				}
			};

			map.addControl(new DirectionsControl(), "top-left");
		}

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
