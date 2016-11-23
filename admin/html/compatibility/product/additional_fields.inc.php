<?php
/* --------------------------------------------------------------
   additional_fields.inc.php 2015-08-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License
   --------------------------------------------------------------
*/

$languages = xtc_get_languages();

$query = 'SELECT additional_field_id FROM additional_fields WHERE item_type = "product"';
$result = xtc_db_query($query);
$additionalFields = array();
while($row = xtc_db_fetch_array($result))
{
	$additionalFields[] = MainFactory::create_object('AdditionalField', array($row['additional_field_id'], (empty($_GET['pID'])) ? 0 : $_GET['pID'] ) );
}
?>
<table class="additional_fields control-group gx-configuration">
	<tr>
		<th>&nbsp;</th>
		<th><?php echo $GLOBALS['coo_lang_file_master']->get_text('additional_fields_name', 'new_product') ?></th>
		<th>&nbsp;</th>
		<th><?php echo $GLOBALS['coo_lang_file_master']->get_text('additional_fields_value', 'new_product') ?></th>
		<th>&nbsp;</th>
	</tr>
	
	<?php
	foreach($additionalFields as $field)
	{
		echo '<tbody>';
		
		$firstPass = true;
		foreach($languages as $language)
		{
			$names = $field->get_name_array();
			
			$fieldValue = '';
			if(!empty($_GET['pID']))
			{
				$fieldValueObject = $field->get_field_value($_GET['pID']);
				if(is_object($fieldValueObject))
				{
					$fieldValues = $fieldValueObject->get_value_array();
					$fieldValue = $fieldValues[$language['id']];
				}
			}

			$deleteIcon = '';
			if($firstPass)
			{
				$deleteIcon = '<td rowspan="' . count($languages) . '">
									<i class="fa fa-trash-o delete_additional_field" 
										data-additional_field_id="' . $field->get_additional_field_id() . '"></i>
									<input type="hidden" 
											name="additional_field_multilingual[' . $field->get_additional_field_id() . ']"
											value="1" />
								</td>';
			}

			echo '<tr>
						<td><img src="../lang/' . $language['directory'] .'/admin/images/icon.gif" border="0" /></td>
						<td><input type="text" name="additional_field_names_array[' . $field->get_additional_field_id() . '][' . $language['id'] . ']" value="' . htmlspecialchars_wrapper($names[$language['id']]) . '" /></td>
						<td>:</td>
						<td><textarea name="additional_field_values_array[' . $field->get_additional_field_id() . '][' . $language['id'] . ']">' . htmlentities_wrapper($fieldValue) . '</textarea></td>
						' . $deleteIcon . '
					</tr>';
			
			$firstPass = false;
		}

		echo '</tbody>';
	}
	?>
</table>

<table class="new_additional_fields hidden">
	<tbody>
	<?php
	$firstPass = true;
	foreach($languages as $language)
	{
		$deleteIcon = '';
		if($firstPass)
		{
			$deleteIcon = '<td rowspan="' . count($languages) . '">
								<i class="fa fa-trash-o delete_additional_field"></i>
								<input type="hidden" 
											name="additional_field_multilingual[new%]"
											value="1" />
							</td>';
		}

		echo '<tr>
						<td><img src="../lang/' . $language['directory'] .'/admin/images/icon.gif" border="0" /></td>
						<td><input type="text" name="additional_field_names_array[new%][' . $language['id'] . ']" value="" disabled /></td>
						<td>:</td>
						<td><textarea name="additional_field_values_array[new%][' . $language['id'] . ']" disabled></textarea></td>
						' . $deleteIcon . '
					</tr>';

		$firstPass = false;
	}
	?>
	</tbody>
</table>

<button type="button" class="btn add_additional_field"><?php echo BUTTON_ADD ?></button>