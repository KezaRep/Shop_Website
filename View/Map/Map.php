<?php
require_once './View/Layout/Header.php';
?>

<link rel="stylesheet" href="Assets/Css/Map/Map.css">

<div class="map-page-container">
    <div class="map-layout">

        <div class="map-sidebar" style="height: 600px;">
            <h2 class="map-title">Tìm cửa hàng</h2>

            <button id="btn-geo" class="btn-locate-me" onclick="findNearestStore()">
                <i class="fas fa-crosshairs"></i> Tìm cửa hàng gần tôi nhất
            </button>

            <p style="text-align: center; margin-bottom: 15px; color: #999; font-size: 12px;">— HOẶC CHỌN KHU VỰC —</p>

            <div class="filter-group">
                <label class="filter-label">Chọn Tỉnh/Thành phố</label>
                <select id="city-select" class="filter-select">
                    <option value="">-- Chọn Tỉnh/Thành --</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Chọn Quận/Huyện</label>
                <select id="district-select" class="filter-select" disabled>
                    <option value="">-- Vui lòng chọn Tỉnh trước --</option>
                </select>
            </div>

            <div id="result-container">
                <p style="text-align: center; color: #888;">
                    Kết quả sẽ hiển thị tại đây.
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
                    lat: store.latitude,
                    lng: store.longitude
                },
                map: map,
                title: store.name,
                // Icon shop màu đỏ
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
               <i class="fas fa-directions"></i> Bấm để chỉ đường
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
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang định vị...';

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;

                    // 1. Vẽ điểm xanh (Vị trí người dùng) lên map
                    if (userMarker) userMarker.setMap(null); // Xóa điểm cũ nếu có
                    userMarker = new google.maps.Marker({
                        position: {
                            lat: userLat,
                            lng: userLng
                        },
                        map: map,
                        title: "Bạn đang ở đây",
                        icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png' // Icon màu xanh
                    });

                    // 2. Thuật toán tìm shop gần nhất (Dùng công thức Haversine)
                    let nearestStore = null;
                    let minDistance = Infinity; // Khởi tạo khoảng cách vô cùng lớn

                    stores.forEach(store => {
                        const dist = getDistanceFromLatLonInKm(userLat, userLng, store.latitude, store.longitude);
                        if (dist < minDistance) {
                            minDistance = dist;
                            nearestStore = store;
                        }
                    });

                    btn.innerHTML = '<i class="fas fa-crosshairs"></i> Tìm cửa hàng gần tôi nhất';

                    if (nearestStore) {
                        // 3. Hiển thị kết quả ra thẻ
                        renderStoreCard(nearestStore, minDistance);

                        // 4. Reset dropdown để người dùng biết là đang auto
                        document.getElementById('city-select').value = "";
                        document.getElementById('district-select').innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                        document.getElementById('district-select').disabled = true;

                        // 5. Zoom map sao cho thấy được cả Người và Shop
                        const bounds = new google.maps.LatLngBounds();
                        bounds.extend(userMarker.getPosition());
                        bounds.extend(nearestStore.markerInstance.getPosition());
                        map.fitBounds(bounds);
                    } else {
                        alert("Không tìm thấy cửa hàng nào trong hệ thống!");
                    }
                },
                () => {
                    alert("Không thể lấy vị trí. Vui lòng cho phép trình duyệt truy cập GPS.");
                    btn.innerHTML = '<i class="fas fa-crosshairs"></i> Tìm cửa hàng gần tôi nhất';
                }
            );
        } else {
            alert("Trình duyệt của bạn không hỗ trợ định vị.");
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
            districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
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
            distanceHtml = `<div style="color:green; font-size:13px; margin-bottom:5px;">
                            <i class="fas fa-route"></i> Cách bạn khoảng <b>${distance.toFixed(1)} km</b>
                        </div>`;
        }

        const card = document.createElement('div');
        card.className = 'store-card active';
        card.innerHTML = `
        <div class="store-name" style="color:#d93025; font-size:18px; margin-bottom:10px;">
            <i class="fas fa-map-marker-alt"></i> ${store.name}
        </div>
        ${distanceHtml}
        <div class="store-info"><strong>Địa chỉ:</strong> ${store.address}</div>
        <div class="store-info"><strong>Giờ mở cửa:</strong> ${store.open_time}</div>
        <div class="store-info"><strong>SĐT:</strong> ${store.phone}</div>
        
        <div style="display: flex; gap: 10px; margin-top: 15px;">
            
            <button id="btn-view-map" style="flex: 1; padding: 8px; background: #fff; color: #555; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;">
                Xem vị trí
            </button>

            <a href="https://www.google.com/maps/dir/?api=1&destination=${store.latitude},${store.longitude}" 
               target="_blank" 
               style="flex: 1; text-align: center; padding: 8px; background: #d93025; color: white; border: none; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 14px;">
               <i class="fas fa-directions"></i> Chỉ đường
            </a>
        </div>
    `;

        card.querySelector('#btn-view-map').addEventListener('click', () => {
            map.setZoom(17);
            map.panTo({
                lat: store.latitude,
                lng: store.longitude
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