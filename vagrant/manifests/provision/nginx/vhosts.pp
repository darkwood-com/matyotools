# Class: provision::nginx::vhosts
#
#
class provision::nginx::vhosts
{
  $sites_dir = $provision::params::sites_dir
  $nginx_dir = "${provision::params::templates_dir}/nginx"

  nginx::vhost { "phpinfo.matyotools.dev":
    root     => "${sites_dir}/php/Info",
    index    => "index.php",
    template => "${nginx_dir}/default.conf.erb"
  }

  nginx::vhost { "phpmyadmin.matyotools.dev":
    root     => "/usr/share/phpmyadmin",
    index    => "index.php",
    template => "${nginx_dir}/default.conf.erb"
  }

  nginx::vhost { "searchreplace.matyotools.dev":
    root     => "${sites_dir}/php/SearchReplace",
    file     => "matyotools.searchreplace.dev",
    index    => "index.php",
    template => "${nginx_dir}/matyotools.searchreplace.dev.conf.erb"
  }
}
