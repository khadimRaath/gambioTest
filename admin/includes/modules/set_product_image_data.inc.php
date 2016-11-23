<?php
/* --------------------------------------------------------------
   set_product_image_data.inc.php 2016-02-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/

/** @var LanguageProvider $languageProvider */
$languageProvider = MainFactory::create('LanguageProvider', StaticGXCoreLoader::getDatabaseQueryBuilder());
$languageCodes    = $languageProvider->getCodes();
$imageContainer   = $product->getImageContainer();
/** @var ProductWriteService $productWriteService */
$productWriteService = StaticGXCoreLoader::getService('ProductWrite');
$uploadedImages      = array();

// Delete images
if(is_array($_POST['image_delete']))
{
	foreach($_POST['image_delete'] as $imageToDelete)
	{
		$filename = new FilenameStringType(xtc_db_prepare_input($imageToDelete));
		$productWriteService->deleteProductImage($filename);
		$imageContainer->delete($filename);
	}
}

// Image upload
if(isset($_FILES['image_file']) && is_array($_FILES['image_file']))
{
	foreach($_FILES['image_file']['tmp_name'] as $filename => $imageFile)
	{
		$savedAsFilename = $productWriteService->importProductImageFile(
			new ExistingFile(new NonEmptyStringType($imageFile)),
			new FilenameStringType($filename)
		);

		if(!empty($savedAsFilename))
		{
			$uploadedImages[$filename] = $savedAsFilename;
		}
	}
}

$additionalImages = $imageContainer->getAdditionals();
foreach($additionalImages as $additionalImage)
{
	$imageContainer->delete(new FilenameStringType($additionalImage->getFilename()));
}

if(isset($_POST['image_name']) && is_array($_POST['image_name']))
{
	$isPrimary = true;
	
	foreach($_POST['image_name'] as $index => $imageNameFromInput)
	{
		// Skips the current image if the image is deleted and no new image is uploaded instead
		if(is_array($_POST['image_delete']) && in_array($_POST['image_original'][$index], $_POST['image_delete']) && !isset($uploadedImages[$imageNameFromInput]))
		{
			// If the primary image was deleted, no primary image will be saved
			$isPrimary = false;
			continue;
		}
		
		$imageName = $imageNameFromInput;
		
		if(isset($uploadedImages[$imageNameFromInput]))
		{
			$imageName = $uploadedImages[$imageNameFromInput];
		}
		elseif(trim($imageNameFromInput) === '')
		{
			$imageName = $_POST['image_original'][$index];
		}
		elseif($imageNameFromInput !== $_POST['image_original'][$index])
		{
			$productWriteService->renameProductImage(new FilenameStringType($_POST['image_original'][$index]),
													 new FilenameStringType($imageName));
		}
		
		$image = MainFactory::create('ProductImage', new FilenameStringType($imageName));
		$isImageVisible = (isset($_POST['image_show'])
		                   && (in_array($imageNameFromInput, $_POST['image_show'])
		                       || in_array($_POST['image_original'][$index], $_POST['image_show'])));
		$image->setVisible(new BoolType($isImageVisible));
		foreach($languageCodes as $languageCode)
		{
			$image->setAltText(new StringType(xtc_db_prepare_input($_POST['image_alt_text'][$languageCode->asString()][$index])),
							   $languageCode);
		}
		
		if($isPrimary)
		{
			$isPrimary = false;
			$imageContainer->setPrimary($image);
		}
		else
		{
			$imageContainer->addAdditional($image);
		}
	}
}

// If an image file was replaced make sure that the old file is removed from the server. 
if(isset($_POST['image_original']) && isset($_POST['image_name']))
{
	foreach($_POST['image_original'] as $index => $imageOriginalName)
	{
		$imageNewName = $_POST['image_name'][$index];
		if($imageOriginalName !== $imageNewName)
		{
			$productWriteService->deleteProductImage(new FilenameStringType($imageOriginalName));
		}
	}
}

$product->setImageContainer($imageContainer);


// GMotion
require_once(DIR_FS_CATALOG . 'gm/classes/GMGMotion.php');
$coo_gm_gmotion = new GMGMotion();
$coo_gm_gmotion->save();