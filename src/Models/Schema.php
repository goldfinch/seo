<?php

namespace Goldfinch\Seo\Models;

use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;
use ByWaterSolutions\JsonEditorField\JsonEditorField;

class Schema extends DataObject
{
    private static $singular_name = 'schema';

    private static $plural_name = 'schemas';

    private static $table_name = 'Schema';

    private static $cascade_deletes = [];

    private static $cascade_duplicates = [];

    private static $db = [
        'Title' => 'Varchar(255)',
        'MyJson' => 'Text',
    ];

    // has_many works, but belongs_many_many will not
    // note that we are explicitly declaring the join class "SchemaMapping" here instead of the "SomeObject" class.
    private static $has_many = [
        'SchemaMappings' => SchemaMapping::class,
    ];

    /**
     * Example iterator placeholder for belongs_many_many.
     * This is a list of arbitrary types of objects
     * @return Generator
     */
    public function SchemaObjects()
    {
        foreach ($this->SchemaMappings() as $mapping) {
            yield $mapping->Parent();
        }
    }

    // private static $casting = [];

    // private static $indexes = null;

    // private static $defaults = [];

    // private static $has_one = [];
    // private static $belongs_to = [];
    // private static $has_many = [];
    // private static $many_many = [];
    // private static $many_many_extraFields = [];
    // private static $belongs_many_many = [];

    // private static $default_sort = null;

    // private static $searchable_fields = [];

    // private static $field_labels = [];

    // // composer require goldfinch/helpers
    // private static $field_descriptions = [];
    // private static $required_fields = [];

    // private static $summary_fields = [];

    public function validate()
    {
        $result = parent::validate();

        // $result->addError('Error message');

        return $result;
    }

    public function onBeforeWrite()
    {
        // ..

        parent::onBeforeWrite();
    }

    public function onBeforeDelete()
    {
        // ..

        parent::onBeforeDelete();
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->insertAfter('Title', JsonEditorField::create('MyJson', 'My JSON Document', '{"name": "Jeremy Dorn","age": 25}', null));

        return $fields;
    }

    // public function canView($member = null)
    // {
    //     return Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    // }

    // public function canEdit($member = null)
    // {
    //     return Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    // }

    // public function canDelete($member = null)
    // {
    //     return Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    // }

    // public function canCreate($member = null, $context = [])
    // {
    //     return Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    // }
}
