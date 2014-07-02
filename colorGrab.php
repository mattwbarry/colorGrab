<?php

include 'sampleImage.php';

//error_reporting(0);
set_error_handler("errorHandler");

function errorHandler($errno, $errstr) {
	echo 'Uh oh ... something went wrong. Check the url you entered.';
	die();
}

//delete image from previus grab
if (is_file('tmpPictureOrig.jpg')) {
	unlink('tmpPictureOrig.jpg');
}
if (is_file('tmpPictureOrig.jpeg')) {
	unlink('tmpPictureOrig.jpeg');
}
if (is_file('tmpPictureOrig.gif')) {
	unlink('tmpPictureOrig.gif');
}
if (is_file('tmpPictureOrig.png')) {
	unlink('tmpPictureOrig.png');
}

//arrays with acceptable file extensions/types -- default validations set to false
$acceptable_ext = array('.jpg', '.jpeg', '.gif', '.png');
$acceptable_type = array('image/jpeg', 'image/gif', 'image/png');
$acceptable_mime = array('image/jpeg', 'image/gif', 'image/png', 'text/plain');
$validated_ext = 0;
$validated_type = 0;
$validated_mime = 0;

//get the image
$file = $_POST['filename'];

$info = getimagesize($_POST['filename']);
$file_info = pathinfo($_POST['filename']);
//get the full url
$file_name = $file_info['basename'];
$file_type = $info['mime'];
$file_ext = '.' . $file_info['extension'];
$mime = $info['mime'];
$mime = explode('/', $mime);
$mime = 'image/' . end($mime);

//validate extension
if(in_array($file_ext, $acceptable_ext)) {
	$validated_ext = 1;
}
//validate type
if(in_array($file_type, $acceptable_type)) {
	$validated_type = 1;
}
//validate mime type
if(in_array($mime, $acceptable_mime)) {
	$validated_mime = 1;
}
//get image type
if ($file_ext == '.jpg' || $file_ext == '.jpeg') {
	$file_type = IMAGETYPE_JPEG;
}
elseif ($file_ext == '.png') {
	$file_type = IMAGETYPE_PNG;
}
elseif ($file_ext == '.gif') {
	$file_type = IMAGETYPE_GIF;
}
else {
	trigger_error('hmm something seems wrong here ... make sure the image is a jpeg, png or gif');
}
//make sure everything passed validation
if($validated_ext && $validated_type && $validated_mime) {

	//set vars to hold current x and y pos within block
	$x = 0;
	$y = 0;

	//create image
	$image = new SampleImage();
	$image->load($file);
	//save image
	$image->save('tmpPictureOrig' . $file_ext, $file_type);
	//get image width / height
	$imageHeight = $image->getHeight();
	$imageWidth = $image->getWidth();

	//set var to hold block size
	if ($_POST['splitByRC'] == 'true') {
		$blockSizeX = $imageWidth / $_POST['numCols'];
		$blockSizeY = $imageHeight / $_POST['numRows'];
	}
	elseif ($_POST['splitByBlock'] == 'true') {
		$blockSizeX = $_POST['blockSize'];
		$blockSizeY = $_POST['blockSize'];
	}

	//resize image
	$image->resize(round($imageWidth / $blockSizeX), round($imageHeight / $blockSizeY));
	$image->save('tmpPictureSmall' . $file_ext, $file_type);

	//create image object
	if ($file_ext == '.jpg' || $file_ext == '.jpeg') {
		$imageTMP = imagecreatefromjpeg('tmpPictureSmall' . $file_ext);
	}
	elseif ($file_ext == '.png') {
		$imageTMP = imagecreatefrompng('tmpPictureSmall' . $file_ext);
	}
	elseif ($file_ext == '.gif') {
		$imageTMP = imagecreatefromgif('tmpPictureSmall' . $file_ext);
	}
	else {
		trigger_error('hmm something\'s wrong here ... make sure the image is a jpeg, png or gif');
	}

	//set array to hold colors of row
	while ($y < $image->getHeight()) {

		echo '<div class="colorRow">';

		while ($x < $image->getWidth()) {

			$color = imagecolorat($imageTMP, $x, $y);
			$colors = imagecolorsforindex($imageTMP, $color);

			//change from decimal to hexidecimal
			$red = dechex($colors['red']);
			if (strlen($red) < 2) {
				$red = '0'.$red;
			}

			$green = dechex($colors['green']);
			if (strlen($green) < 2) {
				$green = '0'.$green;
			}

			$blue = dechex($colors['blue']);
			if (strlen($blue) < 2) {
				$blue = '0'.$blue;
			}

			$colorFin = $red . $green . $blue;

			echo '<span class="colorBlock" data-color="' . $colorFin . '" style="display: inline-block; width: ' . $blockSizeX . 'px; height: ' . $blockSizeY . 'px; background: #' . $colorFin . ';"></span>';

			$x++;
		}

		echo '</div>';

		$x = 0;
		$y++;

	}

	echo '<img src="tmpPictureOrig' . $file_ext . '" id="tmpPictureOrig" /><br />';

}
	//delete the temp file
	unlink('tmpPictureSmall' . $file_ext);

?>
