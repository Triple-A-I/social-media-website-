<?php
include("includes/header.php");

if ($_GET['profile_username']) {
	$username = $_GET['profile_username'];
	$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
	$user_array = mysqli_fetch_array($user_details_query);

	$num_friends = (substr_count($user_array['friend_array'], ",")) - 1;
}


if (isset($_POST['remove_friend'])) {
	$user = new User($con, $userLoggedIn);
	$user->removeFriend($username);
}
if (isset($_POST['add_friend'])) {
	$user = new User($con, $userLoggedIn);
	$user->sendRequest($username);
}
if (isset($_POST['respond_request'])) {
	header("Location: requests.php");
}
?>
<style>
	.wrapper {
		padding-left: 0;
		margin-left: 0;
	}
</style>

<div class="profile_left">
	<img src="<?php echo $user_array['profile_pic']; ?>" alt="$username" style="width: 100px; height: 100px;
		border-radius: 50%;">
	<div class="profile_info">
		<p><?php echo "Posts: " . $user_array['num_posts']; ?></p>
		<p><?php echo "Likes: " . $user_array['num_likes']; ?></p>
		<p><?php echo "Friends: " . $num_friends; ?></p>
	</div>
	<form action="<?php echo $username; ?>" method="post">
		<?php
		$profile_user_obj = new User($con, $username);
		if ($profile_user_obj->isClosed()) {
			header("Location: user_closed.php");
		}

		$logged_in_user_obj = new User($con, $userLoggedIn);

		if ($userLoggedIn != $username) {
			if ($logged_in_user_obj->isFriend($username)) {
				echo '<input type="submit" name="remove_friend" class="danger" value="Remove Friend"><br>';
			} elseif ($logged_in_user_obj->didReceiveRequest($username)) {
				echo '<input type="submit" name="respond_request" class="warning" value="Remove to Request"><br>';
			} elseif ($logged_in_user_obj->didSendRequest($username)) {
				echo '<input type="submit" name="" class="default" value=" Request Sent"><br>';
			} else {
				echo '<input type="submit" name="add_friend" class="success" value="Add Friend"><br>';
			}
		}
		?>


	</form>
	<input type="submit" value="Post Something" class="deep_blue" data-toggle="modal" data-target="#post_form">

	<?php

	if ($userLoggedIn != $username) {
		echo '<div class="profile_info_bottom">';
		if( $logged_in_user_obj->getMutualFriends($username))
		echo $logged_in_user_obj->getMutualFriends($username) ." Mutual Friends";
		echo '</div>';
	}
	?>

</div>

<div class="profile_main_column column">
	<div class="posts_area"></div>
	<p style="text-align: center; ">
		<img id="loading" src="assets/images/icons/loading.gif">
	</p>
</div>




<!-- Modal -->
<div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">

			<div class="modal-header">
				<h4 class="modal-title" id="post_form">Post Something!</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<p>This will appear in the user's profile page and also their newsfeed for your friends to see!</p>

				<form action="ajax_submit_profile_post.php" class="profile_post" method="POST">
					<div class="form-group">
						<textarea name="post_body" class="form-control" rows="10"></textarea>
						<input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>">
						<input type="hidden" name="user_to" value="<?php echo $username; ?>">
					</div>
				</form>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" name="post_button" id="submit_profile_post">Post</button>
			</div>
		</div>
	</div>
</div>


<script>
	var userLoggedIn = '<?php echo $userLoggedIn; ?>';
	var profileUsername = '<?php echo $username; ?>';

	$(document).ready(function() {

		$('#loading').show();

		//Original ajax request for loading first posts 
		$.ajax({
			url: "includes/handlers/ajax_load_profile_posts.php",
			type: "POST",
			data: "page=1&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
			cache: false,

			success: function(data) {
				$('#loading').hide();
				$('.posts_area').html(data);
			}
		});

		$(window).scroll(function() {
			var height = $('.posts_area').height(); //Div containing posts
			var scroll_top = $(this).scrollTop();
			var page = $('.posts_area').find('.nextPage').val();
			var noMorePosts = $('.posts_area').find('.noMorePosts').val();

			if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
				$('#loading').show();

				var ajaxReq = $.ajax({
					url: "includes/handlers/ajax_load_profile_posts.php",
					type: "POST",
					data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
					cache: false,

					success: function(response) {
						$('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
						$('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 

						$('#loading').hide();
						$('.posts_area').append(response);
					}
				});

			} //End if 

			return false;

		}); //End (window).scroll(function())


	});
</script>
</div>
</body>

</html>