<?php
/*
Template Name: CAC Twitter Stream
Author: Dominic Giglio
Author URI: humanshell.net
Author Email: humanshell@gmail.com
License: GPLv3
*/

/**
 * The CAC_Twitter_Stream class is responsible for consolidating and updating all streams
 * and storing them in the DB as transients to help control rate limits
 */
class CAC_Twitter_Stream {

  // array of stream names and corresponding urls
  public $urls = array(
    'cuny_hashtag'     => 'http://search.twitter.com/search.json?q=%23cuny',
    'list_members'     => 'http://api.twitter.com/1/lists/members.json?slug=cunycommons&owner_screen_name=cunycommons',
    'list_timeline'    => 'http://api.twitter.com/1/lists/statuses.json?slug=cunycommons&owner_screen_name=cunycommons&include_rts=true',
    'commons_timeline' => 'http://api.twitter.com/1/statuses/user_timeline.json?screen_name=cunycommons&include_rts=true',
    'commons_mentions' => 'http://search.twitter.com/search.json?q=@cunycommons',
    'list_subscribers' => 'http://api.twitter.com/1/lists/subscribers.json?slug=cunycommons&owner_screen_name=cunycommons'
  );

  // array of streams returned from twitter's api
  public $streams = array();

  /**
   * fetch_stream()
   *
   * This method is responsible for getting a requested stream's transient entry from the options table.
   * If an entry does not exist, it will call parse_response() to get the stream from twitter's api.
   * This method returns nothing. It simply ensures the $streams[] array has been properly populated.
   *
   * @uses wp_remote_get() Provides graceful fallbacks for HTTP request methods
   * @param string $name The name of the stream in the $urls[] and $streams[] arrays
   */
  public function fetch_stream( $name ) {
    if ( ! $this->streams[$name] = maybe_unserialize( get_transient( "cac_twitter_$name" ) ) ) {
      $response = wp_remote_get( $this->urls[$name] );
      if ( $body = $response['body'] ) {
        $this->parse_response( $name, json_decode( $body ) );
      }
    }
  }

  /**
   * parse_response()
   *
   * This method's sole responsibility is to parse the info we want from twitter's response,
   * populate the $streams[] array with that info and then store it in a transient to improve
   * page load times and avoid twitter's rate limiting.
   *
   * @param string $name The name used as the key in the $streams[] array
   * @param object $res The json decoded response from twitter's api
   */
  private function parse_response( $name, $res ) {

    switch ( $name ) {
      case 'commons_timeline': case 'list_timeline':

        // loop over each returned tweet to parse out only the info we need
        foreach ( $res as $tweet ) {

          // store each tweet as an array in the $streams[] array
          $this->streams[$name][] = array(
            'name'          => $tweet->user->name,
            'text'          => $this->twitterize( $tweet->text ),
            'source'        => $tweet->source,
            'created_at'    => $tweet->created_at,
            'screen_name'   => $tweet->user->screen_name,
            'profile_image' => $tweet->user->profile_image_url
          );

        }
        break;

      case 'cuny_hashtag': case 'commons_mentions':

        // loop over each returned tweet to parse out only the info we need
        foreach ( $res->results as $tweet ) {

          // store them in the $streams[] array
          $this->streams[$name][] = array(
            'name'          => $tweet->from_user_name,
            'text'          => $this->twitterize( $tweet->text ),
            'source'        => htmlspecialchars_decode( $tweet->source ),
            'created_at'    => $tweet->created_at,
            'screen_name'   => $tweet->from_user,
            'profile_image' => $tweet->profile_image_url
          );

        }
        break;

      case 'list_members': case 'list_subscribers':

        // loop over each returned user to parse out only the info we need
        foreach ( $res->users as $user ) {

          // store each user as an array in the $streams[] array
          $this->streams[$name][] = array(
            'name'          => $user->name,
            'text'          => $this->twitterize( $user->description ),
            'location'      => $user->location,
            'screen_name'   => $user->screen_name,
            'profile_image' => $user->profile_image_url
          );

        }
        break;
    }

    // store the parsed data in a transient so we don't need to do this on each request
    set_transient( "cac_twitter_$name", $this->streams[$name], 60*60*2 ); // 2 hours
  }

  /**
   * twitterize()
   *
   * This little function "twitterizes" the hashtags and @mentions returned from twitter's api, turning them into links.
   * from Boone Gorges: https://github.com/boonebgorges/Hard-G/blob/master/wp-content/themes/boones-photoblog/functions.php
   *
   * @param string $text The text containing @mentions and hastags to be converted
   * @return string $text The text with @mentions and hastags converted
   */
  private function twitterize( $text ) {
    $text = preg_replace("/[@]+([A-Za-z0-9-_]+)/", "<a href=\"http://twitter.com/\\1\" target=\"_blank\">\\0</a>", $text );
    $text = preg_replace("/[#]+([A-Za-z0-9-_]+)/", "<a href=\"http://twitter.com/search?q=%23\\1\" target=\"_blank\">\\0</a>", $text );
    $text = preg_replace("/(http:\/\/t\.co\/)+\w+/", "<a href=\"\\0\" target=\"_blank\">\\0</a>", $text );
    return $text;
  }

  /**
   * render_html()
   *
   * This method creates and returns a string containing the full html structure
   * for every tweet in the stream requested, ready for output directly to the page.
   *
   * @param string $name The name of the stream requested by the user
   * @return string $output A single string with the full html structure to be displayed
   */
  public function render_html( $name ) {
    $output = "";

    foreach ( $this->streams[$name] as $stream ) {
      $output .= "<li class='tweet'>
                    <img src='{$stream['profile_image']}' alt='{$stream['name']}' width='48' height='48' />
                    <a class='username' href='http://twitter.com/{$stream['screen_name']}'>{$stream['name']}</a>
                    <p>{$stream['text']}<br />";

      if ( $name == 'list_members' || $name == 'list_subscribers' )
        $output .= "<span>from: {$stream['location']}</span></p>";
      else
        $output .= "<span>via: {$stream['source']}</span></p>";

      $output .= '</li>';
    }

    return $output;
  }

} // end class CAC_Twitter_Stream

/**
 * This page will be accessed in one of two ways:
 *
 * 1.) when first visiting the page
 * 2.) when loading new tweets via an ajax call
 *
 * When an ajax request comes in, we just need to get the tweets the user is looking for, so
 * we return them and then bail. On initial page load we want to make sure the commons_timeline
 * has been fetched so the page isn't blank.
 */
$cac_twitter_stream = new CAC_Twitter_Stream();

if ( isset( $_REQUEST['stream'] ) && $_REQUEST['stream'] ) { // this is ajax
  $cac_twitter_stream->fetch_stream( $_REQUEST['stream'] );
  echo $cac_twitter_stream->render_html( $_REQUEST['stream'] );
  return false;
} else { // this is initial page load
  $cac_twitter_stream->fetch_stream( 'commons_timeline' );
}

get_header(); ?>

<style type="text/css">
  .introbox { height: inherit; }
  .introbox ul { margin: 0 40px; }
  .tweet { margin: 10px; padding: 10px; border-top: solid #eee 1px; min-height: 85px; }
  .tweet a { text-decoration: none; }
  .tweet img { float: left; margin: 10px 25px 50px 10px; display: block; border: solid #f3f3f3 2px; box-shadow: 0px 0px 5px #444; }
  .username { font-weight: bold; }
  .cac-twitter-widget ul { margin-left: 10px; }
  .cac-twitter-widget a { text-decoration: none; }
</style>

<script type="text/javascript">
  jQuery(function() {
    // click event for the sidebar widget boxes
    jQuery('#top-right-widget li a').click(function(e) {

      // prevent default action
      e.preventDefault();

      // stash the name of the stream the user wants
      $stream_name = jQuery(this).attr('rel');

      // hide all the headers and tweets
      jQuery('.introbox ul, .introbox h4, #cac_about_twitter').hide();

      if (e.target.id == 'cac_about_twitter_link') {
        // show the about twitter div with correct h4
        jQuery('#cac_about_twitter h4').show();
        jQuery('#cac_about_twitter').fadeIn('slow');
      } else {
        // load the new stream via ajax and show the correct h4
        jQuery('#' + $stream_name + '_stream').prev().show();
        jQuery('#' + $stream_name + '_stream').load('/twitter/', {stream:$stream_name}).fadeIn('slow');
      }
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
          <ul><li><a id="cac_about_twitter_link" href="#">Read More &raquo;</a></li></ul>
        </div>
      </div>
      <div class="cac-twitter-widget featured">
        <h3><a href="https://twitter.com/cunycommons" title="@cunycommons twitter feed">@cunycommons</a></h3>
        <div class="cac-content">
          <ul>
            <li><a id="commons_timeline_link" rel="commons_timeline" href="#">Tweets</a></li>
            <li><a id="commons_mentions_link" rel="commons_mentions" href="#">Mentions</a></li>
            <li><a id="cuny_hashtag_link" rel="cuny_hashtag" href="#">#cuny</a></li>
          </ul>
        </div>
      </div>
      <div class="cac-twitter-widget featured">
        <h3><a href="https://twitter.com/cunycommons/cunycommons">cunycommons Twitter List</a></h3>
        <div class="cac-content">
          <ul>
            <li><a id="list_timeline_link" rel="list_timeline" href="#">Tweets</a></li>
            <li><a id="list_members_link" rel="list_members" href="#">List Members</a></li>
            <li><a id="list_subscribers_link" rel="list_subscribers" href="#">List Subscribers</a></li>
          </ul>
        </div>
      </div>
      <div class="cac-twitter-widget featured">
        <h3>Twitter Resources</h3>
        <div class="cac-content">
          <p>Online resources that provide in-depth information about Twitter, best practices, software and other useful tools.</p>
          <p><a href="http://mashable.com/guidebook/twitter/">Mashable's Twitter Guide Book &raquo;</a></p>
          <p><a href="https://support.twitter.com/groups/31-twitter-basics">Twitter Basics &raquo;</a></p>
          <p><a href="https://support.twitter.com/groups/31-twitter-basics/topics/104-welcome-to-twitter-support/articles/166337-the-twitter-glossary">The Twitter Glossary &raquo;</a></p>
        </div>
      </div>
      <div class="cac-twitter-widget featured">
        <h3>Download the Source</h3>
        <div class="cac-content">
          <p>We proudly offer the source used to create this page for free.</p>
          <p><a href="https://github.com/cuny-academic-commons/CAC-Twitter-Stream">Download Now &raquo;</a></p>
        </div>
      </div>
    </div>
    <div class="introbox">
      <h4>Tweets from The CUNY Academic Commons</h4>
      <ul id="commons_timeline_stream">
        <?php echo $cac_twitter_stream->render_html( 'commons_timeline' ); ?>
      </ul>
      <h4 style="display:none">Tweets mentioning The CUNY Academic Commons</h4>
      <ul id="commons_mentions_stream" style="display:none"></ul>
      <h4 style="display:none">Tweets containing the #cuny hashtag</h4>
      <ul id="cuny_hashtag_stream" style="display:none"></ul>
      <h4 style="display:none">Tweets from the "cunycommons" Twitter List</h4>
      <ul id="list_timeline_stream" style="display:none"></ul>
      <h4 style="display:none">Members of the "cunycommons" Twitter List</h4>
      <ul id="list_members_stream" style="display:none"></ul>
      <h4 style="display:none">Subscribers of the "cunycommons" Twitter List</h4>
      <ul id="list_subscribers_stream" style="display:none"></ul>
    </div>
    <div id="cac_about_twitter" class="introbox" style="display:none">
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
