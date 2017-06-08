<form class="column" data-type="facebook">
	<h2 class="headline">Facebook account</h2>
		<?php
		if($user != FALSE)
		{
			// display login data
			echo '<div class="social-user-card">'.
				'<div class="image"><img src="'.$user['image'].'" /></div>'.
				'<div class="disconnect"><a href="'.base_url().'settings/facebook/revoke">disconnect</a></div>
				<div class="card-content">
					<div class="connected-text">You are connected as:</div>
					<div class="username"><a href="'.$user['urls']['facebook'].'" target="_blank">'.$user['nickname'].' ('.$user['name'].')</a></div>'.
					'<div class="email">'.$user['email'].'</div>'.
					'<div class="post-to-wall"><label for="'.$user['uid'].'">Post to my wall <input id="'.$user['uid'].'" name="post_to" class="post_to" type="checkbox"'.(isset($stored['post']) && $stored['post'] != false && in_array($user['uid'], $stored['post']) ? ' checked="checked"' : '').' value="'.$user['uid'].'" /></label></div>
				</div>
			</div>';
			// add user name to list of follow items
			$follow['users'][] = '<option value="'.$user['uid'].'">'.$user['nickname'].'</optin>';
			//
			if( isset($user['pages']) && count($user['pages']) > 0)
			{
				// display pages
				$pages = '<ul class="pages"><lh><h3 class="option-headline">Select pages to post to</h3></lh>';
				foreach($user['pages'] as $page)
				{
					if( in_array('CREATE_CONTENT', $page['perms']) )
					{
						$follow['pages'][] = '<option value="'.$page['id'].'">'.$page['name'].'</optin>';
						// add page to list
						$pages .= '<li><label for="'.$page['id'].'"><input name="post_to" class="post_to" type="checkbox"'.(isset($stored['post']) && $stored['post'] != false && in_array($page['id'], $stored['post']) ? ' checked="checked"' : '').' id="'.$page['id'].'" value="'.$page['id'].'" />'.$page['name'].'</label></li>';
					}
				}
				echo '<div class="post-to-list">'.$pages.'</ul></div>';
			}
			//
			echo '<div class="follow-selection">
				<h3 class="option-headline">Use this account for the facebook follow link on the page:</h3>
				<select name="follow" class="follow dropdown">
					<optgroup label="Users">'.implode('',$follow['users']).'</optgroup>
					<optgroup label="Pages">'.implode('',$follow['pages']).'</optgroup>
				</select>
			</div>';
		}
		else
		{
			echo '<a class="button left" href="'.base_url().'settings/facebook/connect">connect to facebook</a>';
		}
		?>
</form>