Vagrant.configure(2) do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.provision :shell, path: "vagrant/provision.sh"

  config.vm.define 'scrumonline' do |node|
    node.vm.hostname = 'scrumonline.local'
    node.vm.network :private_network, ip: '192.168.89.200'
  end

  config.vm.provider "virtualbox" do |v|
    v.memory = 2048
    v.cpus = 2
  end

  config.vm.synced_folder "./", "/vagrant", type: "nfs",  mount_options: ['rw', 'vers=3', 'tcp', 'fsc' ,'actimeo=2']

  config.hostmanager.enabled = true
  config.hostmanager.manage_host = true
end
