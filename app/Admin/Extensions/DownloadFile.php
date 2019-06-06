<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class DownloadFile
{
	protected $id;
	protected $url;

	public function __construct($id, $url)
	{
		$this->id = $id;
		$this->url = $url;
	}

	protected function script()
	{
		return <<<SCRIPT

$(".download-file-btn").on("click", function () {

	var f = document.createElement("form"),
		input1 = document.createElement("input"),
		input2 = document.createElement("input");

	f.setAttribute("method", "post");
	f.setAttribute("action", "{$this->url}");
	f.setAttribute("target", "_blank");

	input1.setAttribute("name", "id");
	input1.setAttribute("value", $(this).data("id"));
	input2.setAttribute("name", "_token");
	input2.setAttribute("value", LA.token);

	f.appendChild(input1);
	f.appendChild(input2);

	document.body.appendChild(f);

	f.submit();

	document.body.removeChild(f);

});

SCRIPT;
	}

	protected function render()
	{
		Admin::script($this->script());

		return '<a href="javascript:void(0);" class="download-file-btn" data-id="' . $this->id. '"><i class="fa fa-download"></i></a>';
	}

	public function __toString()
	{
		return $this->render();
	}

}
