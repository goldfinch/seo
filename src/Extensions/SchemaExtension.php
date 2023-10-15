<?php

namespace Goldfinch\Seo\Extensions;

use Goldfinch\Seo\Models\SchemaMapping;
use SilverStripe\Forms\GridField\GridField;
use Goldfinch\Helpers\Forms\GridField\GridFieldManyManyConfig;

class SchemaExtension extends SeoDataExtension
{
    private static $many_many = [
        'Schemas' => [
            'through' => SchemaMapping::class,
            'from' => 'Parent',
            'to' => 'Schema',
        ]
    ];

    protected function seoFieldsTab(&$fields, $tab)
    {
        if ($this->owner->ID)
        {
            $fields->addFieldsToTab(
                $tab,
                [
                    $schemaGrid = GridField::create(
                        'Schemas',
                        'Schemas',
                        $this->owner->Schemas(),
                    )
                ]
            );

            $config = GridFieldManyManyConfig::create();

            $schemaGrid->setConfig($config);
        }
    }
}
