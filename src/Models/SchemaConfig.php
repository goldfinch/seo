<?php

namespace Goldfinch\Seo\Models;

use Goldfinch\Seo\Models\Schema;
use JonoM\SomeConfig\SomeConfig;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\TemplateGlobalProvider;
use Kinglozzer\MultiSelectField\Forms\MultiSelectField;

class SchemaConfig extends DataObject implements TemplateGlobalProvider
{
    use SomeConfig;

    private static $table_name = 'SchemaConfig';

    private static $many_many = [
        'DefaultSchemas' => Schema::class,
    ];

    private static $many_many_extraFields = [
        'DefaultSchemas' => [
            'SortExtra' => 'Int',
        ],
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // $fields->removeByName(['DefaultOpenGraphObjectID']);

        $fields->addFieldsToTab('Root.Main', [
            MultiSelectField::create(
                'DefaultSchemas',
                'Default Schemas',
                $this,
                'SortExtra',
            ),
        ]);

        return $fields;
    }
}
