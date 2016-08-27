CRON TASKS
-----------

*VotersDataIngestionTask: ingest cvs files from path "/elections/input" 
csv file names should follow following format voters_ELECTIONID_YYYYMMDD_YYYYMMDD.csv
where first YYYYMMDD is open date and second YYYYMMDD is close date
ex:
voters_19751_20120814_20120818.csv
voters_22034_20130113_20130117.csv
voters_28407_20140113_20140117.csv
where ELECTIONID should be a valid election that exists on table election. once file is processed it is deleted and 
added its name to table VoterFile to avoid to ingest it again.
*RevocationNotificationSenderTask: this task sends all foundation membership revocation notifications to all user that 
 does not voted on 2 of the 3 elections
*RevocationExecutorTask: this tasks automatically revoke any voided notification                                                                                                                                                                                    