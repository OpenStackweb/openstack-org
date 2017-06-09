# Cron Schedule Module

## configuration

add following entry to crontab

```bash
* * * * * su dployio -s /bin/bash -c "/var/www/www.openstack.org/framework/sake SchedulerCronTask" 1>> /dev/null 2>&1
```

to install sake please run on your site root 

```bash

sudo ./framework/sake installsake;

```

then every time that you want to create a new cronjob
just inherit from CronTask base class, and add an entry on cron_jobs_scheduler/_config/jobs.yml

like

```yaml

 - name: "EventbriteSummitTicketTypeConciliation"
   params: "summit_external_id=32888262679"
   cron_expression: "00 01 * * *"
   enabled: 1
    
```

where name, and expression is mandatory and params optional