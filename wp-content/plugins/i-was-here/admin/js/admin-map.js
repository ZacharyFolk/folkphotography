jQuery(document).ready(function ($) {
    // Initialize map
    const map = L.map('iwh-map').setView(
        [IWH_MAP.lat, IWH_MAP.lng],
        IWH_MAP.lat && IWH_MAP.lng ? 12 : 2
    )

    // Free OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map)

    // Draggable marker
    let marker = L.marker([IWH_MAP.lat, IWH_MAP.lng], { draggable: true }).addTo(map)

    marker.on('dragend', function (e) {
        const pos = e.target.getLatLng()
        $('#iwh-lat').val(pos.lat)
        $('#iwh-lng').val(pos.lng)
    })

    // Mapbox geocoding search
    $('#iwh-search-button').on('click', async function () {
        const query = $('#iwh-location-search').val()
        if (!query || !IWH_MAP.mapboxKey) return

        const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(
            query
        )}.json?access_token=${IWH_MAP.mapboxKey}&limit=1`
        const res = await fetch(url)
        const data = await res.json()

        if (data.features && data.features.length > 0) {
            const [lng, lat] = data.features[0].center
            marker.setLatLng([lat, lng])
            map.setView([lat, lng], 12)
            $('#iwh-lat').val(lat)
            $('#iwh-lng').val(lng)
            $('#iwh-place-name').val(data.features[0].place_name)
        }
    })
})
