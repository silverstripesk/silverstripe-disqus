<?php

/**
 * Config file
 *
 * @package silverstripe-disqus-module
 * @author Pavol Ondráš <admin_silverstripe.sk>
 * @notice SilverStripe.sk is not affiliated with the company SilverStripe Ltd.
 * @date April 2011
 */

if (!defined('PHP_VERSION_ID')) {
    $version = explode('.', PHP_VERSION);
    define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

if (PHP_VERSION_ID < 50300) {
	define('SYNCDISQUS', false);
} else {
	define('SYNCDISQUS', true);
}

Object::add_extension('Page','DisqusDecorator');
Object::add_extension('SiteConfig','DisqusSiteConfig');
Object::add_extension('CMSMain', 'DisqusCMSActionDecorator');