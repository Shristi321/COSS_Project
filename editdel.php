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
	$update = "fal";
	?>

	<?php 

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

		$wpdb->get_results("INSERT INTO project_information (projectTitle, projectAbstract) VALUES ('$projectTitle', '$projectAbstract')"); 	
	}


	?>


<?php 

	function display_table()
	{
		global $wpdb;


		if (isset($_POST['edit'])){
			$id = $_POST['edit'];
			$_POST['update']= "tru";//$update=true;
			$rec = $wpdb->get_results("SELECT * FROM project_information WHERE id=$id");	

			//Send information to $_POST
			$_POST = array_merge($_POST,$rec);	
		}

		if(isset($_POST['delete'])){
			$wpdb->delete( 'project_information', 
				array(
					'id'=>$_POST['delete']
				)
			);
		}


		$results = $wpdb->get_results(" SELECT * FROM project_information");
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
		?>

		<form method="post" action="?page_id=530">
			<input type="hidden" name="id" value="<?php echo $id; ?>">
			<label>Project Title</label><br>
			<input type="text" name="projectTitle" value="<?php echo $_POST[0]->projectTitle;?>" placeholder="Project Title"><br>
			<label>Project Abstract</label><br>

			<input type="text" name="projectAbstract" value="<?php echo $_POST[0]->projectAbstract;?>" placeholder="Project Abstract"><br>

			<!--  <textarea name='projectAbstract' placeholder="Your Abstract"> <?php //echo $_POST[0]->projectAbstract;?> </textarea><br>  -->
			

		<div>
			<?php if ($_POST['update'] == "tru"): ?>

				<button type="submit" name="update" value="<?php echo $_POST[0]->id; ?>">Update</button>
			<?php else: ?>
				<button  type="submit" name="save">Save</button>
			<?php endif ?>
		</div>
		</form>
	
		<?php  

	}
	add_shortcode('project_information_contact_form','project_information_form');

	