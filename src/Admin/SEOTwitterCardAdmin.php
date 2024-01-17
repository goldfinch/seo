<?php

namespace Goldfinch\Seo\Admin;

use SilverStripe\Admin\ModelAdmin;
use Goldfinch\Seo\Models\TwitterCard;
use JonoM\SomeConfig\SomeConfigAdmin;
use Goldfinch\Seo\Models\TwitterCardConfig;

class SEOTwitterCardAdmin extends ModelAdmin
{
    use SomeConfigAdmin;

    private static $url_segment = 'seo-twitter-card';

    private static $menu_title = 'Twitter card';

    private static $menu_priority = -0.5;

    private static $menu_icon_class = 'font-icon-database';

    public $showImportForm = true;

    public $showSearchForm = true;

    private static $page_length = 30;

    private static $managed_models = [
        TwitterCard::class => [
            'title' => 'Twitter Cards',
        ],
        TwitterCardConfig::class => [
            'title'=> 'Settings',
        ],
    ];
}
