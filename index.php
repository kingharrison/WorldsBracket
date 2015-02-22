<?php 
	$pagetitle = "Fierceboard Bracket Contest";
	
	$root = $_SERVER['DOCUMENT_ROOT'];
	include $root . '/worldsbracket/header.php';
	

?>

		<div id="content">
			<div class="menubar">
				<div class="sidebar-toggler visible-xs">
					<i class="ion-navicon"></i>
				</div>

				<div class="page-title">
					<?php echo $pagetitle ?>
				</div>
			</div>
			<div class="content-wrapper">

<?php
if (!isset($CURRENT_USER)) {
	echo '<div class="alert alert-warning" role="alert">Please <a href="' . $config['fierceboardUrl'] . '/login/">first log into fierceboard</a> to take part in the bracket contest</div>';
}
	
?>
	
Welcome!	

<?php include $root . '/worldsbracket/footer.php'; ?>	

