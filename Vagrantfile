# -*- mode: ruby -*-
# vi: set ft=ruby :
# https://github.com/DevNIX/Vagrant-dependency-manager
VAGRANTFILE_API_VERSION = "2"

#configuration constants
MYSQL_SERVICE_PROVIDER = ENV["MYSQL_SERVICE_PROVIDER"] || "upstart"
USE_SWAP               = ENV["USE_SWAP"] || 1
SERVER_NAME            = ENV["SERVER_NAME"] || "local.openstack.org"

required_plugins = %w( vagrant-vbguest vagrant-hosts vagrant-hostsupdater )
require File.dirname(__FILE__)+"/scripts/dependency_manager"
check_plugins required_plugins

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  # The most common configuration options are documented and commented below.
  # For a complete reference, please see the online documentation at
  # https://docs.vagrantup.com.

  # Every Vagrant development environment requires a box. You can search for
  # boxes at https://atlas.hashicorp.com/search.
  config.vm.hostname = SERVER_NAME

  # Share an additional folder to the guest VM. The first argument is
  # the path on the host to the actual folder. The second argument is
  # the path on the guest to mount the folder. And the optional third
  # argument is a set of non-required options.
  # config.vm.synced_folder "../data", "/vagrant_data"
    config.vm.synced_folder("puppet/hiera", "/etc/puppet/data")
    config.vm.synced_folder("puppet/certs", "/etc/ssl_certs")	
    config.vm.synced_folder("puppet", "/etc/puppet/modules/site")
    config.vm.synced_folder ".", "/var/www/www.openstack.org", create: true, owner: "vagrant", group: "www-data", mount_options: ["dmode=777,fmode=777"]

  # virtualbox provider
  config.vm.provider "virtualbox" do |vb, override|
     vb.memory           = "2048"
     vb.name             = SERVER_NAME
	 vb.cpus             = 1
	 override.vm.box     = "ubuntu/trusty64"
     override.vm.box_url = "https://atlas.hashicorp.com/ubuntu/trusty64"
     override.vm.network "forwarded_port", guest: 3306, host: 3306
     override.vm.network "private_network", ip: "192.168.33.10"
  end

  # docker provider
  config.vm.provider "docker" do |d, override|
      d.build_dir = "."
      d.name = SERVER_NAME
      d.has_ssh = true
      config.vm.network :forwarded_port, host: 80, guest: 80 #web
  end

  # use https://github.com/oscar-stack/vagrant-hosts
  # vagrant plugin install vagrant-hosts
  
  config.vm.provision :hosts do |provisioner|
      provisioner.add_host '127.0.0.1', [SERVER_NAME]
  end

  config.vm.provision "bootstrap", type:"shell" do |s|
      s.path = "scripts/bootstrap.sh"
  end

  config.vm.provision "puppetbuild", type: "puppet" do |puppet|
      puppet.manifests_path = "puppet"
      puppet.manifest_file = "site.pp"
      puppet.hiera_config_path = "puppet/hiera/hiera.yaml"
      puppet.working_directory = "/etc/puppet/data"
      puppet.facter   = {
                            "mysql_service_provider" => MYSQL_SERVICE_PROVIDER,
                            "use_swap"               => USE_SWAP,
                            "server_name"            => SERVER_NAME
                        }
      #puppet.options = "--verbose --debug"
  end
  
  config.vm.provision "postbuild", type:"shell" do |s|
      s.path = "scripts/postdeployment.sh"
  end

  config.vm.provision "update", type:"shell" do |s|
      s.path = "scripts/updatedeployment.sh"
  end

end
