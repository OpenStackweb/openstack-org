<?php
/**
 * Class NewsSubmitter
 */
final class NewsSubmitter {
	/**
	 * @var string
	 */
    private $first_name;
	/**
	 * @var string
	 */
	private $last_name;
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $company;
    /**
     * @var integer
     */
    private $phone;

	/**
	 * @param string $first_name
	 * @param string $last_name
     * @param string $email
     * @param string $company
     * @param integer $phone
	 */
	public function __construct($first_name, $last_name, $email, $company, $phone){
		$this->first_name = $first_name;
		$this->last_name  = $last_name;
        $this->email      = $email;
        $this->company    = $company;
        $this->phone      = $phone;
	}

	/**
	 * @return string
	 */
	public function getFirstName(){
		return $this->first_name;
	}

	/**
	 * @return string
	 */
	public function getLastName(){
		return $this->last_name;
	}

    /**
     * @return string
     */
    public function getEmail(){
        return $this->email;
    }

    /**
     * @return string
     */
    public function getCompany(){
        return $this->company;
    }

    /**
     * @return phone
     */
    public function getPhone(){
        return $this->phone;
    }
} 