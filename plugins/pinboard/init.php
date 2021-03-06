<?php
class Pinboard extends Plugin {
	private $link;
	private $host;

	function init($host) {
		$this->link = $host->get_link();
		$this->host = $host;

		$host->add_hook($host::HOOK_ARTICLE_BUTTON, $this);
	}

	function about() {
		return array(1.1,
			"Share article on Pinboard",
			"m.s");
	}

	function get_js() {
		return file_get_contents(dirname(__FILE__) . "/pinboard.js");
	}

	function hook_article_button($line) {
		$article_id = $line["id"];

		$rv = "<img src=\"plugins/pinboard/pinboard.png\" class='tagsPic' style=\"cursor : pointer\" onclick=\"shareArticleToPinboard($article_id)\" title='".__('Share on Pinboard')."'>";

		return $rv;
	}

	function getInfo() {
		$id = db_escape_string($this->link, $_REQUEST['id']);

		$result = db_query($this->link, "SELECT title, link
				FROM ttrss_entries, ttrss_user_entries
				WHERE id = '$id' AND ref_id = id AND owner_uid = " .$_SESSION['uid']);

		if (db_num_rows($result) != 0) {
			$title = truncate_string(strip_tags(db_fetch_result($result, 0, 'title')),
				100, '...');
			$article_link = db_fetch_result($result, 0, 'link');
		}

		print json_encode(array("title" => $title, "link" => $article_link,
				"id" => $id));
	}


}
?>
