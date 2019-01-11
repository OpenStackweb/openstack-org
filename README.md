# OpenStack Foundation Website

## What is it?

This project includes the code that powers the openstack.org website, which is itself powered by a PHP web application called Silverstripe, and we've made several customizations to meet the specific needs of OpenStack. More about the Silverstripe CMS is available here: http://silverstripe.org/

This repository is designed to help other project members develop, test, and contribute to the openstack.org website project, or to build other websites. Note that this project is based on ther code that powers the public openstack.org website , not the openstack cloud software itself. To participate in building the actual OpenStack cloud platform software, go to: http://wiki.openstack.org/HowToContribute

## Why release the source?

The OpenStack.org website helps promote OpenStack, the open source cloud computing platform. We felt it only make sense to share the code that powers our website so that other open source projects might benefit, and so that developers in our community might help us improve the code that powers the website dedicated to promoting their favourite open source project.

## What's not included

Images - You'll note many missing images throughout the site. This is due to one of the following: trademark restrictions (see above), copyright restrictions, OpenStack sponsors, file size restrictions.

## A reminder on trademarks

In light of the trademarks held by the OpenStack Foundation, it is important that you not use the code to build a website or webpage that could be confused with the openstack.org website, including by building a site or page with the same look and feel of the openstack.org site or by using trademarks that are the same as or similar to marks found on the openstack.org site. For the rules regarding other uses of OpenStack trademarks, see the OpenStack Trademark Policy http://www.openstack.org/brand/openstack-trademark-policy/ and the OpenStack Brand Guide http://www.openstack.org/brand/. Please contact logo@openstack.org with any questions.

## License

Unless otherwise noted, all code is released under the APACHE 2.0 License http://www.apache.org/licenses/LICENSE-2.0.html

## Installation and further documentation

Detailed installation instructions for a virtual machine environment using Vagrant are located at:
[Vagrant virtual machine installation](./installation.md) 

Information for installation to a local machine environment can be found at:
<http://openstackweb.github.io/openstack-org/>

Information on publishing a page on OpenStackweb and working with the development environment can be found at:
[Publishing a new web page](./publishing.md)

## Cloud storage

assets folder is now using cloud storage (swift object storage)

configuration file for this should be located here

openstack/_config/cloudassets.yml

and with following content 


* https://docs.openstack.org/keystone/rocky/user/application_credentials.html


```yaml

---
name: assetsconfig
---
CloudAssets:
  map:
    'assets':
      Type: SwiftBucket
      BaseURL: 'http://yourcdnbaseurl.com/'
      Container: site-uploads
      Region: Region Name
      ApplicationCredentialId: application credential id
      ApplicationCredentialSecret: application credential secret
      ProjectName: your project name
      AuthURL: keystone base url 
      LocalCopy: false     
````
