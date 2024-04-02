<?php
//oai_imggenerate.php

// input ["prompt", "model", "oai_url",  "size", "apikey"]
// output  ['imgUrl']

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true); 


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $prompt = $data['prompt'] ?? [];
    $size = $data['size'] ?? '1024x1024'; 

    $url = $data['oai_url'] ?? 'https://api.openai.com/v1/images/generations'; 
    $model = $data['model'] ?? 'dall-e-3'; 
    $apikey = $data['apikey']; 

    $data = json_decode(file_get_contents('php://input'), true);

    $postData = [
        'model' => $model,
        'prompt' => $prompt,
        'n' => 1,
        'size' => $size,
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
        // Traitement de la réponse
        $responseData = json_decode($response, true);
            if (isset($responseData['error'])) {
                echo json_encode(['error' => 'Erreur API OpenAI: ' . $responseData['error']['message']]);
            } else if (isset($responseData['data'][0]['url'])) {
                $conso = $responseData['usage']['total_tokens'];
                $imgUrl = $responseData['data'][0]['url'];
                $responseArray = ["imgUrl" => $imgUrl, "conso" => $conso];
                echo json_encode($responseArray);
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

