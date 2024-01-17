<?php

namespace Goldfinch\Seo;

use SilverStripe\Control\HTTPResponse;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Control\RequestHandler;

class Humans extends RequestHandler
{
    public function index()
    {
        $cfg = SiteConfig::current_site_config();

        $text = '';

        $text .= '
        /* TEAM */

        ';

        if (ss_env('APP_HUMANS_CREATOR'))
        {
            $text .= '
            Creator: ' . ss_env('APP_HUMANS_CREATOR') . '
            ';
        }

        if (ss_env('APP_HUMANS_CREATOR'))
        {
            $text .= '
            Site: ' . ss_env('APP_HUMANS_SITE') . '
            ';
        }

        if (ss_env('APP_HUMANS_CREATOR'))
        {
            $text .= '
            Location: ' . ss_env('APP_HUMANS_LOCATION') . '
            ';
        }

        $text .= '

        /* THANKS */

        /* SITE */
        ';

        if (ss_env('APP_HUMANS_CREATOR'))
        {
            $text .= '
            Standards: ' . ss_env('APP_HUMANS_STANDARDS') . '
            ';
        }

        $text = str_replace('        ', '', $text);
        $text = preg_replace(['/[ \t]*($|\R)/u', '/^\n*|(\n)\n*$|(\n{3})\n+/'], ["\n", '\1\2'], $text);

        $response = new HTTPResponse($text, 200);
        $response->addHeader("Content-Type", "text/plain; charset=\"utf-8\"");
        return $response;
    }
}
