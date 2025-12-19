document.addEventListener('DOMContentLoaded', function () {
    const mapEl = document.querySelector('[data-iwh-map]')
    if (!mapEl || !window.IWH_FRONTEND) return

    const pins = IWH_FRONTEND.pins || []

    const map = L.map(mapEl).setView([20, 0], 2)

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
    }).addTo(map)

    const bounds = []

    pins.forEach((pin) => {
        const marker = L.marker([pin.lat, pin.lng]).addTo(map)

        let popup = `<strong>${pin.title}</strong>`
        if (pin.thumb) {
            popup += `<br><img src="${pin.thumb}" style="max-width:150px; margin-top:5px;" />`
        }
        popup += `<br><a href="${pin.link}">View</a>`

        marker.bindPopup(popup)
        bounds.push([pin.lat, pin.lng])
    })

    if (bounds.length) {
        map.fitBounds(bounds, { padding: [40, 40] })
    }
})
