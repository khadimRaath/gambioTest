<?php
if(!defined('_GM_VALID_CALL'))
{
	chdir('../../');
	require 'includes/application_top.php';
	$include_mode = false;
}
else
{
	$include_mode = true;
}

isset($itrk_file_type) or die('unable to determine file type');

$itrk_supported_languages = array('de');
$fallback_language = 'de';

$itrk_language = $_SESSION['language'];
if(!in_array($itrk_language, $itrk_supported_languages))
{
	$itrk_language = $fallback_language;
}

$itrkFile = __DIR__.'/itrk_'.$itrk_file_type.'_'.$itrk_language.'.html';
if(!(file_exists($itrkFile) && is_readable($itrkFile)))
{
	echo "File not found";
	exit;
}
?>
<?php if(!$include_mode): ?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $itrk_file_type ?></title>
	<style>
	body { font: 0.85em sans-serif; }
	</style>
</head>
<body>
<?php endif ?>
<?php include $itrkFile ?>
<?php if(!$include_mode): ?>
</body>
</html>
<?php endif ?>

