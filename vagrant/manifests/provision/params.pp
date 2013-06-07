# Class: provision::params
#
#
class provision::params
{
  # Puppet variables
  $puppet_dir     = "/vagrant/vagrant"
  $templates_dir  = "$puppet_dir/templates"

  # Webserver variables
  $sites_dir      = "/vagrant"
  $fpm_listen     = "/var/run/php5-fpm.sock"
  $nginx_template = "nginx/vhost.php.conf.erb"

  # Database variables
  $dbuser         = "root"
  $dbpassword     = "vagrant"
  $dbconfig       = "/etc/.puppet.cnf"
}
