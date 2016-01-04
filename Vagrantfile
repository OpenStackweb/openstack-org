# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.
Vagrant.configure(2) do |config|
  # The most common configuration options are documented and commented below.
  # For a complete reference, please see the online documentation at
  # https://docs.vagrantup.com.

  # Every Vagrant development environment requires a box. You can search for
  # boxes at https://atlas.hashicorp.com/search.
  #config.vm.box = "ubuntu/trusty64"
  #config.vm.box_url = "https://atlas.hashicorp.com/ubuntu/trusty64"	
  config.vm.box = "ubuntu/vivid64"
  config.vm.box_url = "https://atlas.hashicorp.com/ubuntu/vivid64"	
  config.vm.name = "local.openstack.org"
  config.vm.hostname = "local.openstack.org"

  # Create a private network, which allows host-only access to the machine
  # using a specific IP.
  config.vm.network "private_network", ip: "192.168.33.10"
  # Create a public network, which generally matched to bridged network.
  # Bridged networks make the machine appear as another physical device on
  # your network.
  # config.vm.network "public_network"

  # Share an additional folder to the guest VM. The first argument is
  # the path on the host to the actual folder. The second argument is
  # the path on the guest to mount the folder. And the optional third
  # argument is a set of non-required options.
  # config.vm.synced_folder "../data", "/vagrant_data"
    config.vm.synced_folder("puppet/hiera", "/etc/puppet/data")
    config.vm.synced_folder("puppet/certs", "/etc/ssl_certs")
    config.vm.synced_folder("puppet", "/etc/puppet/modules/site")
    config.vm.synced_folder ".", "/var/www/local.openstack.org", create: true, owner: "vagrant", group: "www-data", mount_options: ["dmode=777,fmode=777"]
  # Provider-specific configuration so you can fine-tune various
  # backing providers for Vagrant. These expose provider-specific options.
  # Example for VirtualBox:
  #
  config.vm.provider "virtualbox" do |vb|
        vb.memory = "4096"
  end

  # use https://github.com/oscar-stack/vagrant-hosts
  # vagrant plugin install vagrant-hosts
  config.vm.provision :hosts do |provisioner|
        provisioner.add_host '127.0.0.1', ['local.openstack.org']
  end

  config.vm.provision :shell, :path => "scripts/bootstrap.sh"

  config.vm.provision :puppet do |puppet|
        puppet.manifests_path = "puppet"
        puppet.manifest_file = "site.pp"
        puppet.hiera_config_path = "puppet/hiera/hiera.yaml"
        puppet.working_directory = "/etc/puppet/data"
        # puppet.options = "--verbose --debug"
  end
  
  config.vm.provision :shell, :path => "scripts/postdeployment.sh"
end
