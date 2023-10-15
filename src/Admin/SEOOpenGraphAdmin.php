<?php

namespace Goldfinch\Seo\Admin;

use Goldfinch\Seo\Models\OpenGraph;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldPrintButton;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\Forms\GridField\GridFieldImportButton;

class SEOOpenGraphAdmin extends ModelAdmin
{
    private static $url_segment = 'seo-open-graph';

    private static $menu_title = 'Open graph';

    private static $managed_models = [
        OpenGraph::class => [
            'title' => 'Open Graph records',
        ],
    ];

    // private static $managed_models = [
    //    ExampleProduct::class,
    //
    //    ExampleCategory::class => [
    //        'title' => 'All categories',
    //    ],
    //
    //    'product-category' => [
    //        'dataClass' => ExampleCategory::class,
    //        'title' => 'Product categories',
    //    ],
    // ];

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

        // $config->removeComponentsByType(GridFieldExportButton::class);
        // $config->removeComponentsByType(GridFieldPrintButton::class);
        // $config->removeComponentsByType(GridFieldImportButton::class);

        return $config;
    }

    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);

        // ..

        return $form;
    }

    public function getExportFields()
    {
        return [
            // 'Name' => 'Name',
            // 'Category.Title' => 'Category'
        ];
    }
}