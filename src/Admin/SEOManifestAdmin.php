<?php

namespace Goldfinch\Seo\Admin;

use SilverStripe\Admin\ModelAdmin;
use JonoM\SomeConfig\SomeConfigAdmin;
use Goldfinch\Seo\Models\ManifestConfig;

class SEOManifestAdmin extends ModelAdmin
{
    use SomeConfigAdmin;

    private static $url_segment = 'seo-manifest';

    private static $menu_title = 'Manifest';

    private static $menu_icon_class = 'font-icon-database';

    private static $menu_priority = -0.5;

    public $showImportForm = true;

    public $showSearchForm = true;

    private static $page_length = 30;

    private static $managed_models = [
        ManifestConfig::class => [
            'title' => 'Manifest',
        ],
    ];

    protected function init()
    {
        parent::init();

        $configSegment = $this->sanitiseClassName(ManifestConfig::class);

        if (strpos($_SERVER['REQUEST_URI'], $configSegment) === false) {
            $config = ManifestConfig::current_config();
            $configSegment = $this->sanitiseClassName(ManifestConfig::class);
            $link =
                str_replace($configSegment, '', parent::Link(null)) .
                $configSegment .
                '/EditForm/field/' .
                $configSegment .
                '/item/' .
                $config->ID .
                '/edit';

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
