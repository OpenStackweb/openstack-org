<?php

/**
 * Interface IOpenStackImplementationRepository
 */
interface IOpenStackImplementationRepository
	extends IEntityRepository {
	public function getWithCapabilitiesEnabled(QueryObject $query, $offset = 0, $limit = 10);
} 