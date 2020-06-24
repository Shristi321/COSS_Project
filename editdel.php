 <?php
/**
*Plugin Name: COSS Abstract Submission Plugin
*Description: This is used for editing, deleting and updating the table.
*Version: 1.0
*Author: Shristi and Dr. Axvig
**/?>

<?php 
	session_start();
	$projectTitle = "";
	$projectAbstract = "";
	$id = 0;
	$update = "false";
	?>

<?php  
	

	global $wpdb; 

	if (isset($_POST['update'])) {
		$id=$_POST['update'];
		$projectTitle = $_POST['projectTitle'];
		$projectAbstract = $_POST['projectAbstract'];
		
		$wpdb->get_results("UPDATE project_info SET projectTitle='$projectTitle', projectAbstract='$projectAbstract' WHERE id=$id");	
	}


	if (isset($_POST['save'])) {
		$projectTitle = $_POST['projectTitle'];
		$projectAbstract = $_POST['projectAbstract'];
		$email=$_POST['email'];
		
		$wpdb->get_results("INSERT INTO project_info (email, projectTitle, projectAbstract) VALUES ('$email', '$projectTitle', '$projectAbstract')");		
	}

	?>


<?php 

	function display_table(){
		global $wpdb;
		global $current_user;
		var_dump($current_user);
		echo 'space here';
		var_dump($current_user->user_email);

		if (isset($_POST['edit'])){
			$id = $_POST['edit'];
			$_POST['update']= "true";//$update=true;
			$rec = $wpdb->get_results("SELECT * FROM project_info WHERE id=$id");	

			//Send information to $_POST
			$_POST = array_merge($_POST,$rec);	
		}

		if(isset($_POST['delete'])){
			$wpdb->delete( 'project_info', 
				array(
					'id'=>$_POST['delete']
				)
			);
		}


		$results = $wpdb->get_results(" SELECT * FROM project_info WHERE email='$current_user->user_email'");
		?>
		<form method="post" action="?page_id=530">
		<table>	
				<tr>
					<th>Project Title</th>
					<th>Project Abstract</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>

		<?php  
		foreach ($results as $obj)
		{?>

		<tr>
			<td><?php  echo $obj->projectTitle;?> </td>
			<td> <?php echo $obj->projectAbstract;  ?> </td>
			<td>  
				<a><button name="edit" value= "<?php echo $obj->id; ?>">Edit</button></a>
			</td>
			<td>
				<a><button name="delete" value= "<?php echo $obj->id; ?>">Delete</button></a>
			</td>
		</tr>

		<?php 
		}
		?>
		</table>
		</form>
		<?php  
		
	}
	add_shortcode('project_information_table','display_table');



/*project_information FORM */

	function project_information_form()
	{
		global $current_user; wp_get_current_user();
		echo 'Username: ' . $current_user->user_login . "\n";echo"<br>";
		echo 'User display name: ' . $current_user->display_name;echo"<br>";

		global $current_user;
		get_currentuserinfo();

		$email = $current_user->user_email;
		echo($email);


		?>
		<form method="post" action="?page_id=530">
			<input type="hidden" name="id" value="<?php echo $id; ?>">
			<label>Email</label><br>
			<input type="text" name="email" value="<?php echo $email ?>" placeholder="<?php echo $email; ?>"><br>

			<label>Project Title</label><br>
			<input type="text" name="projectTitle" value="<?php echo $_POST[0]->projectTitle;?>" placeholder="Project Title"><br>
			<label>Project Abstract</label><br>

			<input type="text" name="projectAbstract" value="<?php echo $_POST[0]->projectAbstract;?>" placeholder="Project Abstract"><br>

			<!--  <textarea name='projectAbstract' placeholder="Your Abstract"> <?php //echo $_POST[0]->projectAbstract;?> </textarea><br>  -->

		<div>
			<?php if ($_POST['update'] == "true"): ?>

				<button type="submit" name="update" value="<?php echo $_POST[0]->id; ?>">Update</button>
			<?php else: ?>
				<button  type="submit" name="save">Save</button>
			<?php endif ?>
		</div>
		</form>
	
		<?php  

	}
	add_shortcode('project_information_contact_form','project_information_form');
