<?php

namespace Goldfinch\Admin;

use Goldfinch\Models\SEOData;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldImportButton;

class SEOAdmin extends ModelAdmin
{
    private static $url_segment = 'seo';

    private static $menu_title = 'SEO';

    private static $managed_models = [
        SEOData::class => [
            'title' => 'Data records',
        ],
    ];

    private static $menu_priority = 0;

    private static $menu_icon_class = 'bi-wrench-adjustable-circle';

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

        $config->removeComponentsByType(GridFieldImportButton::class);

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
