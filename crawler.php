<?php

// Fonction pour parcourir une URL et récupérer le contenu
function crawl_url($url, $errorLogFile) {
    // Initialiser une session cURL
    $ch = curl_init();

    // Définir les options cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');

    // Exécuter la session cURL
    $response = curl_exec($ch);

    // Vérifier les erreurs cURL
    if (curl_errno($ch)) {
        $error = 'Erreur cURL pour ' . $url . ' : ' . curl_error($ch) . PHP_EOL;
        file_put_contents($errorLogFile, $error, FILE_APPEND);
    } else {
        // Enregistrement du succès dans le fichier de logs
        $success = 'URL OK : ' . $url . PHP_EOL;
        file_put_contents($errorLogFile, $success, FILE_APPEND);
    }

    // Fermer la session cURL
    curl_close($ch);

    return $response;
}

// Chemin vers le fichier journal des erreurs cURL
$errorLogFile = 'crawl_logs.log';

// Lire la liste des URLs à partir d'un fichier texte
$urls = file('urls.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Nombre total d'URLs à vérifier
$totalUrls = count($urls);

// Nombre maximal d'URLs à vérifier avant de faire une pause
$maxUrlsBeforePause = 1000;

// Compteur pour suivre le nombre d'URLs vérifiées
$urlCounter = 0;

// Enregistrer le nombre total d'URLs dans le fichier de logs
file_put_contents($errorLogFile, "Nombre total d'URLs à vérifier : $totalUrls" . PHP_EOL, FILE_APPEND);

// Parcourir chaque URL
foreach ($urls as $url) {
    echo "Crawling URL: $url" . PHP_EOL;
    $content = crawl_url($url, $errorLogFile);
    if ($content) {
        echo "Contenu de l'URL: $url" . PHP_EOL;
        echo $content . PHP_EOL . PHP_EOL;
    }
    
    // Incrémenter le compteur d'URLs vérifiées
    $urlCounter++;

    // Si le nombre maximal d'URLs vérifiées est atteint, faire une pause de 5 minutes
    if ($urlCounter % $maxUrlsBeforePause === 0 && $urlCounter !== $totalUrls) {
        echo "Pause de 5 minutes avant de continuer..." . PHP_EOL;
        sleep(300); // 5 minutes de pause
    } else {
        // Faire une pause de 3 secondes entre chaque requête
        sleep(3);
    }
}
