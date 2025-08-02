<?php

namespace WeLabs\PluginComposer\Providers;

use WeLabs\PluginComposer\Contracts\BuilderContract;
use WeLabs\PluginComposer\Contracts\FileSystemContract;
use WeLabs\PluginComposer\Lib\FileSystem;
use WeLabs\PluginComposer\Lib\PluginBuilder;
use WeLabs\PluginComposer\DependencyManagement\BaseServiceProvider;

class BuilderProvider extends BaseServiceProvider {

    public function register(): void {
        $this->getContainer()->add( FileSystemContract::class, FileSystem::class );
		$this->getContainer()->add(
            BuilderContract::class, function () {
				return new PluginBuilder( $this->getContainer()->get( FileSystemContract::class ) );
			}
        );
    }
}
