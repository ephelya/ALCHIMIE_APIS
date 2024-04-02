<?php
//oai_imgdescribe.php

// input ["prompt", "model", "oai_url",  "imgUrl", "apikey", "maxtokens", "temperature"]
// output  ['description', 'conso']

// Récupérer les données JSON envoyées
$json_data = file_get_contents('php://input');

// Décoder les données JSON
$data = json_decode($json_data, true); 

// Afficher les données
//print_r($data);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);

    $url = $data['oai_url'] ?? 'https://api.openai.com/v1/chat/completions'; 
    $prompt = $data['model'] ?? ['gpt-4-vision-preview'];
    $prompt = $data['prompt'] ?? ['décris cette image'];
    $imgUrl = $data['imgUrl'] ?? [];
    $apiKey = $data['apiKey'] ?? [];
    $maxtokens = $data['maxtokens'] ?? [200];
    $temperature = $data['temperature'] ?? [0.5];

    // Préparation des données pour l'API OpenAI
    $postData = [
        'model' => $model,
        "messages" => [
            [
              "role" => "user",
              "content" => [
                [
                  "type"=> "text",
                  "text"=> $prompt
                ],
                [
                  "type"=> "image_url",
                  "image_url"=> [
                    "url"=> $imgUrl
                  ]
                ]
              ]
                  ],
        "max_tokens" => $maxtokens,
        "temperature": $temperature
        ]
      ];

   // echo json_encode($postData); exit();

    // Initialisation de cURL
    $ch = curl_init($url);

    // Configuration des options de cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
    ]);

    // Exécution de la requête et récupération de la réponse
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    // Vérifiez si une erreur cURL s'est produite
    if ($err) {
        echo json_encode(['error' => 'Erreur Curl: ' . $err]);
    } else {
        // Traitement de la réponse
        $responseData = json_decode($response, true);

        // Vérification et gestion des erreurs potentielles de l'API OpenAI
        if (isset($responseData['error'])) {
            echo json_encode(['error' => 'Erreur API OpenAI: ' . $responseData['error']['message']]);
        } else if (isset($responseData['choices'][0]['message']['content'])) {
            //print_r($responseData);
            $conso = $responseData['usage']['total_tokens'];
            $description = $responseData['choices'][0]['message']['content'];
            //$botResponse = ["conso" => $conso, "message" => $message];
            $responseArray = ["description" => $description, "conso" => $conso];
            echo json_encode(["description" => $description, "conso" => $conso]);
            exit;
        } else {
            // Réponse inattendue ou mal formée
            echo json_encode(['error' => 'Réponse inattendue de l\'API OpenAI']);
        }
    }
} else {
    // Si la requête n'est pas POST, renvoyez un message d'erreur
    http_response_code(405); // Méthode non autorisée
    echo json_encode(['error' => 'Méthode non autorisée, seules les requêtes POST sont acceptées']);
}

?>

