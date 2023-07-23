<?php

namespace Goldfinch\Models;

use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;

class SEOData extends DataObject
{
    private static $singular_name = null;

    private static $plural_name = null;

    private static $table_name = 'SEOData';

    private static $cascade_deletes = [];

    private static $cascade_duplicates = [];

    private static $db = [];

    private static $casting = [];

    private static $indexes = null;

    private static $defaults = [];

    private static $has_one = [];
    private static $belongs_to = [];
    private static $has_many = [];
    private static $many_many = [];
    private static $many_many_extraFields = [];
    private static $belongs_many_many = [];

    private static $default_sort = null;

    private static $searchable_fields = [];

    private static $field_labels = [];

    private static $summary_fields = [];

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

        //

        return $fields;
    }

    public function canView($member = null)
    {
        return Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    }

    public function canEdit($member = null)
    {
        return Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    }

    public function canDelete($member = null)
    {
        return Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    }

    public function canCreate($member = null, $context = [])
    {
        return false; // Permission::check('CMS_ACCESS_Company\Website\MyAdmin', 'any', $member);
    }
}
