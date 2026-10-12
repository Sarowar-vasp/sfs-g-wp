<?php
// Base URL of your API
$baseUrl = "https://websiteapi.sfsguwahati.ac.in/api/home";

// API key
$apiKey = "G@xrL1Zil1T49Uv~DfJdsT@m8O31zSIf";

// Common headers
$headers = [
    'Accept: application/json',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 '
        . '(KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
    'Referer: https://sfsguwahati.ac.in',
    'Accept-Language: en-US,en;q=0.9',
    'Connection: keep-alive',
    'X-API-KEY: ' . $apiKey
];

// --------------------
// 1. Call ActiveSession
// --------------------
$url1 = $baseUrl . "/activesession";
$ch1 = curl_init($url1);
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch1, CURLOPT_HTTPHEADER, $headers);
$response1 = curl_exec($ch1);
curl_close($ch1);

echo "<h3>ActiveSession Response</h3>";
echo "<pre>" . htmlspecialchars($response1) . "</pre>";

$data1 = json_decode($response1, true);

// Extract values for second call
if (json_last_error() === JSON_ERROR_NONE && !empty($data1)) {
    $fromyear = $data1[0]['name'];   // e.g. "2026-2027"
    $sessyear = $data1[0]['id'];     // e.g. "20262027"

    // --------------------
    // 2. Call ClassByYear
    // --------------------
    $url2 = $baseUrl . "/classbyyear?fromyear=" . urlencode($fromyear) . "&sessyear=" . urlencode($sessyear);
    $ch2 = curl_init($url2);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
    $response2 = curl_exec($ch2);
    curl_close($ch2);

    echo "<h3>ClassByYear Response</h3>";
    echo "<pre>" . htmlspecialchars($response2) . "</pre>";

    $data2 = json_decode($response2, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "<h4>Decoded JSON (ClassByYear)</h4><pre>";
        print_r($data2);
        echo "</pre>";
    }
} else {
    echo "<strong>Failed to decode ActiveSession JSON</strong>";
}
?>