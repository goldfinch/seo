<?php

namespace Goldfinch\Seo\Admin;

use SilverStripe\Assets\Image;
use SilverStripe\Admin\ModelAdmin;
use SilverStripe\View\Requirements;
use SilverStripe\Forms\GridField\GridFieldConfig;
use Goldfinch\Seo\Forms\GridField\MetaEditorFocusPointColumn;
use SilverStripe\Forms\GridField\GridFieldPrintButton;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\Forms\GridField\GridFieldImportButton;
use Goldfinch\FocusPointExtra\Forms\GridField\GridFieldManyManyFocusConfig;
use Axllent\MetaEditor\MetaEditor;

class SEOImageAdmin extends ModelAdmin
{
    private static $url_segment = 'image-editor';

    private static $menu_title = 'Image editor';

    private static $managed_models = [
        Image::class => [
            'title' => 'Images',
        ],
    ];

    private static $menu_priority = -0.5;

    private static $menu_icon_class = 'font-icon-database';

    public $showImportForm = true;

    public $showSearchForm = true;

    private static $page_length = 30;

    public function init()
    {
        parent::init();
        Requirements::javascript('goldfinch/seo: javascript/image-editor.js');
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
        $config = GridFieldManyManyFocusConfig::create();

        $config->addComponent(MetaEditorFocusPointColumn::create());

        return $config;
    }

    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);

        $form->addExtraClass('image-editor');

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
