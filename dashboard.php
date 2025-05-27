<?php
session_start();
if (!isset($_SESSION['user'])) {
  header('Location: login.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="flex bg-gray-100">
  <?php include 'side_menu.php'; ?>

  <main class="flex-1 p-6 space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-3xl font-bold">Welcome, <?= htmlspecialchars($_SESSION['user']) ?></h1>
      <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded">Logout</a>
    </div>

    <!-- Weather Widget -->
    <section>
      <h2 class="text-xl font-semibold mb-2">Weather</h2>
      <iframe src="https://api.wo-cloud.com/content/widget/?geoObjectKey=6620456&language=en&region=US&timeFormat=HH:mm&windUnit=mph&systemOfMeasurement=imperial&temperatureUnit=fahrenheit" name="CW2" scrolling="no" width="290" height="318" frameborder="0" style="border: 1px solid #10658E;border-radius: 8px"></iframe>
    </section>

    <!-- Real-time Sensor Summary -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold">Temperature</h3>
        <p id="temp" class="text-2xl text-blue-600">Loading...</p>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold">Humidity</h3>
        <p id="humidity" class="text-2xl text-green-600">Loading...</p>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-lg font-semibold">Air Pressure</h3>
        <p id="pressure" class="text-2xl text-purple-600">Loading...</p>
      </div>
    </section>

    <!-- Sensor Data Chart -->
    <section class="bg-white p-6 rounded-lg shadow">
      <h2 class="text-xl font-semibold mb-4">Sensor Data Graph</h2>
      <canvas id="sensorChart"></canvas>
    </section>

    <!-- Live Map -->
    <section class="bg-white p-6 rounded-lg shadow">
      <h2 class="text-xl font-semibold mb-4">Sensor Location Map</h2>
      <div id="map" class="h-96 rounded"></div>
    </section>
  </main>

  <script>
    const map = L.map('map').setView([4.9031, 114.9398], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([4.9031, 114.9398]).addTo(map)
      .bindPopup('Sensor Location')
      .openPopup();

    // Dummy chart data - replace with AJAX for real data
    const ctx = document.getElementById('sensorChart').getContext('2d');
    const chart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['00:00', '01:00', '02:00', '03:00', '04:00'],
        datasets: [
          {
            label: 'Temperature',
            data: [22, 23, 21, 24, 25],
            borderColor: 'rgb(59, 130, 246)',
            fill: false
          },
          {
            label: 'Humidity',
            data: [60, 62, 58, 65, 66],
            borderColor: 'rgb(34, 197, 94)',
            fill: false
          }
        ]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

    // Dummy real-time update
    setInterval(() => {
      document.getElementById('temp').textContent = `${(20 + Math.random() * 5).toFixed(1)} °C`;
      document.getElementById('humidity').textContent = `${(50 + Math.random() * 10).toFixed(0)} %`;
      document.getElementById('pressure').textContent = `${(1000 + Math.random() * 10).toFixed(0)} hPa`;
    }, 5000);
  </script>
</body>
</html>
