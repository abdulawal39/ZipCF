<?php
/**
 * zipCF - Create Zip File with directory content
 * Author : Abdul Awal
 * Article Url: http://go.abdulawal.com/1
 * Version: 1.1
 * Released on: February 26 2016
 * Updated On: July 02, 2018
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>ZipCF PHP - Create a Zip with contents in the current Direcory (php script)</title>
	<style type="text/css">
		body{
			font-family: arial;
			font-size: 14px;
			padding: 0;
			margin: 0;
			text-align: center;
		}
		h3{
			text-align: center;
		}
		.container{
			width: 600px;
			margin: 100px auto 0 auto;
			max-width: 100%;
		}
		label{
			font-weight: bold;
			margin: 10px 0;
		}
		input[type="text"]{
			border: 1px solid #eee;
			padding: 10px;
			display: block;
			margin: 10px auto;
		}
		input[type="submit"]{
			padding: 10px 20px;
			display: block;
			margin: 10px auto;
			border: 2px solid green;
			background: #fff;
		}
		.copyright{
			position: fixed;
			bottom:0;
			background: #333;
			color: #fff;
			width: 100%;
			padding: 10px 20px;
			text-align: center;
		}
		.copyright a{
			color: #eee;
		}
	</style>
</head>
<body>
	<div class="container">
		<h3>ZipCF - Make zip file with current directory!</h3>
		<form action="" method="POST">
			<label for="zip-file-name">Zip File Name</label> <br>
			<input type="text" id="zip-file-name" name="zip_file_name" value="" placeholder="Name of the zip file" />
			<input type="submit" value="Create Zip File" />
		</form>
		<?php
			if(isset($_POST['zip_file_name'])){
				if(!empty($_POST['zip_file_name'])){
					ini_set('max_execution_time', 10000);
					/* creates a compressed zip file */
					function generate_zip_file($files = array(),$destination = '',$overwrite = false) {
						//if the zip file already exists and overwrite is false, return false
						if(file_exists($destination) && !$overwrite) { return false; }
						//vars
						$valid_files = array();
						//if files were passed in...
						if(is_array($files)) {
							//cycle through each file
							foreach($files as $file) {
								//make sure the file exists
								if(file_exists($file)) {
									$valid_files[] = $file;
								}
							}
						}
						//if we have good files...
						if(count($valid_files)) {
							//create the archive
							$zip = new ZipArchive();
							if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
								return false;
							}
							//add the files
							foreach($valid_files as $file) {
								if (file_exists($file) && is_file($file)){
									$zip->addFile($file,$file);
								}	
							}
							//debug
							//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
							
							//close the zip -- done!
							$zip->close();
							
							//check to make sure the file exists
							return file_exists($destination);
						}
						else
						{
							return false;
						}
					}

					function getDirItems($dir, &$results = array()){
					    $files = scandir($dir);
					    foreach($files as $key => $value){
					        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
					        list($unused_path, $used_path) = explode(basename(__DIR__).'/', $path);
					        $file_name = $dir.DIRECTORY_SEPARATOR.$value;
					        if(!is_dir($path)) {
					            $results[] = $used_path;
					        } else if($value != "." && $value != "..") {
					            getDirItems($path, $results);
					            $results[] = $value.'/';
					        }
					    }
					    return $results;
					}
					$get_name = $_POST['zip_file_name'];
					$get_ext  = '.zip';
					$final_name = $get_name.$get_ext;
					//if true, good; if false, zip creation failed
					$result = generate_zip_file(getDirItems(dirname(__FILE__)),$final_name);
					if($result){
						echo "Successfully Created Zip file $final_name";
					} else {
						echo "Failed to create zip file, Please try again";
					}
				} else {
					echo "Please provide a name for the zip file";
				}
			}
		?>
	</div>

	<div class="copyright">Copyright &copy; <?php echo date("Y"); ?> . All rights Reserved by <a href="http://abdulawal.com/" target="_blank">Abdul Awal Uzzal</a></div>
</body>
</html>
