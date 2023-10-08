<?php

namespace Goldfinch\Seo;

use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\RequestHandler;

class Humans extends RequestHandler
{
    public function index()
    {
        $text = '
        /* TEAM */
        Your title: Your name.
        Site: email, link to a contact form, etc.
        Twitter: your Twitter username.
        Location: City, Country.

        /* THANKS */
        Name: name or url

        /* SITE */
        Last update: YYYY/MM/DD
        Standards: HTML5, CSS3,..
        Components: Modernizr, jQuery, etc.
        Software: Software used for the development
        ';

        $text = str_replace('        ', '', $text);
        $text = preg_replace(['/[ \t]*($|\R)/u', '/^\n*|(\n)\n*$|(\n{3})\n+/'], ["\n", '\1\2'], $text);

        $response = new HTTPResponse($text, 200);
        $response->addHeader("Content-Type", "text/plain; charset=\"utf-8\"");
        return $response;
    }
}
