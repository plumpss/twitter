<?php

use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * Plump Twitter feed class
 */
class PlumpTwitterFeed extends Object {
	
	public static function get_tweets($username, $limit = 5, $includeRetweets = FALSE, $excludeReplies = TRUE) {

		$config = self::config();
		
		$connection = new TwitterOAuth(
			$config->twitter_consumer_key,
			$config->twitter_consumer_secret,
			$config->twitter_oauth_token,
			$config->twitter_oauth_token_secret
		);

		$connection->setTimeouts(30, 30);
		
		$parameters = array(
			'count'             => $limit,
			'include_entities' 	=> TRUE,
			'include_rts' 		=> $includeRetweets,
			'exclude_replies'   => $excludeReplies,
			'screen_name' 		=> $username
		);
		
		$tweets = $connection->get('statuses/user_timeline', $parameters);
		
		$tweetList = new ArrayList();
		
		if (count($tweets) > 0 && !isset($tweets->error)) {
			foreach($tweets as $i => $tweet) {
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
