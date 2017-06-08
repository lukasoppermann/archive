<?
echo form_open('/login', array('id' => 'login_form', 'class'=>'login-form'));
echo validation_errors();
echo form_input(array(	'name'  		=> 'username',
  						'id'    		=> 'username',
  						'value' 		=> set_value('username',variable($username)),
						'placeholder' 	=> 'Username',
						'class' 		=> 'input-hidden username'
						));
						
echo form_password(array('name'  		=> 'password',
  						'id'    		=> 'password',
  						'value' 		=> set_value('password',variable($password)),
						'placeholder' 	=> 'Password',
						'class' 		=> 'input-hidden password'));	
													
echo form_submit('login', 'Login');
echo form_close();
?>