<?php

namespace Goldfinch\Seo\Models;

use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;

class SchemaMapping extends DataObject
{
    private static $singular_name = 'schema mapping';

    private static $plural_name = 'schemas mapping';

    private static $table_name = 'SchemaMapping';

    private static $cascade_deletes = [];

    private static $cascade_duplicates = [];

    private static $summary_fields = [
        'ParentClass' => 'Object',
        'ParentID' => 'Object ID',
    ];

    private static $db = [
        'SortOrder' => 'Int',
    ];

    private static $has_one = [
        'Parent' => DataObject::class, // Polymorphic has_one
        'Schema' => Schema::class,
    ];
}
