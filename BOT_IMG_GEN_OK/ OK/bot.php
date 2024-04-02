<?php
// bot.php
$data = array(
    "prompt" => "chaton mignon" 
);

$result = sendToApi($data);
$conso = $result['conso'];
$imgUrl = $result['imgUrl'];
echo "img: <img src='" . $result['imgUrl'] . "'> " ; 

//FONCTION DE L'API
// input $data["prompt"]
// output  $result['imgUrl']
function sendToApi($data)
{
    // Convertir les données en JSON
    $json_data = json_encode($data);
    $url = "http://nathaliebrigitte.com/BOTS/BOT_IMG_GEN/api.php";

    // Initialisation de cURL 
    $curl = curl_init($url);

    // Configuration de cURL pour envoyer une requête POST
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Pour obtenir la réponse sous forme de chaîne

    // Exécution de la requête
    $response = curl_exec($curl);
    //var_dump($response);
    if ($response === false) {
        echo "Erreur cURL: " . curl_error($curl);
    } else {
        file_put_contents('api_response_log.txt', $response, FILE_APPEND);

        $responseData = json_decode($response, true);
        // Si vous souhaitez aussi loguer le tableau décodé :
        file_put_contents('api_response_decoded_log.txt', print_r($responseData, true), FILE_APPEND);
    
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Erreur de décodage JSON: " . json_last_error_msg();
        }
        if ($responseData !== null) {
            // Supposons que ces clés existent toujours dans la réponse
            $result['imgUrl'] = $responseData['imgUrl'];
            $result['conso'] = $responseData['conso'];
        } else {
            echo "Erreur: La réponse de l'API n'a pas pu être décodée.\n";
        }
    }
    curl_close($curl);
    return $result;
}
?>
