<?php
include 'db_connect.php';
$stat = array("Pending","In Progress","Done");
$qry = $conn->query("SELECT * FROM project_list where id = ".$_GET['id'])->fetch_array();
foreach($qry as $k => $v){
	$$k = $v;
}

// $tprog = $conn->query("SELECT * FROM task_list where project_id = {$id}")->num_rows;
// $cprog = $conn->query("SELECT * FROM task_list where project_id = {$id} and status = 3")->num_rows;
// $prog = $tprog > 0 ? ($cprog/$tprog) * 100 : 0;
// $prog = $prog > 0 ?  number_format($prog,2) : $prog;
// $prod = $conn->query("SELECT * FROM user_productivity where project_id = {$id}")->num_rows;
// if($status == 0 && strtotime(date('Y-m-d')) >= strtotime($start_date)):
// if($prod  > 0  || $cprog > 0)
//   $status = 2;
// else
//   $status = 1;
// elseif($status == 0 && strtotime(date('Y-m-d')) > strtotime($end_date)):
// $status = 4;
// endif;
$manager = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where id = $manager_id");
$manager = $manager->num_rows > 0 ? $manager->fetch_array() : array();
?>
<div class="col-lg-12">
	<div class="row">
		<div class="col-md-12">
			<div class="callout callout-info">
				<div class="col-md-12">
					<div class="row">
						<div class="col-sm-6">
							<dl>
								<dt><b class="border-bottom border-primary">Project Name</b></dt>
								<dd><?php echo ucwords($name) ?></dd>
								<dt><b class="border-bottom border-primary">Description</b></dt>
								<dd><?php echo html_entity_decode($description) ?></dd>
							</dl>
						</div>
						<div class="col-md-6">
							<dl>
								<dt><b class="border-bottom border-primary">Start Date</b></dt>
								<dd><?php echo date("F d, Y",strtotime($start_date)) ?></dd>
							</dl>
							<dl>
								<dt><b class="border-bottom border-primary">End Date</b></dt>
								<dd><?php echo date("F d, Y",strtotime($end_date)) ?></dd>
							</dl>
							<dl>
								<dt><b class="border-bottom border-primary">Status</b></dt>
								<dd>
									<?php
									  if($status == 1){
									  	echo "<span class='badge badge-secondary'>Pending</span>";
									  }elseif($status == 2){
									  	echo "<span class='badge badge-primary'>In Progress</span>";
									  }elseif($status == 3){
									  	echo "<span class='badge badge-success'>Done</span>";
									  }
									?>
								</dd>
							</dl>
							<dl>
								<dt><b class="border-bottom border-primary">Project Manager</b></dt>
								<dd>
									<?php if(isset($manager['id'])) : ?>
									<div class="d-flex align-items-center mt-1">
										<img class="img-circle img-thumbnail p-0 shadow-sm border-info img-sm mr-3" src="assets/uploads/<?php echo $manager['avatar'] ?>" alt="Avatar">
										<b><?php echo ucwords($manager['name']) ?></b>
									</div>
									<?php else: ?>
										<small><i>Manager Deleted from Database</i></small>
									<?php endif; ?>
								</dd>
							</dl>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<div class="card card-outline card-primary">
				<div class="card-header">
					<span><b>Team Member/s:</b></span>
					<div class="card-tools">
						<!-- <button class="btn btn-primary bg-gradient-primary btn-sm" type="button" id="manage_team">Manage</button> -->
					</div>
				</div>
				<div class="card-body">
					<ul class="users-list clearfix">
						<?php 
							// get users information of the project
							$users = $conn -> query("SELECT *, CONCAT(firstname, ' ', lastname) as name FROM works_on JOIN users on user_id=users.id WHERE project_id=" . $_GET['id']);
							while ($row = $users -> fetch_assoc()):
						?>
								<li>
										<img src="assets/uploads/<?php echo $row['avatar'] ?>" alt="User Image">
										<a class="users-list-name" href="javascript:void(0)"><?php echo ucwords($row['name']) ?></a>
										<!-- <span class="users-list-date">Today</span> -->
								</li>
						<?php 
							endwhile;
						?>
					</ul>
				</div>
			</div>
		</div>
		<div class="col-md-8">
			<div class="card card-outline card-primary">
				<div class="card-header">
					<span><b>Task List:</b></span>
					<?php if($_SESSION['login_type'] != 3): ?>
					<div class="card-tools">
						<button class="btn btn-primary btn-sm" type="button" id="new_task" data-bs-toggle="modal" data-bs-target="#data_modal">
							<i class="fa fa-plus"></i> New Task
						</button>
					</div>
				<?php endif; ?>
				</div>
				<div class="card-body p-0">
					<div class="table-responsive">
					<table class="table table-condensed m-0 table-hover">
						<colgroup>
							<col width="5%">
							<col width="20%">
							<col width="20%">
							<col width="25%">
							<col width="15%">
							<col width="15%">
						</colgroup>
						<thead>
							<th>#</th>
							<th>Task</th>
							<th>Assignee</th>
							<th>Description</th>
							<th>Status</th>
							<th>Action</th>
						</thead>
						<tbody>
							<?php 
							$i = 1;
							$tasks = $conn->query("SELECT * FROM task_list where project_id = {$id} order by task asc");
							while($row=$tasks->fetch_assoc()):
								$assignee_id = $row["assignee_id"];
								$assignee = $conn->query("SELECT CONCAT(firstname, ' ', lastname) as `name` FROM users WHERE id='$assignee_id'");
								$assignee_name = $assignee->fetch_assoc()["name"];
								// description
								$trans = get_html_translation_table(HTML_ENTITIES,ENT_QUOTES);
								unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
								$desc = strtr(html_entity_decode($row['description']),$trans);
								$desc=str_replace(array("<li>","</li>"), array("",", "), $desc);
							?>
								<tr>
									<td class="text-center"><?php echo $i++ ?></td>
									<td class=""><b><?php echo ucwords($row['task']) ?></b></td>
									<td class=""><b><?php echo $assignee_name?></b></td>
									<td class=""><p class="truncate"><?php echo strip_tags($desc) ?></p></td>
									<td>
										<?php 
										if($row['status'] == 1){
											echo "<span class='badge badge-secondary'>Pending</span>";
										}elseif($row['status'] == 2){
											echo "<span class='badge badge-primary'>In Progress</span>";
										}elseif($row['status'] == 3){
											echo "<span class='badge badge-success'>Done</span>";
										}
										?>
									</td>
									<td class="">
										<button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="true">
											Action
										</button>
										<div class="dropdown-menu">
											<a class="dropdown-item view_task" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"  data-task="<?php echo $row['task'] ?>"
											>
												View
											</a>
											<div class="dropdown-divider"></div>
											<?php if($_SESSION['login_type'] != 3): ?>
												<a class="dropdown-item edit_task" href="javascript:void(0)" data-id="<?php echo $row['id'] ?> " data-bs-toggle="modal" data-bs-target="#data_modal"  data-task="<?php echo $row['task'] ?>">Edit</a>
												<div class="dropdown-divider"></div>
												<a class="dropdown-item delete_task" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>" data-bs-toggle="modal" data-bs-target="#confirm_modal">Delete</a>
									<?php endif; ?>
										</div>
									</td>
								</tr>
							<?php 
							endwhile;
							?>
						</tbody>
					</table>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
<style>
	.users-list>li img {
	    border-radius: 50%;
	    height: 67px;
	    width: 67px;
	    object-fit: cover;
	}
	.users-list>li {
		width: 33.33% !important
	}
	.truncate {
		-webkit-line-clamp:1 !important;
	}
</style>
<script>
	// add new task
	$('#new_task').click(function(){
		dataModal('New Task', 'manage_task.php?pid=<?php echo $id ?>')
	})

	$('.edit_task').click(function(){
		dataModal("Edit Task: "+$(this).attr('data-task'),"manage_task.php?pid=<?php echo $id ?>&id="+$(this).attr('data-id'))
	})
	
	$('.view_task').click(function(){
		dataModal("Task Details","view_task.php?id="+$(this).attr('data-id'))
	})

	$('.delete_task').click(function(){
		confirmModal("Are you sure to delete this task?","delete_task",[$(this).attr('data-id')])
	})
	function delete_task($id){
		start_load()
		$.ajax({
			url:'ajax.php?action=delete_task',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("Data successfully deleted",'success')
					setTimeout(function(){
						location.reload()
					},1500)

				}
			}
		})
	}



</script>