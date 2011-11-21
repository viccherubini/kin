<?php namespace kin;

class index_controller extends \kin\controller {

	public function action_get_index() {
		$this->set_content_type("");
		$this->render("response");
	}

}
