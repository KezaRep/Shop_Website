<?php
require_once './View/Layout/Header.php';
// Đảm bảo biến $lang tồn tại
if (!isset($lang)) {
    $current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'vi';
    $lang = include "Assets/Lang/$current_lang.php";
}
?>

<link rel="stylesheet" href="Assets/Css/Map/Map.css">

<div class="map-page-container">
    <div class="map-layout">

        <div class="map-sidebar" style="height: 600px;">
            <h2 class="map-title"><?= $lang['map_title'] ?></h2>

            <button id="btn-geo" class="btn-locate-me" onclick="findNearestStore()">
                <i class="fas fa-crosshairs"></i> <?= $lang['map_btn_locate'] ?>
            </button>

            <p style="text-align: center; margin-bottom: 15px; color: #999; font-size: 12px;"><?= $lang['map_or_label'] ?></p>

            <div class="filter-group">
                <label class="filter-label"><?= $lang['map_city_label'] ?></label>
                <select id="city-select" class="filter-select">
                    <option value=""><?= $lang['map_city_default'] ?></option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label"><?= $lang['map_dist_label'] ?></label>
                <select id="district-select" class="filter-select" disabled>
                    <option value=""><?= $lang['map_dist_default'] ?></option>
                </select>
            </div>

            <div id="result-container">
                <p style="text-align: center; color: #888;">
                    <?= $lang['map_result_placeholder'] ?>
                </p>
            </div>
        </div>

        <div class="map-display">
            <div id="map"></div>
        </div>

    </div>
</div>

<script>
    const stores = <?php echo isset($json_stores) ? $json_stores : '[]'; ?>;

    //  Truyền biến ngôn ngữ từ PHP sang JS ---
    const langData = {
        you_are_here: "<?= $lang['map_you_are_here'] ?>",
        near_you: "<?= $lang['map_near_you'] ?>",
        address: "<?= $lang['map_address'] ?>",
        open_time: "<?= $lang['map_open_time'] ?>",
        phone: "<?= $lang['map_phone'] ?>",
        btn_view: "<?= $lang['map_btn_view'] ?>",
        btn_direction: "<?= $lang['map_btn_direction'] ?>",
        btn_locate: "<?= $lang['map_btn_locate'] ?>",
        locating: "<?= $lang['map_locating'] ?>",
        error_not_found: "<?= $lang['map_error_not_found'] ?>",
        error_gps: "<?= $lang['map_error_gps'] ?>",
        error_browser: "<?= $lang['map_error_browser'] ?>",
        dist_hint: "<?= $lang['map_dist_hint'] ?>"
    };

    let map;
    let userMarker = null;

    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            zoom: 6,
            center: {
                lat: 16.0544,
                lng: 108.2022
            },
        });

        // Vẽ marker các shop
        stores.forEach(store => {
            const marker = new google.maps.Marker({
                position: {
                    lat: parseFloat(store.latitude),
                    lng: parseFloat(store.longitude)
                },
                map: map,
                title: store.name,
                icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
            });
            store.markerInstance = marker;

            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div style="width:200px; font-family: sans-serif;">
                        <h4 style="margin:0 0 5px; color:#d93025">${store.name}</h4>
                        <p style="font-size:13px; margin-bottom:5px">${store.address}</p>
                        
                        <a href="https://www.google.com/maps/dir/?api=1&destination=${store.latitude},${store.longitude}" 
                           target="_blank" 
                           style="color:#1a73e8; font-weight:bold; text-decoration:none; font-size:13px;">
                           <i class="fas fa-directions"></i> ${langData.btn_direction}
                        </a>
                    </div>
                `
            });
            marker.addListener("click", () => infoWindow.open(map, marker));
        });

        initFilters();
    }

    // --- TÍNH NĂNG TÌM NGƯỜI DÙNG & TÌM SHOP GẦN NHẤT ---
    function findNearestStore() {
        const btn = document.getElementById('btn-geo');
        // Sử dụng biến langData
        btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${langData.locating}`;

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;

                    if (userMarker) userMarker.setMap(null);
                    userMarker = new google.maps.Marker({
                        position: {
                            lat: userLat,
                            lng: userLng
                        },
                        map: map,
                        title: langData.you_are_here,
                        icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                    });

                    let nearestStore = null;
                    let minDistance = Infinity;

                    stores.forEach(store => {
                        // Ép kiểu float để tính toán
                        const dist = getDistanceFromLatLonInKm(userLat, userLng, parseFloat(store.latitude), parseFloat(store.longitude));
                        if (dist < minDistance) {
                            minDistance = dist;
                            nearestStore = store;
                        }
                    });

                    btn.innerHTML = `<i class="fas fa-crosshairs"></i> ${langData.btn_locate}`;

                    if (nearestStore) {
                        renderStoreCard(nearestStore, minDistance);

                        document.getElementById('city-select').value = "";
                        document.getElementById('district-select').innerHTML = `<option value="">${langData.dist_hint}</option>`;
                        document.getElementById('district-select').disabled = true;

                        const bounds = new google.maps.LatLngBounds();
                        bounds.extend(userMarker.getPosition());
                        bounds.extend(nearestStore.markerInstance.getPosition());
                        map.fitBounds(bounds);
                    } else {
                        alert(langData.error_not_found);
                    }
                },
                () => {
                    alert(langData.error_gps);
                    btn.innerHTML = `<i class="fas fa-crosshairs"></i> ${langData.btn_locate}`;
                }
            );
        } else {
            alert(langData.error_browser);
        }
    }

    function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
        var R = 6371;
        var dLat = deg2rad(lat2 - lat1);
        var dLon = deg2rad(lon2 - lon1);
        var a =
            Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        var d = R * c;
        return d;
    }

    function deg2rad(deg) {
        return deg * (Math.PI / 180);
    }

    function initFilters() {
        const citySelect = document.getElementById('city-select');
        const districtSelect = document.getElementById('district-select');
        const uniqueCities = [...new Set(stores.map(item => item.city))];

        uniqueCities.forEach(city => {
            const option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            citySelect.appendChild(option);
        });

        citySelect.addEventListener('change', function() {
            const selectedCity = this.value;
            districtSelect.innerHTML = `<option value="">${langData.dist_hint}</option>`;
            districtSelect.disabled = true;

            if (selectedCity) {
                const filteredStores = stores.filter(s => s.city === selectedCity);
                const uniqueDistricts = [...new Set(filteredStores.map(s => s.district))];
                uniqueDistricts.forEach(dist => {
                    const option = document.createElement('option');
                    option.value = dist;
                    option.textContent = dist;
                    districtSelect.appendChild(option);
                });
                districtSelect.disabled = false;
            }
        });

        districtSelect.addEventListener('change', function() {
            const selectedCity = citySelect.value;
            const selectedDistrict = this.value;
            if (selectedCity && selectedDistrict) {
                const foundStore = stores.find(s => s.city === selectedCity && s.district === selectedDistrict);
                if (foundStore) renderStoreCard(foundStore);
            }
        });
    }

    function renderStoreCard(store, distance = null) {
        const container = document.getElementById('result-container');
        container.innerHTML = '';

        let distanceHtml = '';
        if (distance) {
            // Sử dụng langData
            distanceHtml = `<div style="color:green; font-size:13px; margin-bottom:5px;">
                            <i class="fas fa-route"></i> ${langData.near_you} <b>${distance.toFixed(1)} km</b>
                        </div>`;
        }

        const card = document.createElement('div');
        card.className = 'store-card active';
        // Thay thế các chữ cứng bằng langData
        card.innerHTML = `
        <div class="store-name" style="color:#d93025; font-size:18px; margin-bottom:10px;">
            <i class="fas fa-map-marker-alt"></i> ${store.name}
        </div>
        ${distanceHtml}
        <div class="store-info"><strong>${langData.address}:</strong> ${store.address}</div>
        <div class="store-info"><strong>${langData.open_time}:</strong> ${store.open_time}</div>
        <div class="store-info"><strong>${langData.phone}:</strong> ${store.phone}</div>
        
        <div style="display: flex; gap: 10px; margin-top: 15px;">
            
            <button id="btn-view-map" style="flex: 1; padding: 8px; background: #fff; color: #555; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;">
                ${langData.btn_view}
            </button>

            <a href="https://www.google.com/maps/dir/?api=1&destination=${store.latitude},${store.longitude}" 
               target="_blank" 
               style="flex: 1; text-align: center; padding: 8px; background: #d93025; color: white; border: none; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 14px;">
               <i class="fas fa-directions"></i> ${langData.btn_direction}
            </a>
        </div>
    `;

        card.querySelector('#btn-view-map').addEventListener('click', () => {
            map.setZoom(17);
            map.panTo({
                lat: parseFloat(store.latitude),
                lng: parseFloat(store.longitude)
            });
            new google.maps.InfoWindow({
                content: `<b>${store.name}</b>`
            }).open(map, store.markerInstance);
        });

        container.appendChild(card);
    }

    window.onload = function() {
        if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
            initMap();
        } else {
            setTimeout(initMap, 1000);
        }
    };
</script>

<script src="https://cdn.jsdelivr.net/gh/somanchiu/Keyless-Google-Maps-API@v7.1/mapsJavaScriptAPI.js"></script>

<?php
include_once __DIR__ . '/../Layout/Footer.php';
?>