<?php

namespace Goldfinch\Seo\Admin;

use SilverStripe\Admin\ModelAdmin;
use JonoM\SomeConfig\SomeConfigAdmin;
use Goldfinch\Seo\Models\ManifestConfig;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldPrintButton;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\Forms\GridField\GridFieldImportButton;

class SEOManifestAdmin extends ModelAdmin
{
    use SomeConfigAdmin;

    private static $url_segment = 'seo-manifest';

    private static $menu_title = 'Manifest';

    private static $managed_models = [
        ManifestConfig::class => [
            'title'=> 'Manifest',
        ],
    ];

    private static $menu_priority = -0.5;

    private static $menu_icon_class = 'font-icon-database';

    public $showImportForm = true;

    public $showSearchForm = true;

    private static $page_length = 30;

    protected function init()
    {
        parent::init();

        $configSegment = $this->sanitiseClassName(ManifestConfig::class);

        if (strpos($_SERVER['REQUEST_URI'], $configSegment) === false)
        {
            $config = ManifestConfig::current_config();
            $configSegment = $this->sanitiseClassName(ManifestConfig::class);
            $link = str_replace($configSegment, '', parent::Link(null)) . $configSegment . '/EditForm/field/' . $configSegment . '/item/' . $config->ID . '/edit';

            return $this->redirect($link);
        }
    }

    public function getList()
    {
        $list =  parent::getList();

        // ..

        return $list;
    }

    public function getSearchContext()
    {
        $context = parent::getSearchContext();

        // ..

        return $context;
    }

    protected function getGridFieldConfig(): GridFieldConfig
    {
        $config = parent::getGridFieldConfig();

        // ..

        return $config;
    }

    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);

        // ..

        return $form;
    }

    public function Link($action = null)
    {
        if (!$action) {
            $action = $this->sanitiseClassName($this->modelTab);
        }

        return parent::Link($action);
    }
}
