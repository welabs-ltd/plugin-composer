<?php

namespace BaseNameSpace\PluginStub\Providers;

use BaseNameSpace\PluginStub\Assets;
use BaseNameSpace\PluginStub\DependencyManagement\BootableServiceProvider;

class ServiceProvider extends BootableServiceProvider {
	protected $services = [
		Assets::class,
	];

	/**
	 * @inheritDoc
	 *
	 * @return void
	 */
	public function boot(): void {
		// TODO: You may register the other service providers.

		// $this->getContainer()->addServiceProvider( new ExampleProvider() );
	}

	/**
     * Register the classes.
     */
	public function register(): void {
		$this->share_with_implements_tags( Assets::class );
    }
}
