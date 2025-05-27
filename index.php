<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OpenSenseMap Viewer</title>
 
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
 
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
 
    <style>
        #map {
            height: 400px;
            width: 100%;
            margin-top: 20px;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">
 
    <!-- Navigation Menu -->
    <?php include 'menu.php'; ?>
 
    <!-- Page Content -->
    <main class="max-w-7xl mx-auto px-4 py-6">
        <?php include 'box.php'; ?>
    </main>
 
    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</body>
</html>