<?php

// box.php
 
// 1. Fetch JSON via cURL with basic error handling

function getJson(string $url): ?array {

    $ch = curl_init($url);

    curl_setopt_array($ch, [

        CURLOPT_RETURNTRANSFER => true,

        CURLOPT_FAILONERROR    => true,

        CURLOPT_CONNECTTIMEOUT => 5,

        CURLOPT_TIMEOUT        => 10,

    ]);

    $resp = curl_exec($ch);

    if ($resp === false) {

        curl_close($ch);

        return null;

    }

    curl_close($ch);

    $data = json_decode($resp, true);

    return (json_last_error() === JSON_ERROR_NONE) ? $data : null;

}
 
$url  = "https://api.opensensemap.org/boxes/65652ae82d58f70008baaa76";

$data = getJson($url);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>OpenSenseMap Box Details</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>

    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }

    .section { background: #fff; border: 1px solid #ccc; margin-bottom: 20px; padding: 15px; border-radius: 8px; }

    #map { height: 400px; width: 100%; border-radius: 8px; }

    #timer { font-size: 18px; color: red; margin-bottom: 10px; }
</style>
</head>
<body>
 
  <?php include 'menu.php'; ?>
 
  <h2>OpenSenseMap Box Full Information</h2>
<div id="timer">Next update in: <span id="countdown">15</span>s</div>
 
  <?php if (!$data): ?>
<p style="color:red;">Could not retrieve or parse the OpenSenseMap API data.</p>
<?php else: ?>
<div class="section" id="generalInfo">
<h3>General Information</h3>
<p><strong>Box ID:</strong>   <span id="boxId"><?= htmlspecialchars($data['_id']) ?></span></p>
<p><strong>Name:</strong>     <span id="boxName"><?= htmlspecialchars($data['name']) ?></span></p>
<p><strong>Description:</strong> <span id="boxDesc"><?= htmlspecialchars($data['description'] ?? 'N/A') ?></span></p>
<!-- add more fields as needed, each wrapped in a span with its own ID -->
</div>
 
    <div class="section" id="location">
<h3>Location</h3>
<p><strong>Latitude:</strong>  <span id="lat"><?= htmlspecialchars($data['geolocation']['coordinates'][1] ?? 0) ?></span></p>
<p><strong>Longitude:</strong> <span id="lon"><?= htmlspecialchars($data['geolocation']['coordinates'][0] ?? 0) ?></span></p>
</div>
 
    <div id="map"></div>
<?php endif; ?>
 
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
<?php if ($data): ?>

    // --- INITIAL SETUP ---

    const latSpan = document.getElementById('lat');

    const lonSpan = document.getElementById('lon');

    const boxId   = document.getElementById('boxId');

    const boxName = document.getElementById('boxName');

    const boxDesc = document.getElementById('boxDesc');
 
    // Parse initial coords (or 0 if missing)

    let lat = parseFloat(latSpan.textContent) || 0;

    let lon = parseFloat(lonSpan.textContent) || 0;
 
    // Initialize Leaflet map and marker

    const map = L.map('map').setView([lat, lon], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {

      attribution: '&copy; OpenStreetMap contributors'

    }).addTo(map);

    const marker = L.marker([lat, lon]).addTo(map);
 
    // Countdown + refresh logic

    let countdown = 15;

    const countdownEl = document.getElementById('countdown');

    let timerId;
 
    function updateTimer() {

      countdownEl.textContent = countdown;

      if (countdown <= 0) {

        clearInterval(timerId);

        fetchBoxData();

      }

      countdown--;

    }
 
    function startTimer() {

      countdown = 15;

      clearInterval(timerId);

      updateTimer();

      timerId = setInterval(updateTimer, 1000);

    }
 
    // Fetch fresh JSON from the API and update only the DOM + marker

    function fetchBoxData() {

      fetch('<?= $url ?>')

        .then(res => {

          if (!res.ok) throw new Error('Network response was not ok');

          return res.json();

        })

        .then(json => {

          // text fields

          boxId.textContent   = json._id   || 'N/A';

          boxName.textContent = json.name  || 'N/A';

          boxDesc.textContent = json.description || 'N/A';
 
          // location

          if (json.geolocation?.coordinates) {

            const [newLon, newLat] = json.geolocation.coordinates;

            latSpan.textContent = newLat;

            lonSpan.textContent = newLon;

            marker.setLatLng([newLat, newLon]);

            map.setView([newLat, newLon]);

          }

        })

        .catch(err => console.error('Fetch error:', err))

        .finally(startTimer);

    }
 
    // Kick everything off

    window.addEventListener('load', startTimer);
<?php endif; ?>
</script>
 
</body>
</html>

 

