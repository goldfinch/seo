<?php

namespace Goldfinch\Seo\Admin;

use Goldfinch\Seo\Models\Schema;
use SilverStripe\Admin\ModelAdmin;
use JonoM\SomeConfig\SomeConfigAdmin;
use Goldfinch\Seo\Models\SchemaConfig;

class SEOSchemaAdmin extends ModelAdmin
{
    use SomeConfigAdmin;

    private static $url_segment = 'seo-schema';

    private static $menu_title = 'Schema';

    private static $menu_priority = -0.5;

    private static $menu_icon_class = 'font-icon-database';

    public $showImportForm = true;

    public $showSearchForm = true;

    private static $page_length = 30;

    private static $managed_models = [
        Schema::class => [
            'title' => 'Schema records',
        ],
        SchemaConfig::class => [
            'title'=> 'Settings',
        ],
    ];
}
