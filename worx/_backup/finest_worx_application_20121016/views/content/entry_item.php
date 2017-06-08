<li class="item status-<?=$status?>" data-type="<?=$type?>" data-id="<?=$id?>" data-status="<?=$status?>">
	<?
		echo '<div class="status status-'.$status.'"></div>';
		echo '<div class="title"><span class="heading">'.$title.'</span>';
		if( $menu_id != false )
		{
			echo '<span class="title-extra menu-item">('.$menu_id.')</span>';
		}
		elseif( $permalink != null && $permalink != false)
		{
			echo '<span class="title-extra permalink">('.$permalink.')</span>';
		}
		echo '</div>';
		echo '<div class="time"><span>'.date("d/m/Y h:i a", strtotime(server_to_user(human_to_unix($last_saved), variable(user('gmt'))))).'</span></div>';
		// editing options
		echo '<div class="delete">';
		// if(variable($type) == "1" || variable($type) == "2"){
			echo '<a href="'.base_url().'content/delete/'.$id.'" title="delete"></a>';
		// }
		echo '</div>';
		echo '<div class="edit"><a href="'.base_url().'content/edit/'.$id.'" title="edit content"></a></div>';
	?>
</li>