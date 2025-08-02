<?php

namespace WeLabs\PluginComposer\Providers;

use WeLabs\PluginComposer\DependencyManagement\BaseServiceProvider;
use WeLabs\PluginComposer\Assets;
use WeLabs\PluginComposer\ShortCode;
use WeLabs\PluginComposer\Admin\Settings;
use WeLabs\PluginComposer\Api\Admin\SettingsController;
use WeLabs\PluginComposer\ThirdParty\Packages\League\Container\ServiceProvider\BootableServiceProviderInterface;

class ServiceProvider extends BaseServiceProvider implements BootableServiceProviderInterface {
	protected $services = [
		Assets::class,
		ShortCode::class,
		Settings::class,
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
		$this->share_with_implements_tags( Settings::class );
		$this->share_with_implements_tags( SettingsController::class );
    }
}
