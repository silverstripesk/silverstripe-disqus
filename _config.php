<?php
Object::add_extension('Page','DisqusDecorator');
Object::add_extension('SiteConfig','DisqusSiteConfig');
Object::add_extension('CMSMain', 'DisqusCMSActionDecorator');

// Add this to mysite/_config.php to suit your needs -> should be only there, 
// where list of articles is shown and comments count is presented
//Object::add_extension('BlogEntry_Controller','DisqusCountExtension');
//Object::add_extension('BlogHolder_Controller','DisqusCountExtension');

