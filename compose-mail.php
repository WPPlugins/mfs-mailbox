<?php
/**
 *File Name: compose-mail.php
 *Description: 
 *Version: 1.0
 *Author:Mindfire Solutions
 *Author URI: http://www.mindfiresolutions.com/
 */
 
global $wpdb;

$user_id = get_current_user_id();
$id = isset( $_GET['mail-id'] ) ? $_GET['mail-id'] : '';

/* this is for edit purpose for the draft message */
if( $id != '' ) {
	$sql_query = "SELECT * 
				FROM {$wpdb->prefix}mfs_mailbox
				WHERE id = '%d' ";
	
	$msg_body_query = $wpdb->prepare( $sql_query, $id );
	
	$val = $wpdb->get_row( $msg_body_query, ARRAY_A );
	
	$receiver_name = $val['receiver_ids'];
	$draft_subject = $val['subject'];
	$time = $val['time'];
	$content = $val['message_body'];
	
	/* this is for save as draft funcionality */
	if( isset($_POST['compose-draft']) && wp_verify_nonce( $_POST['name_of_nonce_field'], 'name_of_my_action' ) ) {
		
		/* if someone will try to send message without having body then the default subject text will be stored in database */
		if( '' == $_POST['mail-subject']) {
			$subject = 'No Subject';
		} else {
			$subject = esc_sql( esc_attr( $_POST['mail-subject'] ) );
		}
		
		$status = 3;
		
		mfs_update_draft( $_POST, $status, $user_id, $subject, $id );
	}
	
	/* this is for save as draft funcionality */
	if( isset($_POST['submit']) && wp_verify_nonce( $_POST['name_of_nonce_field'], 'name_of_my_action' ) ) {
		
		/* if someone will try to send message without having body then the default subject text will be stored in database */
		if( '' == $_POST['mail-subject']) {
			$subject = 'No Subject';
		} else {
			$subject = esc_sql( esc_attr( $_POST['mail-subject'] ) );
		}
		$status = '';
		mfs_update_draft( $_POST, $status, $user_id, $subject, $id );
	}

} elseif( isset($_GET['reply-mail']) &&  ( 'reply' == $_GET['type'] ) ) {
	$id = $_GET['reply-mail'];
	$sql_query = "SELECT * 
					FROM {$wpdb->prefix}mfs_mailbox
					WHERE id = '%d' ";
	
	$msg_body_query = $wpdb->prepare( $sql_query, $id );
	
	$val = $wpdb->get_row( $msg_body_query, ARRAY_A );
	
	$receiver_name = $val['sender_id'];
	$draft_subject = 'Re: ' . $val['subject'];
	$time = $val['time'];
	$content = $val['message_body'];
	$concat = '<br/><br/><div id="prev-details">';
	$concat .= '<span class="original-msg">-------Original Message-------</span><br/>';
	$concat .= '<span><b>From: </b>' . get_userdata( $val['sender_id'] )->user_login . '</span><br />';
	$concat .= '<span><b>Sent: </b>' . date('F d, Y h:i A', strtotime($time)) . '</span><br />';
	$concat .= '<span><b>To: </b>' . get_userdata( $val['receiver_ids'] )->user_login . '</span><br />';
	$concat .= '<span><b>Subject: </b> ' . $draft_subject . '</span><br />';
	$concat .= '</div>';
	$content = $concat . $content;
	
	/* this is for save as draft funcionality */
	if( isset( $_POST['compose-draft'] ) && wp_verify_nonce( $_POST['name_of_nonce_field'], 'name_of_my_action' ) ) {
		
		/* if someone will try to send message without having body then the default subject text will be stored in database */
		if( '' == $_POST['mail-subject']) {
			$subject = 'No Subject';
		} else {
			$subject = esc_sql( esc_attr( $_POST['mail-subject'] ) );
		}
		
		$status = 3;
		
		mfs_update_draft( $_POST, $status, $user_id, $subject, $id );
	}
	
	/* this is for save as draft funcionality */
	if( isset( $_POST['submit'] ) && wp_verify_nonce( $_POST['name_of_nonce_field'], 'name_of_my_action' ) ) {
		
		/* if someone will try to send message without having body then the default subject text will be stored in database */
		if( '' == $_POST['mail-subject']) {
			$subject = 'No Subject';
		} else {
			$subject = stripslashes($_POST['mail-subject']);
		}
		$status = '';
		mfs_insert_mail( $_POST, $status, $user_id, $subject );
	}
} elseif( isset($_GET['forward-mail']) && ( 'forward' == $_GET['type'] ) ) {
	$id = $_GET['forward-mail'];
	$sql_query = "SELECT * 
				FROM {$wpdb->prefix}mfs_mailbox
				WHERE id = '%d' ";

	$msg_body_query = $wpdb->prepare( $sql_query, $id );
	
	$val = $wpdb->get_row( $msg_body_query, ARRAY_A );
	
	$receiver_name 	= $val['sender_id'];
	$draft_subject 	= "[Fwd: " . stripslashes( $val['subject'] ) . "']";
	$time 			= $val['time'];
	$content 		= stripslashes( $val['message_body'] );
	
	$concat  = '<br/><br/><div id="prev-details">';
	$concat .= '<span class="original-msg">-------Original Message-------</span><br/>';
	$concat .= '<span><b>From: </b>' . get_userdata( $val['sender_id'] )->user_login . '</span><br />';
	$concat .= '<span><b>Sent: </b>' . date('F d, Y h:i A', strtotime($time)) . '</span><br />';
	$concat .= '<span><b>To: </b>' . get_userdata( $val['receiver_ids'] )->user_login . '</span><br />';
	$concat .= '<span><b>Subject: </b> ' . $draft_subject . '</span><br />';
	$concat .= '</div>';
	$content = $concat . $content;
	
	/* this is for save as draft funcionality */
	if( isset( $_POST['compose-draft'] ) && wp_verify_nonce( $_POST['name_of_nonce_field'], 'name_of_my_action' ) ) {
		
		/* if someone will try to send message without having body then the default subject text will be stored in database */
		if( '' == $_POST['mail-subject']) {
			$subject = 'No Subject';
		} else {
			$subject = esc_sql( esc_attr( $_POST['mail-subject'] ) );
		}
		$status = 3;
		mfs_insert_mail( $_POST, $status, $user_id, $subject );
	}
	
	/* this is for save as draft funcionality */
	if( isset( $_POST['submit'] ) && wp_verify_nonce( $_POST['name_of_nonce_field'], 'name_of_my_action' ) ) {
		
		/* if someone will try to send message without having body then the default subject text will be stored in database */
		if( '' == $_POST['mail-subject']) {
			$subject = 'No Subject';
		} else {
			$subject = esc_sql( esc_attr( $_POST['mail-subject'] ) );
		}
		
		$status = '';
		
		mfs_insert_mail( $_POST, $status, $user_id, $subject );
	}
} else {
	/* this is for sending message funcionality */
	if( isset( $_POST['submit'] ) && wp_verify_nonce( $_POST['name_of_nonce_field'], 'name_of_my_action' )) {
		/* if someone will try to send message without having body then the default subject text will be stored in database */
		if( '' == $_POST['mail-subject']) {
			$subject = 'No Subject';
		} else {
			$subject = esc_sql( esc_attr( $_POST['mail-subject'] ) );
		}
		$status = '';
		mfs_insert_mail( $_POST, $status, $user_id, $subject );
	}
	/* This is for save as draft funcionality */
	if( isset( $_POST['compose-draft'] ) && ( $id == '' ) && wp_verify_nonce( $_POST['name_of_nonce_field'], 'name_of_my_action' ) ) {
		/* if someone will try to send message without having body then the default subject text will be stored in database */
		if( '' == $_POST['mail-subject']) {
			$subject = 'No Subject';
		} else {
			$subject = esc_sql( esc_attr( $_POST['mail-subject'] ) );
		}
		$status = 3;
		mfs_insert_mail( $_POST, $status, $user_id, $subject );
	}
}
?>
<form action="#" class="create-mail-form" id="create-mail-form" method="post" name="create-mail-form">
	<?php wp_nonce_field('name_of_my_action','name_of_nonce_field'); ?>
	<?php
	if( isset( $receiver_name ) && !empty( $receiver_name ) ) {
		?>
		<label class="compose-lbl">To :</label>
		<?php 
			wp_dropdown_users( array( 
							'show'     => 'user_login',
							'selected' => $receiver_name,
							'orderby'  => 'display_name'
							)
					   ); ?>
		<br />
		<?php
	} else {
		?>
		<label class="compose-lbl">To :</label>
		<?php 
			wp_dropdown_users( array( 
							'show'     => 'user_login',
							'orderby'  => 'display_name'
							)
							);
		?>
	     <br />
		<?php
	}
	?> 
	<label class="compose-lbl">Subject :</label>
	<input class="mail-subject" id="mail-subject" name="mail-subject" type="text" value="<?php echo $draft_subject; ?>"  placeholder="Enter a subject" />
	<br /><br />
	<label class="compose-lbl">Message :</label><br />
	<?php
	if( isset( $content ) && !empty( $content ) ) {
		the_editor( stripslashes($content) );
	} else {
		$content = '';
		the_editor( $content );
	}
	?> 
	<br />
	<input class="button-secondary" id="compose-submit" name="submit" type="submit" value="Send" />
	<input class="button-secondary" id="compose-draft" name="compose-draft" type="submit" value="Save as draft" />
</form>
<?php

/**
* Function Name: mfs_update_draft
* Description: This function will update the mail
*/

function mfs_update_draft( $post_data, $status, $user_id, $subject, $id ) {
	global $wpdb;
    /* the mails will be stored in this table */
	$table = "{$wpdb->prefix}mfs_mailbox";
	
	/*Create data array that needs to insert into the databse*/
	$data = array(
				'receiver_ids' => $post_data['user'],
				'sender_id' => $user_id,
				'subject' => addslashes( $subject ),
				'message_body' => addslashes( $post_data['content'] ),
				'time' => date('Y-m-j H:m:s'),
				'folder_id' => $status,
				'read_unread' 	=> 2
			);
	
	$where = array('id' => $id);
	$wpdb->update( $table, $data, $where );
	
	$_SESSION['message'] = '<p class="success">Draft updated successfully</p>';
	?>
	<script>
	window.location = root + '/wp-admin/admin.php?page=drafts-mail';
	</script>
	<?php
}

/**
* Function Name: mfs_insert_mail
* Description: This function will insert the mail into the database
*/
function mfs_insert_mail( $post_data, $status, $user_id, $subject ) {
	global $wpdb;
    /* the mails will be stored in this table */
	$table = "{$wpdb->prefix}mfs_mailbox";
		
	/*Create data array that needs to insert into the databse*/
	$data = array(
				'receiver_ids' => $post_data['user'],
				'sender_id' => $user_id,
				'subject' => addslashes( $subject ),
				'message_body' => addslashes( $post_data['content'] ),
				'time' => date('Y-m-j H:m:s'),
				'folder_id' => $status,
				'read_unread' 	=> 2
			);
			
	if( $wpdb->insert( $table, $data ) ) {	
	
		/* Get the mail_id of the last inserated mail */
		$last_inserted_mail_id = $wpdb->insert_id;
		
		/* Table to store who can see this mail */
		$table_who_can_see = "{$wpdb->prefix}who_can_see";
	
		/*Create data array that needs to insert into the table who_can_see for sender */
		$data_who_can_see = array(
								'mail_id' => $last_inserted_mail_id,
								'user_id' => $user_id
							);
								
		$wpdb->insert( $table_who_can_see, $data_who_can_see );
		
		/*Create data array that needs to insert into the table who_can_see for reciever */
		$data_who_can_see = array(
								'mail_id' => $last_inserted_mail_id,
								'user_id' => $post_data['user']
							);
								
		$wpdb->insert( $table_who_can_see, $data_who_can_see );
		
		if( $status == 3 ) {
			$_SESSION['message'] = '<p class="success">Message saved as draft </p>successfully';
		} else {
			$_SESSION['message'] = '<p class="success">Message sent successfully</p>';
		}
		?>
		<script>
			window.location = root + '/wp-admin/admin.php?page=mail-box';
		</script>
		<?php
	}
}