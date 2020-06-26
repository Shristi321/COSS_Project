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
	$button_type = "";
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
		// $user_id=$current_user->ID;
		// $projectId=$_POST['save'];
		$projectTitle = $_POST['projectTitle'];
		$projectAbstract = $_POST['projectAbstract'];
		$email=$_POST['email'];
		
		$wpdb->get_results("INSERT INTO project_info (email, projectTitle, projectAbstract) VALUES ('$email', '$projectTitle', '$projectAbstract')");

		$lastID=$wpdb->get_results("SELECT LAST_INSERT_ID()");
		$lastIDarray = (array) $lastID[0];
		$stuff = array_values($lastIDarray);
		$projectId = (int) $stuff[0];
		//this last chunk fetches the id of the most recently added project, but ini a super janky way.  FIND A BETTER WAY!
		 // $projectId = $projectId[0]->LAST_INSERT_ID();
		
		$wpdb->get_results("INSERT INTO user_info (project_id, email) VALUES ('$projectId', '$email')");
			
	}

	if (isset($_POST['cancel'])) {
		header('location: ?page_id=571'); 
	}

	if (isset($_POST['confirm'])) {
		
		$projectId=$_POST['confirm'];
		$useremail=$_POST['collab_email'];

		$wpdb->get_results("INSERT INTO user_info (project_id, email) VALUES ('$projectId', '$useremail')");

		//header('location: ?page_id=571'); 
	}


	?>


<?php 

	function display_table(){
		global $wpdb;
		global $current_user;
		if (isset($_POST['edit'])){
			$id = $_POST['edit'];

			$_POST['button_type']= "edit";//$update=true;
			// $_POST['collaborator']= "true";
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

			$wpdb->delete( 'user_info', 
				array(
					'project_id'=>$_POST['delete']
				)
			);

			header('location: ?page_id=571'); 
		}


		if (isset($_POST['add'])) {
			
			$id = $_POST['add'];
			$_POST['button_type']= "add";
			$projectTitle=$_POST['projectTitle'];
			$add = $wpdb->get_results("SELECT * FROM project_info WHERE id=$id");
			$_POST = array_merge($_POST,$add);


		// header('location: ?page_id=582'); 	
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
					<th>Add Collaborators</th>
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
			<td>
				<a><button name="add" value= "<?php echo $obj->id; ?>">Add</button></a>
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
		global $current_user; 
		$email = $current_user->user_email;

		?>
		<form method="post" action="?page_id=571">
			<input type="hidden" name="id" value="<?php echo $id; ?>">

			<?php if ($_POST['button_type'] == "add"): ?>

			<label>Email</label><br>
			<input type="text" name="email" value="<?php echo $email ?>" placeholder="<?php echo $email; ?>"><br>

			<label>Project Title</label><br>
			<input type="text" name="projectTitle" value="<?php echo $_POST[0]->projectTitle;?>" placeholder="Project Title"><br>

			<label>Collaborator's Email</label><br>
			<input type="text" name="collab_email" placeholder="Collaborator's Email"><br>

			<button type="submit" name="confirm" value="<?php echo $_POST[0]->id; ?>">Confirm</button>
			<button type="submit" name="cancel" value="<?php echo $_POST[0]->id; ?>">Cancel</button>

			<?php else: ?>

			<label>Email</label><br>
			<input type="text" name="email" value="<?php echo $email ?>" placeholder="<?php echo $email; ?>"><br>

			<label>Project Title</label><br>
			<input type="text" name="projectTitle" value="<?php echo $_POST[0]->projectTitle;?>" placeholder="Project Title"><br>

			<label>Project Abstract</label><br>
			<input type="text" name="projectAbstract" value="<?php echo $_POST[0]->projectAbstract;?>" placeholder="Project Abstract"><br>


			<!--  <textarea name='projectAbstract' placeholder="Your Abstract"> <?php //echo $_POST[0]->projectAbstract;?> </textarea><br>  -->

		
			<?php if ($_POST['button_type'] == "edit"): ?>

				<button type="submit" name="update" value="<?php echo $_POST[0]->id; ?>">Update</button>
				<button type="submit" name="cancel" value="<?php echo $_POST[0]->id; ?>">Cancel</button>

			<?php else: ?>
				<button type="submit" name="save" value="<?php echo $_POST[0]->id; ?>">Save</button>
			<?php endif ?>
			<?php endif ?>
		
		</form>
	
		<?php  

	}
	add_shortcode('project_information_contact_form','project_information_form');


	function add_new_project(){
		?>
		<a href="http://localhost/codeanddesign/?page_id=573"><button>Add New Project</button></a>
		<?php 
	}
	add_shortcode('add_project_button','add_new_project');


	

	?>



