<?php
/**
 * Copyright 2014 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
// Use _ss_environment.php file for configuration
require_once("conf/ConfigureFromEnv.php");

// Register custom site configuration extension
DataObject::add_extension('SiteConfig', 'CustomSiteConfig');

// Set the site locale
i18n::set_locale('en_US');

//Turn on Silverstripe Translation

// Search index for Orgs
Object::add_extension('Org', 'AutocompleteOrgDecorator');

Page::remove_extension('OpenGraphObjectExtension');

// Enable SSL for specific subdomains

if(Director::isLive()) Director::forceSSL(array('/^Security/','/^profile/',
	'/^join/','/^user-survey/','/^summit/','/^news-manage/',
	'/^vote-vancouver/','/^admin/'));


// Email errors and warnings

global $email_log;

if(Director::isDev()) {
    SS_Log::add_writer(new SS_LogFileWriter(Director::baseFolder() . '/logs/site.log'), SS_Log::ERR);
}

$email_log_writer = new Custom_SS_LogEmailWriter($email_log);
$email_log_writer->setFormatter(new SS_CustomLogErrorEmailFormatter());
SS_Log::add_writer($email_log_writer, SS_Log::ERR, '<=');

// Default From address for email
global $email_from;
Config::inst()->update('Email', 'admin_email', $email_from);

//Register Shortcodes
ShortcodeParser::get()->register('Sched',array('Page','SchedShortCodeHandler'));
ShortcodeParser::get()->register('outlink',array('Page','ExternalLinkShortCodeHandler'));
ShortcodeParser::get()->register('icon',array('Page','IconShortCodeHandler'));

//cache configuration
/*
SS_Cache::add_backend('two-level', 'Two-Levels', array(
  'slow_backend' => 'File',
  'fast_backend' => 'Apc',
  'slow_backend_options' => array('cache_dir' => TEMP_FOLDER . DIRECTORY_SEPARATOR . 'cache')
));

SS_Cache::pick_backend('two-level', 'any', 10); // No need for special backend for aggregate - TwoLevels with a File slow backend supports tags
*/

SS_Cache::add_backend('file-level', 'File', array('cache_dir' => TEMP_FOLDER . DIRECTORY_SEPARATOR . 'cache'));

SS_Cache::pick_backend('file-level', 'any', 10);

SS_Cache::set_cache_lifetime($for = 'cache_entity_count', $lifetime = 3600, $priority = 100);

//entity counter extension
Object::add_extension('HomePage_Controller', 'EntityCounter');
Object::add_extension('AnniversaryPage_Controller', 'EntityCounter');
Object::add_extension('Group', 'GroupDecorator');
Object::add_extension('SecurityAdmin', 'SecurityAdminExtension');


//Force cache to flush on page load if in Dev mode (prevents needing ?flush=1 on the end of a URL)
if (Director::isDev()) {
	//Set default login
	//Security::setDefaultAdmin('admin','pass');
}

/* TinyMCE Configuration */
$tinyMCE = HtmlEditorConfig::get('cms');
$tinyMCE->setOption('extended_valid_elements', '@[id|class|style|title],#a[id|rel|rev|dir|tabindex|accesskey|type|name|href|target|title|class],-strong/-b[class],-em/-i[class],-strike[class],-u[class],#p[id|dir|class|align|style],-ol[class],-ul[class],-li[class],br,i,em,img[id|dir|longdesc|usemap|class|src|border|alt=|title|width|height|align],-sub[class],-sup[class],-blockquote[dir|class],-table[border=0|cellspacing|cellpadding|width|height|class|align|summary|dir|id|style],-tr[id|dir|class|rowspan|width|height|align|valign|bgcolor|background|bordercolor|style],tbody[id|class|style],thead[id|class|style],tfoot[id|class|style],#td[id|dir|class|colspan|rowspan|width|height|align|valign|scope|style],-th[id|dir|class|colspan|rowspan|width|height|align|valign|scope|style],caption[id|dir|class],-div[id|dir|class|align|style],-span[class|align|style],-pre[class|align|name],address[class|align],-h1[id|dir|class|align|style],-h2[id|dir|class|align|style],-h3[id|dir|class|align|style],-h4[id|dir|class|align|style],-h5[id|dir|class|align|style],-h6[id|dir|class|align|style],hr[class],dd[id|class|title|dir],dl[id|class|title|dir],dt[id|class|title|dir],@[id,style,class]');