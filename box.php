<?php
$boxId = '65652ae82d58f70008baaa76';
$url   = "https://api.opensensemap.org/boxes/{$boxId}";
$json  = @file_get_contents($url);
$data  = $json ? json_decode($json, true) : null;

$name = $data['name'] ?? 'Unknown';
$description = $data['description'] ?? 'No description';
$lat  = 0;
$lon  = 0;
if (!empty($data['currentLocation']['coordinates'])) {
    list($lon, $lat) = $data['currentLocation']['coordinates'];
} elseif (!empty($data['currentLocation']['geometry']['coordinates'])) {
    list($lon, $lat) = $data['currentLocation']['geometry']['coordinates'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>SenseBox Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="bg-gray-50 text-gray-800 p-6">
  <div class="max-w-4xl mx-auto space-y-6">
    <header class="text-center">
      <h1 class="text-4xl font-bold">SenseBox Dashboard</h1>
      <p class="text-sm text-gray-500 mt-2">Auto-refreshes in <span id="timer" class="font-semibold text-blue-600">15</span> seconds</p>
    </header>

    <section id="boxContent">
      <?php if (!$data): ?>
        <div class="p-4 bg-red-100 text-red-700 rounded">
          Error: Could not load box data.
        </div>
      <?php else: ?>
        <section class="bg-white shadow rounded-lg p-6">
          <h2 class="text-2xl font-semibold mb-4">General Information</h2>
          <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
            <div>
              <dt class="font-medium">Box ID</dt>
              <dd id="boxId"><?= htmlspecialchars($data['_id']) ?></dd>
            </div>
            <div>
              <dt class="font-medium">Name</dt>
              <dd id="boxName"><?= htmlspecialchars($name) ?></dd>
            </div>
            <div class="md:col-span-2">
              <dt class="font-medium">Description</dt>
              <dd id="boxDesc"><?= htmlspecialchars($description) ?></dd>
            </div>
          </dl>
        </section>

        <section class="bg-white shadow rounded-lg p-6">
          <h2 class="text-2xl font-semibold mb-4">Location</h2>
          <p class="text-sm mb-4">
            Latitude: <span id="lat" class="font-medium"><?= htmlspecialchars($lat) ?></span><br>
            Longitude: <span id="lon" class="font-medium"><?= htmlspecialchars($lon) ?></span>
          </p>
          <div id="map" class="h-80 rounded-lg"></div>
        </section>

        <?php if (!empty($data['sensors'])): ?>
        <section class="bg-white shadow rounded-lg p-6" id="sensorSection">
          <h2 class="text-2xl font-semibold mb-4">Sensors</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="sensorData">
            <?php foreach ($data['sensors'] as $sensor):
              $val = $sensor['lastMeasurement']['value'] ?? 'N/A';
              $unit = $sensor['unit'] ?? '';
              $title = $sensor['title'] ?? 'Sensor';
            ?>
              <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 shadow-sm">
                <h3 class="text-lg font-medium"><?= htmlspecialchars($title) ?></h3>
                <p class="mt-2 text-2xl font-bold text-blue-600">
                  <?= htmlspecialchars($val) ?> <?= htmlspecialchars($unit) ?>
                </p>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
        <?php endif; ?>
      <?php endif; ?>
    </section>
  </div>

  <script>
    let map;
    const boxId = <?= json_encode($boxId) ?>;

    function initMap(lat, lon, name) {
      map = L.map('map').setView([lat, lon], 13);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(map);
      L.marker([lat, lon]).addTo(map).bindPopup(name).openPopup();
    }

    function updateMap(lat, lon, name) {
      map.setView([lat, lon], 13);
      L.marker([lat, lon]).addTo(map).bindPopup(name).openPopup();
    }

    function fetchData() {
      fetch(`https://api.opensensemap.org/boxes/${boxId}`)
        .then(res => res.json())
        .then(data => {
          document.getElementById("boxId").textContent = data._id;
          document.getElementById("boxName").textContent = data.name || "Unknown";
          document.getElementById("boxDesc").textContent = data.description || "No description";

          const coords = data.currentLocation?.coordinates || [0, 0];
          const lat = coords[1];
          const lon = coords[0];

          document.getElementById("lat").textContent = lat;
          document.getElementById("lon").textContent = lon;
          updateMap(lat, lon, data.name);

          // Update sensors
          const sensorData = data.sensors.map(sensor => {
            const val = sensor.lastMeasurement?.value || 'N/A';
            const unit = sensor.unit || '';
            const title = sensor.title || 'Sensor';
            return `
              <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 shadow-sm">
                <h3 class="text-lg font-medium">${title}</h3>
                <p class="mt-2 text-2xl font-bold text-blue-600">${val} ${unit}</p>
              </div>
            `;
          }).join('');
          document.getElementById("sensorData").innerHTML = sensorData;
        })
        .catch(err => console.error('Error fetching data:', err));
    }

    // Countdown Timer
    let countdown = 15;
    const timerEl = document.getElementById('timer');
    setInterval(() => {
      countdown--;
      if (countdown <= 0) {
        fetchData();
        countdown = 15;
      }
      timerEl.textContent = countdown;
    }, 1000);

    // Initialize map on load
    document.addEventListener('DOMContentLoaded', () => {
      initMap(<?= json_encode($lat) ?>, <?= json_encode($lon) ?>, <?= json_encode($name) ?>);
    });
  </script>
</body>
</html>

 

