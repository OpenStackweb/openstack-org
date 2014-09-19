<?php

interface IUnitOfWork {

	public function markDirty(IEntity $entity);
	public function markNew(IEntity $entity);
	public function markDelete(IEntity $entity);
	public function commit();
	public function rollback();
} 