<?php

if ( stristr( $_SERVER['PHP_SELF'],'class_ping.php') ) die( 'You are not allowed to see this page directly' );

/**
 * Class ping
 */
class ping{
	/**
	 * class_uebungen::__construct()
	 *
	 * ping constructor.
	 */
	function __construct()
	{
		//Verbindung mit DB
		global $db;
		$this->db=&$db;
	}

	/**
	 * class_vorlage::ping_now()
	 *
	 * @param string $title
	 * @param string $blogurl
	 * @param string $rssfeed
	 * @return void
	 */
	public function ping_now($title="",$blogurl="",$rssfeed="")
	{
		//Alle Ping Dienste durchgehen

		if (!empty($title) && !empty($blogurl)) {
			self::ping_omatic($title,$blogurl,$rssfeed);
		}
	}

	/**
	 * class_vorlage::ping_omatic()
	 *
	 * @param string $title
	 * @param string $blogurl
	 * @param string $rssfeed
	 * @return void
	 */
	protected function ping_omatic($title="",$blogurl="",$rssfeed="")
	{
		// Ping mit allem was geht!
		$url='http://pingomatic.com/ping/?title='.$title.'&blogurl='.$blogurl.'&rssurl='.$rssfeed.'&chk_weblogscom=on&chk_blogs=on&chk_feedburner=on&chk_syndic8=on&chk_newsgator=on&chk_myyahoo=on&chk_pubsubcom=on&chk_blogdigger=on&chk_blogstreet=on&chk_weblogalot=on&chk_newsisfree=on&chk_topicexchange=on&chk_google=on&chk_tailrank=on&chk_postrank=on&chk_skygrid=on&chk_collecta=on&chk_superfeedr=on';
		self::http_request_open($url);
	}

	/**
	 * ping::http_request_open()
	 *
	 * @param mixed $url
	 * @param int $timeout
	 * @return bool|string
	 */
	protected function http_request_open($url,$timeout=10)
	{
		if(function_exists("curl_init")) {
			$ch = curl_init( $url );
			curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_HEADER, 1 );
			##curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
			curl_setopt( $ch, CURLOPT_USERAGENT,
				"Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.8.1.4) Gecko/20070515 Firefox/2.0.0.4" );

			$curl_ret = curl_exec( $ch );
			curl_close( $ch );
			return $curl_ret;
		}
		return false;
	}
}
