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

        const popupContainer = document.createElement('div')

        const titleEl = document.createElement('strong')
        titleEl.textContent = pin.title || ''
        popupContainer.appendChild(titleEl)

        if (pin.thumb) {
            popupContainer.appendChild(document.createElement('br'))

            const imgEl = document.createElement('img')
            imgEl.src = pin.thumb
            imgEl.style.maxWidth = '150px'
            imgEl.style.marginTop = '5px'
            popupContainer.appendChild(imgEl)
        }

        popupContainer.appendChild(document.createElement('br'))

        const linkEl = document.createElement('a')
        linkEl.textContent = 'View'

        if (typeof pin.link === 'string') {
            try {
                const url = new URL(pin.link, window.location.origin)
                if (url.protocol === 'http:' || url.protocol === 'https:') {
                    linkEl.href = url.toString()
                }
            } catch (e) {
                // If the URL is invalid, omit href to avoid unsafe navigation.
            }
        }

        popupContainer.appendChild(linkEl)

        marker.bindPopup(popupContainer)
        bounds.push([pin.lat, pin.lng])
    })

    if (bounds.length) {
        map.fitBounds(bounds, { padding: [40, 40] })
    }
})
