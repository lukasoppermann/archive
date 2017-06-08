<?
echo form_open('/settings/personal', array('id' => 'settings_personal', 'class'=>'form'));
echo form_hidden(array('page' => 'personal', 'user_id' => user('user_id')));
// ---------------------------------------------------
// sidebar
echo "<div id='sidebar'>".
	// ---------------------------------------------------
	// Save Button
	"<div class='save full'><div class='button save' id='save'>save changes</div></div>".
	// -------
	// END
	"</div>";
// ---------------------------------------------------
// form
$user = user('user_data');
//
echo "<div class='form-container'>";
echo "<h1>".variable($title)."</h1>";
// user name
echo "<div id='password_box'>";
echo "<label for='username' class='content-label'>User name</label>";
echo "<div id='username' class='text'>".user('user_name')."</div>";
echo "<label for='password' class='content-label'>Password</label>";
echo form_password(array('name'  		=> 'password',
  						'id'    		=> 'password',
  						'value' 		=> set_value(''),
						'placeholder' 	=> 'password',
						'class' 		=> 'input-hidden title'
						));
echo form_password(array('name'  		=> 'repassword',
  						'id'    		=> 'repassword',
  						'value' 		=> set_value(''),
						'placeholder' 	=> 're-type password',
						'class' 		=> 'input-hidden title'
						));						
echo "</div>";
// keep login
echo "<div id='keep_login_box'>";
echo "<label for='keep_login' class='content-label'>Keep me logged in (cookie)</label>";
echo '<input type="checkbox" checked="'.variable($user['keep_login']).'" value="true" name="keep_login" id="keep_login">';
echo "</div>";
// email
echo "<div id='email_box'>";
echo "<label for='email' class='content-label'>Email address</label>";
echo form_input(array(	'name'  		=> 'email',
  						'id'    		=> 'email',
  						'value' 		=> set_value('email',variable(user('user_email'))),
						'placeholder' 	=> 'Email address',
						'class' 		=> 'input-hidden title'
						));
echo "</div>";						
// author_name
echo "<div id='author_name_box'>";
echo "<label for='author_name' class='content-label'>Displayed name</label>";
echo form_input(array(	'name'  		=> 'author_name',
  						'id'    		=> 'author_name',
  						'value' 		=> set_value('author_name',variable($user['firstname']).' '.variable($user['lastname']) ),
						'placeholder' 	=> 'Name displayed below Posts',
						'class' 		=> 'input-hidden title'
						));
echo "</div>";

//
echo "</div>";
echo form_close();