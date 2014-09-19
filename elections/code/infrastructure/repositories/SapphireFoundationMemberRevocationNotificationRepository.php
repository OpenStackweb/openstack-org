<?php

/**
 * Class SapphireFoundationMemberRevocationNotificationRepository
 */
final class SapphireFoundationMemberRevocationNotificationRepository
extends SapphireRepository implements IFoundationMemberRevocationNotificationRepository
{

	public function __construct(){
		parent::__construct(new FoundationMemberRevocationNotification);
	}

	/**
	 * @param int $foundation_member_id
	 * @return IFoundationMemberRevocationNotification
	 */
	public function getByFoundationMember($foundation_member_id)
	{
		$query = new QueryObject(new FoundationMemberRevocationNotification);
		$query->addAddCondition(QueryCriteria::equal('Member.ID',$foundation_member_id));
		return $this->getBy($query);
	}

	/**
	 * @param int $days
	 * @param int $batch_size
	 * @return IFoundationMemberRevocationNotification[]
	 */
	public function getNotificationsSentXDaysAgo($days, $batch_size)
	{
		$query = new QueryObject(new FoundationMemberRevocationNotification);
		$query->addAddCondition(QueryCriteria::equal('Action','None'));
		$today = new DateTime('now',new DateTimeZone("UTC"));
		$query->addAddCondition(QueryCriteria::lowerOrEqual('ADDDATE(SentDate, INTERVAL '.$days.' DAY)', $today->format('Y-m-d H:i:s'),false));
		list($res,$size) = $this->getAll($query,0, $batch_size);
		return $res;
	}

	/**
	 * @param string $hash
	 * @return bool
	 */
	public function existsHash($hash)
	{
		$query = new QueryObject(new FoundationMemberRevocationNotification);
		$query->addAddCondition(QueryCriteria::equal('Hash', $hash));
		return $this->getBy($query) != null;
	}

	/**
	 * @param string $hash
	 * @return IFoundationMemberRevocationNotification
	 */
	public function getByHash($hash)
	{
		$query = new QueryObject(new FoundationMemberRevocationNotification);
		$query->addAddCondition(QueryCriteria::equal('Hash',FoundationMemberRevocationNotification::HashConfirmationToken($hash)));
		return $this->getBy($query);
	}
}