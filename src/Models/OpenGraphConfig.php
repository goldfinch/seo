<?php

namespace Goldfinch\Seo\Models;

use SilverStripe\Assets\Image;
use JonoM\SomeConfig\SomeConfig;
use SilverStripe\ORM\DataObject;
use Goldfinch\Seo\Models\OpenGraph;
use SilverShop\HasOneField\HasOneButtonField;
use SilverStripe\View\TemplateGlobalProvider;
use SilverStripe\AssetAdmin\Forms\UploadField;

class OpenGraphConfig extends DataObject implements TemplateGlobalProvider
{
    use SomeConfig;

    private static $table_name = 'OpenGraphConfig';

    private static $db = [];

    private static $has_one = [
        'DefaultImage' => Image::class,
        'DefaultObject' => OpenGraph::class,
    ];

    private static $owns = [
        'DefaultImage',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName(['DefaultObjectID']);

        $fields->addFieldsToTab('Root.Main', [

            UploadField::create('DefaultImage', 'Default image'),
            HasOneButtonField::create($this, 'DefaultObject', 'DefaultObjectID', 'Default object'),

        ]);

        return $fields;
    }
}
