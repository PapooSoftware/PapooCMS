<?php
require_once("./all_inc.php");
if ($user->check_access(1)) {
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo $content->template['system_video_auswaehlen'] ?></title>
		<script language="javascript" type="text/javascript" src="./tiny_mce/tiny_mce_popup.js">


		</script>
		<script language="javascript" type="text/javascript" src="./tiny_mce/utils/mctabs.js"></script>
		<script src='js/jquery-1.3.2.min.js' type='text/javascript'></script>

		<script src='js/jquery-ui-1.7.2.custom.min.js' type='text/javascript'></script>
		<script src='js/jquery.cookie.js' type='text/javascript'></script>

		<style type="text/css">
			@import url(./css/image_list.css);
		</style>

		<script language="javascript" type="text/javascript">

			var FileBrowserDialogue = {
				init: function () {
					// Here goes your code for setting your custom things onLoad.
				},
				mySubmit: function (url, rel, title) {
					var URL = url;
					var REL = rel;
					var win = tinyMCEPopup.getWindowArg("window");
					// insert information now
					win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = URL;

					//win.document.getElementById("rel").value = REL;
					//alert(win.document.src);
					win.updatePreview();

					// for image browsers: update image dimensions
					//if (win.ImageDialog.getImageData) win.ImageDialog.getImageData();
					//if (win.ImageDialog.updatePreview) win.ImageDialog.updatePreview();

					// close popup window
					tinyMCEPopup.close();
				}
			}

			tinyMCEPopup.onInit.add(FileBrowserDialogue.init, FileBrowserDialogue);

		</script>
		<base target="_self"/>
	</head>
	<body>
	<div class="background">
		<div class="tabs">
			<ul>
				<li id="baum_tab" <?php if (empty($checked->reiter_active)) {
					echo 'class="current"';
				} ?>><span><a href="javascript:mcTabs.displayTab('baum_tab','baum_panel');"
							  onmousedown="return false;"><?php echo $content->template['system_video_auswaehlen'] ?></a></span>
				</li>
				<li id="mp3_tab" <?php if (isset($checked->reiter_active) && $checked->reiter_active == "mp3") {
					echo 'class="current"';
				} ?>><span><a href="javascript:mcTabs.displayTab('mp3_tab','mp3_panel');"
							  onmousedown="return false;"><?php echo $content->template['system_mp3_auswaehlen'] ?></a></span>
				</li>

			</ul>
		</div>

		<div class="panel_wrapper">
			<div id="baum_panel" class="panel <?php if (empty($checked->reiter_active)) {
				echo 'current';
			} ?>">
				<div class="image_folder_container_complete">
					<script src="./js/jq_content_tree.js" type="text/javascript"></script>
					<?php
					$video_class->do_video();
					$video_class->nur_flv = 1;
					$video_class->do_change();
					//Liste der Videos rausholen
					$content->assign();
					echo $content->template['system_video_auswaehlen_text'];

					// templates parsen
					$output = $smarty->fetch("sub_templates/video_tiny.html");
					print_r($output);
					?>
				</div>
			</div>

			<div id="mp3_panel" class="panel <?php if (isset($checked->reiter_active) && $checked->reiter_active == "mp3") {
				echo 'current';
			} ?>">
				<?php
				$intern_upload->nur_mp3 = 1;
				$intern_upload->change_upload();
				$intern_upload->get_cat_list();
				$content->assign();
				echo $content->template['system_link_seitenbaum_text'];

				// templates parsen
				$output = $smarty->fetch("sub_templates/mp3_tiny.html");
				print_r($output);
				?>
			</div>
		</div>
	</div>

	</body>
	</html>
<?php } ?>