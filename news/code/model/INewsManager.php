<?php

/**
 * Interface INewsManager
 */
interface INewsManager extends IEntity {

	const NewsManagerGroupSlug = 'news-managers';
	/**
	 * @return void
	 */
	public function convert2SiteUser();

	/**
	 * @return bool
	 */
	public function isNewsManager();

	/**
	 * @return void
	 */
	public function upgradeToNewsManager();

	public function resign();

} 