# Class: provision::nginx::vhosts
#
#
class provision::nginx::vhosts
{
  $sites_dir = $provision::params::sites_dir
  $nginx_dir = "${provision::params::templates_dir}/nginx"

  nginx::vhost { "default":
    root     => "${sites_dir}/php/Info",
    index    => "index.php",
    template => "${nginx_dir}/default.conf.erb"
  }

  nginx::vhost { "searchreplace.matyotools.darkwood":
      root     => "${sites_dir}/php/SearchReplace",
      file     => "darkwood.matyotools.searchreplace",
      index    => "index.php",
      template => "${nginx_dir}/darkwood.matyotools.searchreplace.conf.erb"
    }
}
