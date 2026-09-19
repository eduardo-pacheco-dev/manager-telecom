import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

/**
 * Renderiza um mapa com uma linha reta entre dois pontos.
 *
 * O elemento alvo deve ter os atributos:
 *   data-map-lat-a / data-map-lng-a  -> estação A
 *   data-map-lat-b / data-map-lng-b  -> estação B
 *   data-map-site-a / data-map-site-b -> rótulos dos marcadores
 */
document.querySelectorAll('[data-map]').forEach((el) => {
    const latA = parseFloat(el.dataset.mapLatA);
    const lngA = parseFloat(el.dataset.mapLngA);
    const latB = parseFloat(el.dataset.mapLatB);
    const lngB = parseFloat(el.dataset.mapLngB);

    if ([latA, lngA, latB, lngB].some(Number.isNaN)) {
        return;
    }

    const pointA = [latA, lngA];
    const pointB = [latB, lngB];

    const map = L.map(el, {
        scrollWheelZoom: false,
        attributionControl: true,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);

    const markerA = L.marker(pointA, {
        icon: L.divIcon({
            className: 'radio-link-marker',
            html: `<div class="radio-link-marker-pin" style="--marker-color:#0284c7"><span>A</span></div>`,
            iconSize: [28, 28],
            iconAnchor: [14, 14],
        }),
    }).addTo(map).bindTooltip(el.dataset.mapSiteA || 'A', { direction: 'top', offset: [0, -14] });

    const markerB = L.marker(pointB, {
        icon: L.divIcon({
            className: 'radio-link-marker',
            html: `<div class="radio-link-marker-pin" style="--marker-color:#7c3aed"><span>B</span></div>`,
            iconSize: [28, 28],
            iconAnchor: [14, 14],
        }),
    }).addTo(map).bindTooltip(el.dataset.mapSiteB || 'B', { direction: 'top', offset: [0, -14] });

    L.polyline([pointA, pointB], {
        color: '#0ea5e9',
        weight: 4,
        opacity: 0.9,
        dashArray: '8 8',
    }).addTo(map);

    const bounds = L.latLngBounds([pointA, pointB]);
    map.fitBounds(bounds.pad(0.3));

    if (window.matchMedia?.('(prefers-reduced-motion: reduce)').matches === false) {
        map.setView(map.getCenter(), map.getZoom());
    }
});