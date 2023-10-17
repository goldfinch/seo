<?php

namespace Goldfinch\Seo\Models;

use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Security\Permission;
use SilverStripe\Forms\CompositeField;
use UncleCheese\DisplayLogic\Forms\Wrapper;
use SilverStripe\AssetAdmin\Forms\UploadField;
use Bummzack\SortableFile\Forms\SortableUploadField;

class TwitterCard extends DataObject
{
    private static $singular_name = 'twitter card';

    private static $plural_name = 'twitter cards';

    private static $table_name = 'TwitterCard';

    private static $cascade_deletes = [];

    private static $cascade_duplicates = [];

    private static $db = [
        'Title' => 'Varchar(255)',
        'Disabled' => 'Boolean',
    ];

    // private static $casting = [];

    // private static $indexes = null;

    // private static $defaults = [];
}
