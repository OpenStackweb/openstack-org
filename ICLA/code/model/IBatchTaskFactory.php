<?php

/**
 * Interface IBatchTaskFactory
 */
interface  IBatchTaskFactory {
	/**
	 * @param string $task_name
	 * @param int $total_record_qty
	 * @return IBatchTask
	 */
	public function buildBatchTask($task_name, $total_record_qty);
} 