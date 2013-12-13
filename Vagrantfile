parameters     = File.expand_path("../VagrantParameters", __FILE__)
parametersDist = File.expand_path("../VagrantParameters.dist", __FILE__)

if File.exist?(parameters)
  load parameters
else
  load parametersDist
end

Vagrant.configure("2") do |config|
  config.vm.box = $vm_box
  config.vm.box_url = $vm_box_url

  config.vm.network :private_network, ip: $ip
  config.ssh.forward_agent = true
  config.vm.hostname = $hostname
  config.hostsupdater.aliases = ["pma." + $hostname]

  config.vm.provider :virtualbox do |v|
    v.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
    v.customize ["modifyvm", :id, "--memory", 1024]
    v.customize ["modifyvm", :id, "--name", $name]
  end

  config.vm.provider :vmware_fusion do |v|
    v.vmx["memsize"] = "1024"
  end

  nfs_setting = RUBY_PLATFORM =~ /darwin/ || RUBY_PLATFORM =~ /linux/
  config.vm.synced_folder "./", "/var/www", id: "vagrant-root" , :nfs => nfs_setting
  config.vm.provision :shell, :inline =>
    "if [[ ! -f /apt-get-run ]]; then sudo apt-get update && sudo touch /apt-get-run; fi"


  config.vm.provision :shell, :inline => 'echo -e "mysql_root_password=root
controluser_password=awesome" > /etc/phpmyadmin.facts;'

  config.vm.provision :puppet do |puppet|
    if $puppet_verbose
      puppet.options = ['--verbose', '--debug']
    end
    puppet.manifests_path = "puppet/manifests"
    puppet.module_path = "puppet/modules"
    puppet.facter = {
        "hostname" => $hostname
    }
  end

  config.vm.provision :shell, :inline => 'if [ -f /var/www/index.html ]; then rm /var/www/index.html; fi'
  config.vm.provision :shell, :inline => 'a2dissite default && /etc/init.d/apache2 reload'
end
