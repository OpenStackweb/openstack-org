<?php
/**
 * Class EventDuration
 */
final class EventDuration {
	/**
	 * @var DateTime
	 */
    private $start_date;
	/**
	 * @var DateTime
	 */
	private $end_date;

	/**
	 * @param DateTime $start_date
	 * @param DateTime $end_date
	 */
	public function __construct(DateTime $start_date,DateTime $end_date){
		$this->start_date = $start_date;
		$this->end_date   = $end_date;
	}

	/**
	 * @return DateTime
	 */
	public function getStartDate(){
		return $this->start_date;
	}

	/**
	 * @return DateTime
	 */
	public function getEndDate(){
		return $this->end_date;
	}
} 