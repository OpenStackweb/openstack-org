<?php

/**
 * Class CourseLocationViewModel
 */
class CourseLocationViewModel extends ViewableData {

	/**
	 * @var TrainingCourseLocationDTO
	 */
	private $dto;

	/**
	 * @param TrainingCourseLocationDTO $dto
	 */
	public function __construct(TrainingCourseLocationDTO $dto){
		$this->dto = $dto;
	}

	/**
	 * @return string
	 */
	public function getCity(){
		return $this->dto->getCity();
	}

	/**
	 * @return string
	 */
	public function getState(){
		return $this->dto->getState();
	}

	/**
	 * @return mixed
	 */
	public function getCountry(){
		return @CountryCodes::$iso_3166_countryCodes[$this->dto->getCountry()];
	}

	/**
	 * @return string
	 */
	public function getLink(){
		return $this->dto->getLink();
	}

	/**
	 * @return string
	 */
	public function getStartDateMonth(){
		return  DateTimeUtils::getMonthShortName($this->dto->getStartDate());
	}

	/**
	 * @return bool|string
	 */
	public function getStartDateDay(){
		return  DateTimeUtils::getDay($this->dto->getStartDate());
	}

	/**
	 * @return bool|string
	 */
	public function getStartDateYear(){
		return  DateTimeUtils::getYear($this->dto->getStartDate());
	}

	/**
	 * @return string
	 */
	public function getEndDateMonth(){
		return  DateTimeUtils::getMonthShortName($this->dto->getEndDate());
	}

	/**
	 * @return bool|string
	 */
	public function getEndDateDay(){
		return  DateTimeUtils::getDay($this->dto->getEndDate());
	}

	/**
	 * @return bool|string
	 */
	public function getEndDateYear(){
		return  DateTimeUtils::getYear($this->dto->getEndDate());
	}

	/**
	 * @return string
	 */
	public function getDays(){
		return DateTimeUtils::getDayDiff($this->dto->getStartDate(),$this->dto->getEndDate());
	}
}