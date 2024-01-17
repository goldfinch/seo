<?php

namespace Goldfinch\Seo\Extensions;

use SilverStripe\ORM\DataExtension;

class RobotsExtension extends DataExtension
{
    public function updateDisallowedUrls(&$urls)
    {
        $adminKey = array_search('/admin', $urls);
        $devKey = array_search('/dev', $urls);

        unset($urls[$adminKey]);
        unset($urls[$devKey]);

        // if (Robots::config()->disallow_unsearchable) {
        //     foreach (SiteTree::get()->filter('ShowInSearch', false) as $page) {
        //         $link = $page->Link();
        // 		// Don't disallow home page, no RedirectorPage with RedirectionType External
        // 		if ($link !== '/' && $page->RedirectionType != 'External') {
        //             $urls[] = $link;
        //         }
        //     }
        // }
    }
}
