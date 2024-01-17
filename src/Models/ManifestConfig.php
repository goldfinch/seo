<?php

namespace Goldfinch\Seo\Models;

use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use JonoM\SomeConfig\SomeConfig;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\TemplateGlobalProvider;

class ManifestConfig extends DataObject implements TemplateGlobalProvider
{
    use SomeConfig;

    private static $table_name = 'ManifestConfig';

    private static $db = [
        'ShortName' => 'Varchar',
        'Name' => 'Varchar',
        'Description' => 'Text',
    ];

    private static $has_one = [
        'PortableImage' => Image::class,
        'VectorIcon' => File::class,
        'IcoIcon' => File::class,
    ];

    private static $owns = ['PortableImage', 'VectorIcon', 'IcoIcon'];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields
            ->dataFieldByName('PortableImage')
            ->setFolderName('seo')
            ->setAllowedExtensions('png')
            ->setDescription('png 512x512');

        $fields
            ->dataFieldByName('VectorIcon')
            ->setFolderName('seo')
            ->setAllowedExtensions('svg')
            ->setDescription('svg 512x512');

        $fields
            ->dataFieldByName('IcoIcon')
            ->setFolderName('seo')
            ->setAllowedExtensions('ico')
            ->setDescription('ico 32x32');

        return $fields;
    }
}
