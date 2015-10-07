<?php  defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang='en'>
<head>
<?php $this->load->view('page/header'); ?>
</head>
<body>
<header>
<?php if($this->session->flashdata('info')): ?>
<div class="alert alert-info" role="alert">
<?php echo $this->session->flashdata('info'); ?>
</div>
<?php endif; ?>
</header>
<div class='main'>
<?php $this->load->view($target); ?>
</div>
<footer id='footer'>
</footer>
</body>

</html>