<?php

namespace WeLabs\PluginComposer\Providers;

use WeLabs\PluginComposer\DependencyManagement\BaseServiceProvider;
use WeLabs\PluginComposer\Assets;
use WeLabs\PluginComposer\ShortCode;
use WeLabs\PluginComposer\ThirdParty\Packages\League\Container\ServiceProvider\BootableServiceProviderInterface;

class ServiceProvider extends BaseServiceProvider implements BootableServiceProviderInterface {
	protected $services = [
		Assets::class,
		ShortCode::class,
	];

	/**
	 * @inheritDoc
	 *
	 * @return void
	 */
	public function boot(): void {
		$this->getContainer()->addServiceProvider( new BuilderProvider() );
	}

	/**
     * Register the classes.
     */
	public function register(): void {
		$this->share_with_implements_tags( Assets::class );
		$this->share_with_implements_tags( ShortCode::class );
    }
}
