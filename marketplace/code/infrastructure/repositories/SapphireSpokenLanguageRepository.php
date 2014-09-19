<?php
/**
 * Class SapphireSpokenLanguageRepository
 */
final class SapphireSpokenLanguageRepository extends SapphireRepository
implements ISpokenLanguageRepository
{
	public function __construct(){
		parent::__construct(new SpokenLanguage);
	}

	/**
	 * @param string $name
	 * @return ISpokenLanguage
	 */
	public function getByName($name)
	{
		$class = $this->entity_class;
		return $class::get()->filter("Name",$name)->first();
	}
}