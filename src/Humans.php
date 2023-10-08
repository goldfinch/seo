<?php

namespace Goldfinch\Seo;

use SilverStripe\Control\HTTPResponse;
use SilverStripe\Control\RequestHandler;

class Humans extends RequestHandler
{
    public function index()
    {
        $text = "";

        $response = new HTTPResponse($text, 200);
        $response->addHeader("Content-Type", "text/plain; charset=\"utf-8\"");
        return $response;
    }
}
