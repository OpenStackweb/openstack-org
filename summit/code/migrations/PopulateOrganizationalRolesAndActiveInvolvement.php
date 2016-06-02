<?php

class PopulateOrganizationalRoleAndActiveInvolvement extends AbstractDBMigrationTask
{

    protected $title = "Populate Organizational Role And Active Involvement";


    protected $description = "Populate Organizational Role And Active Involvement with default values";


    function doUp()
    {
        global $database;
        DB::query("INSERT INTO SpeakerOrganizationalRole (Role,IsDefault) VALUES
        ('Upstream Developer',1),('Cloud Application Developer',1),('OpenStack User / Operator',1),('Operations / SysAdmin',1),
        ('CIO / CTO / IT Manager',1),('Business Development / Marketing',1),('CEO / Executive Leadership',1),('Cloud Architect',1),
        ('Product Strategy',1),('Product Management',1)");

        DB::query("INSERT INTO SpeakerActiveInvolvement (Involvement,IsDefault) VALUES
        ('User Group organizer',1),('User Group member',1),('Summit attendee (current or prior)',1),('Summit speaker (current or prior)',1),
        ('Working Group organizer',1),('Working Group member',1),('OpenStack Day organizer',1),('OpenStack Day attendee (current or prior)',1),
        ('Women of OpenStack group member / supporter',1),('OpenStack App Hackathon attendee (current or prior)',1),
        ('Active Technical Contributor (ATC) (current or prior)',1),('OpenStack Documentation Contributor',1),('OpenStack Translation Contributor',1),
        ('Midcycle Meetup organizer',1),('Midcycle Meetup attendee (current or prior)',1),
        ('Active on OpenStack mailing lists (Dev, Ops, Community, Women of OpenStack, App Hackathon, etc)',1),('Superuser publication interviewee',1),
        ('Superuser publication contributor',1),('SuperuserTV video interviewee',1),('Superuser Award nominee',1),
        ('OpenStack Project Team Lead (PTL)',1),('OpenStack Foundation Board of Directors',1),('OpenStack Foundation Staff',1),
        ('OpenStack Technical Committee (TC)',1),('OpenStack User Committee (UC)',1)");

    }

    function doDown()
    {

    }
}