<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once MODPATH.'core/controllers/nova_manage.php';

class Manage extends Nova_manage {

	public function __construct()
	{
		parent::__construct();
	}

	protected function _email($type, $data)
	{
		// load the libraries
		$this->load->library('mail');
		$this->load->library('parser');

		// define the variables
		$email = false;

		switch ($type)
		{
			case 'news':
				// set some variables
				$from_name = $this->char->get_character_name($data['author'], true, true);
				$from_email = $this->user->get_email_address('character', $data['author']);
				$subject = $data['category'] .' - '. $data['title'];

				// set the content
				$content = sprintf(
					lang('email_content_news_item'),
					$from_name,
					$data['content']
				);

				// set the email data
				$email_data = array(
					'email_subject' => $subject,
					'email_content' => ($this->mail->mailtype == 'html') ? nl2br($content) : $content
				);

				// where should the email be coming from
				$em_loc = Location::email('write_newsitem', $this->mail->mailtype);

				// parse the message
				$message = $this->parser->parse_string($em_loc, $email_data, true);

				// get the email addresses
				$emails = $this->user->get_crew_emails(true, 'email_news_items');

				// make a string of email addresses
				$to = implode(',', $emails);

				// set the parameters for sending the email
				$this->mail->from(Util::email_sender(), $from_name);
				$this->mail->to($to);
				$this->mail->reply_to($from_email);
				$this->mail->cc($this->settings->get_setting('external_mailing_list'));
				$this->mail->subject($this->options['email_subject'] .' '. $subject);
				$this->mail->message($message);
			break;

			case 'log':
				// set some variables
				$from_name = $this->char->get_character_name($data['author'], true, true);
				$from_email = $this->user->get_email_address('character', $data['author']);
				$subject = $from_name ."'s ". lang('email_subject_personal_log') ." - ". $data['title'];

				// set the content
				$content = sprintf(
					lang('email_content_personal_log'),
					$from_name,
					$data['content']
				);

				// set the email data
				$email_data = array(
					'email_subject' => $subject,
					'email_content' => ($this->mail->mailtype == 'html') ? nl2br($content) : $content
				);

				// where should the email be coming from
				$em_loc = Location::email('write_personallog', $this->mail->mailtype);

				// parse the message
				$message = $this->parser->parse_string($em_loc, $email_data, true);

				// get the email addresses
				$emails = $this->user->get_crew_emails(true, 'email_personal_logs');

				// make a string of email addresses
				$to = implode(',', $emails);

				// set the parameters for sending the email
				$this->mail->from(Util::email_sender(), $from_name);
				$this->mail->to($to);
				$this->mail->reply_to($from_email);
				$this->mail->cc($this->settings->get_setting('external_mailing_list'));
				$this->mail->subject($this->options['email_subject'] .' '. $subject);
				$this->mail->message($message);
			break;

			case 'post':
				// set some variables
				$subject = $data['mission'] ." - ". $data['title'];
				$mission = lang('email_content_post_mission') . $data['mission'];
				$authors = lang('email_content_post_author') . $this->char->get_authors($data['authors'], true);
				$timeline = lang('email_content_post_timeline') . $data['timeline'];
				$location = lang('email_content_post_location') . $data['location'];

				// figure out who it needs to come from
				$my_chars = array();

				// find out how many of the submitter's characters are in the string
				foreach ($this->session->userdata('characters') as $value)
				{
					if (strstr($data['authors'], $value) !== false)
					{
						$my_chars[] = $value;
					}
				}

				// set who the email is coming from
				$from_name = $this->char->get_character_name($my_chars[0], true, true);
				$from_email = $this->user->get_email_address('character', $my_chars[0]);

				// set the content
				$content = sprintf(
					lang('email_content_mission_post'),
					$authors,
					$mission,
					$location,
					$timeline,
					$data['content']
				);

				// set the email data
				$email_data = array(
					'email_content' => ($this->mail->mailtype == 'html') ? nl2br($content) : $content
				);

				// where should the email be coming from
				$em_loc = Location::email('write_missionpost', $this->mail->mailtype);

				// parse the message
				$message = $this->parser->parse_string($em_loc, $email_data, true);

				// get the email addresses
				$emails = $this->user->get_crew_emails(true, 'email_mission_posts');

				// make a string of email addresses
				$to = implode(',', $emails);

				// set the parameters for sending the email
				$this->mail->from(Util::email_sender(), $from_name);
				$this->mail->to($to);
				$this->mail->reply_to($from_email);
				$this->mail->cc($this->settings->get_setting('external_mailing_list'));
				$this->mail->subject($this->options['email_subject'] .' '. $subject);
				$this->mail->message($message);
			break;

			case 'log_comment':
				// load the models
				$this->load->model('personallogs_model', 'logs');

				// run the methods
				$row = $this->logs->get_log($data['log']);
				$name = $this->char->get_character_name($data['author']);
				$from = $this->user->get_email_address('character', $data['author']);
				$to = $this->user->get_email_address('character', $row->log_author);

				// set the content
				$content = sprintf(
					lang('email_content_log_comment_added'),
					"<strong>". $row->log_title ."</strong>",
					$data['comment']
				);

				// create the array passing the data to the email
				$email_data = array(
					'email_subject' => lang('email_subject_log_comment_added'),
					'email_from' => ucfirst(lang('time_from')) .': '. $name .' - '. $from,
					'email_content' => ($this->mail->mailtype == 'html') ? nl2br($content) : $content
				);

				// where should the email be coming from
				$em_loc = Location::email('sim_log_comment', $this->mail->mailtype);

				// parse the message
				$message = $this->parser->parse_string($em_loc, $email_data, true);

				// set the parameters for sending the email
				$this->mail->from(Util::email_sender(), $name);
				$this->mail->to($to);
				$this->mail->reply_to($from);
				$this->mail->subject($this->options['email_subject'] .' '. $email_data['email_subject']);
				$this->mail->message($message);
			break;

			case 'news_comment':
				// load the models
				$this->load->model('news_model', 'news');

				// run the methods
				$row = $this->news->get_news_item($data['news_item']);
				$name = $this->char->get_character_name($data['author']);
				$from = $this->user->get_email_address('character', $data['author']);
				$to = $this->user->get_email_address('character', $row->news_author_character);

				// set the content
				$content = sprintf(
					lang('email_content_news_comment_added'),
					"<strong>". $row->news_title ."</strong>",
					$data['comment']
				);

				// create the array passing the data to the email
				$email_data = array(
					'email_subject' => lang('email_subject_news_comment_added'),
					'email_from' => ucfirst(lang('time_from')) .': '. $name .' - '. $from,
					'email_content' => ($this->mail->mailtype == 'html') ? nl2br($content) : $content
				);

				// where should the email be coming from
				$em_loc = Location::email('main_news_comment', $this->mail->mailtype);

				// parse the message
				$message = $this->parser->parse_string($em_loc, $email_data, true);

				// set the parameters for sending the email
				$this->mail->from(Util::email_sender(), $name);
				$this->mail->to($to);
				$this->mail->reply_to($from);
				$this->mail->subject($this->options['email_subject'] .' '. $email_data['email_subject']);
				$this->mail->message($message);
			break;

			case 'post_comment':
				$this->load->model('posts_model', 'posts');

				$row = $this->posts->get_post($data['post']);

				$name = $this->char->get_character_name($data['author']);
				$from = $this->user->get_email_address('character', $data['author']);

				$authors = $this->posts->get_author_emails($data['post']);

				foreach ($authors as $key => $value)
				{
					$user = $this->user->get_user_id_from_email($value);

					$pref = $this->user->get_pref('email_new_post_comments', $user);

					if ($pref == 'n' or $pref == '')
					{
						unset($authors[$key]);
					}
				}

				$to = implode(',', $authors);

				$content = sprintf(
					lang('email_content_post_comment_added'),
					"<strong>". $row->post_title ."</strong>",
					$data['comment']
				);

				$email_data = array(
					'email_subject' => lang('email_subject_post_comment_added'),
					'email_from' => ucfirst(lang('time_from')) .': '. $name .' - '. $from,
					'email_content' => ($this->mail->mailtype == 'html') ? nl2br($content) : $content
				);

				$em_loc = Location::email('sim_post_comment', $this->mail->mailtype);

				$message = $this->parser->parse_string($em_loc, $email_data, true);

				$this->mail->from(Util::email_sender(), $name);
				$this->mail->to($to);
				$this->mail->reply_to($from);
				$this->mail->subject($this->options['email_subject'] .' '. $email_data['email_subject']);
				$this->mail->message($message);
			break;

			case 'wiki_comment':
				// load the models
				$this->load->model('wiki_model', 'wiki');

				// run the methods
				$page = $this->wiki->get_page($data['page']);
				$row = $page->row();
				$name = $this->char->get_character_name($data['author']);
				$from = $this->user->get_email_address('character', $data['author']);

				// get all the contributors of a wiki page
				$cont = $this->wiki->get_all_contributors($data['page']);

				foreach ($cont as $c)
				{
					$pref = $this->user->get_pref('email_new_wiki_comments', $c);

					if ($pref == 'y')
					{
						$to_array[] = $this->user->get_email_address('user', $c);
					}
				}

				// set the to string
				$to = implode(',', $to_array);

				// set the content
				$content = sprintf(
					lang('email_content_wiki_comment_added'),
					"<strong>". $row->draft_title ."</strong>",
					$data['comment']
				);

				// create the array passing the data to the email
				$email_data = array(
					'email_subject' => lang('email_subject_wiki_comment_added'),
					'email_from' => ucfirst(lang('time_from')) .': '. $name .' - '. $from,
					'email_content' => ($this->mail->mailtype == 'html') ? nl2br($content) : $content
				);

				// where should the email be coming from
				$em_loc = Location::email('wiki_comment', $this->mail->mailtype);

				// parse the message
				$message = $this->parser->parse_string($em_loc, $email_data, true);

				// set the parameters for sending the email
				$this->mail->from(Util::email_sender(), $name);
				$this->mail->to($to);
				$this->mail->reply_to($from);
				$this->mail->subject($this->options['email_subject'] .' '. $email_data['email_subject']);
				$this->mail->message($message);
			break;

			case 'docking_accept':
				$cc = implode(',', $this->user->get_emails_with_access('manage/docked'));

				$email_data = array(
					'email_subject' => lang('email_subject_docking_approved') .' - '. $data['sim'],
					'email_from' => ucfirst(lang('time_from')) .': '. $this->options['sim_name'] .' - '. $this->options['default_email_address'],
					'email_content' => ($this->mail->mailtype == 'html') ? nl2br($data['message']) : $data['message']
				);

				$em_loc = Location::email('docked_action', $this->mail->mailtype);

				$message = $this->parser->parse_string($em_loc, $email_data, true);

				$this->mail->from(Util::email_sender(), $this->options['sim_name']);
				$this->mail->to($data['email']);
				$this->mail->reply_to($data['fromEmail']);
				$this->mail->cc($cc);
				$this->mail->subject($this->options['email_subject'] .' '. $email_data['email_subject']);
				$this->mail->message($message);
			break;

			case 'docking_reject':
				$cc = implode(',', $this->user->get_emails_with_access('manage/docked'));

				$email_data = array(
					'email_subject' => lang('email_subject_docking_rejected') .' - '. $data['sim'],
					'email_from' => ucfirst(lang('time_from')) .': '. $this->options['sim_name'] .' - '. $this->options['default_email_address'],
					'email_content' => ($this->mail->mailtype == 'html') ? nl2br($data['message']) : $data['message']
				);

				$em_loc = Location::email('docked_action', $this->mail->mailtype);

				$message = $this->parser->parse_string($em_loc, $email_data, true);

				$this->mail->from(Util::email_sender(), $this->options['sim_name']);
				$this->mail->to($data['email']);
				$this->mail->reply_to($data['fromEmail']);
				$this->mail->cc($cc);
				$this->mail->subject($this->options['email_subject'] .' '. $email_data['email_subject']);
				$this->mail->message($message);
			break;
		}

		// send the email
		$email = $this->mail->send();

		return $email;
	}
}
