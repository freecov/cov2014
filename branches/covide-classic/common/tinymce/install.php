#!/usr/bin/php5
<?php
	function logx($msg) {
		echo sprintf("- %s ...\n", $msg);
	}


	logx('bootstrap tinymce installer');

	// read config
	
	logx('read config file [config.ini]');
	$config = array();
	foreach ( explode("\n", file_get_contents('config.ini')) as $line) {
		$var = explode('=', $line);
		if (count($var) > 1) {
			$config[ array_shift($var) ] = implode('=', $var) ;
		}
	}
	logx('downloading required files');
	foreach ($config as $field => $value) {
		if (in_array($field, array('tinymce', 'compressor', 'spellchecker'))) {
			$fname = sprintf('tmp/%s.zip', $field);
			if (!file_exists($fname) || filesize($fname) == 0) {
				logx(sprintf('+ downloading [%s]', $field));
				$cmd = sprintf("wget '%s' -O tmp/%s.zip -o tmp/log", $value, $field);
				exec($cmd);
			}
		}
	}
	// tinymce
	logx('unpack [tinymce]');
	$cmd = 'cd tmp/ && rm tinymce -rf && mkdir tinymce && cd tinymce && unzip ../tinymce.zip';
	exec($cmd);

	// spellchecker
	logx('+ unpack and install [spellchecker]');
	$cmd = 'cd tmp/tinymce/jscripts/tiny_mce/plugins/ && rm spellchecker -rf && unzip ../../../../spellchecker.zip';
	exec($cmd);

	// compressor
	logx('+ unpack and install [compressor]');
	$cmd = 'cd tmp/ && rm tinymce_compressor_php -rf && unzip compressor.zip && cp tinymce_compressor_php/tiny_mce_gzip.* tinymce/jscripts/tiny_mce/';
	exec($cmd);

	// copy over local files
	logx('replace plugins files');

	$cmd = 'cp local/link.htm tmp/tinymce/jscripts/tiny_mce/plugins/advlink/';
	exec($cmd);

	$cmd = 'cp local/image.htm tmp/tinymce/jscripts/tiny_mce/plugins/advimage/';
	exec($cmd);

	$cmd = 'cp local/media.htm tmp/tinymce/jscripts/tiny_mce/plugins/media/';
	exec($cmd);
	
	$cmd = 'cp local/config.php tmp/tinymce/jscripts/tiny_mce/plugins/spellchecker/';
	exec($cmd);

	// modify editor files
	logx('patching editor plugins');
	$dirs = array('advlink', 'advimage', 'media');
	foreach($dirs as $dir) {
		$file = sprintf('tmp/tinymce/jscripts/tiny_mce/plugins/%s/editor_plugin.js', $dir);
		$data = file_get_contents($file);
		$data = preg_replace('/width:\d{3}/s', 'width:780', $data);
		file_put_contents($file, $data);
	}

	// language pack
	logx('install translations');
	logx('+ patching translations');
	$cmd = 'cd tmp/tinymce/jscripts/tiny_mce/ && unzip -o ../../../../local/langpack.zip';
	// move to target dir
	logx('install to target directory');
	$target = sprintf('../../tinymce%d', $config['version']);

	if (file_exists($target)) {
		$cmd = sprintf('rm %s -rf', $target);
		exec($cmd);
	}

	$cmd = sprintf('mv -f tmp/tinymce %s && rm tmp/* -rf', $target);
	exec($cmd);

	logx(sprintf('installed to [%s]', $target));
	exit;
?>
