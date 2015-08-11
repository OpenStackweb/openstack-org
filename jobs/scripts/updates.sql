update JobPage set active =1;

update JobPage set ExpirationDate = DATE_ADD(JobPostedDate, INTERVAL 2 MONTH)
where JobPage.JobPostedDate IS NOT NULL;