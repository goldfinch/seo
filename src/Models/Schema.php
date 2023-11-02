<?php

namespace Goldfinch\Seo\Models;

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Security\Permission;
use SilverStripe\Forms\CompositeField;
use Goldfinch\JSONEditor\Forms\JSONEditorField;

class Schema extends DataObject
{
    private static $singular_name = 'schema';

    private static $plural_name = 'schemas';

    private static $table_name = 'Schema';

    private static $cascade_deletes = [];

    private static $cascade_duplicates = [];

    private static $db = [
        'Title' => 'Varchar(255)',
        'Disabled' => 'Boolean',
        'JsonLD' => 'Text',
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

        $fields->removeByName([
          'Title',
          'JsonLD',
          'Disabled',
        ]);

        $fields->addFieldsToTab(
          'Root.Main',
          [
              CompositeField::create(

                TextField::create('Title', 'Title'),
                CheckboxField::create('Disabled', 'Disable this schema')->setDescription('Any page that is using this schema record will not be displayed'),
                JSONEditorField::create('JsonLD', 'Data', $this),

              ),
          ]
        );

        return $fields;
    }
}
