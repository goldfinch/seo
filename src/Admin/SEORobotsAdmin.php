<?php

namespace Goldfinch\Seo\Admin;

use SilverStripe\Admin\ModelAdmin;
use JonoM\SomeConfig\SomeConfigAdmin;
use Goldfinch\Seo\Models\RobotsConfig;

class SEORobotsAdmin extends ModelAdmin
{
    use SomeConfigAdmin;

    private static $url_segment = 'seo-robots';

    private static $menu_title = 'Robots';

    private static $menu_priority = -0.5;

    private static $menu_icon_class = 'font-icon-database';

    public $showImportForm = true;

    public $showSearchForm = true;

    private static $page_length = 30;

    private static $managed_models = [
        RobotsConfig::class => [
            'title'=> 'Robots',
        ],
    ];

    protected function init()
    {
        parent::init();

        $configSegment = $this->sanitiseClassName(RobotsConfig::class);

        if (strpos($_SERVER['REQUEST_URI'], $configSegment) === false)
        {
            $config = RobotsConfig::current_config();
            $configSegment = $this->sanitiseClassName(RobotsConfig::class);
            $link = str_replace($configSegment, '', parent::Link(null)) . $configSegment . '/EditForm/field/' . $configSegment . '/item/' . $config->ID . '/edit';

            return $this->redirect($link);
        }
    }

    public function Link($action = null)
    {
        if (!$action) {
            $action = $this->sanitiseClassName($this->modelTab);
        }

        return parent::Link($action);
    }
}
