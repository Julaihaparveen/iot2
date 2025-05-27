<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard - OpenSenseMap</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    html, body {
      height: 100%;
      margin: 0;
      font-size: 16px;
      transition: font-size 0.3s ease;
    }
    #map {
      height: 300px;
      width: 100%;
    }
    #activityLogs {
      max-height: 200px;
      overflow-y: auto;
      background: #f9fafb;
      border: 1px solid #d1d5db;
      padding: 10px;
      border-radius: 8px;
      font-family: monospace;
    }
  </style>
</head>
<body class="flex bg-gray-100">
 
<!-- Side Navigation -->
<nav class="bg-white w-64 min-h-screen shadow-md fixed">
  <div class="p-6 flex flex-col h-full">
    <h1 class="text-2xl font-bold text-blue-600 mb-8">OpenSenseMap</h1>
    <ul class="flex-grow space-y-4">
      <li><a href="#" class="text-blue-700 font-semibold hover:bg-blue-100 p-2 rounded block">Dashboard</a></li>
      <li><a href="#" class="text-gray-700 hover:bg-blue-100 p-2 rounded block">Profile</a></li>
      <li><a href="#" class="text-gray-700 hover:bg-blue-100 p-2 rounded block">Settings</a></li>
      <li><a href="#" class="text-gray-700 hover:bg-blue-100 p-2 rounded block">Reports</a></li>
      <li><a href="#" class="text-gray-700 hover:bg-blue-100 p-2 rounded block">Achievements</a></li>
    </ul>
    <div class="pt-4 border-t">
      <p class="mb-2 text-gray-600">Welcome, <strong><?= htmlspecialchars($_SESSION['user']) ?></strong></p>
      <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white text-center py-2 rounded block">Logout</a>
    </div>
  </div>
</nav>
 
<!-- Main Content -->
<main class="ml-64 p-6 w-full space-y-8">
 
  <!-- Header + Accessibility -->
  <div class="flex justify-between items-center">
    <h2 class="text-2xl font-bold">Welcome, <?= htmlspecialchars($_SESSION['user']) ?>!</h2>
    <div>
      <span class="mr-2 font-semibold">Font Size:</span>
      <button onclick="setFontSize('14px')" class="px-2 py-1 border rounded hover:bg-gray-200">Small</button>
      <button onclick="setFontSize('16px')" class="px-2 py-1 border rounded hover:bg-gray-200">Medium</button>
      <button onclick="setFontSize('20px')" class="px-2 py-1 border rounded hover:bg-gray-200">Large</button>
    </div>
  </div>
 
  <!-- Weather & Map -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Weather Widget -->
    <div class="bg-blue-100 text-blue-800 p-4 rounded shadow">
      <h3 class="text-lg font-semibold mb-2">Weather</h3>
      <iframe
        src="https://api.wo-cloud.com/content/widget/?geoObjectKey=6112695&language=en&region=US&timeFormat=HH:mm&windUnit=mph&systemOfMeasurement=imperial&temperatureUnit=fahrenheit"
        width="290" height="318" frameborder="0" name="CW2" scrolling="no"
        style="border: 1px solid #10658E; border-radius: 8px;"></iframe>
    </div>
 
    <!-- Live Map -->
    <div id="map" class="col-span-2 bg-white rounded shadow"></div>
  </div>
 
  <!-- Sensor Section -->
  <div class="bg-white p-6 rounded shadow">
    <h3 class="text-xl font-semibold mb-4">Real-Time Sensor Data</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Sensor Cards -->
      <div id="sensorCards" class="space-y-4 md:col-span-1"></div>
 
      <!-- Chart with Sensor Data -->
      <div class="md:col-span-2">
        <canvas id="sensorChart"></canvas>
        <img src="sensor_summary.jpg" alt="Sensor Data Summary" class="mt-4 rounded shadow" />
      </div>
    </div>
  </div>
 
  <!-- Achievements -->
  <div class="bg-white p-6 rounded shadow">
    <h3 class="text-xl font-semibold mb-4">Achievements & Badges</h3>
    <div class="flex flex-wrap gap-4">
      <div class="bg-yellow-100 p-3 rounded shadow flex items-center gap-2 text-yellow-800">
        🥇 <span>First Login</span>
      </div>
      <div class="bg-green-100 p-3 rounded shadow flex items-center gap-2 text-green-800">
        ✅ <span>Tutorial Completed</span>
      </div>
      <div class="bg-blue-100 p-3 rounded shadow flex items-center gap-2 text-blue-800">
        📡 <span>Sensor Expert</span>
      </div>
    </div>
  </div>
 
  <!-- Activity Logs -->
  <div class="bg-white p-6 rounded shadow">
    <h3 class="text-xl font-semibold mb-4">User Activity Logs</h3>
    <div id="activityLogs">Loading logs...</div>
  </div>
</main>
 
<!-- Scripts -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
  // Font size control
  function setFontSize(size) {
    document.documentElement.style.fontSize = size;
  }
 
  // Activity logs
  const logs = [
    "[2025-05-21 09:15] User logged in",
    "[2025-05-21 09:20] Viewed dashboard",
    "[2025-05-21 09:25] Checked weather",
    "[2025-05-21 09:30] Viewed sensor data",
    "[2025-05-21 09:35] Logged out"
  ];
  document.getElementById('activityLogs').innerHTML = logs.map(log => `<div>${log}</div>`).join('');
 
  // Sensor & map
  const boxId = "5f2b56f4263635001c1dd1fd";
  fetch(`https://api.opensensemap.org/boxes/${boxId}`)
    .then(res => res.json())
    .then(data => {
      const [lon, lat] = data.currentLocation.coordinates;
      const map = L.map('map').setView([lat, lon], 13);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
      L.marker([lat, lon]).addTo(map).bindPopup("Sensor Location").openPopup();
 
      const sensorCards = document.getElementById('sensorCards');
      sensorCards.innerHTML = '';
      const labels = [], values = [];
 
      data.sensors.forEach(sensor => {
        const last = sensor.lastMeasurement;
        if (!last) return;
 
        const card = document.createElement('div');
        card.className = "bg-gray-50 p-4 border rounded shadow";
 
        const title = document.createElement('h4');
        title.className = "font-semibold mb-1";
        title.textContent = sensor.title;
 
        const value = document.createElement('p');
        value.className = "text-lg";
        value.textContent = `${last.value} ${sensor.unit}`;
 
        card.appendChild(title);
        card.appendChild(value);
        sensorCards.appendChild(card);
 
        labels.push(sensor.title);
        values.push(last.value);
      });
 
      const ctx = document.getElementById('sensorChart').getContext('2d');
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Latest Sensor Readings',
            data: values,
            backgroundColor: 'rgba(59, 130, 246, 0.6)',
            borderColor: 'rgba(59, 130, 246, 1)',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          scales: { y: { beginAtZero: true } }
        }
      });
    })
    .catch(err => {
      document.getElementById('sensorCards').innerHTML = '<p class="text-red-500">Failed to load sensor data.</p>';
      console.error(err);
    });
</script>
</body>
</html>