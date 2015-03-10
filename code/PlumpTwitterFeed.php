<?php

use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Plump Twitter feed class
 */
class PlumpTwitterFeed {
	
	public static $twitter_consumer_key;
	public static $twitter_consumer_secret;
	public static $twitter_oauth_token;
	public static $twitter_oauth_token_secret;
	
	public static function get_tweets($username, $limit = 5, $includeRetweets = FALSE) {
		
		require_once(Director::baseFolder() . '/' . PLUMP_TWITTER_BASE . '/thirdparty/twitteroauth/twitteroauth.php');
		
		$connection = new TwitterOAuth(
			self::$twitter_consumer_key,
			self::$twitter_consumer_secret,
			self::$twitter_oauth_token,
			self::$twitter_oauth_token_secret
		);
		
		$connection->host = 'https://api.twitter.com/1.1/';
		
		$config = array(
			'include_entities' 	=> 'true',
			'include_rts' 		=> ($includeRetweets ? 'true' : 'false'),
			'screen_name' 		=> $username
		);
		
		$tweets = $connection->get('statuses/user_timeline', $config);
		
		$tweetList = new ArrayList();
		
		if (count($tweets) > 0 && !isset($tweets->error)) {
			foreach($tweets as $i => $tweet) {
				if ($i + 1 > $limit) break;
				$tweetList->push(self::create_tweet($tweet));
			}
		}
		
		return $tweetList;
	}
	
	private static function create_tweet($tweet) {
		
		$date = new SS_Datetime();
		$date->setValue(strtotime($tweet->created_at));
			
		$html = $tweet->text;
		
		if ($tweet->entities) {
			
			//url links
			if ($tweet->entities->urls) {
				foreach ($tweet->entities->urls as $url) {
					$html = str_replace($url->url, '<a href="' . $url->url .'" target="_blank">' . $url->url . '</a>', $html);
				}
			}
			
			//hashtag links
			if ($tweet->entities->hashtags) {
				foreach ($tweet->entities->hashtags as $hashtag) {
					$html = str_replace('#' . $hashtag->text, '<a target="_blank" href="https://twitter.com/search?q=%23' . $hashtag->text . '">#' . $hashtag->text . '</a>', $html);
				}
			}
			
			//user links
			if ($tweet->entities->user_mentions) {
				foreach ($tweet->entities->user_mentions as $mention) {
					$html = str_replace('@' . $mention->screen_name, '<a target="_blank" href="https://twitter.com/' . $mention->screen_name . '">@' . $mention->screen_name . '</a>', $html);
				}
			}
			
		}
		
		return new ArrayData(array(
			'Date' => $date,
			'Text' => $tweet->text,
			'Html' => $html
		));
	}
	
}
