<?php
/**
 * zipCF - Create Zip File with directory content
 * Author : Abdul Awal
 * Article Url: http://go.abdulawal.com/1
 * Version: 2.0
 * Released on: February 26 2016
 * Updated On: October 26, 2019
 */

// set password
$_require_password 	= false; // Set it to true if you want to use a password to access this script. Password can be set in the next variable.
$_password 			= ''; // Enter any string as password. You'll need to use this password as value of password parameter if you enable require_password option above

if($_require_password){
	$zipcf_pass = htmlspecialchars($_REQUEST['password']);
	if(empty($zipcf_pass) || $zipcf_pass != $_password){
		die();
	}
}

function getDirItems($dir, $recursive_display = false, &$results = array()){
    $files = scandir($dir);
    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        list($unused_path, $used_path) = explode(basename(__DIR__).'/', $path);
        $file_name = $dir.DIRECTORY_SEPARATOR.$value;
        if(!is_dir($path)) {
            $results[] = $used_path;
        } else if($value != "." && $value != "..") {
        	// if recursive_display is set to true, it will display all the files inside every directory
        	if($recursive_display){
        		getDirItems($path, true, $results);
        	}
            $results[] = $value.'/';
        }
    }
    return $results;
}

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

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>ZipCF PHP 2.0 - Create a Zip with contents in the current Direcory (php script)</title>
	<style type="text/css">
		body{
			font-family: arial;
			font-size: 14px;
			padding: 0;
			margin: 0;
			text-align: left;
			padding-bottom: 50px;
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
			width:100%;
		}
		input[type="checkbox"]{
			margin: 10px 0;
		}
		label.fs-label{
			padding-left: 5px;
			font-weight: normal;
		}
		input[type="submit"]{
			padding: 10px 20px;
			display: block;
			margin: 20px auto;
			border: 2px solid green;
			background: #fff;
			width: 100%;
			font-weight: bold;
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
		<h3>ZipCF 2.0 - Make zip file with current directory! </h3>
		<form action="" method="POST">
			<label for="zip-file-name">Zip File Name</label> <br>
			<input type="text" id="zip-file-name" name="zip_file_name" value="" placeholder="Name of the zip file" />
			<p><strong>Select Items</strong></p>
			<p><input type="checkbox" id="select-all-files" value="Select All"><label for="select-all-files" class="fs-label">Select All</label></p>
			<?php
			$list_all_files_folders = getDirItems(dirname(__FILE__));

			foreach ($list_all_files_folders as $key => $value) {
				echo '<input type="checkbox" name="selected_files[]" id="file-'.$key.'" value="'.$value.'" /> <label for="file-'.$key.'" class="fs-label">'.$value.'</label><br />';
			}
			?>
			<input type="submit" value="Create Zip File" />
		</form>
		<?php
			if(isset($_POST['zip_file_name'])){
				if(!empty($_POST['zip_file_name'])){
					ini_set('max_execution_time', 10000);

					$get_name = $_POST['zip_file_name'];
					$get_ext  = '.zip';
					$final_name = $get_name.$get_ext;

					$get_selected_files = $_POST['selected_files'];

					$file_array = array();

					foreach ($get_selected_files as $key => $file_dir_name) {
						if(is_dir($file_dir_name)){
							$get_files = getDirItems($file_dir_name,true);
							foreach($get_files as $key => $file){
								$file_array[] = $file;
							}
						} else {
							$file_array[] = $file_dir_name;
						}
					}

					//if true, good; if false, zip creation failed
					$result = generate_zip_file($file_array,$final_name);
					if($result){
						echo "Successfully Created Zip file $final_name , <strong style='color: red;'>Please don't forget to either set a password at the top of ZipCF.php file or delete this file when you're done.</strong>";
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
	<script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
	<script type="text/javascript">
		$('#select-all-files').click(function(event) {   
		    if(this.checked) {
		        // Iterate each checkbox
		        $(':checkbox').each(function() {
		            this.checked = true;                        
		        });
		    } else {
		        $(':checkbox').each(function() {
		            this.checked = false;                       
		        });
		    }
		});
	</script>
</body>
</html>
