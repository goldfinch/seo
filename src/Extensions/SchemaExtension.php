<?php

namespace Goldfinch\Seo\Extensions;

use SilverStripe\Forms\CheckboxField;
use Goldfinch\Seo\Models\SchemaMapping;
use SilverStripe\Forms\GridField\GridField;
use Kinglozzer\MultiSelectField\Forms\MultiSelectField;
use Goldfinch\Helpers\Forms\GridField\GridFieldManyManyConfig;

class SchemaExtension extends SeoDataExtension
{
    private static $db = [
        'DisableDefaultSchema' => 'Boolean',
    ];

    private static $many_many = [
        'Schemas' => [
            'through' => SchemaMapping::class,
            'from' => 'Parent',
            'to' => 'Schema',
        ],
    ];

    protected function seoFieldsTab(&$fields, $tab)
    {
        if ($this->owner->ID) {
            $fields->addFieldsToTab($tab, [
                // $schemaGrid = GridField::create(
                //     'Schemas',
                //     'Schemas',
                //     $this->owner->Schemas(),
                // ),
                MultiSelectField::create(
                    'Schemas',
                    'Schemas',
                    $this->owner,
                    'SortOrder',
                ),
                CheckboxField::create(
                    'DisableDefaultSchema',
                    'Disable default schema for this page',
                ),
            ]);

            // $config = GridFieldManyManyConfig::create();

            // $schemaGrid->setConfig($config);
        }
    }
}
