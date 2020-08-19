<?php
require_once("./all_inc.php");

$intern_image->change_liste();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{#advimage_dlg.dialog_title}</title>
	<script language="javascript" type="text/javascript" src="./tiny_mce/tiny_mce_popup.js">
	</script>
	<style type="text/css">
		@import url(./css/image_list.css);
	</style>

	<script language="javascript" type="text/javascript">

		var FileBrowserDialogue = {
			init: function () {
				// Here goes your code for setting your custom things onLoad.
			},
			mySubmit: function (url, alt, title) {
				var URL = url;
				var win = tinyMCEPopup.getWindowArg("window");

				// insert information now
				win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = URL;
				win.document.getElementById("alt").value = alt;
				win.document.getElementById("title").value = title;

				// for image browsers: update image dimensions
				if (win.ImageDialog.getImageData) win.ImageDialog.getImageData();
				if (win.ImageDialog.showPreviewImage) win.ImageDialog.showPreviewImage(URL);

				// close popup window
				tinyMCEPopup.close();
			}
		}

		tinyMCEPopup.onInit.add(FileBrowserDialogue.init, FileBrowserDialogue);

	</script>
	<base target="_self"/>
</head>
<body>
<div class="bilder_liste">
	<div class="image_folder_container">
		<?php

		if ($checked->image_dir < 1) {
			foreach ($content->template['result_cat'] as $dat) {
				echo "<div class=\"image_folder\">";
				echo '<a href="./images_liste.php?image_dir=' . $dat['bilder_cat_id'] . '">' . $dat['bilder_cat_name'] . '</a>';
				echo "</div>";
			}
		}

		?>
	</div>
	<div class="image_images">
		<ul class="die_image_liste">
			<?php
			//$this->content->template['image_data']
			if ($checked->image_dir >= 1) {
				echo '<div style="width: 140px; padding: 5px;float:left;">
			<a href="./images_liste.php" class="image_link"><img src="bilder/folder.png" style="border:none;background:#fff;" alt="' . $content->template['message_395'] . '" title="' . $content->template['$message_395'] . '" />
		
			' . $content->template['message_395'] . '<br /></a>
		</div>';

			}
			foreach ($content->template['image_data'] as $dat) {
				$thumbs = "";
				if ($dat['image_width'] >= 160) {
					$thumbs = "thumbs/";
				}
				echo '<li><div style="padding: 5px; width: 140px;">';
				echo '<a href="#"><img src="../images/' . $thumbs . $dat['image_name'] . '" alt="' . $dat['image_alt'] . '"  onClick="FileBrowserDialogue.mySubmit(\'./images/' . $dat['image_name'] . '\',\'' . $dat['image_alt'] . '\',\'' . $dat['image_title'] . '\');" /></a><br />	';
				echo $dat['image_alt'] . "<br />(" . $dat['image_height'] . "x" . $dat['image_width'] . " px)";
				echo "</div></li>";
			}

			//$this->content->template['image_data']
			?>
		</ul>
	</div>
</div>
</body>
</html>