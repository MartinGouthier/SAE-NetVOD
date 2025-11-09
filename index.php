<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';

use iutnc\netvod\dispatch\Dispatcher;
use iutnc\netvod\exception\DatabaseConnectionException;
use iutnc\netvod\repository\NetvodRepository;

try {
    NetvodRepository::setConfig(__DIR__ . '/config/db.ini');
    // Lancement du dispatcher
    $dispatcher = new Dispatcher();
    $dispatcher->run();
}
catch (DatabaseConnectionException $e) {
    $pageComplete = <<<END
            <!DOCTYPE html>
            <html lang="fr">
                <head>
                    <meta charset="UTF-8">
                    <title>NetVod - Visionnage de séries en ligne</title>
                    <link rel="stylesheet" href="style.css">
                </head>
            
                <body>
                    <h1>NetVod</h1>
                    <p>Application de visionnage de séries</p>
                    <hr>
            
                    <p>{$e->getMessage()}</p>
                </body>
            </html>
        END;
    echo($pageComplete);
}
