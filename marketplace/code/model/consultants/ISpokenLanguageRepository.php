<?php
/**
 * Interface ISpokenLanguageRepository
 */
interface ISpokenLanguageRepository extends IEntityRepository {
	/**
	 * @param string $name
	 * @return ISpokenLanguage
	 */
	public function getByName($name);
} 