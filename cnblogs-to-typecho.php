<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$ispost = true;
	$success = 0;
	$count = 0;

	$one = 1;
	$author = (int)$_POST['author'];
	$category = (int)$_POST['category'];

	// 读取 xml 文件
	$xml = simplexml_load_file($_POST['filename'], null, LIBXML_NOCDATA);
	$xml->registerXPathNamespace('dc', 'http://purl.org/dc/elements/1.1/');
	$items = $xml->xpath('/rss/channel/item');
	$count = count($items);

	// 创建 pdo 对象
	try
	{
		$dsn = 'mysql:host=' . $_POST['dbhost'] . ';dbname=' . $_POST['dbname'];
		$db = new PDO($dsn, $_POST['dbuser'], $_POST['dbpass']);
		$db->query('set names utf8;'); 

		// 遍历文章
		foreach ($items as $item)
		{
			// 文章标题
			$title = (string)$item->title;

			// 发布时间
			$time = strtotime($item->pubDate) - (8 * 3600);

			// 文章内容
			$content = (string)$item->description;

			$pre = $db->prepare('INSERT INTO ' . $_POST['prefix'] . 'contents (title, created, modified, text, authorId, allowComment, allowPing, allowFeed) VALUES(?, ?, ?, ?, ?, ?, ?, ?)');

			$pre->bindParam(1, $title);
			$pre->bindParam(2, $time);
			$pre->bindParam(3, $time);
			$pre->bindParam(4, $content);
			$pre->bindParam(5, $author);
			$pre->bindParam(6, $one);
			$pre->bindParam(7, $one);
			$pre->bindParam(8, $one);

			if($pre->execute() == true)
			{
				$cid = $db->lastInsertId();

				// 插入文章所属分类关系
				$pre = $db->prepare('INSERT INTO ' . $_POST['prefix'] . 'relationships (cid, mid) VALUES(?, ?)');
				$pre->bindParam(1, $cid);
				$pre->bindParam(2, $category);

				if($pre->execute() == true) $success++;
			}
		}
	}
	catch(PDOException $ex)
	{
		echo 'fuck';
		die();
	}
}
else
{
	$ispost = false;
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>cnblogs to typecho</title>
	</head>
	<body>
		<?php if ($ispost == false) { ?>
			<h1>cnblogs to typecho</h1>
			<p style="color:#699;">
				因为懒，所以就没去读取 typecho 的配置文件，所以就填一下吧～<br>
				另外，这种一次性的页面，就别去纠结长得这么难看啦～<br>
				话说～本程序可以在任何地方运行，只要支持 pdo 以及能访问到你的数据库即可～
			</p>
			<hr>
			<form method="post">
				<table>
					<tr>
						<td>博客园 XML 文件名：</td>
						<td>
							<input type="text" name="filename" value="cnblogs.xml">
							<span>请将该文件放在本页面相同目录！</span>
						</td>
					</tr>
					<tr>
						<td>typecho 数据库主机：</td>
						<td>
							<input type="text" name="dbhost" value="localhost">
						</td>
					</tr>
					<tr>
						<td>typecho 数据库名称：</td>
						<td>
							<input type="text" name="dbname" value="typecho">
						</td>
					</tr>
					<tr>
						<td>typecho 数据库用户：</td>
						<td>
							<input type="text" name="dbuser" value="root">
						</td>
					</tr>
					<tr>
						<td>typecho 数据库密码：</td>
						<td>
							<input type="text" name="dbpass" value="root">
						</td>
					</tr>
					<tr>
						<td>typecho 数据表前缀：</td>
						<td>
							<input type="text" name="prefix" value="typecho_">
						</td>
					</tr>
					<tr>
						<td>导入后文章所属作者：</td>
						<td>
							<input type="text" name="author" value="1">
							<span>1 为默认第一个用户，如果没有创建过其它用户，则为 1 默认</span>
						</td>
					</tr>
					<tr>
						<td>导入后文章所属分类：</td>
						<td>
							<input type="text" name="category" value="1">
							<span>填写导入后文章所属的分类，默认为 1</span>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="submit" value="确 定 导 入" style="width:110px; height:35px; font-size:14px;">
							<span style="color:#C33;">注意：本程序没有经过大范围测试，请自行做好 typecho 的备份！</span>
						</td>
					</tr>
				</table>
			</form>
		<?php } else { ?>
			<div style="padding:15px 20px; background:#DBEDDB; border:1px solid #3C933E; color:#2E7931;">
				在 XML 文件中发现 <?php echo $count; ?> 篇文章，已成功导入 <?php echo $success; ?> 篇！<br>
				现在去你的博客看看奇迹发生了没有吧！
			</div>
		<?php }	?>
		<hr>
		<p>code by <a href="http://www.abelyao.com/" target="_blank" style="text-decoration:none; color:#0A8CD2;">abelyao</a> @ 2014</p>
	</body>
</html>
