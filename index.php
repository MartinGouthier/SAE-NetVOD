<?php
require_once __DIR__ . '/vendor/autoload.php';

use iutnc\onsie\dispatch\Dispatcher;
use iutnc\onsie\repository\OnsieRepository;

OnsieRepository::setConfig(__DIR__ . '/config/db.ini');

// Lancement du dispatcher
$dispatcher = new Dispatcher();
$dispatcher->run();
