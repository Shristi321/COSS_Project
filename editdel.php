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
	$update = false;
	?>

	<?php 

	global $wpdb; 
	// if (isset($_GET['edit'])){
	// 	$id = $_GET['edit'];
	// 	$update = true;
	// 	$rec = $wpdb->get_results("SELECT * FROM project_information WHERE id=$id");
		               
	// 		$projectTitle =  $_POST['projectTitle'];//$rec->projectTitle???????
	// 		$projectAbstract = $rec->projectAbstract;//$rec->projectAbstract???????
	// 		$id= $rec['id'];
	// }


	if (isset($_POST['update'])) {
		$id = $_POST['id'];
		$projectTitle = $_POST['projectTitle'];
		$projectAbstract = $_POST['projectAbstract'];

		$wpdb->get_results("UPDATE project_information SET projectTitle='$projectTitle', projectAbstract='$projectAbstract' WHERE id=$id"); 
		//header('location: page_id=530');
	}


	if (isset($_POST['save'])) {
		$projectTitle = $_POST['projectTitle'];
		$projectAbstract = $_POST['projectAbstract'];

		$wpdb->get_results("INSERT INTO project_information (projectTitle, projectAbstract) VALUES ('$projectTitle', '$projectAbstract')"); 
		//header('location: page_id=530');
	}


	?>


<?php 


	function display_table()
	{
		global $wpdb;

		if(isset($_POST['delete'])){
			$wpdb->delete( 'project_information', 
				array(
					'id'=>$_POST['delete']
				)

			);

		}


		if (isset($_POST['edit'])){
			//$update = true;
			$id = $_POST['edit'];
			echo "$id";
			$rec = $wpdb->get_results("SELECT * FROM project_information WHERE id=$id");
			echo "<pre>";print_r($rec); echo "</pre>";
			
			// echo $_POST['projectTitle'];//$rec->projectTitle???????
			// echo $rec->projectAbstract;  //$rec->projectAbstract???????
			// $id= $rec['id'];
			
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
		echo"</table>";
		
	}
	add_shortcode('project_information_table','display_table');



/*project_information FORM */

	function project_information_form()
	{?>

		<form method="post" action="?page_id=530">
			<input type="hidden" name="id" value="<?php echo $id; ?>">
			<label>Project Title</label><br>
			<input type="text" name="projectTitle" value="<?php echo $_POST['projectTitle'];?>" placeholder="<?php echo $_POST['projectTitle'];?>"/><br>
			<label>Project Abstract<?php echo $projectAbstract; ?></label><br>

			<textarea name="projectAbstract" value="<?php echo $projectAbstract; ?>" placeholder="<?php echo $projectAbstract; ?>"></textarea><br>
			<!-- <input type="submit" name="submit"> -->


			<?php if ($update == true): ?>
				<button  type="submit" name="update" >Update</button>
			<?php else: ?>
				<button type="submit" name="save" >Save</button>
			<?php endif ?>
	
		<?php  

	}
	add_shortcode('project_information_contact_form','project_information_form');

	