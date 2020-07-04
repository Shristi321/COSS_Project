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



// $column= array("Project Title","Project Abstract","Edit","Delete","Add", "View");

	
	global $wpdb; 

	if (isset($_POST['update'])) {
		$id=$_POST['update'];
		$projectTitle = $_POST['projectTitle'];
		$projectAbstract = $_POST['projectAbstract'];
		
		$wpdb->get_results("UPDATE project_information SET projectTitle='$projectTitle', projectAbstract='$projectAbstract' WHERE id=$id");	
	}

	if (isset($_POST['save'])) {
		$projectTitle = $_POST['projectTitle'];
		$projectAbstract = $_POST['projectAbstract'];
		$email=$_POST['email'];
		$wpdb->get_results("INSERT INTO project_information (email, projectTitle, projectAbstract) VALUES ('$email', '$projectTitle', '$projectAbstract')");

		$lastID=$wpdb->get_results("SELECT LAST_INSERT_ID()");
		$lastIDarray = (array) $lastID[0];
		$stuff = array_values($lastIDarray);
		$projectId = (int) $stuff[0];
		$wpdb->get_results("INSERT INTO user_information (project_id, email) VALUES ('$projectId', '$email')");		
	}

	if (isset($_POST['cancel'])) {
		header('location: ?page_id=571'); 
	}

	if (isset($_POST['confirm'])) {
		$projectId=$_POST['confirm'];
		$useremail=$_POST['collab_email'];
		$wpdb->get_results("INSERT INTO user_information (project_id, email) VALUES ('$projectId', '$useremail')");
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
			header('location: ?page_id=571'); 
		}
	?>

<?php 
	function display_table(){
		global $wpdb;
		global $current_user;
		global $column;

		if (isset($_POST['edit'])){
			$id = $_POST['edit'];
			$_POST['button_type']= "edit";
			$rec = $wpdb->get_results("SELECT * FROM project_information WHERE id=$id");	
			$_POST = array_merge($_POST,$rec);	//Send information to $_POST
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


if($current_user->user_email=="chapagains261@gmail.com"||$current_user->user_email=="alice@cord.edu"){
	$results=$wpdb->get_results(" SELECT project_information.id, project_information.projectTitle, project_information.projectAbstract FROM project_information INNER JOIN user_information ON project_information.id=user_information.project_id ");
}
else{
	$results = $wpdb->get_results(" SELECT project_information.id, project_information.projectTitle, project_information.projectAbstract FROM project_information INNER JOIN user_information ON project_information.id=user_information.project_id WHERE user_information.email='$current_user->user_email'");
}
		?>

		<form method="post" action="?page_id=530">
			<table>	
				<tr>
					<?php 
					$column= array("Project Title","Project Abstract","Edit","Delete","Add", "View");


					if($current_user->user_email=="chapagains261@gmail.com"||$current_user->user_email=="alice@cord.edu"){
						$column= array("Project Title","Project Abstract","Edit","Delete","Add", "View");
					}
					else{
						$column= array("Project Title","Project Abstract","Edit","Delete","Add");
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

		<form method="post" action="?page_id=571">
			<input type="hidden" name="id" value="<?php echo $id; ?>">
			<h2><?php echo $_POST[0]->projectTitle;?></h2>

			<?php if ($_POST['button_type'] == "add"): ?>
			<label>Collaborator's Email</label><br>
			<input type="text" name="collab_email" placeholder="Collaborator's Email"><br>
			<?php 
			press("confirm","Confirm");
			press("cancel","Cancel");

		elseif ($_POST['button_type'] == "view"):
			var_dump($_POST);
			 ?>

			<label><?php echo $_POST[0]->email;?></label><br>
			<label><?php echo $_POST[1]->email;?></label><br>
			<?php 
			
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

			<label> How many Concordia student authors contributed to this presentation? </label><br>
			<input type="number" name="authornumber" value="" min="1" max="50"><br>

			<label> Make sure all student authors have registered with URSCA by clicking the Login button in the top banner before proceeding. Enter all student author names below. If you need to enter more author/presenter names, please do so at the end of this survey. </label><br>
			<input type="text"><br>

			<label> How many faculty or staff mentored this project? (affiliated with Concordia or another institution) </label><br>
			<input type="number" name="mentornumber" min="1" max="10"><br>

		<label> Please provide a concise, descriptive title for your project. Be sure to include any necessary formatting and symbols. </label><br>
			<input type="text"><br><br>

	    <label><input type="checkbox" name="acceptanceabstract" value="1" >By clicking this box, you are providing permission to include your abstract in the COSS program.</label><br>

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
		<a href="http://localhost/codeanddesign/?page_id=573"><button>Add New Project</button></a>
		<?php 
	}
	add_shortcode('add_project_button','add_new_project');


	function press($name, $button_name){
		?>
		<button type="submit" name="<?php echo "$name";  ?>" value="<?php echo $_POST[0]->id; ?>"><?php echo "$button_name";  ?></button>
		<?php 
	}
	?>