<?php

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class ImportCsv extends AbstractTool
{
	protected $options = [];

	public function __construct($url)
	{
		$this->options['url'] = $url;
	}

	protected function script()
	{
		return <<<SCRIPT

$("input#csv-file").change(function() {
	var that = this,
		fd = new FormData(),
		btn = $(this).parent(".btn"),
		btn_text = $(this).siblings(".btn-text"),
		text = btn_text.text();
	fd.append("csv_file", this.files[0]);
	$.ajax({
		url: "{$this->options['url']}",
		type: "post",
		data: fd,
		dataType: "json",
		processData: false,
		contentType: false,
		headers: {
			"X-CSRF-TOKEN": LA.token
		},
		beforeSend: function() {
			that.disabled = true;
			btn.addClass("disabled");
			btn_text.text("importing...");
		},
		success: function(res) {
			if (res.error === "000") {
				$.pjax.reload("#pjax-container");
				toastr.success("汇入成功 !");
			} else if (res.msg) {
				alert(res.msg);
			} else {
				alert("发生未知的错误.");
			}
		},
		error: function(jqXHR) {
			if (jqXHR.status == "419") {
				if (confirm("Session 已失效，请重新整理页面.")) {
					location.reload();
				}
			}
		},
		complete: function() {
			that.disabled = false;
			btn.removeClass("disabled");
			btn_text.text(text);
			that.value = "";
		}
	});
});

SCRIPT;
	}

	public function render()
	{
		Admin::script($this->script());

		return view('admin.tools.import_csv', $this->options);
	}

}
