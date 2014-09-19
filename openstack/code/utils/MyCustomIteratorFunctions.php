<?php

/**
 * Class MyCustomIteratorFunctions
 */
final class MyCustomIteratorFunctions implements TemplateIteratorProvider
{

	protected $iteratorPos;
	protected $iteratorTotalItems;

	public static function get_template_iterator_variables()
	{
		return array('Mid','IsFourth','MultipleOf');
	}

	public function iteratorProperties($pos, $totalItems)
	{
		$this->iteratorPos = $pos;
		$this->iteratorTotalItems = $totalItems;
	}

	/**
	 * @return bool
	 */
	function Mid()
	{
		$mid = round( $this->iteratorTotalItems / 2);
		return ($this->iteratorPos+1) == $mid;
	}

	public function IsFourth(){
		return ($this->iteratorPos % 4) == 0;
	}

	public function MultipleOf($mul){
		$mul = (int)$mul;
		return ($this->iteratorPos + 1) % $mul == 0;
	}
}