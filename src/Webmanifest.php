<?php

namespace Goldfinch\Seo;

use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\RequestHandler;

class Webmanifest extends RequestHandler
{
    public function index()
    {
        $text = "";

        $response = new HTTPResponse($text, 200);
        $response->addHeader("Content-Type", "application/json; charset=\"utf-8\"");
        return $response;
    }
}
