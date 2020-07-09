 <?php
/**
*Plugin Name: COSS Abstract Submission Plugin
*Description: This is used for editing, deleting and updating the table.
*Version: 1.0
*Author: Shristi and Dr. Axvig
**/?>

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
<link href="css/styles.css" rel="stylesheet" type="text/css"> 

<?php 
	session_start();
	$projectTitle = "";
	$projectAbstract = "";
	$noOfStudents=0;
	$noOfMentors=0;
	$id = 0;
	$button_type = "";
	$check="";
	?>

<?php  
	global $wpdb;
	global $current_user;

	if (isset($_POST['update'])) {
		$id=$_POST['update'];
		$projectTitle = $_POST['projectTitle'];
		$projectAbstract = $_POST['projectAbstract'];
		$noOfStudents = $wpdb->get_var(" SELECT COUNT(email) from user_information WHERE project_id=$id ");
		$noOfMentors = $_POST['noOfMentors'];
		$wpdb->get_results("UPDATE project_information SET projectTitle='$projectTitle', projectAbstract='$projectAbstract', noOfStudents='$noOfStudents', noOfMentors=$noOfMentors WHERE id=$id");	
	}

	if (isset($_POST['save'])) {
		$projectTitle = $_POST['projectTitle'];
		$projectAbstract = $_POST['projectAbstract'];
		$noOfStudents = $_POST['noOfStudents'];
		$noOfMentors = $_POST['noOfMentors'];
		$email=$_POST['email'];
		$wpdb->get_results("INSERT INTO project_information (email, projectTitle, projectAbstract, noOfStudents, noOfMentors) VALUES ('$email', '$projectTitle', '$projectAbstract', '$noOfStudents','$noOfMentors')");

		$lastID=$wpdb->get_results("SELECT LAST_INSERT_ID()");
		$lastIDarray = (array) $lastID[0];
		$stuff = array_values($lastIDarray);
		$projectId = (int) $stuff[0];
		$wpdb->get_results("INSERT INTO user_information (project_id, email) VALUES ('$projectId', '$email')");	
	}

	if (isset($_POST['cancel'])) {
		header('location: http://localhost/mainclonetwo/table/'); //table
	}

	if (isset($_POST['confirm'])) {
		$wpdb->query( "START TRANSACTION" );
		$projectId=$_POST['confirm'];
		$useremail=$_POST['collab_email'];
		
		$check_user_input=$wpdb->get_results("SELECT email FROM user_information WHERE project_id=$projectId AND email='$useremail'");

		 if($check_user_input){
		 	echo "the user is already listed";
		 	$wpdb->query( "ROLLBACK" );
		 }
		 else{
		 	$wpdb->get_results("INSERT IGNORE INTO user_information (project_id, email) VALUES ('$projectId', '$useremail')");

		 	$wpdb->query( "COMMIT" );
		 }	
	}

	if(isset($_POST['confirmDel'])){
		
		$wpdb->delete( 'project_information', 
			array(
				'id'=>$_POST['confirmDel']
			)
		);
		$wpdb->delete( 'user_information', 
			array(
				'project_id'=>$_POST['confirmDel'] 
			) 
		);
		header('location: http://localhost/mainclonetwo/table/'); //table
	}

	if(isset($_POST['remove'])){
		$wpdb->delete( 'user_information', 
			array(
				'email'=>$_POST['remove']
			)
		);
		header('location: http://localhost/mainclonetwo/table/'); //table
	}
	?>

<?php 
	function display_table(){
		global $wpdb;
		global $current_user;
		global $column;

// $hello= $wpdb->get_results("SELECT meta_value FROM `81ptak7j_usermeta` WHERE`meta_key` = 'user_groups' AND user_id=35");
// var_dump($hello);
// 	print_r($hello); 
// 	echo "<pre>";print_r($hello); echo "</pre>";
?>














<?php 


		if (isset($_POST['edit'])){
			$id = $_POST['edit'];
			$_POST['button_type']= "edit";
			$rec = $wpdb->get_results("SELECT * FROM project_information WHERE id=$id");	
			$_POST = array_merge($_POST,$rec);	//Send information to $_POST
			// $noOfS=$wpdb->get_var(" SELECT COUNT(email) from user_information WHERE project_id=$id ");
			// echo "$noOfS";
		}

		if(isset($_POST['delete'])){
			$id = $_POST['delete'];
			$_POST['button_type']= "delete"; 
			$del = $wpdb->get_results("SELECT * FROM project_information WHERE id=$id");
			$_POST = array_merge($_POST,$del); 
		}

		if (isset($_POST['add'])) {
			$id = $_POST['add'];
			$_POST['button_type']= "add";
			$add = $wpdb->get_results("SELECT * FROM project_information WHERE id=$id");
			$_POST = array_merge($_POST,$add); 	
		}

		if (isset($_POST['view'])) {
			$id = $_POST['view'];
			$_POST['button_type']= "view";
			$view = $wpdb->get_results("SELECT * FROM user_information WHERE project_id=$id");
			$_POST = array_merge($_POST,$view);	 	 	
		}

if($current_user->user_email=="strand@cord.edu"){
	$results=$wpdb->get_results(" SELECT * from project_information ");
}
else{
	$results = $wpdb->get_results(" SELECT project_information.id, project_information.projectTitle, project_information.projectAbstract, project_information.noOfStudents, project_information.noOfMentors FROM project_information INNER JOIN user_information ON project_information.id=user_information.project_id WHERE user_information.email='$current_user->user_email'");
}
		?>
			<form method="post" action="http://localhost/mainclonetwo/project-form/"><!-- project-form -->
				<table>	
					<tr>
						<?php 
						if($current_user->user_email=="strand@cord.edu"){
							$column= array("Project Title","Project Abstract","Edit","Delete","Add", "View");
						}
						else{
							$column= array("Project Title","Project Abstract","Edit","Delete","Add","View");
						}

						foreach ($column as $value) {?> 
						<th><strong><?php echo $value?></strong></th>
						<?php }	 ?>		
				</tr>

				<?php  
				foreach ($results as $obj){?>
				<tr>
					<td><?php  echo $obj->projectTitle;?> </td>
					<td> <?php echo $obj->projectAbstract;  ?> </td>
					<td><a><button name="edit" value= "<?php echo $obj->id; ?>">Edit</button></a></td>
					<td><a><button name="delete" value= "<?php echo $obj->id; ?>">Delete</button></a></td>
					<td><a><button name="add" value= "<?php echo $obj->id; ?>">Add</button></a></td>
					<td><a><button name="view" value= "<?php echo $obj->id; ?>">View</button></a></td>
				</tr>
				<?php }?>

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

		<form method="post" action="http://localhost/mainclonetwo/table/">
			
			<input type="hidden" name="id" value="<?php echo $id; ?>">
			<h2><?php echo $_POST[0]->projectTitle;?></h2>

			<?php if ($_POST['button_type'] == "add"): ?>
			<label>Collaborator's Email</label><br>
			<input type="text" name="collab_email" placeholder="Collaborator's Email"><br>
			<?php 
			press("confirm","Confirm");
			press("cancel","Cancel");

		elseif ($_POST['button_type'] == "view"):

				for ($x = 0; $x <= 15; $x++) {//use count(email)
					$email_address=$_POST[$x]->email;
				 ?>  <label><?php echo $email_address;?></label> <?php 

				 if ($email_address){
				 	?><a><button name="remove" value= "<?php echo $_POST[$x]->email ?>" style="width: 100px; height: 25px;">Remove</button></a>
				 	<?php 
					}
				 ?><br>
				 <?php  
				}
			press("cancel","Go Back");

			 
			 elseif ($_POST['button_type'] == "delete"):
			 ?>
			<label>Are you sure you want to delete <?php echo $_POST[0]->projectTitle;?>?</label><br>
			<?php 
			press("confirmDel","Yes!");
			press("cancel","No");

			 else: ?>
			<input type="hidden" name="email" value="<?php echo $email ?>" placeholder="<?php echo $email; ?>"><br>

			<label>Project Title</label><br>
			<input type="text" name="projectTitle" value="<?php echo $_POST[0]->projectTitle;?>" placeholder="Project Title"><br>

			<label>Project Abstract</label><br>
			  <?php wp_editor( $_POST[0]->projectAbstract , 'projectAbstract', $settings = array('projectAbstract'=>'projectAbstract') ); ?> 
		<!-- 
			<label> How many Concordia student authors contributed to this presentation? </label><br>
			<input type="number" name="noOfStudents" value="<?php //echo $_POST[0]->noOfStudents ?>" min="1" max="50"><br>


			<label> How many faculty or staff mentored this project? (affiliated with Concordia or another institution) </label><br>
			<input type="number" name="noOfMentors" value="<?php //echo $_POST[0]->noOfMentors;?>" min="1" max="10"><br> -->


			<?php if ($_POST['button_type'] == "edit"): 
				press("update","Update");
				press("cancel","Cancel");	
				
				 else: 
				press("save","Save");
				press("cancel","Cancel");
				
				 endif ?>
			<?php endif ?>
		</form>
		<?php  
	}
	add_shortcode('project_information_contact_form','project_information_form');


	function add_new_project(){
		?>
		<a href="http://localhost/mainclonetwo/project-form/"><button>Add New Project</button></a>
		<?php 
	}
	add_shortcode('add_project_button','add_new_project');


	function press($name, $button_name){
		?>
		<button type="submit" name="<?php echo "$name";  ?>" value="<?php echo $_POST[0]->id; ?>"><?php echo "$button_name";  ?></button>
		<?php 
	}
	?>