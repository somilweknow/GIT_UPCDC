<?php

header('Content-Type: application/json');
include("../scripts/settings.php");

$endpoint = "https://api.cooperatives.gov.in/en/MasterApi/stateWiseBasicDetails";

$payload = [
    "key" => "84950dfe63c3a294f83e8e656763475c50625dc8c577c84f479785b6d00e4e31",
    "state_code" => "9"
];

// =============================
// 🔥 CURL FUNCTION (FIXED SSL)
// =============================
function call_api($url, $data)
{
    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS => json_encode($data),

        // 🔥 FIX FOR SSL ERROR (TEST ONLY)
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,

        CURLOPT_TIMEOUT => 60
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return [
            "status" => "error",
            "message" => $error
        ];
    }

    return json_decode($response, true);
}

// =============================
// 🔥 CALL API
// =============================
$data = call_api($endpoint, $payload);

// =============================
// 🔥 CHECK RESPONSE
// =============================
if (!isset($data['result']) || empty($data['result'])) {
    echo json_encode([
        "status" => "fail",
        "message" => "No data returned or invalid response",
        "raw" => $data
    ], JSON_PRETTY_PRINT);
    exit;
}

// =============================
// 🔥 LIMIT 20 RECORDS
// =============================
$limited = array_slice($data['result'], 0, 20);

// =============================
// 🔥 OUTPUT
// =============================
echo json_encode([
    "status" => "success",
    "total_received" => count($data['result']),
    "returned" => count($limited),
    "data" => $limited
], JSON_PRETTY_PRINT);

?>