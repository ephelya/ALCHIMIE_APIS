<?php
// oai_assistant.php

// input ["conversationHistory", "apikey", "model"]
// output  ['imgUrl']


$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $url = 'https://api.openai.com/v1/chat/completions';
    $data = json_decode(file_get_contents('php://input'), true);
    $conversationHistory = $data['conversationHistory'] ?? [];
    $model = $data['model'] ?? 'gpt-3.5-turbo'; 
    $apiKey = $data['apiKey']; 
    $postData = [
        'model' => $model ,
        'messages' => $conversationHistory,
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
    ]);

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    

    if ($err) {
        echo json_encode(['error' => 'Erreur Curl: ' . $err]);
    } else {
-        $responseData = json_decode($response, true);

        /*
        file_put_contents('api_response_log.txt', $response, FILE_APPEND);
        file_put_contents('api_response_decoded_log.txt', print_r($responseData, true), FILE_APPEND);
        */
        
        if (isset($responseData['error'])) {
            echo json_encode(['error' => 'Erreur API OpenAI: ' . $responseData['error']['message']]);
        } else if (isset($responseData['choices'][0]['message']['content'])) {
            $conso = $responseData['usage']['total_tokens'];
            $message = $responseData['choices'][0]['message']['content'];
            $responseArray = ["message" => $message, "conso" => $conso];
            echo json_encode(["message" => $message, "conso" => $conso]);
            exit;
        } else {
            echo json_encode(['error' => 'Réponse inattendue de l\'API OpenAI']);
        }
    }
} else {
    http_response_code(405); 
    echo json_encode(['error' => 'Méthode non autorisée, seules les requêtes POST sont acceptées']);
}

?>

