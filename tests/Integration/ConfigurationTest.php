<?php

namespace Spatie\UptimeMonitor\Test;

use Spatie\UptimeMonitor\Models\Site;
use Spatie\UptimeMonitor\SiteRepository;

class ConfigurationTest extends TestCase
{
    /** @test */
    public function a_custom_model_can_be_specified()
    {
        factory(Site::class)->create();

        $customModel = new class extends Site {
            public $table = 'sites';
        };

        $this->app['config']->set('laravel-uptime-monitor.site_model', get_class($customModel));



        $this->assertInstanceOf(get_class($customModel), SiteRepository::getAllEnabledSites()->first());
    }
}