<?
echo form_open('/settings/user', array('id' => 'settings_user', 'class'=>'form'));
echo form_hidden(array('page' => 'user'));
// ---------------------------------------------------
// form
echo "<div class='form-container'>";
echo "<h1>".variable($title)."</h1>";
//
echo "<div id='new_user'>";
// username
echo "<div id='username_box' class='field'>";
echo "<label for='username' class='content-label'>User name</label>";
echo form_input(array(	'name'  		=> 'username',
  						'id'    		=> 'username',
  						'value' 		=> set_value('username'),
						'placeholder' 	=> 'username',
						'class' 		=> 'input-hidden title'
						));
echo "</div>";
// email
echo "<div id='email_box' class='field'>";
echo "<label for='email' class='content-label'>Email address</label>";
echo form_input(array(	'name'  		=> 'email',
  						'id'    		=> 'email',
  						'value' 		=> set_value('email'),
						'placeholder' 	=> 'Email address',
						'class' 		=> 'input-hidden title'
						));
echo "</div>";
// password
echo "<div id='password_box' class='field'>";
echo "<label for='password' class='content-label'>Password</label>";
echo form_password(array('name'  		=> 'password',
  						'id'    		=> 'password',
  						'value' 		=> '',
						'placeholder' 	=> 'Password',
						'class' 		=> 'input-hidden title'
						));
echo "</div>";
// retype password
echo "<div id='repassword_box' class='field'>";
echo "<label for='repassword' class='content-label'>re-type password</label>";
echo form_password(array('name'  		=> 'repassword',
  						'id'    		=> 'repassword',
  						'value' 		=> '',
						'placeholder' 	=> 're-type password',
						'class' 		=> 'input-hidden title'
						));
echo "</div>";
// group
echo "<div id='group_box' class='field'>";
echo "<label for='group' class='content-label'>user group</label>";
$options = array(
                  '1'  	=> 'Steele Worker',
                  '2'   => 'admin',
                  '3'  	=> 'user'
                );

echo form_dropdown('group', $options, '3');
echo "</div>";
// Button
echo "<div class='button' id='add_user'>add user</div>";
// END New User
echo "</div>";
//					
//
echo "</div>";
echo form_close();