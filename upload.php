<?php
include("includes/header.php");


$profile_id = $user['username'];




if (isset($_POST['Submit'])) {
	$Image          = $_FILES["Image"]["name"];
	$Target         = "assets/images/profile_pics/" . basename($Image);
	$sql  = mysqli_query($con, "UPDATE users SET profile_pic='$Target' WHERE username='$profile_id'");
	move_uploaded_file($_FILES["Image"]["tmp_name"], $Target);
}
?>
<div id="Overlay" style=" width:100%; height:100%; border:0px #990000 solid; position:absolute; top:0px; left:0px; z-index:2000; display:none;"></div>
<div class="main_column column">


	<div id="formExample">

		<p><b> <? echo "IMAGE : $Image"; ?> </b></p>

		<form action="upload.php" method="post" enctype="multipart/form-data">
			Upload something<br /><br />
			<div class="custom-file">
				<input type="File" class="custom-file-input" name="Image" id="imageSelect" value="">
			</div> <input type="submit" value="Submit" name="Submit" style="width:85px; height:25px;" />
		</form><br /><br />

	</div> <!-- Form-->

</div>