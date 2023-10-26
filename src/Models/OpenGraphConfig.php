<?php

namespace Goldfinch\Seo\Models;

use SilverStripe\Assets\Image;
use JonoM\SomeConfig\SomeConfig;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\TextField;
use Goldfinch\Seo\Models\OpenGraph;
use SilverShop\HasOneField\HasOneButtonField;
use SilverStripe\View\TemplateGlobalProvider;
use SilverStripe\AssetAdmin\Forms\UploadField;

class OpenGraphConfig extends DataObject implements TemplateGlobalProvider
{
    use SomeConfig;

    private static $table_name = 'OpenGraphConfig';

    private static $db = [
        'FB_AppID' => 'Varchar',
        'OG_Locale' => 'Varchar',
    ];

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

            HasOneButtonField::create($this, 'DefaultObject', 'DefaultObjectID', 'Default object'),
            UploadField::create('DefaultImage', 'Default image')->setDescription('Default image for all OG records.'),
            TextField::create('FB_AppID', 'Faceboook App ID')->setDescription('Default value for all OG records.'),
            TextField::create('OG_Locale', 'Locale')->setDescription('Default value for all OG records.'),

        ]);

        return $fields;
    }
}
