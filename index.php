<?php
require_once __DIR__ . '/vendor/autoload.php';

use iutnc\netvod\dispatch\Dispatcher;
use iutnc\netvod\repository\NetvodRepository;

NetvodRepository::setConfig(__DIR__ . '/config/db.ini');

// Lancement du dispatcher
$dispatcher = new Dispatcher();
$dispatcher->run();
