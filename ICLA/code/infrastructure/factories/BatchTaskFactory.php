<?php

/**
 * Class BatchTaskFactory
 */
final class BatchTaskFactory implements IBatchTaskFactory {

	/**
	 * @param string $task_name
	 * @param int    $total_record_qty
	 * @return IBatchTask
	 */
	public function buildBatchTask($task_name, $total_record_qty) {
		$task               = new BatchTask();
		$task->Name         = $task_name;
		$task->initialize($total_record_qty);
		return $task;
	}
}