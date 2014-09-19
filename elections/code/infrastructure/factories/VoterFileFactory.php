<?php

/**
 * Class VoterFileFactory
 */
final class VoterFileFactory implements IVoterFileFactory {

	/**
	 * @param string $filename
	 * @return IVoterFile
	 */
	public function build($filename)
	{
		$file = new VoterFile();
		$file->FileName = $filename;
		return $file;
	}
}