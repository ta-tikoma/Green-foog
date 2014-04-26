<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Test</title>
	<link href="<?=$path_to_theme?>css/main.css" rel="stylesheet" type="text/css"/>
</head>
<body>
	<?
		if(isset($_GET["l"]))
			$l = $_GET["l"];
		else
			$l = "english";
	?>

	<div class="container">
		<h1><?=$gf_get->get("data/greetings/".$l."/greeting")?></h1>

		<ul class="language_list">
		<?foreach ($gf_get->get("data/greetings") as $key => $value):?>
			<li <?if($key == $l):?>class="active"<?endif;?> ><a href="/?l=<?=$key?>"><?=$value['language-name']?></a></li>
		<?endforeach;?>
		</ul>
	</div>
	
</body>
</html>