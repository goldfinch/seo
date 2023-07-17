<?php

namespace Goldfinch\Seokit\Extensions;

use SilverStripe\Assets\Image;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\ValidationResult;

class SiteConfigExtension extends DataExtension
{
    private static $has_one = [
        'OpenGraphDefaultImage' => Image::class,
    ];

    private static $owns = [
        'OpenGraphDefaultImage',
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab('Root.Main', [

            UploadField::create('OpenGraphDefaultImage', 'Open Graph default image'),

        ]);
    }

    public function validate(ValidationResult $validationResult)
    {
        // $validationResult->addError('Error message');
    }
}
