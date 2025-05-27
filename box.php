<?php
// box.php

function fetchSensorData() {
    $url = "https://api.opensensemap.org/boxes/6126f02a04fd9f001b419450";
    $options = [
        "http" => [
            "method" => "GET",
            "header" => "Accept: application/json\r\n"
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        return "Failed to fetch data.";
    }

    $data = json_decode($response, true);
    return $data;
}
?>
