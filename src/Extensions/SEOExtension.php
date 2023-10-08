<?php

namespace Goldfinch\Seo\Extensions;

use SilverStripe\ORM\FieldType\DBHTMLText;
use Astrotomic\OpenGraph\OpenGraph;
use SilverStripe\Core\Extension;
use Spatie\SchemaOrg\Schema;

class SEOExtension extends Extension
{
    private static $casting = [
        'SchemaData' => DBHTMLText::class,
        'OpenGraph' => DBHTMLText::class,
    ];

    public function SchemaData()
    {
        // Schema
    }

    public function OpenGraph()
    {
        // OpenGraph
    }
}
