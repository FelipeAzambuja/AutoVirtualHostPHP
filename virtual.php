<?php
chdir("/var/www");
$hosts = file_get_contents("/etc/hosts");
$current_hosts = explode('# FIX #',$hosts)[0];
$current_hosts .= '# FIX #'.PHP_EOL;
$vhosts = '';
foreach(glob('*') as $value){
	if(is_dir($value)){
		echo $value.PHP_EOL;
		exec("chmod 777 -R /var/www/".$value);
		$current_hosts .= '127.0.0.1    '.$value.'.test'.PHP_EOL; 
		ob_start();
		?>

<VirtualHost *:80>
	DocumentRoot /var/www/<?=$value?>	
	ServerName <?=$value?>.test
	ServerAlias *.<?=$value?>.test	
	<Directory /var/www/<?=$value?>>
		AllowOverride All
		Require all granted
	</Directory>
	ErrorLog /var/www/<?=$value?>/error.log
</VirtualHost>
<VirtualHost *:443>
	DocumentRoot /var/www/<?=$value?>	
	ServerName <?=$value?>.test
	ServerAlias *.<?=$value?>.test	
	<Directory /var/www/<?=$value?>>
		AllowOverride All
		Require all granted
	</Directory>
	ErrorLog /var/www/<?=$value?>/error.log
	SSLEngine on
	SSLCertificateFile /etc/ssl/certs/ssl-cert-snakeoil.pem
	SSLCertificateKeyFile /etc/ssl/private/ssl-cert-snakeoil.key
</VirtualHost>
		<?php
		$vhosts .= ob_get_clean();
	}
}
file_put_contents("/etc/hosts",$current_hosts);
file_put_contents("/etc/apache2/sites-enabled/999-virtual.conf",$vhosts);
exec("a2enmod rewrite");
exec("a2enmod ssl");
exec("service apache2 reload");

//echo $current_hosts;
//echo $vhosts;
