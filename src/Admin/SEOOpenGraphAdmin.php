<?php

namespace Goldfinch\Seo\Admin;

use SilverStripe\Admin\ModelAdmin;
use Goldfinch\Seo\Models\OpenGraph;
use JonoM\SomeConfig\SomeConfigAdmin;
use Goldfinch\Seo\Models\OpenGraphConfig;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldPrintButton;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\Forms\GridField\GridFieldImportButton;

class SEOOpenGraphAdmin extends ModelAdmin
{
    use SomeConfigAdmin;

    private static $url_segment = 'seo-open-graph';

    private static $menu_title = 'Open Graph';

    private static $managed_models = [
        OpenGraph::class => [
            'title' => 'Open Graph records',
        ],
        OpenGraphConfig::class => [
            'title'=> 'Settings',
        ],
    ];

    private static $menu_priority = -0.5;

    private static $menu_icon_class = 'font-icon-database';

    public $showImportForm = true;

    public $showSearchForm = true;

    private static $page_length = 30;

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
}
