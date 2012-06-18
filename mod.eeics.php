<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * EE-ICS Module Front End File
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Module
 * @author		Jesse Bunch
 * @link		http://getbunch.com/
 */

class Eeics {
	
	/**
	 * Return variable for constructor
	 * @author Jesse Bunch
	*/
	public $return_data;
	
	/**
	 * Constructor
	 * @author Jesse Bunch
	*/
	public function __construct() {
		$this->EE =& get_instance();
	}

	public function generate_link() {

		$event_title = $this->EE->TMPL->fetch_param('title', 'New Event');
		$event_description = $this->EE->TMPL->fetch_param('description', '');
		$event_start_time = $this->EE->TMPL->fetch_param('start');
		$event_end_time = $this->EE->TMPL->fetch_param('end');

		if (empty($event_start_time)) {
			$event_start_time = time();
		} else {
			$event_start_time = strtotime($event_start_time);
		}

		if (empty($event_end_time)) {
			$event_end_time = time();
		} else {
			$event_end_time = strtotime($event_end_time);
		}
		
		$url_base = $this->EE->functions->fetch_site_index(FALSE, FALSE);
		$url_vars = array(
			'title' => $event_title,
			'description' => $event_description,
			'start' => $event_start_time,
			'end' => $event_end_time
		);
		
		$url = $url_base.QUERY_MARKER;
		$url .= 'ACT='.$this->EE->functions->fetch_action_id('Eeics', 'generate_ics');
		$url .= '&'.http_build_query($url_vars);

		return $url;

	}

	public function generate_ics() {

		$this->EE->load->helper("URL");

		$event_title = $this->EE->input->get('title', 'New Event');
		$event_title_slug = strtolower(url_title($event_title));
		$event_description = $this->EE->input->get('description', '');
		$event_start_time = intval($this->EE->input->get('start'));
		$event_end_time = intval($this->EE->input->get('end'));
		
		$event_title = str_replace("\n\r", '', $event_title);
		$event_description = str_replace("\n\r", '=0D=0A=', $event_description);

		$event_start_time = gmdate('Ymd\THms\Z', $event_start_time);
		$event_end_time = gmdate('Ymd\THms\Z', $event_end_time);

		$ics_content = "BEGIN:VCALENDAR\n";
		$ics_content .= "VERSION:2.0\n";
		$ics_content .= "BEGIN:VEVENT\n";
		$ics_content .= "SUMMARY:$event_title\n";
		$ics_content .= "DESCRIPTION;ENCODING=QUOTED-PRINTABLE:$event_description\n";
		$ics_content .= "DTSTART:$event_start_time\n";
		$ics_content .= "DTEND:$event_end_time\n";
		$ics_content .= "END:VEVENT\n";
		$ics_content .= "END:VCALENDAR\n";
		
		// Set content type and download header
		// Print out the ICS file for download
		header('Content-type: text/calendar; charset=utf-8');
		header("Content-Disposition: inline; filename=$event_title_slug.ics");
		echo $ics_content;
		exit;

	}

	public function escape() {

		$strip_newlines = $this->EE->TMPL->fetch_param('strip_newlines', 'no');
		$content = $this->EE->TMPL->tagdata;

		$content = str_replace(array("\n","\r"), '\n', $content);
		$content = str_replace(array("\t"), '', $content);
		$content = strip_tags($content);

		return $content;

	}
	


}
/* End of file mod.eeics.php */
/* Location: /system/expressionengine/third_party/eeics/mod.eeics.php */