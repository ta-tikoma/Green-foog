<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<link href="../themes/admin/css/main.css" rel="stylesheet" type="text/css">
	<link href="../themes/admin/css/inner_wrap.css" rel="stylesheet" type="text/css">
	<link href="../themes/admin/css/tree.css" rel="stylesheet" type="text/css">
	<link href="../themes/admin/css/action.css" rel="stylesheet" type="text/css">

</head>
<body>
	<div class="header">
	</div>	
	<div class="conteiner">
		<div class="content">
			<form method="POST">
				<?=$gf_get->get("configuration/words/login")?> <input name="login" type="text"><br>
				<?=$gf_get->get("configuration/words/password")?> <input name="password" type="password"><br>
				<input name="submit" type="submit" value="<?=$gf_get->get("configuration/words/enter")?>">
			</form>
		</div>
	</div>
	<div class="footer"></div>
</body>
</html>