<?php

/**
 * ########################################
 * # 2-Klick-Video-Plugin                 #
 * # (c) 2018 Papoo Software & Media GmbH #
 * #          Dr. Carsten Euwens          #
 * # Authors: Christoph Zimmer            #
 * # http://www.papoo.de                  #
 * ########################################
 * # PHP Version >= 5.3                   #
 * ########################################
 * @copyright 2018 Papoo Software & Media GmbH
 * @author Christoph Grenz <cg@papoo.de>
 * @date 2018-07-26
 */

namespace Papoo\Plugins\TwoClickVideo;
/**
 * Class YoutubeHandler
 *
 * @package Papoo\Plugins\TwoClickVideo
 */
class YoutubeHandler extends HandlerBase
{
	const PROVIDER_ID = 'youtube';
	const PRETTY_NAME = 'Youtube';

	/**
	 * @override
	 *
	 * @param array $data
	 * @return mixed|string|null
	 */
	public function getVideoId($data)
	{
		$match = null;
		if (preg_match('~^/(?:v/|v=|embed/)(?<video_id>[\w-]{10,12})[^?]*(?<query_string>(?:\?.+))?$~i', $data['path'], $match)) {
			return $match['video_id'];
		}
		else {
			return null;
		}
	}

	/**
	 * @override
	 *
	 * @param array $data
	 * @return string
	 */
	public function getEmbedUrl($data)
	{
		$match = null;
		preg_match('~^/(?:v/|v=|embed/)(?<video_id>[\w-]{10,12})[^?]*(?<query_string>(?:\?.+))?$~i', $data['path'], $match);
		if ($match['query_string']) {
			$match['query_string'] .= '&autoplay=1';
		}
		else {
			$match['query_string'] = '?autoplay=1';
		}
		return 'https://www.youtube-nocookie.com/embed/'.$match['video_id'].$match['query_string'];
	}

	/**
	 * @override
	 *
	 * @param array $data
	 * @param bool $get_thumbnails
	 */
	public function fillCache($data, $get_thumbnails)
	{
		$filenames = ['maxresdefault.jpg'=>'_maxres.jpg', 'sddefault.jpg'=>'_sd.jpg', 'hqdefault.jpg'=>'_hq.jpg', 'mqdefault.jpg'=>'_mq.jpg'];
		$urlbase = 'https://img.youtube.com/vi/'.$data['video_id'].'/';
		$json_url = 'https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v='.$data['video_id'].'&format=json';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $json_url);
		curl_setopt($ch,CURLOPT_TIMEOUT, 5);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_HEADER, false);
		curl_setopt($ch,CURLOPT_USERAGENT, 'Mozilla');

		#$curl = new \curl($json_url);
		#$curl->setopt(CURLOPT_TIMEOUT, 5);
		#$curl->setopt(CURLOPT_RETURNTRANSFER, true);
		#$curl->setopt(CURLOPT_HEADER, false);
		#$curl->setopt(CURLOPT_USERAGENT, 'Papoo CMS');

		$datart = curl_exec($ch);
		$meta_info = @json_decode($datart, true);

		#$meta_info = @json_decode($curl->exec(), true);
		if ($meta_info) {
			$title = $meta_info['title'];
		}
		else {
			$title = '';
		}

		$etags = [];
		foreach ($this->plugin->getCacheImages(static::PROVIDER_ID, $data['video_id']) as $img) {
			if ($img['etag']) {
				$etags[$img['file_name']] = $img['etag'];
			}
		}

		#$curl->setopt(CURLOPT_HEADER, true);
		curl_setopt($ch,CURLOPT_HEADER, true);

		$images = [];
		if ($get_thumbnails) {
			foreach ($filenames as $filename => $suffix) {
				$dest_filename =  'yt_' . $data['video_id'] . $suffix;
				$dest = $this->plugin->cachePath . $dest_filename;
				$url = $urlbase.$filename;

				$headers = [];
				if (file_exists($dest) && isset($etags[$dest_filename])) {
					$etag = $etags[$dest_filename];
					$headers[] = 'If-None-Match: "'.$etag.'"';
				}

				#$curl->setopt(CURLOPT_HTTPHEADER, $headers);
				#$curl->setopt(CURLOPT_URL, $url);

				curl_setopt($ch,CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch,CURLOPT_URL, $url);

				#$imgdata = $curl->exec();
				//$headersize = $curl->getinfo(CURLINFO_HEADER_SIZE);


				$imgdata = curl_exec($ch);
				$headersize = curl_getinfo($ch,CURLINFO_HEADER_SIZE);

				$headers = substr($imgdata, 0, $headersize);
				$imgdata = substr($imgdata, $headersize);

				#$code = $curl->getinfo(CURLINFO_HTTP_CODE);
				$code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
				if ($code == 304) {
					$sizes = \getimagesize($dest);
					$images[] = [
						'file_name' => $dest_filename,
						'etag' => $etag,
						'image_width' => $sizes[0],
						'image_height' => $sizes[1],
					];
				}
				elseif ($code == 200) {
					$sizes = \getimagesizefromstring($imgdata);
					$ratio = $sizes[0]/(float)$sizes[1];
					// 16:9-Vorschaubild umbauen
					if ($ratio > 8.7) {
						$img = \imagecreatefromstring($imgdata);
						$bg = \imagecolorallocatealpha($img, 0, 0, 0, 0);
						$cropped = \imagecropauto($img, IMG_CROP_THRESHOLD, 0.3, $bg);
						\imagecolordeallocate($img, $bg);
						if ($cropped !== false) {
							\imagedestroy($img);
							$img = $cropped;
						}
						$width = \imagesx($img);
						$oldheight = \imagesy($img);
						$newimg = \imagecreatetruecolor($width, (int)($width*0.75));
						$bg = \imagecolorallocatealpha($newimg, 0, 0, 0, 0);
						\imagefill($newimg, 0, 0, $bg);
						\imagecolordeallocate($newimg, $bg);
						\imagecopy($newimg, $img, 0, (($width*0.75)-$oldheight)/2, 0, 0, $width, $oldheight);
						\imagedestroy($img);
						\ob_start();
						\imagejpeg($newimg, null, 90);
						$imgdata = ob_get_clean();
						\imagedestroy($newimg);
						$sizes = [$width, (int)$width*0.75];
					}

					$match = null;
					preg_match('/^Etag:\s*"([^"]{1,16})"/mi', $headers, $match);
					$etag = $match ? $match[1] : null;
					file_put_contents($dest, $imgdata, LOCK_EX);
					$images[] = [
						'file_name' => $dest_filename,
						'etag' => $etag,
						'image_width' => $sizes[0],
						'image_height' => $sizes[1],
					];
				}
			}
		}
		$this->plugin->setCacheContent(static::PROVIDER_ID, $data['video_id'], $title, $images);
	}
}

TwoClickVideo::registerHandler('\\Papoo\\Plugins\\TwoClickVideo\\YoutubeHandler', ['youtube.com', 'youtube-nocookie.com']);
