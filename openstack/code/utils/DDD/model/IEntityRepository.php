<?php
/**
 * Interface IEntityRepository
 */
interface IEntityRepository {
	/**
	 * @param QueryObject $query
	 * @return IEntity
	 */
	public function getBy(QueryObject $query);

	/**
	 * @param IEntity $entity
	 * @return int
	 */
	public function add(IEntity $entity);

	/**
	 * @param IEntity $entity
	 * @return void
	 */
	public function delete(IEntity $entity);

	/**
	 * @param int $id
	 * @return IEntity
	 */
	public function getById($id);

	/**
	 * @param QueryObject $query
	 * @param int         $offset
	 * @param int         $limit
	 * @return array
	 */
	public function getAll(QueryObject $query, $offset=0, $limit = 10);
} 