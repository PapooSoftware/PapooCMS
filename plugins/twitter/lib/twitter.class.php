<?php
/*
*
*   @name: Twitter Plugin
*   @author: Sebastian Hartmann
*   @date: 07.04.2014
*
*/


/*
*
*   @name: twitter
*   @functions:
*       + oauth()
*       + get_tweets_hashtag()
*       + get_tweets_userid()
*       + get_followers()
*
*/

class twitter
{

    // Pattern for username and hashtags
    public $pattern = array("userid" => "/^[a-zA-Z0-9]/", "hashtag" => "/^[a-zA-Z0-9]/");


    // OAuth settings
    public $oauth = array("consumer_key" => "IF8tdkt7xfmSaaglwv2atUQNA",
        "consumer_secret" => "FjTqTHX8pWA1GgPEFvyZ8VM4BvCk8tcDNzYAiGXwwwXT4l859m",
        "access_token" => "2427083090-M0Ql5W3aWEXIuxn0g3c2dI9V8i2wfeFIEcZUbpS",
        "access_token_secret" => "8mtmhxoyXeqenHs9sQ7qcnH9j9DwvPPwUuKu8ZV0VaUqr");

    // Request URI
    public $url = array("tweets" => "https://api.twitter.com/1.1/search/tweets.json",
        "timeline" => "https://api.twitter.com/1.1/statuses/user_timeline.json",
        "followers" => "https://api.twitter.com/1.1/followers/list.json");


    // Initialize json variable
    public $json;


    function __construct()
    {
        global $content;
        $this->content = &$content;

        global $checked;
        $this->checked = &$checked;

        global $cms;
        $this->cms = &$cms;

        global $db;
        $this->db = &$db;

		if (isset($content->template["plugins"]) == false || is_array($content->template["plugins"]) == false) {
			$content->template["plugins"] = [];
		}
		$content->template["plugins"]["twitter"] = [
			"web_path" => PAPOO_WEB_PFAD."/plugins/".pathinfo(dirname(__DIR__), PATHINFO_FILENAME)."/"
		];
    }


    /*
    *
    *   @name: OAuth Signing
    *   @desc: Create OAuth Signature base string and Authorization header.
    *   @params: URL and GET parameter in alphabetical order
    *
    */
    public function oauth()
    {
        $args = func_get_args();

        $url = $args[0];
        $get = array($args[1], $args[2]);

        // Create URL
        $uri = $url . '?' . $get[0] . '&' . $get[1];

        // OAuth Signature base string
        $hash = '';
        $hash .= $get[0] . '&';
        $hash .= 'oauth_consumer_key=' . $this->oauth["consumer_key"] . '&';
        $hash .= 'oauth_nonce=' . time() . '&';
        $hash .= 'oauth_signature_method=HMAC-SHA1&';
        $hash .= 'oauth_timestamp=' . time() . '&';
        $hash .= 'oauth_token=' . $this->oauth["access_token"] . '&';
        $hash .= 'oauth_version=1.0&';
        $hash .= $get[1];

        $base = '';
        $base .= 'GET';
        $base .= '&';
        $base .= rawurlencode($url);
        $base .= '&';
        $base .= rawurlencode($hash);

        $key = '';
        $key .= rawurlencode($this->oauth["consumer_secret"]);
        $key .= '&';
        $key .= rawurlencode($this->oauth["access_token_secret"]);

        $signature = base64_encode(hash_hmac('sha1', $base, $key, true));
        $signature = rawurlencode($signature);

        // Authorization header
        $header = '';
        $header .= $get[0] . ', ';
        $header .= 'oauth_consumer_key="' . $this->oauth["consumer_key"] . '", ';
        $header .= 'oauth_nonce="' . time() . '", ';
        $header .= 'oauth_signature="' . $signature . '", ';
        $header .= 'oauth_signature_method="HMAC-SHA1", ';
        $header .= 'oauth_timestamp="' . time() . '", ';
        $header .= 'oauth_token="' . $this->oauth["access_token"] . '", ';
        $header .= 'oauth_version="1.0", ';
        $header .= $get[1];

        // cURL Header
        $curl_header = array("Authorization: OAuth {$header}");


        // cURL Request
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_HTTPHEADER => $curl_header,
            CURLOPT_HEADER => false,
            CURLOPT_URL => $uri,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        ));

        #debug::print_d($uri);
        $output = curl_exec($curl);

        #debug::print_d($output);

        curl_close($curl);

        // json decode
        $this->json = json_decode($output, true);
    }


    /*
    *
    *   @name: show_tweets_hashtag
    *   @desc: Returns a collection of relevant Tweets matching a specified hashtag query.
    *   @params: hashtag, count
    *
    */
    public function show_tweets_hashtag($hashtag, $count, $include_media)
    {
        if (preg_match($this->pattern["hashtag"], $hashtag))
        {

            // URL encode
            $hashtag = rawurlencode("#" . $hashtag);

            // GET Parameters
            $get = array("count" => "count=$count",
                "q" => "q=$hashtag");

            twitter::oauth($this->url["tweets"], $get["count"], $get["q"]);


            if (is_array($this->json))
            {

                if (array_key_exists("statuses", $this->json))
                {

                    if ($include_media != '1')
                    {
                        $data_cards = ' data-cards="hidden" ';
                    }
                    else
                    {
                        $data_cards = ' ';
                    }

                    $data = '';
                    $data .= '<div class="tweet_container hide-until-ready">';

                    foreach ($this->json["statuses"] as $tweet)
                    {
                        $href = "https://twitter.com/" . $tweet['user']['screen_name'] . "/status/" . $tweet['id'];
                        $text = $this->make_links($tweet['text']);

                        $data .= '<blockquote class="twitter-tweet"' . $data_cards . 'lang="en">';
                        $data .= '    <p>';
                        $data .= '        ' . $text;
                        $data .= '    </p>';
                        $data .= '    ' . $tweet['user']['name'] . " ( @" . $tweet['user']['screen_name'] . ")";
                        $data .= '    <a href="' . $href . '">' . $tweet['created_at'] . '</a>';
                        $data .= '</blockquote>';
                    }

                    $data .= "</div>";

                    return $data;

                }
            }

        }
        else
        {
            die("Bitte geben Sie einen gültigen Hashtag an.");
        }
    }


    /*
    *
    *   @name: show_tweets_userid
    *   @desc: Returns a collection of the most recent Tweets posted by the user.
    *   @params: userid, count
    *
    */
    public function show_tweets_userid($userid, $count, $include_media)
    {
        if (preg_match($this->pattern["userid"], $userid))
        {
            // URL encode
            $userid = rawurlencode($userid);

            // GET Parameters
            $get = array("count" => "count=$count",
                "screen_name" => "screen_name=$userid");


            twitter::oauth($this->url["timeline"], $get["count"], $get["screen_name"]);

            if (is_array($this->json))
            {

                if ($include_media != '1')
                {
                    $data_cards = ' data-cards="hidden" ';
                }
                else
                {
                    $data_cards = ' ';
                }

                if (@array_key_exists("user", $this->json[0]))
                {

                    $data = '';
                    $data .= '<div class="tweet_container hide-until-ready">';

                    foreach ($this->json as $tweet)
                    {
                        $href = "https://twitter.com/" . $tweet['user']['screen_name'] . "/status/" . $tweet['id'];
                        $text = $this->make_links($tweet['text']);

                        $data .= '<blockquote class="twitter-tweet"' . $data_cards . 'lang="en">';
                        $data .= '    <p>';
                        $data .= '        ' . $text;
                        $data .= '    </p>';
                        $data .= '    ' . $tweet['user']['name'] . " ( @" . $tweet['user']['screen_name'] . ")";
                        $data .= '    <a href="' . $href . '">' . $tweet['created_at'] . '</a>';
                        $data .= '</blockquote>';
                    }

                    $data .= '</div>';

                    return $data;

                }
            }

        }
        else
        {
            die("Bitte geben Sie einen gültigen Benutzernamen an.");
        }
    }

	public function make_links($text)
	{
		$text = preg_replace_callback(
			'/(?(?=<a[^>]*>.+<\/a>)(?:<a[^>]*>.+<\/a>)|([^="\']?)((?:https?|ftp|bf2|):\/\/[^<> \n\r]+))/i',
			function ($match) {
				return stripslashes((strlen($match[2])>0?"{$match[1]}<a href=\"{$match[2]}\">{$match[2]}</a>{$match[3]}":$match[0]));
			}, $text);

		$text = preg_replace(
			['/<a([^>]*)target="?[^"\']+"?/i', '/<a([^>]+)>/i'], ['<a\\1', '<a\\1 target="_blank">'], $text
		);

		$text = preg_replace_callback(
			'/(^|\s)(www.[^<> \n\r]+)/i',
			function ($match) {
				return stripslashes((strlen($match[2])>0?"{$match[1]}<a href=\"http://{$match[2]}\">{$match[2]}</a>{$match[3]}":$match[0]));
			}, $text);

		$text = preg_replace_callback(
			'/(([_A-Za-z0-9-]+)(\\.[_A-Za-z0-9-]+)*@([A-Za-z0-9-]+)(\\.[A-Za-z0-9-]+)*)/i',
			function ($match) {
				return stripslashes((strlen($match[2])>0?"<a href=\"mailto:{$match[0]}\">{$match[0]}</a>":$match[0]));
			}, $text);

		return $text;
	}

    /*
    *
    *   @name: show_followers
    *   @desc: Returns a cursored collection of users following the specified user.
    *   @params: userid, count
    *
    */
    public function show_followers($userid, $count)
    {
        if (preg_match($this->pattern["userid"], $userid))
        {

            // URL encode
            $userid = rawurlencode($userid);

            // GET Parameters
            $get = array("count" => "count=$count",
                "screen_name" => "screen_name=$userid");

            twitter::oauth($this->url["followers"], $get["count"], $get["screen_name"]);

            if (is_array($this->json))
            {

                if (array_key_exists("users", $this->json))
                {

                    $data = '';
                    $data .= '<div class="followers_container">';
                    $data .= '<div class="followers_header">' . $count . ' Followers von <b>' . rawurldecode($userid) . '</b></div>';
                    $data .= '<div class="followers">';
                    $data .= '<ul class="followers_list">';

                    foreach ($this->json["users"] as $key => $value)
                    {
                        $data .= '<li><a href="https://twitter.com/' . $this->json["users"][$key]["screen_name"] . '"><img src="' . $this->json["users"][$key]["profile_image_url"] . '" /></a>';
                    }

                    $data .= '</ul>';
                    $data .= '</div>';
                    $data .= '</div>';

                    return $data;

                }
            }

        }
        else
        {
            die("Bitte geben Sie einen g�ltigen Benutzernamen an.");
        }
    }


    public function output_filter()
    {
        if (!defined("admin") || ($_GET['is_lp'] == 1))
        {
            global $output;

            if (strstr($output, "#twitter"))
            {
                $output = $this->create_twitterintegration($output);
            }
        }
    }

    public function create_twitterintegration($inhalt = "")
    {
		$bodyOffset = strpos($inhalt, "<body");
		$body = substr($inhalt, $bodyOffset);

		$body = preg_replace('~(?|<p>\s*#(twitter.com/[^#]+/status/[^#]+)#\s*</p>|#(twitter.com/[^#]+/status/[^#]+)#)~',
			'<blockquote class="twitter-tweet"><a href="https://\1"></a></blockquote>',
			$body);

        preg_match_all("|#twitter(.*?)#|", $inhalt, $ausgabe, PREG_PATTERN_ORDER);

        $i = 0;

        foreach ($ausgabe['1'] as $dat)
        {
            $ndat = explode("+", $dat);

//            debug::print_d($ndat); exit;

            if ($ndat['1'] == "usertweets")
            {
                $twitter_daten = $this->show_tweets_userid($ndat['2'], $ndat['3'], $ndat['4']);
            }
            else
            {
                if ($ndat['1'] == "hashtweets")
                {
                    $twitter_daten = $this->show_tweets_hashtag($ndat['2'], $ndat['3'], $ndat['4']);
                }
                else
                {
                    if ($ndat['1'] == "followers")
                    {
                        $twitter_daten = $this->show_followers($ndat['2'], $ndat['3']);
                    }
                }
            }
            #debug::print_d($twitter_daten);
			$body = str_ireplace($ausgabe['0'][$i], $twitter_daten, $body);
            $i++;
        }

		$inhalt = substr_replace($inhalt, $body, $bodyOffset);

		if (strpos($inhalt, '<blockquote class="twitter-tweet"') !== false) {
			$inhalt = str_replace("</body>",
				'<script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>'."\n</body>", $inhalt);
		}

        return $inhalt;
    }
}

$twitter = new twitter();
?>