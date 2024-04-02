<?php
//sublym_avatar_generate.php

//input : $avatarPhotos, $dreams

foreach ($avatarPhotos as $photo_url)
{
    //on récupère la description physique détaillée
    $avatarDescription = getAvatarDescription($photo_url);
    //on récupère la description de la photo respectant les critères demandés par la bdd
    $userPhotoDescription = getPhotoDescription($photo_url);
    // on génère 6 photos avec MJ
    $avatarsPhotos[] = getAvatarPhotos($userPhotoDescription);
}
//on récupère la photo  la plus fidèle avec l'api facerecognition
$topAvatarPhoto = getTopAvatarPhoto($avatarsPhotos);


define ("NB_PHOTOS", $nb_photos_user);
foreach ($dreams as $dream)
{
    //on crée les situations à imaginer
    $dreamSituations = getUserDreamPrompt($dream);
    foreach($dreamSituations as $dreamsituation)
    {
        //on génère le prompt correspondant
        $situationPrompt = getSituationPrompt($dreamsituation, $topAvatarPhoto, $avatarDescription);
        //on génère les photos d'attraction
        $sublymPhotos = sublymPhotosGenerate(NB_PHOTOS, $situationPrompt);
        
    }

}


?>