<?php
//api.php
// Récupérer les données JSON envoyées
$json_data = file_get_contents('php://input');

// Décoder les données JSON
$data = json_decode($json_data, true); 


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // Vos paramètres d'authentification et d'API
    $url = 'https://api.openai.com/v1/images/generations';

    // Récupération du corps de la requête
    $data = json_decode(file_get_contents('php://input'), true);

    $prompt = $data['prompt'] ?? [];

    define ("OAISK", "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");
    $apiKey = OAISK;
    // Préparation des données pour l'API OpenAI
    $postData = [
        'model' => 'dall-e-3',
        'prompt' => $prompt,
        'n' => 1,
        'size' => '1024x1024',
      ];


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
    $response = curl_exec($ch); //var_dump($response);
    $err = curl_error($ch);
    curl_close($ch);

    // Vérifiez si une erreur cURL s'est produite
    if ($err) {
        echo json_encode(['error' => 'Erreur Curl: ' . $err]);
    } else {
        // Traitement de la réponse
        
 //file_put_contents('api_response_log.txt', $response, FILE_APPEND);

    $responseData = json_decode($response, true);
    // Si vous souhaitez aussi loguer le tableau décodé :
   // file_put_contents('api_response_decoded_log.txt', print_r($responseData, true), FILE_APPEND);

        // Vérification et gestion des erreurs potentielles de l'API OpenAI
        if (isset($responseData['error'])) {
            echo json_encode(['error' => 'Erreur API OpenAI: ' . $responseData['error']['message']]);
        } else if (isset($responseData['data'][0]['url'])) {
            //print_r($responseData);
            $conso = $responseData['usage']['total_tokens'];
            $imgUrl = $responseData['data'][0]['url'];
            //$botResponse = ["conso" => $conso, "message" => $message];
            $responseArray = ["imgUrl" => $imgUrl, "conso" => $conso];
            echo json_encode($responseArray);

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

