jQuery(document).ready(function ($) {
    var mapEl = document.getElementById('iwh-map');
    if (!mapEl) return;

    // Initialize map
    var startLat = parseFloat(IWH_MAP.lat) || 0;
    var startLng = parseFloat(IWH_MAP.lng) || 0;
    var startZoom = (startLat && startLng) ? 12 : 2;

    var map = L.map('iwh-map').setView([startLat, startLng], startZoom);

    // OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map);

    // Draggable marker
    var marker = L.marker([startLat, startLng], { draggable: true }).addTo(map);

    marker.on('dragend', function (e) {
        var pos = e.target.getLatLng();
        $('#iwh-lat').val(pos.lat.toFixed(6));
        $('#iwh-lng').val(pos.lng.toFixed(6));
    });

    // Click map to set location
    map.on('click', function (e) {
        marker.setLatLng(e.latlng);
        $('#iwh-lat').val(e.latlng.lat.toFixed(6));
        $('#iwh-lng').val(e.latlng.lng.toFixed(6));
    });

    // Nominatim geocoding search (free, no API key needed)
    $('#iwh-search-button').on('click', function () {
        var query = $('#iwh-location-search').val();
        if (!query) return;

        var $btn = $(this);
        $btn.prop('disabled', true).text('Searching...');

        var url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(query);

        fetch(url, {
            headers: {
                'Accept': 'application/json',
            }
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (data && data.length > 0) {
                var lat = parseFloat(data[0].lat);
                var lng = parseFloat(data[0].lon);
                marker.setLatLng([lat, lng]);
                map.setView([lat, lng], 12);
                $('#iwh-lat').val(lat.toFixed(6));
                $('#iwh-lng').val(lng.toFixed(6));
                $('#iwh-place-name').val(data[0].display_name);
            } else {
                alert('Location not found. Try a different search term.');
            }
        })
        .catch(function () {
            alert('Search failed. Please try again.');
        })
        .finally(function () {
            $btn.prop('disabled', false).text('Search');
        });
    });

    // Allow pressing Enter in search field
    $('#iwh-location-search').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#iwh-search-button').click();
        }
    });
});
