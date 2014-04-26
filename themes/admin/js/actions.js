var new_action = function(hash) {
	$.get( 
		'/admin/index.php?action=get_value_action&hash=configuration/type',
		function(types_data){
			console.log(types_data);
			var type_list = Array();
			var type_list_option = "";
			for(var index in types_data) { 
				var attr = types_data[index]; 
				type_list.push(attr);
				type_list_option += '<option value="' + attr + '">' + attr + '</option>';
			}
			$().w2form({
				name: 	'new',
				url: 	'/admin/index.php?action=new_action', 
				style: 	'border: 0px; background-color: transparent;',
				formHTML: 
					'<div class="w2ui-page page-0">'+
					'	<input name="hash" type="hidden" size="35" />'+
					'	<div class="w2ui-label">description:</div>'+
					'	<div class="w2ui-field">'+
					'		<input name="description" type="text" size="35"/>'+
					'	</div>'+
					'	<div class="w2ui-label">name:</div>'+
					'	<div class="w2ui-field">'+
					'		<input name="name" type="text" size="35"/>'+
					'	</div>'+
					'	<div class="w2ui-label">position:</div>'+
					'	<div class="w2ui-field">'+
					'		<input name="position" type="text" size="35"/>'+
					'	</div>'+
					'	<div class="w2ui-label">type:</div>'+
					'	<div class="w2ui-field">'+
					'		<select name="type" >'+
						type_list_option +
					'		</select>'+
					'	</div>'+
					'</div>'+
					'<div class="w2ui-buttons">'+
					'	<input type="button" value="Add" name="add">'+
					'</div>',

				fields: [
					{ name: 'hash', type: 'text' },
					{ name: 'description', type: 'text', required: true },
					{ name: 'position', type: 'int', required: true },
					{ name: 'type', type: 'select', required: true, 
						options: { items: type_list } },
					{ name: 'name', type: 'text', required: true },
				],
				record: {
					hash: hash.replace(/_/g, '/'),
					position: 0
				},
				actions: {
					add: function () {
						if (!/[a-zA-Z0-9\-]+/.test(this.record.name)) {
							w2alert("Error: name can only contain letters, numbers, and '-'");
							return;
						}

						var obj = this;
						this.save({}, 
						function (data) { 
							if (data.status == 'error') {
								w2alert(data.message);
								return;
							}
							w2popup.close();
							get_template(
								hash,
								data.record.name,  
								data.record.type, 
								data.record.description, 
								data.record.position,  
								"",
								true,
								function(data){
									$("#list_block_" + hash).append(data);
								}
								);
							
						});
					},
				}
			});

			$().w2popup('open', {
				title	: 'New',
				body	: '<div id="form" style="width: 100%; height: 100%;"></div>',
				style	: 'padding: 15px 0px 0px 0px',
				width	: 400,
				height	: 270, 
				showMax : true,
				onOpen	: function (event) {
					event.onComplete = function () {
						$('#w2ui-popup #form').w2render('new');
					}
				},
				onClose : function () {
					$().w2destroy("new");
				}
			});
		},
		"json"
	);
}

var edit_params_action = function(hash, name) {
	$.get( 
		'/admin/index.php?action=get_item_action&hash=' + hash.replace(/_/g, '/') + '/' + name
		, function(data) {
			console.log(data);
			if (data.status == 'error') {
				w2alert(data.message);
				return;
			}

			$().w2form({
				name: 	'edit',
				url: 	'/admin/index.php?action=edit_params_action&hash=' + hash.replace(/_/g, '/') + '&name=' + name, 
				style: 	'border: 0px; background-color: transparent;',
				fields: [
					{ name: 'description', type: 'text', required: true },
					{ name: 'name', type: 'text', required: true },
					{ name: 'position', type: 'int', required: true },
				],
				record: data['record'],
				actions: {
					save: function () {
						if (!/[a-zA-Z0-9\-]+/.test(this.record.name)) {
							w2alert("Error: name can only contain letters, numbers, and '-'");
							return;
						}

						var obj = this;
						this.save({}, 
							function (data) { 
								if (data.status == 'error') {
									w2alert(data.message);
									return;
								}
								w2popup.close();
								get_template(
									hash, 
									data.record.name,  
									data.record.type, 
									data.record.description, 
									data.record.position, 
									data.record.value, 
									false,
									function(data){
										$('#item_' + hash + '_' + name).html(data);
									}
								);
						});
					},
				}
			});
			$().w2popup('open', {
				title	: 'Edit',
				body	: '<div id="form" style="width: 100%; height: 100%;"></div>',
				style	: 'padding: 15px 0px 0px 0px',
				width	: 500,
				height	: 250, 
				showMax : true,
				onOpen	: function (event) {
					event.onComplete = function () {
						$('#w2ui-popup #form').w2render('edit');
					}
				},
				onClose : function () {
					$().w2destroy("edit");
				}
			});
		}
		, "json"
	);
}

var save_value_action = function(id) {
	var arg = {};

	$("#" + id+" .item_value").each(function(){
		arg[$(this).attr('name').replace(/_/g, '/')] = ($(this).attr('type') == 'checkbox') ?  $(this).is(':checked') : $(this).val();
	});

	console.log(arg);

	w2popup.open({
		title: 'Update...',
		body: ''
	});
	w2popup.lock('Update...', true);
	$.post( 
		'/admin/index.php?action=save_value_action', 
		{ 
			value: arg
		},
		function(data) {
			console.log(data);
			if (data.status == 'error') {
				w2alert(data.message);
			}
		},
		'json'
	).done(function(){ 
		w2popup.unlock();
		w2popup.close();
	});
}

var remove_action = function(hash) {
	w2popup.open({
		title: 'Remove...',
		body: ''
	});
	w2popup.lock('Remove...', true);
	$.post( 
		'/admin/index.php?action=remove_action', 
		{ 
			hash: hash.replace(/_/g, '/')
		},
		function(data) {
			if (data.status == 'error') {
				w2alert(data.message);
			}
		}
	).done(function(){
		$('#item_' + hash).remove();
		w2popup.unlock();
		w2popup.close();
	});
}

var clone_action = function(hash, name) {
	w2popup.open({
		title: 'Cloning...',
		body: ''
	});
	w2popup.lock('Cloning...', true);
	$.post( 
		'/admin/index.php?action=clone_action', 
		{ 
			hash: hash.replace(/_/g, '/'),
			name: name
		},
		function(data) {
			console.log(data);
			if (data.status == 'error') {
				w2alert(data.message);
			}
			var parent_id = data.record.parent;
			get_template(
				hash, 
				data.record.name, 
				data.record.type, 
				data.record.description, 
				data.record.position, 
				data.record.value, 
				true, 
				function(data){
					$("#list_block_" + hash).append(data);
				}
				);
		},
		'json'
	).done(function(){ 
		w2popup.unlock();
		w2popup.close();
	});
}



var get_template_loop = function(id, index, list, hash) {
	var item = list[index];
	console.log(item);
	get_template(
		hash, 
		item['name'], 
		item['type'], 
		item['description'], 
		item['position'], 
		item['value'], 
		true, 
		function(data){
			$("#" + id).append(data);
			if (index + 1 < list.length)
				get_template_loop(id, index + 1, list, hash);
			else
				$("#load_gif_" + hash).hide();
		});
}

var get_list = function(id, hash) {

	if ($("#" + id).children().length != 0) {
		$("#"+id).html("");
		return;
	}
	$("#load_gif_" + hash).show();
	$.get( 
		'/admin/index.php?action=get_item_action&hash=' + hash.replace(/_/g, '/'), 
		function(data){
			console.log(data);
			if (data.status == 'error') {
				return;
			}

			if (data['record']['value'].length > 0) {
				get_template_loop(id, 0, data['record']['value'], hash);
			} else {
				$("#load_gif_" + hash).hide();
			}
		}
		, "json" 	
		);
}

var get_template = function(hash, name, type, description, position, value, wrap, done_function) {
	$.ajaxSetup({ cache: false });
	$.get( 'item_template/' + type + '.html')
		.done(function( data ) {
			//value mode
			if (value == null)
				value = "";
			if (type == "bool")
				value = (value == 'true') ? "checked" : "";

			var this_hash = hash + '_' + name;

			var template = 
				'	<div class="inner_wrap">'+
 					data.replace(/##ID_HASH##/g, this_hash)
			  			.replace(/##VALUE##/g, value)
			  			.replace(/##DESCRIPTION##/g, description)+
				'		<div class="action_wrap">'+
				'			<button class="action action_hider"></button>'+
				'			<button class="action edit_action" onclick="edit_params_action(\'' + hash + '\', \'' + name + '\')"></button>'+
				'			<button class="action remove_action" onclick="remove_action(\'' + this_hash + '\')"></button>'+
				'			##ADD_ACTION##'+
				'			<button class="action clone_action" onclick="clone_action(\'' + hash + '\', \'' + name + '\')"></button>'+
				'		</div>'+				
				'		<div style="clear:both;"></div>'+
				'	</div>'+

				'	##LIST_BLOCK##'+
				'';
			
			if (type == "list")
				template = template
					.replace("##ADD_ACTION##", '<button class="action add_action" onclick="new_action(\'' + this_hash + '\')"></button>' )
					.replace("##LIST_BLOCK##", '<ul id="list_block_' + this_hash + '" class="list_item"></ul>' );
			else
				template = template
					.replace("##ADD_ACTION##", '' )
					.replace("##LIST_BLOCK##", '' );


			if (wrap)
				template = '<li class="item_wrapper" id="item_' + this_hash + '">' + template + '</li>';

			done_function(template);
			script_type_set();
  		});
}

var info_action = function(ths) {
	// ths.parent().next().toggle();
}
