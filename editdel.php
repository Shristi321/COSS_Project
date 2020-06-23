 <?php
/**
*Plugin Name: Edit Test Plugin
*Description: This is just an example plugin
*Version: 1.0
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
	if (array_key_exists('edit',$_GET))
		{
		$id = $_GET['edit'];
		$update = true;
		$rec = $wpdb->get_results("SELECT * FROM project_information WHERE id=$id");

			
			$projectTitle = $rec['projectTitle'];//$rec->projectTitle???????
			$projectAbstract = $rec['projectAbstract'];//$rec->projectAbstract???????
			$id= $record['id'];
	}

	if (isset($_POST['del'])) {
		$id = $_POST['id'];
		$wpdb->get_results("DELETE FROM project_information WHERE id=$id");
		header('location: page_id=530');
	}

	if (isset($_POST['update'])) {
		$id = $_POST['id'];
		$projectTitle = $_POST['projectTitle'];
		$projectAbstract = $_POST['projectAbstract'];

		$wpdb->get_results("UPDATE project_information SET projectTitle='$projectTitle', projectAbstract='$projectAbstract' WHERE id=$id"); 
		header('location: page_id=530');
	}


	if (isset($_POST['submit'])) {
		$projectTitle = $_POST['projectTitle'];
		$projectAbstract = $_POST['projectAbstract'];

		$wpdb->get_results("INSERT INTO project_information (projectTitle, projectAbstract) VALUES ('$projectTitle', '$projectAbstract')"); 
		header('location: page_id=530');
	}


	?>


<?php 


	function display_table()
	{
		global $wpdb;
		$result = $wpdb->get_results(" SELECT * FROM project_information");
		?>

		<table>	
				<tr>
					<th>Project Title</th>
					<th>Project Abstract</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>

		<?php  
		foreach ($result as $obj)
		{?>

		<tr>
			<td><?php  echo $obj->projectTitle;?> </td>
			<td> <?php echo $obj->projectAbstract;  ?> </td>
			<td>  
			<a href="?page_id=530?edit=<?php echo $obj->id; ?>"><?php echo $obj->id; ?>Edit</a>
			</td>
			<td>
				 <button type="submit" name="del">Del</button>
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
			<input type="text" name="projectTitle" value="<?php echo $projectTitle; ?>" placeholder="<?php echo $obj->id;?>"/><br>
			<label>Project Abstract<?php echo $projectAbstract; ?></label><br>
			<textarea name="projectAbstract" value="<?php echo $projectAbstract; ?>" placeholder="Write down the abstract here."></textarea><br>
			<!-- <input type="submit" name="submit"> -->


			<?php if ($update == true): ?>
				<button  type="submit" name="update" >update</button>
			<?php else: ?>
				<button type="submit" name="submit" >Save</button>
			<?php endif ?>
		


		<?php  

	}
	add_shortcode('project_information_contact_form','project_information_form');

	