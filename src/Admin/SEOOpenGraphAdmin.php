<?php

namespace Goldfinch\Seo\Admin;

use SilverStripe\Admin\ModelAdmin;
use Goldfinch\Seo\Models\OpenGraph;
use JonoM\SomeConfig\SomeConfigAdmin;
use Goldfinch\Seo\Models\OpenGraphConfig;

class SEOOpenGraphAdmin extends ModelAdmin
{
    use SomeConfigAdmin;

    private static $url_segment = 'seo-open-graph';

    private static $menu_title = 'Open Graph';

    private static $menu_priority = -0.5;

    private static $menu_icon_class = 'font-icon-database';

    public $showImportForm = true;

    public $showSearchForm = true;

    private static $page_length = 30;

    private static $managed_models = [
        OpenGraph::class => [
            'title' => 'Open Graph records',
        ],
        OpenGraphConfig::class => [
            'title'=> 'Settings',
        ],
    ];
}
