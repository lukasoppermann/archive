<li class="item status-<?=$status?>" data-id="<?=$id?>" data-status="<?=$status?>"><form>
	<?
		echo '<input type="hidden" name="id" value="'.$id.'" />';
		echo '<div class="status status-'.$status.'"></div>';
		echo '<div class="ticket-nr">#'.$ticket_nr.'</div><div class="title customer-name">'.(isset($customer_name) ? $customer_name : 'customer name' ).'</div>';
		echo '<div class="time">Pickup <span class="date">'.(isset($resolved) ? $resolved : 'set date').'</span> at <span class="pickup-time" contenteditable="true">'.(isset($pickup_time) ? $pickup_time : 'set time').'</span></div>';
		// editing options
		echo '<div class="delete">';
			echo '<a href="#" title="delete"></a>';
		echo '</div>';
		echo '<div class="info"><div class="columns">';
			echo '<div class="column">';
				echo '<div class="form-element discreet">
					<label for="customer_phone">Customer phone</label>
					<input type="text" name="customer_phone" value="'.variable($customer_phone).'" />
				</div>';
				echo '<div class="form-element discreet">
					<label for="customer_email">Customer email</label>
					<input type="text" name="customer_email" value="'.variable($customer_email).'" />
				</div>';
				echo '<div class="form-element discreet">
					<label for="customer_address">Customer address</label>
					<textarea name="customer_address">'.variable($customer_address).'</textarea>
				</div>';
			echo '</div><div class="column">';
			echo '<div class="form-element discreet">
				<label>Ticket opened</label>
				<div class="request-time fixed-content">'.(isset($time) ? date('d/m/Y \a\t h:i a', mysql_to_unix($time)) : '').'</div>
			</div>';
			echo '<div class="form-element discreet">
				<label for="price">Price in AUD</label>
				<input type="text" name="price" value="'.variable($price).'" />
			</div>';
			echo '<div class="form-element discreet">
				'.form_dropdown('status',config('ticket_states'),$status).'
			</div>';
			echo '<div class="form-element discreet">
				'.form_dropdown('store_id',$stores,$store_id).'
			</div>';
		echo '</div>';
		echo '<div class="form-element discreet">
			<label>Customer request</label>
			<div class="text fixed-content">'.variable($text).'</div>
		</div>';
		echo '<div class="form-element discreet">
			<label for="notes">Staff Notes</label>		
			<div class="notes textarea" name="notes" contenteditable="true">'.variable($notes).'</div>
		</div>';
		echo '<div class="form-element discreet">
			<div class="button save">Save Changes</div>
			<div class="notify">
				<label for="notify">Notify customer</label><input type="checkbox" name="notify" value="notify" />
			</div>
		</div>';
		echo '</div></div>';
	?>
</form></li>