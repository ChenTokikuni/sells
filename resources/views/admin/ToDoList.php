<!-- TO DO List -->
          <div class="box box-primary">
            <div class="box-header">
              <i class="ion ion-clipboard"></i>
              <div class="box-tools pull-right">
                <ul class="pagination pagination-sm inline" >
                  <li><a href="#">&laquo;</a></li>
                  <li><a href="#">1</a></li>
                  <li><a href="#">2</a></li>
                  <li><a href="#">3</a></li>
                  <li><a href="#">&raquo;</a></li>
                </ul>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <!-- See dist/js/pages/dashboard.js to activate the todoList plugin -->
              <ul class="todo-list" id = "list-ul">
			  <?php
				foreach($listdatas as $v)
				{
					?>
					<li>
					  <!-- drag handle -->
					  <span class="handle">
							<i class="fa fa-ellipsis-v"></i>
							<i class="fa fa-ellipsis-v"></i>
						  </span>

					  <!-- todo text -->
						  <input readonly id="in-<?php echo $v->id ?>" name ="in-<?php echo $v->id ?>" value="<?php echo $v->text ?>" />
					  <div  id ="edit-<?php echo $v->id ?>"style = "display:none">
						  <button  class=" label-success" onclick = "submitList(<?php echo $v->id ?>)">提交</button>
					  </div>
					  <!-- Emphasis label -->
					  <!--
					  <small class="label label-danger"><i class="fa fa-clock-o"></i> 2 mins</small>
					  -->
					  <!-- General tools such as edit or delete-->
					  <div class="tools">
						<i class="fa fa-edit " id = "listEdit-<?php echo $v->id ?>" onClick = "listEdit(<?php echo $v->id ?>)"></i>
						<i class="fa fa-trash-o " onClick = "listChange('remove',<?php echo $v->id ?>)"></i>
					  </div>
					</li>
				<?php } ?>
				<!--
                <li>
                      <span class="handle">
                        <i class="fa fa-ellipsis-v"></i>
                        <i class="fa fa-ellipsis-v"></i>
                      </span>
                  <input type="checkbox" value="">
                  <span class="text">Make the theme responsive</span>
                  <small class="label label-info"><i class="fa fa-clock-o"></i> 4 hours</small>
                  <div class="tools">
                    <i class="fa fa-edit"></i>
                    <i class="fa fa-trash-o"></i>
                  </div>
                </li>
                <li>
                      <span class="handle">
                        <i class="fa fa-ellipsis-v"></i>
                        <i class="fa fa-ellipsis-v"></i>
                      </span>
                  <input type="checkbox" value="">
                  <span class="text">Let theme shine like a star</span>
                  <small class="label label-warning"><i class="fa fa-clock-o"></i> 1 day</small>
                  <div class="tools">
                    <i class="fa fa-edit"></i>
                    <i class="fa fa-trash-o"></i>
                  </div>
                </li>
                <li>
                      <span class="handle">
                        <i class="fa fa-ellipsis-v"></i>
                        <i class="fa fa-ellipsis-v"></i>
                      </span>
                  <input type="checkbox" value="">
                  <span class="text">Let theme shine like a star</span>
                  <small class="label label-success"><i class="fa fa-clock-o"></i> 3 days</small>
                  <div class="tools">
                    <i class="fa fa-edit"></i>
                    <i class="fa fa-trash-o"></i>
                  </div>
                </li>
                <li>
                      <span class="handle">
                        <i class="fa fa-ellipsis-v"></i>
                        <i class="fa fa-ellipsis-v"></i>
                      </span>
                  <input type="checkbox" value="">
                  <span class="text">Check your messages and notifications</span>
                  <small class="label label-primary"><i class="fa fa-clock-o"></i> 1 week</small>
                  <div class="tools">
                    <i class="fa fa-edit"></i>
                    <i class="fa fa-trash-o"></i>
                  </div>
                </li>
                <li>
                      <span class="handle">
                        <i class="fa fa-ellipsis-v"></i>
                        <i class="fa fa-ellipsis-v"></i>
                      </span>
                  <input type="checkbox" value="">
                  <span class="text">Let theme shine like a star</span>
                  <small class="label label-default"><i class="fa fa-clock-o"></i> 1 month</small>
                  <div class="tools">
                    <i class="fa fa-edit"></i>
                    <i class="fa fa-trash-o"></i>
                  </div>
                </li>-->
              </ul>
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix no-border">
				<div class="input-group">
					<input class="form-control"  id ="textInput"placeholder="输入待办事项....">
					<div class="input-group-btn" onClick = "listAdd()">
					  <button type="button" class="btn btn-success" ><i class="fa fa-plus"></i></button>
					</div>
				</div>
			</div>
          </div>
          <!-- /.box -->

<script src="/js/pages/dashboard.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="/js/pages/jquery-ui/jquery-ui.min.js"></script>

<script>

	//list data api
	function listChange(action,id,text = ''){
		//console.log( action + id );
			$.ajax({
				type: 'get',
				url: '/admin/listChange',
				dataType: 'json',
				data: { action , id , text },
				success: function(load_data){
					//console.log(load_data['error']);
					toastr.success(load_data['error']);
					$.pjax.reload("#pjax-container");
				},
			});
	}
	
	//add list data when buttom click
	function listAdd(){
		var input = document.getElementById('textInput').value;
		var checkIsnull = isNull(input);
		//console.log(checkIsnull);
		if(!checkIsnull){
			var action = 'add';
			//add input data
			listChange(action,input);
			//clear input
			document.getElementById('textInput').value="";
		}else{
			alert('需输入待办事项且不能全为空格');
		}
		
	}
	
	//edit list data when buttom click
	function listEdit(id){
		document.getElementById('edit-' + id).style.display = 'inline';
		document.getElementById('listEdit-' + id).style.display = 'none';
		document.getElementById('in-' + id).readOnly = false;
		
	}
	
	function submitList(id){
		var action = 'edit';
		var input = document.getElementById('in-' + id).value;

		listChange(action,id,input);
		document.getElementById('in-' + id).readOnly = true;
		document.getElementById('listEdit-' + id).style.display = 'inline';
		document.getElementById('edit-' + id).style.display = 'none';
	}
	//check input value is null and all space
	function isNull( str ){
	if ( str == "" ) return true;
	var regu = "^[ ]+$";
	var re = new RegExp(regu);
	return re.test(str);
	}
	

</script>