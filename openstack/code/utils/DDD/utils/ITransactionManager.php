<?php
interface ITransactionManager {
	/**
	 * @param callable $callback
	 * @return mixed
	 */
	public function transaction(Closure $callback);
} 