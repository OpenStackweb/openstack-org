<?php

/**
 * Class MarketPlaceAssembler
 */
final class MarketPlaceAssembler {
	/**
	 * @param IMarketPlaceVideo $video
	 * @return array
	 */
	public static function convertVideo2Array(IMarketPlaceVideo $video){
		$res = array();
		$res['id']          = $video->getIdentifier();
		$res['type_id']     = $video->getType()->getIdentifier();
		$res['title']       = $video->getName();
		$res['description'] = $video->getDescription();
		$res['length']      = $video->getLength();
		$res['youtube_id']  = $video->getYouTubeId();
		return $res;
	}
}