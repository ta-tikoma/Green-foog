<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?=$gf_get->get("configuration/words/admin-panel-title")?></title>
	<link href="../themes/admin/css/main.css" rel="stylesheet" type="text/css">
	<link href="../themes/admin/css/inner_wrap.css" rel="stylesheet" type="text/css">
	<link href="../themes/admin/css/tree.css" rel="stylesheet" type="text/css">
	<link href="../themes/admin/css/action.css" rel="stylesheet" type="text/css">
	<link href="../themes/admin/css/w2ui-1.3.1.min.css" rel="stylesheet" type="text/css"/>

	<script src="../themes/admin/js/jquery-2.1.0.min.js"></script>
	<script src="../themes/admin/js/w2ui-1.3.1.min.js"></script>
	<script src="../themes/admin/js/actions.js"></script>
	<script src="../themes/admin/js/item_load.js"></script>

</head>
<body>
	<div class="header">
		<a href="/admin/index.php?action=logout_action" class="link"><?=$gf_get->get("configuration/words/exit")?></a>
		<a href="../" class="link" target="_blank" ><?=$gf_get->get("configuration/words/to-site")?></a>
	</div>	
	<div class="conteiner">
		<div class="content">
			<ul id="items" class="tree"></ul>
			<script type="text/javascript">
				get_list("items", "root")
			</script>
			<button class="action save_value_action" onclick="save_value_action('items')"><?=$gf_get->get("configuration/words/save")?></button>
		</div>
	</div>
	<div class="footer"></div>

	<script src="../themes/admin/js/main.js"></script>
</body>
</html>