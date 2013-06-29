![Commons Logo](http://commons.gc.cuny.edu/wp-content/themes/bp-nelo/_inc/images/bg-header.gif)

# CAC Twitter Stream
This repo contains a single PHP file that has been designed as a drop-in [WordPress](http://wordpress.org) page template to provide an interface that displays and organizes any number of [Twitter's](http://twitter.com) Embedded Timelines. It contains all the PHP, HTML, CSS and Javascript needed to connect to [Twitter's oembed API](https://dev.twitter.com/docs/embedded-tweets), download the information requested and then display that information on a WordPress page. As this was designed for use on [The CUNY Academic Commons](http://commons.gc.cuny.edu) site, its structure is highly customized to work with our layout and specific Timelines. However, customizing the structure and page layout shouldn't be too difficult, which is why we are releasing it to the public in the hopes that someone will find it useful for their own implementation.

## Installation
Download or clone this repo and then copy the `cac-twitter-stream.php` file into your WordPress theme. In the admin section of your site, create a new page (named whatever you would like your URL to be, e.g. - example.edu/twitter) and set it's page template to "CAC Twitter Stream."

## Overview
The page template has been designed to connect to and embed any number of timelines from the Twitter Widgets you've created at https://twitter.com/settings/widgets. Instructions for creating and configuring these widgets can be found [here](http://theedublogger.com/2012/09/24/how-to-add-a-twitter-widget/). Customizing the template to fit your own implementation requires the editing of two sections.

The links in the sidebar that show and hide the different timelines:

```
<div class="cac-twitter-widget featured">
  <h3>Our Tweets</h3>
  <div class="cac-content">
    <ul>
      <li><a id="commons_timeline_link" rel="commons_timeline" href="#">Tweets</a></li>
      <li><a id="commons_mentions_link" rel="commons_mentions" href="#">Mentions</a></li>
      <li><a id="cuny_hashtag_link" rel="cuny_hashtag" href="#">#cuny</a></li>
      <li><a id="list_timeline_link" rel="list_timeline" href="#">cunycommons list</a></li>
    </ul>
  </div>
</div>
```

And the actual timeline sections themselves:

```
<div id="commons_timeline" class="introbox">
  <a class="twitter-timeline"
     href="[YOUR TIMELINE WIDGET URL]"
     data-widget-id="[YOUR TIMELINE WIDGET ID]"
     data-theme="<?php echo $theme ?>"
     data-link-color="<?php echo $link_color ?>"
     data-chrome="<?php echo $chrome ?>"
     data-border-color="<?php echo $border_color ?>">
   </a>
</div>
```

The `rel=""` attribute of each link needs to match the `id=""` attribute of each timeline div. The javascript included in the template will handle the showing and hiding of each div. The widget URL and ID need to be copied from the embed code generated by Twitter when you create each widget at https://twitter.com/settings/widgets. You can further customize the output on your page by adding [custom fields](http://codex.wordpress.org/Custom_Fields) on the WordPress page you created to display the template. There are four custom fields that will affect the display of the timeline(s):

1. cts_theme
2. cts_chrome
3. cts_link_color
4. cts_border_color

Twitter's documentation on [Embedded Timelines](https://dev.twitter.com/docs/embedded-timelines) outlines what each of those attributes does and their available values.

Please direct any questions or complaints to the [issues](https://github.com/cuny-academic-commons/CAC-Twitter-Stream/issues) section of this repo.

I also accept compliments.  :-)
