<?php

/**
 * Interface INewsManager
 */
interface INewsManager extends IEntity {

	const NewsManagerGroupSlug = 'news-managers';
	/**
	 * @return bool
	 */
	public function isNewsManager();

} 