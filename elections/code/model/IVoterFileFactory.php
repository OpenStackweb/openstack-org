<?php
/**
 * Interface IVoterFileFactory
 */
interface IVoterFileFactory {
	/**
	 * @param string $filename
	 * @return IVoterFile
	 */
	public function build($filename);
} 