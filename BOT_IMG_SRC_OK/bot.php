<?php
// bot.php
$data = array(
    "imgUrl" => "https://i.pinimg.com/236x/8e/9f/0f/8e9f0f87b122421ae4a4d66f99e810cf.jpg",
    "prompt" => "décris cette image" 
);


$result = sendToApi($data);
$conso = $result['conso'];
$description = $result['description'];
echo "description: " . $result['description'] . ", Consommation: " . $result['conso'];

function sendToApi($data)
{
    // Convertir les données en JSON
    $json_data = json_encode($data);
    $url = "http://nathaliebrigitte.com/BOTS/BOT_IMG_SRC/api.php";

    // Initialisation de cURL
    $curl = curl_init($url);

    // Configuration de cURL pour envoyer une requête POST
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Pour obtenir la réponse sous forme de chaîne

    // Exécution de la requête
    $response = curl_exec($curl);
    var_dump($response);
    if ($response === false) {
        echo "Erreur cURL: " . curl_error($curl);
    } else {
        $responseData = json_decode($response, true); // Décodage en tableau associatif
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Erreur de décodage JSON: " . json_last_error_msg();
        }
        if ($responseData !== null) {
            // Supposons que ces clés existent toujours dans la réponse
            $result['description'] = $responseData['description'];
            $result['conso'] = $responseData['conso'];
        } else {
            echo "Erreur: La réponse de l'API n'a pas pu être décodée.\n";
        }
    }
    curl_close($curl);

    return $result;
}
?>
