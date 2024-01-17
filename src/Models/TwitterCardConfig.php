<?php

namespace Goldfinch\Seo\Models;

use SilverStripe\Assets\Image;
use JonoM\SomeConfig\SomeConfig;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\TextField;
use Goldfinch\Seo\Models\TwitterCard;
use SilverShop\HasOneField\HasOneButtonField;
use SilverStripe\View\TemplateGlobalProvider;
use SilverStripe\AssetAdmin\Forms\UploadField;

class TwitterCardConfig extends DataObject implements TemplateGlobalProvider
{
    use SomeConfig;

    private static $table_name = 'TwitterCardConfig';

    private static $db = [
        'TC_Site' => 'Varchar',
        'TC_SiteID' => 'Varchar',
    ];

    private static $has_one = [
        'DefaultImage' => Image::class,
        'DefaultObject' => TwitterCard::class,
    ];

    private static $owns = ['DefaultImage'];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName(['DefaultObjectID']);

        $fields->addFieldsToTab('Root.Main', [
            HasOneButtonField::create(
                $this,
                'DefaultObject',
                'DefaultObjectID',
                'Default object',
            ),
            UploadField::create('DefaultImage', 'Default image')
                ->setDescription('Default image for all TC records.')
                ->setFolderName('seo'),
            TextField::create('TC_Site', 'Site')->setDescription(
                'Default value for all TC records.',
            ),
            TextField::create('TC_SiteID', 'Site ID')->setDescription(
                'Default value for all TC records.',
            ),
        ]);

        return $fields;
    }
}
