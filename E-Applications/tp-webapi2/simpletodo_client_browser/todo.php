<?php
session_start();
include_once 'apicaller.php';

$apicaller = new ApiCaller('APP001', '28e336ac6c9423d946ba02d19c6a2632', 'http://localhost/tp-webapi2/simpletodo_api/');

$todo_items = $apicaller->sendRequest(array(
	'controller' => 'todo',
	'action' => 'read',
	'username' => $_SESSION['username'],
	'userpass' => $_SESSION['userpass']
));

?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html" charset="UTF-8"/>
	<title>SimpleTODO</title>
	
	<link rel="stylesheet" href="css/reset.css" type="text/css" />
	<link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" />
	<link rel="stylesheet" href="css/flick/jquery-ui-1.8.16.custom.css" type="text/css" />
	
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery-ui-1.8.16.custom.min.js"></script>
 	<script type="text/javascript" src="./Diaporama Simple JQuery_files/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="./Diaporama Simple JQuery_files/jquery.diaporama.js"></script>
	<script type="text/javascript" src="./Diaporama Simple JQuery_files/script.js"></script>
	
	<style>
	body {
		padding-top: 40px;
	}
	#main {
		margin-top: 80px;
	}
	
	.textalignright {
		text-align: right;
	}


	.image {
		text-align: center;
	}


	
	.marginbottom10 {
		margin-bottom: 10px;
	}
	#newtodo_window {
		text-align: left;
		display: none;
	}
	</style>
	
	<script>
	$(document).ready(function() {
		$("#todolist").accordion({
			collapsible: true
		});
		$(".datepicker").datepicker();
		$('#newtodo_window').dialog({
			autoOpen: false,
			height: 'auto',
			width: 'auto',
			modal: true
		});
		$('#newtodo').click(function() {
			$('#newtodo_window').dialog('open');
		});
	});
	</script>
</head>



 <?php
// Inclure les API GoogleMaps et Flickr
    include_once 'ApiGoogleMaps.php';
    include_once 'ApiFlickr.php';


//initialisation de l'API Google Maps
    $google = new GoogleMapApi("ABQIAAAAssNHsvRmdjbfaHQLGJe4IBRUsz-pYg_Ma22JMdFSMvaUp2krUhQPGyzeHUvioNuo_7zqLxrqnekFOQ", "map");
    $google->definirTaille(400, 350);


//initialisation de l'API Flickr
    $flickr = new ApiFlickr("flickr", "9aaf0d445b006926d348ad8e300dd6bb", 25);
    $flickr->definirTaille(400, 350)
    ?>

<body>
	<div class="topbar">
		<div class="fill">
			<div class="container">
				<a class="brand" href="index.php">SimpleTODO</a>
			</div>
		</div>
	</div>
	<div id="main" class="container">
		<div class="textalignright marginbottom10">
			<span id="newtodo" class="btn info">Create a new TODO item</span>
			<div id="newtodo_window" title="Create a new TODO item">
				<form method="POST" action="new_todo.php">
					<p>Title:<br /><input type="text" class="title" name="title" placeholder="TODO title" /></p>
					<p>Date Due:<br /><input type="text" class="datepicker" name="due_date" placeholder="MM/DD/YYYY" /></p>
					<p>Description:<br /><textarea class="description" name="description"></textarea></p>
                       			<p>Lieu:<br /><input class="description" name="lieu"/></p>
					<div class="actions">
						<input type="submit" value="Create" name="new_submit" class="btn primary" />
					</div>
				</form>
			</div>
		</div>
		<div id="todolist">
			<!-- Pour chaque tache trouvée -->
			<?php foreach($todo_items as $todo): ?>
			<h3><a href="#"><?php echo $todo->title; ?></a></h3>
			<div>
				<form method="POST" action="update_todo.php">
				<div class="textalignright">
					<a href="delete_todo.php?todo_id=<?php echo $todo->todo_id; ?>">Delete</a>
				</div>
				<div>
					<p>Date Due:<br /><input type="text" id="datepicker_<?php echo $todo->todo_id; ?>" class="datepicker" name="due_date" value="<?php echo $todo->due_date; ?>" /></p>
					<p>Description:<br /><textarea class="span8" id="description_<?php echo $todo->todo_id; ?>" class="description" name="description"><?php echo $todo->description; ?></textarea></p>
				 <!-- Affichage du lieu, de la carte Google Maps et des images Flickr -->
					<p>Lieu:<br /><input class="description" id="lieu_<?php echo $todo->lieu; ?>" name="lieu" value="<?php echo $todo->lieu; ?>"/></p>
                                <table>
                                    <tr>

                                        <td><div><?php $flickr->printFlickrPhotos($todo->todo_id, $todo->lieu); ?></div></td>
                                        <td><div><?php $google->affichageMap($todo->lieu); ?></div></td>
                                    </tr>
                                </table>

				</div>
				<div class="textalignright">
					<?php if( $todo->is_done == 'false' ): ?>
					<input type="hidden" value="false" name="is_done" />
					<input type="submit" class="btn" value="Mark as Done?" name="markasdone_button" />
					<?php else: ?>
					<input type="hidden" value="true" name="is_done" />
					<input type="button" class="btn success" value="Done!" name="done_button" />
					<?php endif; ?>
					<input type="hidden" value="<?php echo $todo->todo_id; ?>" name="todo_id" />
					<input type="hidden" value="<?php echo $todo->title; ?>" name="title" />
					<input type="submit" class="btn primary" value="Save Changes" name="update_button" />
				</div>
				</form>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
 <?php
if(!empty($todo_items)){
            $google->appelApi($todo_items[0]->lieu);
}

            ?>

    </body>
</html>
