<?php

namespace Goldfinch\Seo;

use SilverStripe\Control\HTTPResponse;
use SilverStripe\SiteConfig\SiteConfig;
use Goldfinch\Seo\Models\ManifestConfig;
use SilverStripe\Control\RequestHandler;

class Webmanifest extends RequestHandler
{
    public function index()
    {
        $baseCfg = SiteConfig::current_site_config();

        $manifestCfg = ManifestConfig::current_config();

        $short_name = '';
        $name = '';
        $background_color = '';
        $theme_color = '';
        $description = '';
        $display_override = '';
        $display = '';
        $icon_vector = '';
        $icon_portable = '';
        $start_url = '';
        $id = '';
        $scope = '';

        $json = [];

        if ($manifestCfg->ShortName || $baseCfg->Title)
        {
            // $short_name = '"short_name": "' . ($manifestCfg->ShortName ?? $baseCfg->Title) . '",';
            $json['short_name'] = $manifestCfg->ShortName ?? $baseCfg->Title;
        }

        if ($manifestCfg->Description)
        {
            // $description = '"description": "' . $manifestCfg->Description . '",';
            $json['description'] = $manifestCfg->Description;
        }

        if ($manifestCfg->Name)
        {
            // $name = '"name": "' . $manifestCfg->Name . '",';
            $json['name'] = $manifestCfg->Name;
        }

        if (ss_env('APP_SEO_MANIFEST_BACKGROUND_COLOR'))
        {
            // $background_color = '"background_color": "' . ss_env('APP_SEO_MANIFEST_BACKGROUND_COLOR') . '",';
            $json['background_color'] = ss_env('APP_SEO_MANIFEST_BACKGROUND_COLOR');
        }

        if (ss_env('APP_SEO_MANIFEST_THEME_COLOR'))
        {
            // $theme_color = '"theme_color": "' . ss_env('APP_SEO_MANIFEST_THEME_COLOR') . '",';
            $json['theme_color'] = ss_env('APP_SEO_MANIFEST_THEME_COLOR');
        }

        if (ss_env('APP_SEO_MANIFEST_DISPLAY_OVERRIDE'))
        {
            // $display_override = '"display_override": ' . ss_env('APP_SEO_MANIFEST_DISPLAY_OVERRIDE') . ',';
            $json['display_override'] = json_decode(ss_env('APP_SEO_MANIFEST_DISPLAY_OVERRIDE'));
        }

        if (ss_env('APP_SEO_MANIFEST_DISPLAY'))
        {
            // $display = '"display": "' . ss_env('APP_SEO_MANIFEST_DISPLAY') . '",';
            $json['display'] = ss_env('APP_SEO_MANIFEST_DISPLAY');
        }

        if (ss_env('APP_SEO_MANIFEST_START_URL'))
        {
            // $start_url = '"start_url": "' . ss_env('APP_SEO_MANIFEST_START_URL') . '",';
            $json['start_url'] = ss_env('APP_SEO_MANIFEST_START_URL');
        }

        if (ss_env('APP_SEO_MANIFEST_ID'))
        {
            // $id = '"id": "' . ss_env('APP_SEO_MANIFEST_ID') . '",';
            $json['id'] = ss_env('APP_SEO_MANIFEST_ID');
        }

        if (ss_env('APP_SEO_MANIFEST_SCOPE'))
        {
            // $scope = '"scope": "' . ss_env('APP_SEO_MANIFEST_SCOPE') . '",';
            $json['scope'] = ss_env('APP_SEO_MANIFEST_SCOPE');
        }

        if ($manifestCfg->VectorIcon->exists())
        {
            // $icon_vector = '
            // {
            //   "src": "'.$manifestCfg->VectorIcon->getURL().'",
            //   "type": "image/svg+xml",
            //   "sizes": "512x512"
            // },
            // ';
            $json['icons'][] = [
                'src' => $manifestCfg->VectorIcon->getURL(),
                'type' => 'image/svg+xml',
                // 'sizes' => '512x512',
                'sizes' => 'any',
                'purpose' => 'any', // maskable
            ];
        }

        if ($manifestCfg->PortableImage->exists())
        {
            // $icon_portable = '
            // {
            //   "src": "'.$manifestCfg->PortableImage->Fill(192, 192)->getURL().'",
            //   "type": "image/png",
            //   "sizes": "192x192"
            // },
            // {
            //   "src": "'.$manifestCfg->PortableImage->Fill(512, 512)->getURL().'",
            //   "type": "image/png",
            //   "sizes": "512x512"
            // }
            // ';
            $json['icons'][] = [
                'src' => $manifestCfg->PortableImage->Fill(192, 192)->getURL(),
                'type' => 'image/png',
                'sizes' => '192x192',
            ];
            $json['icons'][] = [
                'src' => $manifestCfg->PortableImage->Fill(512, 512)->getURL(),
                'type' => 'image/png',
                'sizes' => '512x512',
            ];
        }

        // $shortcuts = '
        //   "shortcuts": [
        //     {
        //       "name": "How\'s weather today?",
        //       "short_name": "Today",
        //       "description": "View weather information for today",
        //       "url": "/today?source=pwa",
        //       "icons": [{ "src": "/images/today.png", "sizes": "192x192" }]
        //     },
        //     {
        //       "name": "How\'s weather tomorrow?",
        //       "short_name": "Tomorrow",
        //       "description": "View weather information for tomorrow",
        //       "url": "/tomorrow?source=pwa",
        //       "icons": [{ "src": "/images/tomorrow.png", "sizes": "192x192" }]
        //     }
        //   ],
        // ';

              // TODO
              // $json['shortcuts'][] = [
              //   'name' => 'How\'s weather today?',
              //   'short_name' => 'Today',
              //   'description' => 'View weather information for today',
              //   'url' => '/today?source=pwa',
              //   'icons' => [
              //       ['src' => '/images/today.png', 'sizes' => '192x192',],
              //   ],
              // ];
              // $json['shortcuts'][] = [
              //   'name' => 'How\'s weather tomorrow?',
              //   'short_name' => 'Tomorrow',
              //   'description' => 'View weather information for tomorrow',
              //   'url' => '/tomorrow?source=pwa',
              //   'icons' => [
              //       ['src' => '/images/tomorrow.png', 'sizes' => '192x192',],
              //   ],
              // ];

        // $screenshots = '
        //   "screenshots": [
        //     {
        //       "src": "/images/screenshot1.png",
        //       "type": "image/png",
        //       "sizes": "540x720",
        //       "form_factor": "narrow"
        //     },
        //     {
        //       "src": "/images/screenshot2.jpg",
        //       "type": "image/jpg",
        //       "sizes": "720x540",
        //       "form_factor": "wide"
        //     }
        //   ],
        // ';

              // TODO
              // $json['screenshots'][] = [
              //   'src' => '/images/screenshot1.png',
              //   'type' => 'image/png',
              //   'sizes' => '540x720',
              //   'form_factor' => 'narrow',
              // ];

              // $json['screenshots'][] = [
              //   'src' => '/images/screenshot2.png',
              //   'type' => 'image/jpg',
              //   'sizes' => '720x540',
              //   'form_factor' => 'wide',
              // ];

        // $text = '
        // {
        //   '.$short_name.'
        //   '.$name.'
        //   '.$description.'
        //   '.$start_url.'
        //   '.$id.'
        //   '.$scope.'
        //   '.$background_color.'
        //   '.$theme_color.'
        //   '.$display_override.'
        //   '.$display.'
        //   "icons": [
        //     '.$icon_vector.'
        //     '.$icon_portable.'
        //   ],
        //   '.$shortcuts.'
        //   '.$screenshots.'
        // }
        // ';

        // $text = str_replace('        ', '', $text);
        // $text = preg_replace('/^[ \t]*[\r\n]+/m', '', $text);

        // $response = new HTTPResponse($text, 200);
        $response = new HTTPResponse(json_encode($json, JSON_UNESCAPED_SLASHES), 200);
        $response->addHeader("Content-Type", "application/json; charset=\"utf-8\"");
        return $response;
    }
}
