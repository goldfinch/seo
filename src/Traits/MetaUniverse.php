<?php

namespace Goldfinch\Seo\Traits;

use BadMethodCallException;
use Spatie\SchemaOrg\Schema;
use SilverStripe\Control\Director;
use SilverStripe\Core\Environment;
use Astrotomic\OpenGraph\OpenGraph;
use SilverStripe\Security\Permission;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Security\SecurityToken;
use SilverStripe\ORM\FieldType\DBHTMLText;

trait MetaUniverse
{
    public $universeClass = 'Goldfinch\Seo\Traits\MetaUniverse';

    public function GenerateMeta()
    {
        $output = DBHTMLText::create();

        $html =
            $this->metaBase() .

            $this->metaTitle() .

            $this->metaContentTypeCharset() .
            $this->metaCompatible() .
            $this->metaDnsPrefetchControl() .
            $this->metaRefresh() .
            $this->metaDates() .

            $this->metaViewport() .
            $this->metaReferrer() .
            $this->metaLang() .
            $this->metaCSRF() .
            $this->metaRobots() .
            $this->metaApplicationName() .
            $this->metaIdentifierURL() .
            $this->metaVerifications() .
            $this->metaTheme() .
            $this->metaRating() .

            $this->metaNewsKeywords() . // only for article
            $this->metaGeo() . // perhaps contact page only
            $this->metaDescription() .
            $this->metaCategory() . // for sites catalogs

            $this->metaMobile() .
            $this->metaFormatDetection() .
            $this->metaAppleMobile() .
            $this->metaWindowsPhone() .
            $this->metaXCMS() .
            $this->metaAuthor() .
            $this->metaCopyright() .

            $this->OpenGraph() .

            $this->linkHome() .
            $this->linkCanonical() .
            $this->linkShortlink() .
            $this->linkSearch() .
            $this->linkPreconnect() .
            $this->linkAmphtml() .
            $this->linkImageSrc() .
            $this->linkAppleMobile() .
            $this->linkIcons() .
            $this->linkHumans() .

            PHP_EOL .
            $this->SchemaData()
        ;

        $html = preg_replace(['/\s{2,}/', '/\n/'], PHP_EOL, $html);
        $html = preg_replace('/^[ \t]*[\r\n]+/m', '', $html);

        $tags = explode(PHP_EOL, $html);

        if ($space = Environment::getEnv('APP_META_SOURCESPACE'))
        {
            $spacing = '';

            for ($i = 0; $space > $i; $i++)
            {
                $spacing .= ' ';
            }
        }
        else
        {
            // 4 space by default
            $spacing = '    ';
        }

        $html = '';

        foreach ($tags as $key => $tag)
        {
            if ($key !== 0)
            {
                $html .= $spacing . $tag . PHP_EOL;
            }
            else
            {
                $html .= $tag . PHP_EOL;
            }
        }

        $output->setValue($html);

        return $output;
    }

    public function metaBase()
    {
        if (!ss_config($this->universeClass, 'headrules', 'base'))
        {
            return;
        }

        // <!--[if lte IE 6]></base><![endif]-->
        $output = '
        <base href="'. Director::absoluteBaseURL() .'">
        ';

        return $output;
    }

    /**
     * Category of the page (for sites catalogs)
     */
    public function metaCategory()
    {
        if (!ss_config($this->universeClass, 'headrules', 'category'))
        {
            return;
        }

        $output = '
        <meta name="category" content="">
        ';

        return $output;
    }

    /**
     * To enable/disable DNS prefetching
     */
    public function metaDnsPrefetchControl()
    {
        if (!ss_config($this->universeClass, 'headrules', 'dns-prefetch-control'))
        {
            return;
        }

        $output = '
        <meta http-equiv="x-dns-prefetch-control" content="on">
        ';

        return $output;
    }

    public function metaCompatible()
    {
        if (!ss_config($this->universeClass, 'headrules', 'compatible'))
        {
            return;
        }
        // imagetoolbar - This is an IE specific meta, In some older versions of Internet Explorer, when an image is hovered, an image toolbar appears. content=no used to disable the image toolbar.
        // "IE=edge" indicates that the webpage should be displayed in the latest version of IE available
        // <meta http-equiv="Page-Enter" content="RevealTrans(Duration=2.0,Transition=2)">
        // <meta http-equiv="Page-Exit" content="RevealTrans(Duration=3.0,Transition=12)">
        // <meta name="Classification" content="Business">
        // <meta name="HandheldFriendly" content="True">
        // <meta name="MobileOptimized" content="320">
        $output = '
        <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
        <meta http-equiv="imagetoolbar" content="no">
        ';

        return $output;
    }

    public function metaLang()
    {
        if (!ss_config($this->universeClass, 'headrules', 'language'))
        {
            return;
        }
        // <meta http-equiv="content-language" content="language–Country"> - Enables language specification, enabling search engines to accurately categorise the document into language and country. The language is the main language code, and the country is the country where the dialect of the language is more specific, such as en-US versus en-GB
        // <meta http-equiv="content-script-type" content="language“> - The default script language for the script element is javascript. This informs the browser which type of scripting language you are using by default.
        // <meta name="google" content="notranslate">
        // <meta name="locale" content="en-NZ">
        $output = '
        <meta name="language" content="en">
        ';

        return $output;
    }

    /**
     * Only for article
     */
    public function metaNewsKeywords()
    {
        if (!ss_config($this->universeClass, 'headrules', 'news_keywords'))
        {
            return;
        }

        $output = '
        <meta name="news_keywords" content="">
        ';

        return $output;
    }

    /**
     * Include the date and time that the page was created.
     * The content is the date and time the page was last modified.
     */
    public function metaDates()
    {
        if (!ss_config($this->universeClass, 'headrules', 'dates'))
        {
            return;
        }

        $output = '
        <meta http-equiv="date" content="date">
        <meta http-equiv="last-modified" content="date">
        ';

        return $output;
    }

    /**
     * URL of your site. This tag is not obligatory. If you use it, put the same value in all the pages of your site.
     */
    public function metaIdentifierURL()
    {
        if (!ss_config($this->universeClass, 'headrules', 'identifier-URL'))
        {
            return;
        }

        $output = '
        <meta name="identifier-URL" content="' . Director::absoluteBaseURL() . '">
        ';

        return $output;
    }

    /**
     * (Here IN represents the country and DL represents the state. It is good to define geo details clearly)
     * (Here you can put the City name which you wanted to represent)
     * (put the city coordinates)
     * (add the location coordinates here)
     *
     * There is some speculation that it might possibly be used for Google local search optimization, which falls outside of regular organic search SEO.  And some SEOs simply add it for a “cover all bases" approach to optimization.   And other search engines may also use the geo meta tags, even if Google does not.
     */
    public function metaGeo()
    {
        if (!ss_config($this->universeClass, 'headrules', 'geo'))
        {
            return;
        }

        // 00.000000;00.000000
        // IN-DL
        // Delhi

        $output = '';

        if (ss_env('APP_SEO_GEO_POSITION'))
        {
            $output .= '
            <meta name="geo.position" content="' . ss_env('APP_SEO_GEO_POSITION') . '">
            ';
        }

        if (ss_env('APP_SEO_GEO_REGION'))
        {
            $output .= '
            <meta name="geo.region" content="' . ss_env('APP_SEO_GEO_REGION') . '">
            ';
        }

        if (ss_env('APP_SEO_GEO_PLACENAME'))
        {
            $output .= '
            <meta name="geo.placename" content="' . ss_env('APP_SEO_GEO_PLACENAME') . '">
            ';
        }

        return $output;
    }

    /**
     * - This meta tag is often used to let the younger web-surfers know the content is appropriate. If you use this tag the wrong way (call an adult website safe for kids is bad!) you will get banned for life.
     * - general / mature / restricted / adult / 14 years / safe for kids
     */
    public function metaRating()
    {
        if (!ss_config($this->universeClass, 'headrules', 'rating'))
        {
            return;
        }
        // <meta http-equiv="pics-label" content="labellist"> - The Platform for Internet Content Selection (PICS) is a standard for labelling online content: basically online content rating.
        // <meta name="rating" content="RTA-5042-1996-1400-1577-RTA">
        // general, mature, restricted, adult, 14 years, safe for kids

        $output = '';

        if (ss_env('APP_SEO_RATING'))
        {
            $output .= '
            <meta name="rating" content="' . ss_env('APP_SEO_RATING') . '">
            ';
        }

        return $output;
    }

    public function metaMobile()
    {
        if (!ss_config($this->universeClass, 'headrules', 'mobile-web-app-capable'))
        {
            return;
        }
        // Since Chrome M31, you can set up your web app to have an application shortcut icon added to a device's homescreen, and have the app launch in full-screen "app mode" using Chrome for Android’s "Add to homescreen" menu item.
        $output = '
        <meta name="mobile-web-app-capable" content="yes">
        ';

        return $output;
    }

    public function metaRefresh()
    {
        if (!ss_config($this->universeClass, 'headrules', 'refresh'))
        {
            return;
        }
        // The refresh meta tag is used to refresh a document. It’s useful if your page uses dynamic content that changes constantly. In the example below, your page will be automatically refreshed every 30 seconds:
        // Redirect page after 3 seconds: <meta http-equiv="refresh" content="3;url=https://www.mozilla.org">
        $output = '
        <meta http-equiv="refresh" content="30">
        ';

        return $output;
    }

    /**
     * Apple Specific Meta Tags
     *
     * apple-mobile-web-app-title - On iOS, you can specify a web application title for the launch icon. By default, the <title> tag is used. To set a different title, add this meta tag to the webpage
     *
     * apple-mobile-web-app-capable : On iOS, as part of optimising your web application, have it use the standalone mode to look more like a native application. When you use this standalone mode, Safari is not used to display the web content — specifically, there is no browser URL text field at the top of the screen or button bar at the bottom of the screen. Only a status bar appears at the top of the screen.
     *
     * apple-mobile-web-app-status-bar-style : <!-- black | black-translucent | default --> If your web application displays in standalone mode like that of a native application, you can minimise the status bar that is displayed at the top of the screen on iOS.
     *
     */
    public function metaAppleMobile()
    {
        if (!ss_config($this->universeClass, 'headrules', 'apple'))
        {
            return;
        }

        $output = '
        <meta name="apple-mobile-web-app-title" content="">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        ';

        return $output;
    }

    /**
     * When running in a browser on a mobile phone, this meta determines whether or not telephone numbers in the HTML content will appear as hypertext links. The user can click a link with a telephone number to initiate a phone call to that phone number.
     */
    public function metaFormatDetection()
    {
        if (!ss_config($this->universeClass, 'headrules', 'format-detection'))
        {
            return;
        }

        $output = '
        <meta name="format-detection" content="telephone=no">
        ';

        return $output;
    }

    public function metaWindowsPhone()
    {
        if (!ss_config($this->universeClass, 'headrules', 'msapplication'))
        {
            return;
        }

        // notification : frequency=30; polling-uri=;
        // window : width=1024;height=768
        // badge : frequency=30; polling-uri=https://demo.com/q/sitemap.xml
        // config : ../browserconfig.xml
        // task : name=Search;action-uri=https://demo.com/search.ico

        $output = '
        <meta name="msapplication-starturl" content="/">
        <meta name="msapplication-navbutton-color" content="">
        <meta name="msapplication-TileColor" content="">
        <meta name="msapplication-TileImage" content="">
        <meta name="msapplication-tooltip" content="">
        <meta name="msapplication-task" content="">
        <meta name="msapplication-config" content="">
        <meta name="msapplication-badge" content="">
        <meta name="msapplication-square70x70logo" content="">
        <meta name="msapplication-square150x150logo" content="">
        <meta name="msapplication-square310x310logo" content="">
        <meta name="msapplication-wide310x150logo" content="">
        <meta name="msapplication-tap-highlight" content="no" />
        <meta name="msapplication-notification" content="">
        <meta name="msapplication-window" content="">
        <meta name="msapplication-allowDomainMetaTags" content="true">
        <meta name="msapplication-allowDomainApiCalls" content="true">
        ';

        return $output;
    }

    public function metaAuthor()
    {
        if (!ss_config($this->universeClass, 'headrules', 'author'))
        {
            return;
        }
        $output = '';

        if (ss_env('APP_SEO_AUTHOR'))
        {
            $output = '
            <meta name="author" content="' . ss_env('APP_SEO_AUTHOR') . '">
            ';
        }

        return $output;
    }

    public function metaCopyright()
    {
        if (!ss_config($this->universeClass, 'headrules', 'copyright'))
        {
            return;
        }

        $output = '';

        if (ss_env('APP_SEO_COPYRIGHT'))
        {
            $output = '
            <meta name="copyright" content="' . ss_env('APP_SEO_COPYRIGHT') . '">
            ';
        }

        return $output;
    }

    /**
     * https://developers.google.com/search/docs/crawling-indexing/robots-meta-tag#directives
     */
    public function metaRobots()
    {
        if (!ss_config($this->universeClass, 'headrules', 'robots'))
        {
            return;
        }

        // <meta name="robots" content="index, follow">
        // <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
        // <meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
        $output = '
        <meta name="robots" content="' . ss_env('APP_SEO_ROBOTS_BASE') . '">
        ';

        return $output;
    }

    public function metaReferrer()
    {
        if (!ss_config($this->universeClass, 'headrules', 'referrer'))
        {
            return;
        }

        // <meta name="referrer" content="origin-when-cross-origin">
        $output = '
        <meta name="referrer" content="no-referrer-when-downgrade">
        ';

        return $output;
    }

    public function metaApplicationName()
    {
        if (!ss_config($this->universeClass, 'headrules', 'application-name'))
        {
            return;
        }

        $cfg = SiteConfig::current_site_config();

        $output = '
        <meta name="application-name" content="' . $cfg->Title . '">
        ';

        return $output;
    }

    public function metaContentTypeCharset()
    {
        if (!ss_config($this->universeClass, 'headrules', 'content-type-charset'))
        {
            return;
        }

        $output = '
        <meta charset="utf-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        ';

        return $output;
    }

    /**
     * - Google typically shows 55-64 characters (keep it under 60).
     * - Keep important keywords within the first 8 words
     */
    public function metaTitle()
    {
        if (!ss_config($this->universeClass, 'headrules', 'title'))
        {
            return;
        }

        $cfg = SiteConfig::current_site_config();

        if ($this->MetaTitle)
        {
            $title = $this->MetaTitle;
        }
        else
        {
            $title = $this->Title . ' - ' . $cfg->Title;
        }

        $output = '
        <title>' . $title . '</title>
        ';

        return $output;
    }

    public function metaVerifications()
    {
        if (!ss_config($this->universeClass, 'headrules', 'verifications'))
        {
            return;
        }

        $output = '';

        if (ss_env('APP_SEO_BING_CONSOLE'))
        {
            $output .= '
            <meta name="google-site-verification" content="' . ss_env('APP_SEO_GOOGLE_CONSOLE') . '">
            ';
        }

        if (ss_env('APP_SEO_BING_CONSOLE'))
        {
            $output .= '
            <meta name="msvalidate.01" content="' . ss_env('APP_SEO_BING_CONSOLE') . '">
            ';
        }

        if (ss_env('APP_SEO_PINTEREST'))
        {
            $output .= '
            <meta name="p:domain_verify" content="' . ss_env('APP_SEO_PINTEREST') . '">
            ';
        }

        return $output;
    }

    public function metaCSRF()
    {
        if (!ss_config($this->universeClass, 'headrules', 'csrf'))
        {
            return;
        }

        $output = '
        <meta name="csrf-param" content="authenticity_token">
        <meta name="csrf-token" content="' . SecurityToken::getSecurityID() . '">
        ';

        return $output;
    }

    /**
     * - Google typically shows max. 165 characters (keep it under 160).
     * ~ 155
     */
    public function metaDescription()
    {
        if (!ss_config($this->universeClass, 'headrules', 'description'))
        {
            return;
        }

        $output = '
        <meta name="description" content="' . strip_tags($this->MetaDescription ?? '') . '">
        ';

        return $output;
    }

    public function metaTheme()
    {
        if (!ss_config($this->universeClass, 'headrules', 'theme'))
        {
            return;
        }

        // color-scheme : Status & Address Bar
        // theme-color : Chrome, Firefox OS and Opera
        // <meta name="theme-color" media="(prefers-color-scheme: light)" content="#F6F7F8">
        // <meta name="theme-color" media="(prefers-color-scheme: dark)" content="#111317">
        $output = '
        <meta name="color-scheme" content="' . ss_env('APP_COLOR_SCHEME') . '">
        <meta name="theme-color" content="' . ss_env('APP_THEME_COLOR') . '">
        ';

        return $output;
    }

    /**
     * #width : Logical width of the viewport, in pixels. The special device-width value indicates that the viewport width should be the screen width of the device.
     * #height : Logical height of the viewport, in pixels. The special device-width value indicates that the viewport height should be the screen width of the device.
     * #user-scalable : Indicates whether the user can zoom in and out of the viewport, scaling the display of a web page. Possible values are yes of no.
     * #initial-scale : Sets the first scaling or zoom out factor (or multiplier) and can be used to view a web page. A value of 1.0 shows an unscaled web document.
     * #maximum-scale : Sets a maximum limit for the user to scale and zoom a webpage. Values are numeric and can range from 0.25 to 10.0.
     * #minimum-scale : Limits the user to a minimum for enlarging or zooming in on a webpage. Values are numeric and can be from 0.25 to 10.0.
     *
    * Prevent shrink: 'shrink-to-fit=no'
     *
     */

    public function metaViewport()
    {
        if (!ss_config($this->universeClass, 'headrules', 'viewport'))
        {
            return;
        }

        $content = [
            'width=device-width',
            'initial-scale=1.0',
            'viewport-fit=cover',
            'user-scalable=yes',
        ];

        $output = '
        <meta name="viewport" content="' . implode(', ', $content) . '">
        ';

        return $output;
    }

    // vendor/silverstripe/cms/code/Model/SiteTree.php # MetaComponents
    public function metaXCMS()
    {
        if (!ss_config($this->universeClass, 'headrules', 'x-cms'))
        {
            return;
        }

        $output = '';

        if (Permission::check('CMS_ACCESS_CMSMain') && $this->ID > 0)
        {
            $output = '
            <meta name="x-page-id" content="' . $this->ID . '">
            ';

            try
            {
                $output .= '
                <meta name="x-cms-edit-link" content="' . $this->CMSEditLink() . '">
                ';
            } catch (BadMethodCallException $e) {}

            try
            {
                $output .= '
                <meta name="x-cms-logout-link" content="' . $this->LogoutURL() . '">
                ';
            } catch (BadMethodCallException $e) {}
        }

        return $output;
    }

    public function linkSearch()
    {
        if (!ss_config($this->universeClass, 'headrules', 'search'))
        {
            return;
        }

        // href : /__static__/semrush-opensearch.xml

        $output = '
        <link rel="search" type="application/opensearchdescription+xml" href="" title="">
        ';

        return $output;
    }

    public function linkImageSrc()
    {
        if (!ss_config($this->universeClass, 'headrules', 'image_src'))
        {
            return;
        }

        $output = '
        <link rel="image_src" href="" type="image/jpeg">
        ';

        return $output;
    }

    public function linkHumans()
    {
        if (!ss_config($this->universeClass, 'headrules', 'humans'))
        {
            return;
        }

        $output = '
        <link rel="author" href="/humans.txt" type="text/plain">
        ';

        return $output;
    }

    public function linkAppleMobile()
    {
        if (!ss_config($this->universeClass, 'headrules', 'apple-touch'))
        {
            return;
        }

        $output = '
        <link rel="apple-touch-startup-image" href="">
        ';

        return $output;
    }

    public function linkHome()
    {
        if (!ss_config($this->universeClass, 'headrules', 'home'))
        {
            return;
        }

        $output = '
        <link rel="home" href="'. Director::absoluteBaseURL() .'">
        ';

        return $output;
    }

    public function linkShortlink()
    {
        if (!ss_config($this->universeClass, 'headrules', 'shortlink'))
        {
            return;
        }

        $output = '
        <link rel="shortlink" href="'. Director::absoluteBaseURL() .'">
        ';

        return $output;
    }

    public function linkPreconnect()
    {
        if (!ss_config($this->universeClass, 'headrules', 'preconnect'))
        {
            return;
        }

        $defaults = [
            'https://www.google.com',
            'https://www.googletagmanager.com',
            'https://www.google-analytics.com',
            'https://www.gstatic.com',
            'https://fonts.gstatic.com',
            'https://maps.gstatic.com',
            'https://maps.googleapis.com',
            // 'https://googleadservices.com',
            // 'https://www.facebook.com',
            // 'https://connect.facebook.net',
            // 'https://ad.doubleclick.net',
        ];

        // https://cdn.example.com

        // <link rel="preconnect" href="//i.demo.com" crossorigin="anonymous" / crossorigin>
        $output = '
        <link rel="preconnect" src="">
        <link rel="dns-prefetch" src="">
        ';

        return $output;
    }

    public function linkCanonical()
    {
        if (!ss_config($this->universeClass, 'headrules', 'canonical'))
        {
            return;
        }

        $output = '
        <link rel="canonical" href="' . Director::absoluteBaseURL() . '">
        ';

        return $output;
    }

    public function linkAmphtml()
    {
        if (!ss_config($this->universeClass, 'headrules', 'amphtml'))
        {
            return;
        }

        $output = '
        <link rel="amphtml" href="">
        ';

        return $output;
    }

    /**
     * - mask-icon : Safari 9 Pinned tabs. Pinned Sites allow your users to keep their favorite websites open, running, and easily accessible. You can set the icon that the user sees when they pin your site by providing a vector image.
     *
     *
     * - 512x512 (png)
     * - 192x192 (png)
     * - 512x512 (svg)
     * - 32x32 (ico)
     * - apple-touch-icon : 180×180
     * -
     */
    public function linkIcons()
    {
        if (!ss_config($this->universeClass, 'headrules', 'icon'))
        {
            return;
        }

        // <link rel="shortcut icon" href="https://cdn.evbstatic.com/s3-bs/favicons/favicon.ico">
        $output = '
        <link rel="mask-icon" href="/icon.svg" color="' . ss_env('APP_THEME_COLOR') . '">
        <link rel="icon" href="/favicon.ico" sizes="32x32">
        <link rel="icon" href="/icon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <link rel="manifest" href="/manifest.webmanifest">
        ';

        return $output;
    }
}
