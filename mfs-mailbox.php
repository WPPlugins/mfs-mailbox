<?php
/*
 *Plugin Name: MFS Mailbox
 *Description: This plugin plugin will allow registered users to send mail(s) to other registered users.
 *Version: 1.0
 *Author: Mindfire-Solutions
 *Author URI: http://www.mindfiresolutions.com/
 */

/*=======================================================================================*/
//error_reporting(1);
session_start();
global $unread_count;
/**
 * Actions calling section
 */
register_activation_hook( __FILE__, 'mfs_mailbox_install' );
add_action('admin_menu', 'mfs_mailbox_link');
add_action('admin_menu', 'mfs_mailbox_sub_links');

/** 
 *  Function Name: mfs_mailbox_install
 *  Description: Create table while installing the plugin.
 */
function mfs_mailbox_install() {
	global $wpdb;

	$mfs_mailbox_table_sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}mfs_mailbox (
	  id int(11) NOT NULL AUTO_INCREMENT,
	  receiver_ids text NOT NULL,
	  sender_id int(11) NOT NULL,
	  subject text NOT NULL,
	  message_body text NOT NULL,
	  time datetime NOT NULL,
	  folder_id tinyint(1) NOT NULL COMMENT '1 for inbox 2 for sent 3 for draft  4 for  trash',
	  read_unread tinyint(1) NOT NULL COMMENT '1 for read 2 for unread',
	  who_can_see text NOT NULL,
	  PRIMARY KEY (id)
	);";
	$wpdb->query($mfs_mailbox_table_sql);

	$who_can_see_sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}who_can_see (
			  mail_id int(11) NOT NULL,
			  user_id int(11) NOT NULL
			);";
	$wpdb->query($who_can_see_sql);	
	
}

/** 
 *  Function Name: mfs_load_js_css_admin
 *  Description: Load css and js files.
 */
function mfs_load_js_css_admin() {
	/* Enqueue plugin style-file */
	wp_register_style('mailbox-css', plugins_url('css/mailbox.css', __FILE__));
	wp_enqueue_style('mailbox-css');
	
	/* Enqueue plugin js-file */
	wp_enqueue_script('mailbox-js', plugins_url('js/mailbox.js', __FILE__), array('jquery'));
	
	wp_localize_script('mailbox-js', 'root', site_url());
}

/**
 * Function Name: mfs_mailbox_link
 * Description: Add a menu link as main menu on left sidebar in admin section
 */
function mfs_mailbox_link() {
	if( function_exists( 'add_menu_page' ) ) {
		global $unread_count;
		$unread_count = get_unread_mail_count();
		/* Add link for inbox mails */
		add_menu_page( 'Mail Box',
					   'MailBox (' . $unread_count . ')',
					   0,
					   'mail-box',
					   'mfs_mailbox_inbox',
					   plugins_url('mfs-mailbox/images/mail-box.png')
					 );
	}
}

/**
 * Function Name: mfs_mailbox_sub_links
 * Description: This function will create sublinks for inbox, sent, drafts, trash mails
 */
function mfs_mailbox_sub_links() {
	if( function_exists( 'add_submenu_page' ) ) {
		global $unread_count;
		/* Add link for received mails */
		$inbox = add_submenu_page( 'mail-box',
						  'Inbox',
						  'Inbox (' . $unread_count . ')',
						  0,
						  'mail-box',
						  'mfs_mailbox_inbox'
						);
		/* Add link for sent mails */
		$send = add_submenu_page( 'mail-box',
						  'Sent',
						  'Sent',
						  0,
						  'sent-mail',
						  'mfs_mailbox_sent'
						);
		/* Add link for draft mails */	
		$draft = add_submenu_page( 'mail-box',
						  'Drafts',
						  'Drafts',
						  0,
						  'drafts-mail',
						  'mfs_mailbox_drafts'
						);
		/* Add link for trash mails */	
		$trash = add_submenu_page( 'mail-box',
						  'Trash',
						  'Trash',
						  0,
						  'trash-mail',
						  'mfs_mailbox_trash'
						);
		/* Add link to show mail */	
		$show_mail = add_submenu_page( 'mail-box',
						  '',
						  '',
						  0,
						  'show-mail',
						  'mfs_show_mail'
						);
		/* Add link for compose mails */
		$compose = add_submenu_page( 'mail-box',
						  'Compose',
						  'Compose',
						  0,
						  'compose-mail',
						  'mfs_compose_mail'
						);
		add_action( "admin_print_scripts-$inbox",  	  'mfs_load_js_css_admin' );
		add_action( "admin_print_scripts-$send",   	  'mfs_load_js_css_admin' );
		add_action( "admin_print_scripts-$draft",  	  'mfs_load_js_css_admin' );
		add_action( "admin_print_scripts-$trash",  	  'mfs_load_js_css_admin' );
		add_action( "admin_print_scripts-$show_mail", 'mfs_load_js_css_admin' );
		add_action( "admin_print_scripts-$compose",   'mfs_load_js_css_admin' );
	
	}
}

/**
 * Function Name: mfs_mailbox_inbox
 * Description: This function will include the inbox.php file
 */
function mfs_mailbox_inbox() {
	include_once( 'inbox.php' );
}

/**
 * Function Name: mfs_mailbox_sent
 * Description: This function will include the sent.php file
 */
function mfs_mailbox_sent() {
	include_once( 'sent.php' );
}

/**
 * Function Name: mfs_mailbox_drafts
 * Description: This function will include the drafts.php file
 */
function mfs_mailbox_drafts() {
	include_once( 'drafts.php' );
}

/**
 * Function Name: mfs_mailbox_trash
 * Description: This function will include the trash.php file
 */

function mfs_mailbox_trash() {
	include_once( 'trash.php' );
}

/**
 * Function Name: mfs_show_mail
 * Description: This function will include the show-mail.php file
 */
function mfs_show_mail() {
	include_once( 'show-mail.php' );
}

/**
 * Function Name: mfs_compose_mail
 * Description: This function will include the compose-mail.php file
 */
function mfs_compose_mail() {
	$user_id = get_current_user_id();
	include_once( 'compose-mail.php' );
}

/**
 * Function Name: get_unread_mail_count
 * Description: This function will count of unread mails
 */
function get_unread_mail_count() {
	global $wpdb;
	$user_id = get_current_user_id();
	
	/* Create raw sql query */
    $inbox_mails_count_sql = "SELECT count( DISTINCT(id) ) 
							  FROM {$wpdb->prefix}mfs_mailbox 
							  INNER JOIN {$wpdb->prefix}who_can_see
								ON id = mail_id
							  WHERE FIND_IN_SET(receiver_ids, '%d')
								AND folder_id NOT IN ( '3', '4' ) 
								AND read_unread = 2 
								AND user_id = %d";
					    
    /* Prepare raw sql query*/
    $inbox_mails_count_sql = $wpdb->prepare( $inbox_mails_count_sql, $user_id, $user_id );
    
    /* Execute the sql query */
    return $inbox_mails_count = $wpdb->get_var( $inbox_mails_count_sql );
}