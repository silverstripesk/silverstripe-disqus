## Installation & setup:
### Download source code: 
```terminal
cd /path/to/your/silverstrie/installation/
git clone git://github.com/silverstripesk/silverstripe-disqus.git disqus
```

### Or install with composer:
```
composer require "silverstripesk/silverstripe-disqus:*"
```

### Activate in your config and Build
create mysite/_config/comments.yml and activate for your commentable class (or Page for all pagetypes)

```
---
Name: mysite-comments
---
BlogPost:
  extensions:
    - DisqusExtension
```


Build your ss installation - in browser visit following url:
```url
http://your-ss-installation/dev/build
```

### Setup in SiteConfig area
In backend visit SiteConfig area, tab Disqus, place required data:

*    disqus shortname (from your disqus account)
*    disqus secret key (from your disqus account)
*    disqus prefix (place there anything you want. Store diferent prefix for local and production, so thread identifier will be different and your local testing comments will not be shown on production.) By default, identifier looks like this: prefix_pageID. You can customize this for each Page (Behaviour tab, right above Comments checkbox) so you can show same comments on multiple pages (alternatively, create different forum for local copy of your site)...
*    Disqus sync time -> comments can be synced with disqus server for SS usage (e.g. generate comments for non javascript usage, search, latest comments listing...). Place time in seconds (3600 for 1 hour)
*    By default, time syncing works as standard php process within visitor's page load (so if there are some difficulties with disqus server connection, page load can be limited). You can enable background syncing, so syncing is independent from visitor's page load. Its alpha feature, tested only on linux!

### Activate in your theme
Replace 
```
$PageComments
```

with following code
```
$DisqusPageComments
```

if you are on some Page holder (list of pages, e.g. showing Children pages), you can show comments count by adding:
```
<% if ProvideComments %>$disqusCountLink<% end_if %>
```

## Usage
### If default comments module installed:
Check SS default **Enable Page Comments** Checkbox (located in Settings tab) to enable disqus comments

### Else
Comments will be shown on all pagetypes activated in comments.yml

## Collaboration
If you want to help out and make some improvements please fork this project and submit a pull request (see this guide on how to do this:  [Pull requests](http://help.github.com/pull-requests/)). 
