<?php

declare(strict_types=1);

namespace App;

use Nette;
use Nette\Bootstrap\Configurator;



class Bootstrap
{
	private Configurator $configurator;
	private string $rootDir;


	public function __construct()
	{
		$this->rootDir = dirname(__DIR__);
		$this->configurator = new Configurator;
		$this->configurator->setTempDirectory($this->rootDir . '/temp');
	}


	public function bootWebApplication(): Nette\DI\Container
	{
		$this->initializeEnvironment();
		$this->setupContainer();
		return $this->configurator->createContainer();
	}


	public function initializeEnvironment(): void
    {
        $envFile = $this->rootDir . '/.env';
        if (file_exists($envFile)) {
            (new \Symfony\Component\Dotenv\Dotenv())->load($envFile);
        }

        // Debug mode z environment nebo default
        $debug = $_ENV['APP_DEBUG'] ?? true;
		$this->configurator->setDebugMode($debug); // enable for your remote IP
		$this->configurator->enableTracy($this->rootDir . '/log');

		$this->configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();

        $this->configurator->addDynamicParameters([
            'env' => [
                'VISUALCROSSING_API_KEY' => $_ENV['VISUALCROSSING_API_KEY'] ?? null,
            ],
        ]);
	}


	private function setupContainer(): void
	{
		$configDir = $this->rootDir . '/config';
		$this->configurator->addConfig($configDir . '/common.neon');
		$this->configurator->addConfig($configDir . '/services.neon');
	}
}
