<?php $this->load->view('includes/head'); ?>

<div id="login_form">

	<h1>Login, Please...</h1>
    <?php 
	echo form_open('login/validate_credentials');
	echo form_input('username', 'Your Username');
	echo form_password('password', 'Your Password');
	echo form_submit('submit', 'Login');
	//echo anchor('login/signup', 'Create Account');
	echo form_close();
	?>

</div><!-- end login_form-->


<?php $this->load->view('includes/footer'); ?>