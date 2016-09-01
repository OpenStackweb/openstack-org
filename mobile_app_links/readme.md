NGINX configuration

location ^~ /.well-known/ {
	try_files $uri /framework/main.php?url=$uri&$query_string;
}


APACHE2 configuration

