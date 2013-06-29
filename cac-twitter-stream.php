<?php
/*
Template Name: CAC Twitter Stream
Author: [YOUR NAME HERE]
Author URI: [YOUR URL HERE]
Author Email: [YOUR EMAIL HERE]
License: GPLv3
*/

get_header();

// get custom field settings
$custom_fields = get_post_custom();
$theme         = $custom_fields['cts_theme']        ? $custom_fields['cts_theme'][0]          : 'light';
$chrome        = $custom_fields['cts_chrome']       ? join($custom_fields['cts_chrome'], ' ') : '';
$link_color    = $custom_fields['cts_link_color']   ? $custom_fields['cts_link_color'][0]     : '#0084b4';
$border_color  = $custom_fields['cts_border_color'] ? $custom_fields['cts_border_color'][0]   : '#e8e8e8';
?>

<style type="text/css">
  iframe[id^='twitter-widget-'] { width:100%; }
</style>

<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

<script type="text/javascript">
  jQuery(function() {
    // click event for the sidebar widget boxes
    jQuery('#top-right-widget li a').click(function(e) {

      // prevent default action
      e.preventDefault();

      // stash the name of the timeline
      $stream_name = '#' + jQuery(this).attr('rel');

      // hide about all tweet divs
      jQuery('.introbox').hide();

      // show the selected div
      jQuery($stream_name).fadeIn('slow');
    });
  }); // end document.ready()
</script>

<div id="content">
  <div id="top-content">
    <div id="top-right-widget">
      <div class="cac-twitter-widget featured">
        <h3>What is Twitter?</h3>
        <div class="cac-content">
          <p>Not sure what Twitter is all about?</p>
          <ul><li><a id="about_twitter_link" rel="about_twitter" href="#">Read More &raquo;</a></li></ul>
        </div>
      </div>
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
      <div class="cac-twitter-widget featured">
        <h3>Twitter Resources</h3>
        <div class="cac-content">
          <p>Online resources that provide in-depth information about Twitter, best practices, software and other useful tools.</p>
          <p><a href="http://mashable.com/guidebook/twitter/">Mashable's Twitter Guide Book &raquo;</a></p>
          <p><a href="https://support.twitter.com/groups/31-twitter-basics/topics/104-welcome-to-twitter-support/articles/166337-the-twitter-glossary">The Twitter Glossary &raquo;</a></p>
        </div>
      </div>
      <div class="cac-twitter-widget featured">
        <h3>Download the Source</h3>
        <div class="cac-content">
          <p>We proudly offer the source used to create this page for free.</p>
          <p><a href="https://github.com/cuny-academic-commons/cac-twitter-stream">Download Now &raquo;</a></p>
        </div>
      </div>
    </div>
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
    <div id="commons_mentions" class="introbox" style="display:none">
      <a class="twitter-timeline"
         href="[YOUR TIMELINE WIDGET URL]"
         data-widget-id="[YOUR TIMELINE WIDGET ID]"
         data-theme="<?php echo $theme ?>"
         data-link-color="<?php echo $link_color ?>"
         data-chrome="<?php echo $chrome ?>"
         data-border-color="<?php echo $border_color ?>">
       </a>
    </div>
    <div id="cuny_hashtag" class="introbox" style="display:none">
      <a class="twitter-timeline"
         href="[YOUR TIMELINE WIDGET URL]"
         data-widget-id="[YOUR TIMELINE WIDGET ID]"
         data-theme="<?php echo $theme ?>"
         data-link-color="<?php echo $link_color ?>"
         data-chrome="<?php echo $chrome ?>"
         data-border-color="<?php echo $border_color ?>">
       </a>
    </div>
    <div id="list_timeline" class="introbox" style="display:none">
      <a class="twitter-timeline"
         href="[YOUR TIMELINE WIDGET URL]"
         data-widget-id="[YOUR TIMELINE WIDGET ID]"
         data-theme="<?php echo $theme ?>"
         data-link-color="<?php echo $link_color ?>"
         data-chrome="<?php echo $chrome ?>"
         data-border-color="<?php echo $border_color ?>">
       </a>      
    </div>
    <div id="about_twitter" class="introbox" style="display:none">
      <h4>What is Twitter?</h4>
      <p>Twitter is a free micro-blogging platform that describes itself as, ‘a service for friends, family, and co–workers to communicate and stay connected through the exchange of quick, frequent answers to one simple question: What are you doing?’ Though users can still answer this prompt, tweets have evolved to more than everyday experiences, taking the shape of shared links to interesting content on the web, conversations around hot topics (using hashtags), photos, videos, music, and, most importantly, real-time accounts from people. All messages or ‘tweets’ are 140 characters or less.</p>
      <h4>How do I sign up?</h4>
      <p>Visit <a href="http://twitter.com">twitter.com</a> with a valid email address, select a username and password, accept the terms and conditions, and boom – you’re in! After you upload a photo and write your one line bio you’re ready to start tweeting and following other users.</p>
      <h4>The Basics:</h4>
      <br />
      <h5>Tweets</h5>
      <p>Twitter posts can be about anything and include links to web pages, videos, photos, or other resources. If your profile is set to public, your tweets will be available for anyone to see. All tweets are limited to 140-characters.</p>
      <p><i>If you are posting a URL, consider using a shortening service to condense long URLs into a smaller format. This will allow you to minimize the character count used by the link. For more information on how to shorten a URL, <a href="http://help.commons.gc.cuny.edu/2010/07/20/how-to-shorten-a-url/">click here</a>.</i></p>
      <h5>Retweeting (RT)</h5>
      <p>Retweeting is when you share interesting tweets and relevant content from other Twitter user. To give credit to the original person, you put ‘RT’ plus the originator’s username at the beginning of the tweet. Your tweets are more likely to be retweeted if you leave enough characters to include “RT @yourusername.”</p>
      <p><i>Just as you would cite a source, retweeting gives credit where credit is due.</i></p>
      <h5>Reply</h5>
      <p>Using the ‘reply’ button will allow you to respond or direct a tweet to another user. Similar to the ‘mention’ feature on the Commons, placing the @ symbol in front of a username will create a link to the user’s profile and notify them of your tweet.</p>
      <h5>Follow</h5>
      <p>The public nature of Twitter encourages users to follow each other. When you follow someone, their status updates appear in the Twitter stream on your homepage. You can follow friends, colleagues, family, or anyone who has a Twitter profile. If a user protect their tweets, your follow request must be approved by the user.</p>
      <p><i>Use <a href="search.twitter.com">search.twitter.com</a> to find others to follow on twitter. You can search by interest, name, and location. You can also explore who other users are following to build your Twitter network.</i></p>
      <p><i>We encourage you to find and connect with other CUNY Twitterers on The Commons by exploring the <a href="http://twitter.com/cunycommons/cunycommons">@cunycommons/cunycommons</a> list.</i></p>
      <h5>Lists</h5>
      <p>Twitter users can use lists to organize other users into groups. Users can also search through lists for new users to follow. You can create a list of people you want to recommend to others, family, co-workers, experts of a particular subject, and so forth. For example, the <a href="http://twitter.com/cunycommons/cunycommons">@cunycommons/cunycommons</a> list includes over 250 Twitterers on The Commons.</p>
      <p><i>For more information about how to create or follow a twitter list, please see the <a href="http://mashable.com/2009/11/02/twitter-lists-guide/">following article on Mashable.</a></i></p>
      <h5>Hashtags</h5>
      <p>Just as entering search terms in a search engine can yield results via the World Wide Web; hashtags ( also referred to as “tags”) allow Twitterers to access real-time tweets around a particular person, place, or thing. Hashtags are used to categorize a topic or keyword with a hash symbol (‘#’) at the start. Using the # symbol creates a hyperlink that, when selected, displays all public tweets that include the tag, helping organize it.</p>
      <p><i>Twitter hashtags <a href="https://twitter.com//search?q=%23followfriday">#followfriday</a> help spread information on Twitter. The hashtag is a favorite tool of conferences, events, and organizations (i.e.- <a href="https://twitter.com//search?q=%23cuny">#cuny</a>). The top five hashtags of the @cunycommons are: <a href="https://twitter.com//search?q=%23cunyevents">#cunyevents</a>, <a href="https://twitter.com//search?q=%23cunyit">#cunyit</a>, <a href="https://twitter.com//search?q=%23helpwanted">#helpwanted</a>, <a href="https://twitter.com//search?q=%23cfp">#cfp</a>, and <a href="https://twitter.com//search?q=%23cunydhi">#cunydhi</a>.</i></p>
      <p><i>While hashtags allow you connect and share information quickly and easily, a little can go a long way. In other words, use hashtags sparingly.</i></p>
    </div>
  </div>
</div>

<?php get_footer(); ?>

