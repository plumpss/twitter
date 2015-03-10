#SilverStripe Twitter Helper

##Installation

`composer require plumpss/twitter`

##Usage

Add YAML config for your keys, tokens and secrets:

```
PlumpTwitterFeed:
  twitter_consumer_key: ''
	twitter_consumer_secret: ''
	twitter_oauth_token: ''
	twitter_oauth_token_secret: ''
```

Then obtain Tweets for the user you require:

`PlumpTwitterFeed::get_tweets('plumpdigital', 10, TRUE);`

The `get_tweets` method returns an `ArrayList` of `ArrayData` so can be output in templates using:

```
<% loop Tweets %>
  $Html
  $Date.Nice
<% end_loop %>
```
