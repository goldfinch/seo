<?php

namespace Goldfinch\Seo\Models;

use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\AssetAdmin\Forms\UploadField;
use JonoM\SomeConfig\SomeConfig;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\TemplateGlobalProvider;

class OpenGraphConfig extends DataObject implements TemplateGlobalProvider
{
    use SomeConfig;

    private static $has_one = [
        'DefaultImage' => Image::class,
    ];

    private static $owns = [
        'DefaultImage',
    ];

    private static $table_name = 'OpenGraphConfig';

    private static $db = [];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab('Root.Main', [

            UploadField::create('DefaultImage', 'Default image'),

        ]);
    }
}
