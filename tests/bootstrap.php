<?php

declare(strict_types=1);

// cesta k Composer autoloadu
require __DIR__ . '/../vendor/autoload.php';

// Inicializace nette tester
Tester\Environment::setup();

// nastavení timezony
date_default_timezone_set('Europe/Prague');

// nastaveni adresare pro docasne soubory
define('TEMP_DIR', __DIR__ . '/tmp');
@mkdir(TEMP_DIR, 0777, true);