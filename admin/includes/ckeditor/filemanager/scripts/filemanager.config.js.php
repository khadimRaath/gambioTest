<?php
/* contains Gambio-modifications */

$t_dir = getcwd();
define('SUPPRESS_REDIRECT', true);
chdir('../../../../../');
ob_start();
require_once('includes/application_top.php');
ob_end_clean();
chdir($t_dir);
if($_SESSION['customers_status']['customers_status_id'] !== '0')
{
	die('You are not allowed to upload files!');
}
?>
{
		"_comment": "IMPORTANT : go to the wiki page to know about options configuration https://github.com/simogeo/Filemanager/wiki/Filemanager-configuration-file",
    "options": {
        "culture": "<?php echo $_SESSION['language_code']; ?>",
        "lang": "php",
        "defaultViewMode": "grid",
        "autoload": true,
        "showFullPath": false,
        "showTitleAttr": false,
        "browseOnly": false,
        "showConfirmation": true,
        "showThumbs": false,
        "generateThumbnails": false,
        "searchBox": true,
        "listFiles": true,
        "fileSorting": "default",
        "chars_only_latin": true,
        "dateFormat": "d M Y H:i",
        "serverRoot": true,
        "fileRoot": "<?php echo DIR_WS_CATALOG . 'images/'; ?>",
        "relPath": "<?php echo DIR_WS_IMAGES; ?>",
        "logger": false,
        "capabilities": ["select", "download", "rename", "move", "delete", "replace"],
        "plugins": []
    },
    "security": {
        "uploadPolicy": "DISALLOW_ALL",
        "uploadRestrictions": [
            "jpg",
            "jpeg",
            "gif",
            "png",
            "svg",
            "txt",
            "pdf",
            "odp",
            "ods",
            "odt",
            "rtf",
            "doc",
            "docx",
            "xls",
            "xlsx",
            "ppt",
            "pptx",
            "ogv",
            "mp4",
            "webm",
            "m4v",
            "ogg",
            "mp3",
            "wav"
        ]
    },
    "upload": {
        "overwrite": false,
        "imagesOnly": false,
        "fileSizeLimit": 16
    },
    "exclude": {
        "unallowed_files": [
            ".htaccess",
            "BarPay.jpg",
            "cv_amex_card.gif",
            "cv_card.gif",
            "de-btn-expresscheckout.gif",
            "einzug.gif",
            "index.html",
            "ladebalken.gif",
            "loading.gif",
            "pixel_black.gif",
            "pixel_silver.gif",
            "pixel_trans.gif",
            "trusted.gif",
            "trusted_bewerten_de.gif",
            "trusted_bewerten_en.gif",
            "trusted_siegel.gif"
        ],
        "unallowed_dirs": [
            "_thumbs",
            "banner",
            "categories",
			"gm",
			"icons",
			"login_admin",
			"logos",
			"logos",
			"manufacturers",
			"product_images",
			"slider_images",
            ".CDN_ACCESS_LOGS",
            "cloudservers"
        ],
        "unallowed_files_REGEXP": "/^\\./uis",
        "unallowed_dirs_REGEXP": "/^\\./uis"
    },
    "images": {
        "imagesExt": [
            "jpg",
            "jpeg",
            "gif",
            "png",
            "svg"
        ],
        "resize": {
        	"enabled":false
        }
    },
    "videos": {
        "showVideoPlayer": true,
        "videosExt": [
            "ogv",
            "mp4",
            "webm",
            "m4v"
        ],
        "videosPlayerWidth": 400,
        "videosPlayerHeight": 222
    },
    "audios": {
        "showAudioPlayer": true,
        "audiosExt": [
            "ogg",
            "mp3",
            "wav"
        ]
    },
    "extras": {
        "extra_js": [],
        "extra_js_async": true
    },
    "icons": {
        "path": "images/fileicons/",
        "directory": "_Open.png",
        "default": "default.png"
    }
}