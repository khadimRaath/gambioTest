<?php

/**
 * import products
 *
 * actindo Faktura/WWS connector
 *
 * @package actindo
 * @author  Patrick Prasse <pprasse@actindo.de>
 * @author  Chris Westerfield <westerfield@actindo.de>
 * @version $Revision: 511 $
 * @copyright Copyright© Actindo GmbH 2015, <support@actindo.de>, Carl-Zeiss-Ring 15 - 85737 Ismaning
 * @license http://opensource.org/licenses/GPL-2.0 GNU Public License 2
*/

function import_product( $params )
{
    if( !parse_args($params,$ret) )
    {
        return $ret;
    }
    $product = $params['product'];
    $failed=0;
    $success=0;

    if ( act_shop_is( SHOP_TYPE_GAMBIOGX ) ) {
        $_SESSION['gm_redirect'] = -32767 * strlen('gambio-redirect-bug-fx');
    }

    if( !is_array( $product ) || !count( $product ) )
    {
        return array( 'ok' => FALSE, 'errno' => EINVAL );
    }

    if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) )
    {
        require_once( "includes/classes/categories.php" );
        require_once ('includes/classes/'.FILENAME_IMAGEMANIPULATOR);
        require_once ('includes/classes/categories.php');
        require_once ('includes/classes/object_info.php');

        if( act_shop_is(SHOP_TYPE_GAMBIOGX) )
        require_once ('includes/gm/classes/GMProductUpload.php');

        require_once (DIR_FS_INC.'xtc_get_tax_rate.inc.php');
        require_once (DIR_FS_INC.'xtc_get_products_mo_images.inc.php');
        require_once (DIR_WS_CLASSES.'currencies.php');
        require_once (DIR_FS_INC.'xtc_wysiwyg.inc.php');

        $warning = array();

        $currencies = new currencies();
        $catfunc = new categories();

        $p = array();
        $_FILES = array();

        // check primary category
        $res = act_db_query( "SELECT COUNT(*) FROM ".TABLE_CATEGORIES." WHERE `categories_id`=".(int)$product['swg'] );
        $cnt = mysqli_fetch_row( $res );
        ((mysqli_free_result( $res ) || (is_object( $res ) && (get_class( $res ) == "mysqli_result"))) ? true : false);
        if( $cnt[0] <= 0 )
        {
            return resp(array( 'ok' => FALSE, 'errno' => ENOENT, 'error'=>'Kategorie nicht mehr vorhanden oder gel�scht.' ));
        }

        $xtp = null;
        $res = act_db_query( $q="SELECT ".TABLE_PRODUCTS.".`products_id`, ".TABLE_PRODUCTS_TO_CATEGORIES.".categories_id, ".TABLE_CATEGORIES.".categories_id AS verify_cat_id FROM ".TABLE_PRODUCTS." LEFT JOIN ".TABLE_PRODUCTS_TO_CATEGORIES." USING(`products_id`) LEFT JOIN ".TABLE_CATEGORIES." USING(`categories_id`) WHERE `products_model`='".esc($product['art_nr'])."'" );
        $n = act_db_num_rows( $res );
        if( $n )
        {
            $pp = act_db_fetch_array($res);

            // Sinn ist folgender:
            // es passiert (don't ask me why), dass:
            // * ein Artikel ohne Kategorie-Zuordnung trotzdem existiert
            // * die Kategorie-Zuordnung vorhanden ist, aber die Kategorie nicht mehr
            // -> check, ob swg existiert, ist oben bereits vorhanden
            if( is_null($pp['categories_id']) || (is_null($pp['verify_cat_id']) && $pp['categories_id']!=0) )
            {
              // holy cow. not supposed to happen.
              // if it does happen, we try to insert category again.
              if( !isset($product['swg']) || !act_db_query($p="INSERT INTO ".TABLE_PRODUCTS_TO_CATEGORIES." SET products_id   = '".(int)$pp['products_id']."', categories_id = '".(int)$product['swg']."'") )
              {
                return resp(array( 'ok' => FALSE, 'errno' => EXDEV, 'error'=>'Produkt da, aber Kategoriezuordnung defekt und irreparabel.' ));
              }
            }
            $product_query = act_db_query("select *, date_format(p.products_date_available, '%Y-%m-%d') as products_date_available
                                           from ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd
                                           where p.products_id = '".(int)$pp['products_id']."'
                                           and p.products_id = pd.products_id
                                           and pd.language_id = '".default_lang()."'");
            $xtp = act_db_fetch_array($product_query);
            $pInfo = new objectInfo($xtp);
            $p['products_id'] = $pInfo->products_id;
            $p['products_status'] = $pInfo->products_status;
        }
        ((mysqli_free_result( $res ) || (is_object( $res ) && (get_class( $res ) == "mysqli_result"))) ? true : false);

        if( isset($product['art_nr']) )
        {
            $p['products_model'] = $product['art_nr'];
        }
        if( isset($product['l_bestand']) )
        {
            $p['products_quantity'] = (int)$product['l_bestand'];
        }


        // taxes
        if( isset($product['taxes_advanced']) )
        {
            $res = _import_product_to_taxes_advanced( $product['taxes_advanced'], $product['leist_art'], $p['products_tax_class_id'] );
            if( !$res )
            {
                return $res;
            }
            $p['products_price'] = import_convert_tax( $product['grundpreis'], $product['is_brutto'], $product['mwst'], $p['products_tax_class_id'], TRUE );
        }
        else if( isset($product['leist_art']) && $product['leist_art'] > 0 )
        {
            $p['products_tax_class_id'] = $product['leist_art'];
            $p['products_price'] = import_convert_tax( $product['grundpreis'], $product['is_brutto'], $product['mwst'], $p['products_tax_class_id'], TRUE );
        }
        else
        {
            switch( $product['mwst_stkey'] )
            {
              case 3:
                $p['products_tax_class_id'] = 1;
                break;
              case 2:
                $p['products_tax_class_id'] = 2;
                break;
              case 0:
              case 1:
              case 11:
                $p['products_tax_class_id'] = 0;
                break;

              default:
                return array( 'ok' => FALSE, 'errno' => EUNKNOWN, 'error' => 'Im Shop nicht verf�gbarer Steuersatz.' );
            }
            $p['products_price'] = import_convert_tax( $product['grundpreis'], $product['is_brutto'], $product['mwst'], $p['products_tax_class_id'], TRUE );
        }


        if( act_shop_is(SHOP_TYPE_GAMBIOGX) )
        {
            is_array($p['gm_alt_text']) or $p['gm_alt_text'] = array();
        }
        if( isset($product['weight']) )
        {
            $p['products_weight'] = weight_convert( $product['weight'], $product['weight_unit'] );
        }
        if( isset($product['lft']) )
        {
            $p['manufacturers_id'] = $product['lft'];
        }
        if( is_array($product['shop']['desc']) && count($product['shop']['desc']) )
        {
            foreach( $product['shop']['desc'] as $num => $description )
            {
              $lang_id = $description['language_id'];
              unset( $description['id'], $description['art_id'], $description['language_id'] );
              if( !strlen($description['products_name']) && strlen($product['art_name']) )
              {
                  $description['products_name'] = $product['art_name'];
              }

              if( act_shop_is(SHOP_TYPE_GAMBIOGX) )
              {   // in GambioGX passt die URL nicht, wenn die meta_keywords leer sind
                if( !strlen($description['products_meta_keywords']) )
                {
                    $description['products_meta_keywords'] = $description['products_name'];
                }

                if( !isset($description['gm_url_keywords']) || !is_string($description['gm_url_keywords']) || strlen($description['gm_url_keywords']) < 8 )
                {
                    $description['gm_url_keywords'] = '';
                }
              }

              foreach( $description as $key => $item )
              {
                $trans = array(
                  "products_description" => "products_description_".$lang_id,
                  "products_short_description" => "products_short_description_".$lang_id,
                );

                if( array_key_exists( $key, $trans ) )
                {
                  $key = strtr( $key, $trans );
                  $p[$key] = $item;
                }
                else
                {
                    $p[$key][$lang_id] = $item;
                }
              }
            }
        }
        else
        {
            return array( 'ok' => FALSE, 'errno' => EUNKNOWN, 'error' => 'Keine Shoptexte hinterlegt' );
        }

        if( is_array($product['shop']['art']) && count($product['shop']['art']) )
        {
            unset( $product['shop']['art']['id'], $product['shop']['art']['art_id'], $product['shop']['art']['in_shop'] );
            foreach( $product['shop']['art'] as $key => $val )
            {
              $p[$key] = $val;
            }
        }
        else
        {
            return array( 'ok' => FALSE, 'errno' => EUNKNOWN, 'error' => 'Keine Shopdetails hinterlegt' );
        }


        if( is_array($product['shop']['images']) && count($product['shop']['images']) )
        {
            foreach( $product['shop']['images'] as $num => $image )
            {
              $idx = $num - 1;
              $_idx = $idx - 1;
              $key = ( $num==1 )?( "products_image" ):( "mo_pics_".($_idx) );
              $res = actindo_create_temporary_file( $image['image'] );
              if( !$res['ok'] )
              {
                  return $res;
              }

              if( act_shop_is(SHOP_TYPE_GAMBIOGX) )
              {
                if( $num == 1 )
                {
                  foreach( $product['shop']['desc'] as $description )
                  {
                    $lang_id = $description['language_id'];
                    $lang_code = get_language_code_by_id( $lang_id );
                    $p['gm_alt_text'][$idx][$lang_id] = isset($image['image_title'][$lang_code]) ? $image['image_title'][$lang_code] : strip_tags( $description['products_name'] );
                  }
                }
              }

              $_FILES[$key] = array( "name" => $image['image_name'], "type" => $image['image_type'], "tmp_name" => $res['file'], "error" => 0, "size" => strlen( $image['image'] ) );

              if( act_shop_is(SHOP_TYPE_GAMBIOGX) )
              {
                if( $key == 'products_image' )
                {
                    $_POST['gm_prd_img_name'] = $_FILES[$key]['name'];
                }
                else
                {
                    $_POST['gm_prd_img_name_' . $_idx] = $_FILES[$key]['name'];
                }

                if( $num > 1 && is_array($image['image_title']) && count($image['image_title']) )
                {
                  foreach( $image['image_title'] as $_langcode => $_title )
                  {
                    $langid = get_language_id_by_code( $_langcode );
                    if( $langid <= 0 )
                    {
                        continue;
                    }
                    $p['gm_alt_text'][$idx][$langid] = $_title;
                  }
                }
              }
            }
        }
        else
        {
            $warning[] = "Keine Bilder hinterlegt";
        }


        if( $n )
        {
            if( $pInfo->products_image )
            {
              $p['products_previous_image_0'] = $pInfo->products_image;
              if( !array_key_exists( "products_image", $_FILES ) )
              {
                  $p['del_pic'] = $pInfo->products_image;
              }
            }

            if( MO_PICS > 0 )
            {
              if( act_shop_is(SHOP_TYPE_GAMBIOGX) )
              {
                  xtc_db_query("DELETE FROM gm_prd_img_alt WHERE products_id	= '" . $pInfo->products_id . "'");
              }

              $mo_images = xtc_get_products_mo_images($pInfo->products_id);
              //for ($i = 0; $i < MO_PICS; $i ++)
              for ($i = 0; $i < count($mo_images); $i ++)
              {
                  if($mo_images[$i]["image_name"])
                  {
                      $p['products_previous_image_'. ($i +1)] = $mo_images[$i]["image_name"];
                      if( !array_key_exists( "mo_pics_".$i, $_FILES ) )
                      {
                          $p['del_mo_pic'][] = $mo_images[$i]["image_name"];
                      }
                  }
                  else
                  {
                      $p['products_previous_image_'. ($i +1)] = "";
                  }
              }
            }

            if( is_array($xtp) && count($xtp) )
            {
              foreach( $xtp as $_key => $_val )
              {
                if( !isset($p[$_key]) && !is_array($_val) && $_key != 'products_image' )
                {
                    $p[$_key] = $_val;
                }
              }
            }
            if(!isset($p['del_mo_pic']) || $p['del_mo_pic']===null)
            {
               $p['del_mo_pic']='';
            }
            $catfunc->insert_product( $p, '', 'update' );
            $res1 = act_db_query( "REPLACE INTO ".TABLE_PRODUCTS_TO_CATEGORIES." SET products_id='".(int)$pp['products_id']."', categories_id='".(int)$product['swg']."'" );
            if( !$res1 )
            {
              return array( 'ok' => FALSE, 'errno' => EIO, 'error' => 'Fehler beim setzen der Prim�rkategorie' );
            }
        }
        else
        {
            if(!isset($p['del_mo_pic']) || $p['del_mo_pic']===null)
            {
               $p['del_mo_pic']='';
            }
            $catfunc->insert_product( $p, $product['swg'] );
            $res = act_db_query( "SELECT ".TABLE_PRODUCTS.".`products_id`, ".TABLE_PRODUCTS_TO_CATEGORIES.".categories_id, ".TABLE_CATEGORIES.".categories_id AS verify_cat_id FROM ".TABLE_PRODUCTS." LEFT JOIN ".TABLE_PRODUCTS_TO_CATEGORIES." USING(`products_id`) LEFT JOIN ".TABLE_CATEGORIES." USING(`categories_id`) WHERE `products_model`='".esc($product['art_nr'])."'" );
            $n = act_db_num_rows( $res );
            if( $n )
            {
                $pp = act_db_fetch_array( $res );
            }
            ((mysqli_free_result( $res ) || (is_object( $res ) && (get_class( $res ) == "mysqli_result"))) ? true : false);

            // insert_product does not take care of this
            if( $p['products_startpage'] )
            {
                $catfunc->link_product( $pp['products_id'], 0 );
            }
        }


        // Preisgruppen
        if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) )
        {
            $groups = array_keys(export_customers_status());
            foreach( $groups as $status_id )
            {
              $res1 = act_db_query( "DELETE FROM personal_offers_by_customers_status_".(int)$status_id." WHERE `products_id`=".(int)$pp['products_id'] );
              if( is_array($product['preisgruppen'][$status_id]) )
              {
                $res1 &= act_db_query( "INSERT INTO personal_offers_by_customers_status_".(int)$status_id." SET `products_id`=".(int)$pp['products_id'].", `quantity`=1, `personal_offer`='".import_convert_tax( $product['preisgruppen'][$status_id]['grundpreis'], $product['preisgruppen'][$status_id]['is_brutto'], $product['mwst'], $p['products_tax_class_id'] )."'" );
                for( $i=1; $i<=4; $i++ )
                {
                  if( $product['preisgruppen'][$status_id]['preis_range'.$i] <= 0 )
                  {
                      continue;
                  }
                  $res1 &= act_db_query( "INSERT INTO personal_offers_by_customers_status_".(int)$status_id." SET `products_id`=".(int)$pp['products_id'].", `quantity`=".(float)$product['preisgruppen'][$status_id]['preis_range'.$i].", `personal_offer`='".import_convert_tax( $product['preisgruppen'][$status_id]['preis_gruppe'.$i], $product['preisgruppen'][$status_id]['is_brutto'], $product['mwst'], $p['products_tax_class_id'] )."'" );
                }
              }
              if( !$res1 )
              {
                  return array( 'ok' => FALSE, 'errno' => EIO, 'error' => 'Fehler beim anlegen der Preisgruppen' );
              }
            }
        }


        // Shop BUGFIX: insert_product does not remove category link to categories_id 0
        if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) && !$p['products_startpage'] )
        {
            $res = act_db_query( "SELECT ".TABLE_PRODUCTS.".`products_id`, ".TABLE_PRODUCTS_TO_CATEGORIES.".categories_id FROM ".TABLE_PRODUCTS.", ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE ".TABLE_PRODUCTS_TO_CATEGORIES.".`products_id`=".TABLE_PRODUCTS.".`products_id` AND `products_model`='".esc($product['art_nr'])."' AND ".TABLE_PRODUCTS_TO_CATEGORIES.".categories_id<>0" );
            $n = act_db_num_rows( $res );
            $pp = act_db_fetch_array($res);
            ((mysqli_free_result( $res ) || (is_object( $res ) && (get_class( $res ) == "mysqli_result"))) ? true : false);

            if( $n )
            {
              $res = act_db_query( "DELETE FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE `products_id`=".(int)$pp['products_id']." AND `categories_id`=0" );
            }
        }

        if( is_array($product['shop']['all_categories']) )
        {
            $res = TRUE;
            $res &= act_db_query( "DELETE FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE `products_id`=".(int)$pp['products_id']." AND `categories_id`<>0 AND `categories_id`<>".(int)$product['swg'] );

            foreach( $product['shop']['all_categories'] as $_i => $_cat )
            {
                if( $_cat == $product['swg'] || $_cat == 0 )
                {
                    unset( $product['shop']['all_categories'][$_i] );
                }
            }

            if( count($product['shop']['all_categories']) )   // still some categories  ;-)
              foreach( $product['shop']['all_categories'] as $_i => $_cat )
              {
                $cntqry = act_db_query( "SELECT COUNT(*) AS cnt FROM ".TABLE_CATEGORIES." WHERE `categories_id`=".(int)$_cat );
                $cnt = act_db_fetch_array( $cntqry );
                ((mysqli_free_result( $cntqry ) || (is_object( $cntqry ) && (get_class( $cntqry ) == "mysqli_result"))) ? true : false);
                if( $cnt['cnt'] )
                {
                    $res &= act_db_query( "INSERT INTO ".TABLE_PRODUCTS_TO_CATEGORIES." SET products_id=".(int)$pp['products_id'].", categories_id=".(int)$_cat );
                }
                else
                {
                    $warning[] = sprintf( "Kategorie %d im Shop nicht mehr verf�gbar", $_cat );
                }
              }

            if( !$res )
            {
                $warning[] = "Der Artikel konnte nicht mit allen Kategorien verbunden werden.";
            }
        }


        if(ACTINDO_ATTRIBUTES_MODE=='properties')
        {
            require_once('attributeHandler.php');
            $attributes = new attributeHandler();
            $attributes->setProductData($product);
            $attributes->setArticleId((int)$pp['products_id']);
            if( is_array($product['shop']['attributes']) )
            {
                $attributes->setToShop();
                $attributes->process();
                if($attributes->errorExists())
                {
                    return $attributes->getErrorMessage();
                }
            }
            else
            {
                $attributes->checkAndDelete();
            }
        }else{
            if( is_array($product['shop']['attributes']) )
            {
                $res = _do_import_attributes_options( $product['shop']['attributes']['names'], $product['shop']['attributes']['values'] );
                if( !$res )
                {
                    return array( 'ok' => FALSE, 'errno' => EIO, 'error' => 'Fehler beim anlegen der Attribute' );
                }

                $res = _do_set_article_attributes( $pp['products_id'], $product, $p['products_tax_class_id'], $product['shop']['attributes']['separator'],
                $product['shop']['attributes']['combination_simple'], $product['shop']['attributes']['names'], $product['shop']['attributes']['values'] );
                if( !$res )
                {
                    return array( 'ok' => FALSE, 'errno' => EIO, 'error' => 'Fehler beim verkn�pfen der Attribute mit dem Artikel' );
                }
            }
            else if( isset($product['shop']['attributes']) )
            {
                $res = act_db_query( "DELETE FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE `products_id`=".(int)$pp['products_id'] );
            }
        }



        if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) && is_array($product['shop']['group_permission']) )
        {
            $group_perm = array();
            $res = act_db_query( "DESCRIBE ".TABLE_PRODUCTS );
            while( $row=mysqli_fetch_row($res) )
            {
              if( preg_match( '/group_permission_(\d+)/', $row[0], $matches) )
              {
                  $group_perm[] = "`{$row[0]}`=".(int)(in_array($matches[1], $product['shop']['group_permission']) ? 1 : 0);
              }
            }
            ((mysqli_free_result( $res ) || (is_object( $res ) && (get_class( $res ) == "mysqli_result"))) ? true : false);

            if( !count($group_perm) )
            {
                $warning[] = "Es konnten keine Kundengruppen gefunden werden.";
            }
            else
            {
              $res = TRUE;
              $res &= act_db_query( $q="UPDATE ".TABLE_PRODUCTS." SET ".join(', ', $group_perm)." WHERE `products_id`=".(int)$pp['products_id'] );
              if( !$res )
              {
                  $warning[] = "Die Kundengruppen konnten nicht geschrieben werden.";
              }
            }
        }

        if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) && is_array($product['shop']['xselling']) )
        {
            $res = act_db_query( "DELETE FROM ".TABLE_PRODUCTS_XSELL." WHERE `products_id`=".(int)$pp['products_id'] );
            $res = TRUE;
            foreach( $product['shop']['xselling'] as $_idx => $xs )
            {
              $res1 = act_db_query( "SELECT `products_id` FROM ".TABLE_PRODUCTS." WHERE `products_model`='".esc($xs['art_nr'])."'" );
              $_p = act_db_fetch_array( $res1 );
              ((mysqli_free_result( $res1 ) || (is_object( $res1 ) && (get_class( $res1 ) == "mysqli_result"))) ? true : false);
              if( !is_array($_p) || !$_p['products_id'] )
              {
                  continue;
              }
              $res &= act_db_query( "INSERT INTO ".TABLE_PRODUCTS_XSELL." SET `products_id`=".(int)$pp['products_id'].", `xsell_id`=".(int)$_p['products_id'].", `products_xsell_grp_name_id`=".(int)$xs['group'].", `sort_order`=".(int)$xs['sort_order'] );
            }
            if( !$res )
            {
                $warning[] = "Fehler beim schreiben der Cross-Selling-Artikel";
            }
        }

        if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) )
        {
          $res = _do_import_content( $product, $pp['products_id'] );
          $warning = array_merge( $warning, $res['warning'] );
        }


        }
    $success++;
    if( !count($warning) )
    {
        $warning = null;
    }
    return resp(array( 'ok' => TRUE, 'success' => $success, 'warning' => $warning ));
}

function _do_import_content( &$product, $products_id )
{
    $warning = array();

    if( is_array($product['shop']['content']) )
    {
    $res = act_db_query( "DELETE FROM ".TABLE_PRODUCTS_CONTENT." WHERE `products_id`=".(int)$products_id );
    $res = TRUE;
    foreach( $product['shop']['content'] as $_idx => $content )
    {
      $language_id = get_language_id_by_code( $content['language_code'] );
      $group_ids = '';

      if( $content['type'] == 'html' )
      {
        $res &= act_db_query( "INSERT INTO ".TABLE_PRODUCTS_CONTENT." SET `products_id`=".(int)$products_id.", `group_ids`='".esc($group_ids)."', `languages_id`=".(int)$language_id.", `content_name`='".esc($content['content_name'])."', `file_comment`='".esc($content['content'])."'" );
      }
      else if( $content['type'] == 'link' )
      {
        $res &= act_db_query( "INSERT INTO ".TABLE_PRODUCTS_CONTENT." SET `products_id`=".(int)$products_id.", `group_ids`='".esc($group_ids)."', `languages_id`=".(int)$language_id.", `content_name`='".esc($content['content_name'])."', `content_link`='".esc($content['content'])."'" );
      }
      else if( $content['type'] == 'file' )
      {
        $dirname = DIR_FS_DOCUMENT_ROOT.'media/products';
        $fn = $dirname.'/'.$content['content_file_name'];

        if( !is_dir($dirname) || !is_writable($dirname) )
        {
          $warning[] = "Content: Fehler beim schreiben in das Verzeichnis '{$dirname}'!";
          continue;
        }

        $written = file_put_contents( $fn, $content['content'] );
        if( $written === FALSE )
        {
          $warning[] = "Content: Fehler beim schreiben in die Datei '{$fn}'!";
          continue;
        }
        if( $written != $content['content_file_size'] )
        {
          $warning[] = "Content: Datei '{$fn}' konnte nicht vollst�ndig geschrieben werden (written=".var_dump_string($written).", size=".var_dump_string($content['content_file_size']).")!";
          continue;
        }

        $res &= act_db_query( "INSERT INTO ".TABLE_PRODUCTS_CONTENT." SET `products_id`=".(int)$products_id.", `group_ids`='".esc($group_ids)."', `languages_id`=".(int)$language_id.", `content_name`='".esc($content['content_name'])."', `content_file`='".esc($content['content_file_name'])."'" );
      }
      else
      {
        $warning[] = "Content: Unbekannter Content-Typ '{$content['type']}'.";
      }

    }
    }

    return array( 'ok'=>TRUE, 'warning'=>$warning );
}


function weight_convert( $weight, $weight_unit )
{
  $mul = array( 'mg' => 1/1000000, 'g' => 1/1000, 'pf'=>0.5, 'kg' => 1 );
  return $weight * $mul[strtolower($weight_unit)];
}

function import_convert_tax( $price, $is_brutto=0, $mwst, $products_tax_class_id, $is_products_price=FALSE )
{
  $is_brutto = $is_brutto > 0;
  $shop_is_brutto = (PRICE_IS_BRUTTO == 'true');
//  printf( "\$is_brutto=".var_dump_string($is_brutto).", \$shop_is_brutto=".var_dump_string($shop_is_brutto).", \$price=".var_dump_string($price).", " );
//  printf( "\$products_tax_class_id=".var_dump_string($products_tax_class_id)."\n" );

  if( $is_brutto )
    $price = round(($price / (xtc_get_tax_rate($products_tax_class_id) + 100) * 100), PRICE_PRECISION);

  if( $is_products_price && $shop_is_brutto )
    $price = round(($price * (xtc_get_tax_rate($products_tax_class_id) + 100) / 100), PRICE_PRECISION);

  return $price;
}



function _do_set_article_attributes( $products_id, $product, $products_tax_class_id, $separator=null, $combination, &$options, &$values, $just_lager=FALSE )
{
  $products_id = (int)$products_id;
  !is_null($separator) or $separator='';
  $res = 1;

  if( !$just_lager )
    $res = act_db_query( "DELETE FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE `products_id`={$products_id}" );

  foreach( $combination as $_name_id => $_values )
  {
    $shop_options_id = (int)$options[$_name_id]['_shop_id'];
    foreach( $_values as $_value_id => $attrib )
    {
      $shop_values_id = (int)$values[$_name_id][$_value_id]['_shop_id'];
      if( !$just_lager )
      {
        $attrib['options_values_price'] = import_convert_tax( $attrib['options_values_price'], $product['is_brutto'], $product['mwst'], $products_tax_class_id );
        if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) )
        {
          $res &= act_db_query( "INSERT INTO ".TABLE_PRODUCTS_ATTRIBUTES." SET `products_id`={$products_id}, `options_id`={$shop_options_id}, `options_values_id`={$shop_values_id}, ".
            "`options_values_price`=".abs($attrib['options_values_price']).", `price_prefix`='".($attrib['options_values_price']<0 ? '-' : '+')."', ".
            "`attributes_model`='".esc($separator.$attrib['attributes_model'])."', `attributes_stock`=".(float)$attrib['l_bestand'].", ".
            "`options_values_weight`=".abs($attrib['options_values_weight']).", `weight_prefix`='".($attrib['options_values_weight']<0 ? '-' : '+')."', ".
            "`sortorder`=".(is_null($attrib['sortorder']) ? 'NULL' : (int)$attrib['sortorder']) );
        }
        else if( act_shop_is(SHOP_TYPE_OSCOMMERCE) )
        {
          $res &= act_db_query( "INSERT INTO ".TABLE_PRODUCTS_ATTRIBUTES." SET `products_id`={$products_id}, `options_id`={$shop_options_id}, `options_values_id`={$shop_values_id}, ".
            "`options_values_price`=".abs($attrib['options_values_price']).", `price_prefix`='".($attrib['options_values_price']<0 ? '-' : '+')."'" );
        }
      }
      else
      {
        if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) )
        {
          $res &= act_db_query( "UPDATE ".TABLE_PRODUCTS_ATTRIBUTES." SET `attributes_stock`=".(float)$attrib['l_bestand']." WHERE `products_id`={$products_id} AND `options_id`={$shop_options_id} AND `options_values_id`={$shop_values_id}" );
        }
        else if( act_shop_is(SHOP_TYPE_OSCOMMERCE) )
        {
          return FALSE;
        }
      }
    }
  }
  if( !$just_lager )
    $res &= act_db_query( "ALTER TABLE ".TABLE_PRODUCTS_ATTRIBUTES." ORDER BY `products_attributes_id` ASC" );

  return $res;
}

/**
 * insert (/ move) products options / values
 *
 * here we create (or insert and move) products options.
 *
 * With $reorder_options, products_options_id ASC order has to be the same as in actindo,
 * as we get problems with art_nr's when downloading orders otherwise.
 *
 * You are not expected to understand this.
 *
 * @param bool $reorder_options Reorder option_id's to match the order in actindo?
 * @return bool TRUE success, FALSE error
 */
function _do_import_attributes_options( &$options, &$values, $just_get=FALSE )
{

  // 1st: find products_options
  $options_arr = array();
  $res = act_db_query( "SELECT * FROM ".TABLE_PRODUCTS_OPTIONS." WHERE 1" );
  while( $row=act_db_fetch_array($res) )
  {
    if( !empty($row['products_options_name']) )
      $options_arr[(int)$row['products_options_id']][get_language_code_by_id($row['language_id'])] = $row['products_options_name'];
  }
  ((mysqli_free_result( $res ) || (is_object( $res ) && (get_class( $res ) == "mysqli_result"))) ? true : false);

  // first try better matches (more languages match)
  uasort( $options_arr, '_attr_opts_sort' );

  foreach( $options as $id => $_arr )
  {
    foreach( $options_arr as $_i => $_oarr )
      if( _attr_opts_cmp($_arr, $_oarr) )
        $options[$id]['_shop_id'] = $_i;
  }


  $res = TRUE;
  act_failsave_db_query( "LOCK TABLES ".TABLE_PRODUCTS_OPTIONS." WRITE, ".TABLE_PRODUCTS_OPTIONS_VALUES." WRITE, ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." WRITE, ".TABLE_PRODUCTS_ATTRIBUTES." WRITE, ".TABLE_CUSTOMERS_BASKET_ATTRIBUTES." WRITE, ".TABLE_LANGUAGES." WRITE" );
  foreach( $options as $id => $_arr )
  {
    if( !$just_get )
    {
      $next_id = _get_next_options_id();

      if( !$_arr['_shop_id'] )
      {
        foreach( $_arr as $_code => $_text )
          if( ($langid=get_language_id_by_code($_code)) > 0 )
          {
            $res &= act_db_query( "INSERT INTO ".TABLE_PRODUCTS_OPTIONS." SET `products_options_id`=".$next_id.", `language_id`=".$langid.", `products_options_name`='".esc($_text)."'" );
            if( $res )
              $options[$id]['_shop_id'] = $_arr['_shop_id'] = $next_id;
          }
      }
      else
      {
        foreach( $_arr as $_code => $_text )
          if( ($langid=get_language_id_by_code($_code)) > 0 )
          {
            $res &= act_db_query( "UPDATE ".TABLE_PRODUCTS_OPTIONS." SET `products_options_name`='".esc($_text)."' WHERE `products_options_id`=".$_arr['_shop_id']." AND `language_id`=".$langid );
          }
      }
      if( !$res )
        break;
    }


    $values_arr = array();
    $res1 = act_db_query( "SELECT * FROM ".TABLE_PRODUCTS_OPTIONS_VALUES.", ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." WHERE ".TABLE_PRODUCTS_OPTIONS_VALUES.".products_options_values_id = ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS.".products_options_values_id AND ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS.".products_options_id=".(int)$_arr['_shop_id'] );
    while( $row=act_db_fetch_array($res1) )
    {
      if( !empty($row['products_options_values_name']) )
        $values_arr[(int)$row['products_options_values_id']][get_language_code_by_id($row['language_id'])] = $row['products_options_values_name'];
    }
    ((mysqli_free_result( $res1 ) || (is_object( $res1 ) && (get_class( $res1 ) == "mysqli_result"))) ? true : false);

    uasort( $values_arr, '_attr_opts_sort' );

    foreach( $values[$id] as $_id => $_arr1 )
    {
      foreach( $values_arr as $_i => $_oarr )
      {
        if( _attr_opts_cmp($_arr1, $_oarr) )
        {
          $values[$id][$_id]['_shop_id'] = $_i;
          if( !$just_get )
          {
            foreach( $_arr1 as $_code => $_text )
              if( ($langid=get_language_id_by_code($_code)) > 0 )
              {
                $res &= act_db_query( $q="UPDATE ".TABLE_PRODUCTS_OPTIONS_VALUES." SET `products_options_values_name`='".esc($_text)."' WHERE `products_options_values_id`=".$_i." AND `language_id`=".$langid );
              }
          }
        }
      }


      if( !$values[$id][$_id]['_shop_id'] && !$just_get )
      {
        $vid = _get_next_options_values_id( );
        foreach( $_arr1 as $_code => $_text )
          if( ($langid=get_language_id_by_code($_code)) > 0 )
          {
            $res &= act_db_query( $q="INSERT INTO ".TABLE_PRODUCTS_OPTIONS_VALUES." SET `products_options_values_id`=".$vid.", `language_id`=".$langid.", `products_options_values_name`='".esc($_text)."'" );
          }
        if( !$res )
          continue;
        $res &= act_db_query( $q="INSERT INTO ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." SET `products_options_id`=".(int)$_arr['_shop_id'].", `products_options_values_id`=".$vid );
        if( $res )
          $values[$id][$_id]['_shop_id'] = $vid;
      }
    }
  }
  act_failsave_db_query( "UNLOCK TABLES" );


  return $res;
}

function _get_next_options_id( )
{
  $r = act_db_query( "SELECT MAX(products_options_id) AS maxid FROM ".TABLE_PRODUCTS_OPTIONS );
  $tmp = act_db_fetch_array($r);
  $next_id = $tmp['maxid']+1;
  ((mysqli_free_result( $r ) || (is_object( $r ) && (get_class( $r ) == "mysqli_result"))) ? true : false);
  return $next_id;
}

function _get_next_options_values_id( )
{
  $r = act_db_query( "SELECT MAX(products_options_values_id) AS maxid FROM ".TABLE_PRODUCTS_OPTIONS_VALUES );
  $tmp = act_db_fetch_array($r);
  $next_id = $tmp['maxid']+1;
  ((mysqli_free_result( $r ) || (is_object( $r ) && (get_class( $r ) == "mysqli_result"))) ? true : false);
  return $next_id;
}

function _attr_opts_sort( $a, $b )
{
  return (count($a) > count($b) ? -1 : (count($a) < count($b) ? 1 : 0));
}

function _attr_opts_cmp( $a, $b )
{
  $keys = array_intersect( array_keys($a), array_keys($b) );
  $same = TRUE;
  foreach( $keys as $k )
    $same &= !strcasecmp($a[$k], $b[$k]);
  return $same;
}


/**
 * Find / create corresponding tax class
 *
 * 
 * @param array $taxes_advanced Steuer-Zuordnung
 * @param int $products_tax_class_id Tax-Class-ID, dazu passend
 * @returns array Array( 'ok'=>, 'errno'=> )
 */
function _import_product_to_taxes_advanced( $taxes_adv, $leist_art, &$products_tax_class_id )
{
  $class_to_land_to_percent_xtc = array();
  $leist_art = (int)$leist_art;

  $taxes_advanced = array();
  foreach( $taxes_adv as $_land => $arr )
  {
    $taxes_advanced[$_land] = $arr['prozent'];
  }


  $r = act_db_query( "SELECT tc.tax_class_id,tc.tax_class_title,c.countries_iso_code_2, tr.tax_rate,tr.tax_priority FROM tax_class AS tc, tax_rates AS tr, zones_to_geo_zones AS zgz, countries AS c WHERE tr.tax_class_id=tc.tax_class_id AND zgz.geo_zone_id=tr.tax_zone_id AND c.countries_id=zgz.zone_country_id" );
  while( $row = act_db_fetch_array($r) )
  {
    $country = strtoupper( trim($row['countries_iso_code_2']) );
    if( !isset($taxes_advanced[$country]) )   // need only EU
      continue;
    $class_to_land_to_percent_xtc[(int)$row['tax_class_id']][$country] = (float)$row['tax_rate'];
  }
  ((mysqli_free_result( $r ) || (is_object( $r ) && (get_class( $r ) == "mysqli_result"))) ? true : false);

  // just in case
  $ta = array();
  foreach( $taxes_advanced as $_lang => $_percent )
    $ta[strtoupper(trim($_lang))] = (float)$_percent;
  $taxes_advanced = $ta;
  unset( $ta );

  // tricky, innit?
  $diff = array_merge(
    $first= array_diff_assoc($taxes_advanced, $class_to_land_to_percent_xtc[$leist_art]),
            array_diff_assoc($class_to_land_to_percent_xtc[$leist_art], $taxes_advanced)
  );
  if( !count($diff) )
  {
    $products_tax_class_id = $leist_art;
    return array( 'ok' => TRUE );
  }

  // first: delete redundant countries
  $res1 = TRUE;
  foreach( array_keys($diff) as $_code )
    $res1 &= act_db_query( "DELETE FROM zgz USING zones_to_geo_zones AS zgz, countries AS c WHERE zgz.zone_country_id=c.countries_id AND c.countries_iso_code_2='".esc($_code)."'" );

  // second: re-create country<->rate, but only for first diff
  foreach( $first as $_code => $_rate )
  {
    $res = act_db_query( "SELECT `tax_zone_id` FROM tax_rates WHERE `tax_class_id`={$leist_art} AND tax_priority=1 AND tax_rate=".round($_rate,4) );
    $r = act_db_fetch_array( $res );
    ((mysqli_free_result( $res ) || (is_object( $res ) && (get_class( $res ) == "mysqli_result"))) ? true : false);
    if( is_array($r) )
      $tax_zone_id = (int)$r['tax_zone_id'];
    else
      $tax_zone_id = 0;

    if( !$tax_zone_id )
    {
      $res = act_db_query( "SELECT MAX(`geo_zone_id`) AS max FROM zones_to_geo_zones" );
      $r = act_db_fetch_array( $res );
      ((mysqli_free_result( $res ) || (is_object( $res ) && (get_class( $res ) == "mysqli_result"))) ? true : false);
      $tax_zone_id = max( 8, $r['max'] + 1 );

      $res1 &= act_db_query( "REPLACE INTO geo_zones SET geo_zone_id='".(int)$tax_zone_id."', geo_zone_name='Steuerzone Lieferschwelle', geo_zone_description='', last_modified=NOW(), date_added=NOW()" );

      $res1 &= act_db_query( "INSERT INTO tax_rates SET tax_zone_id='".(int)$tax_zone_id."', tax_class_id={$leist_art}, tax_priority=1, tax_rate='".round($_rate,4)."', `tax_description`='".esc(sprintf("UST %0.1f%%", round($_rate,4)))."', last_modified=NOW(), date_added=NOW()" );
    }

    $res1 &= act_db_query( "INSERT INTO zones_to_geo_zones (zone_country_id,zone_id,geo_zone_id,last_modified,date_added) SELECT `countries_id`,0,'".(int)$tax_zone_id."',NOW(),NOW() FROM `countries` WHERE countries_iso_code_2='".esc($_code)."'" );

    if( $tax_zone_id >= 8 )
    {
      $countries = array();
      $res = act_db_query( "SELECT `countries_iso_code_2` FROM zones_to_geo_zones AS zgz, countries AS c WHERE zgz.zone_country_id=c.countries_id AND zgz.geo_zone_id='".(int)$tax_zone_id."'" );
      while( $r = act_db_fetch_array($res) )
        $countries[] = $r['countries_iso_code_2'];
      ((mysqli_free_result( $res ) || (is_object( $res ) && (get_class( $res ) == "mysqli_result"))) ? true : false);
      $countries = join( ', ', $countries );
      $res1 &= act_db_query( "UPDATE geo_zones SET `geo_zone_name`='Steuerzone Lieferschwelle ".esc($countries)."' WHERE `geo_zone_id`=".(int)$tax_zone_id );
    }
  }

  if( !$res1 )
    return array( 'ok' => FALSE, 'errno'=>EIO, 'error'=>'Fehler beim Update der Steuers�tze' );

  $products_tax_class_id = $leist_art;
  return array( 'ok' => TRUE );
}


function import_delete_product( $params )
{
    if( !parse_args($params,$ret) )
    {
        return $ret;
    }
    $art_nr = $params['orderNumber'];

    $product_query = act_db_query( $q="SELECT `products_id` FROM ".TABLE_PRODUCTS." WHERE `products_model`='".esc($art_nr)."'" );
    $n = act_db_num_rows( $product_query );
    if( !$n )
    {
        return resp(array( 'ok' => FALSE, 'errno' => ENOENT ));
    }

    $res = $xtp = act_db_fetch_array($product_query);
    $product_id = $res['products_id'];

    if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) )
    {
        require_once( 'includes/classes/categories.php' );
        require_once ('includes/classes/'.FILENAME_IMAGEMANIPULATOR);
        require_once ('includes/classes/object_info.php');
        require_once (DIR_FS_INC.'xtc_get_tax_rate.inc.php');
        require_once (DIR_FS_INC.'xtc_get_products_mo_images.inc.php');
        require_once (DIR_WS_CLASSES.'currencies.php');
        require_once (DIR_FS_INC.'xtc_wysiwyg.inc.php');

        $currencies = new currencies();
        $catfunc = new categories();

        $catfunc->remove_product($product_id);
    }
    else if( act_shop_is(SHOP_TYPE_OSCOMMERCE) )
    {
        tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$product_id . "'");

        $product_categories_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$product_id . "'");
        $product_categories = tep_db_fetch_array($product_categories_query);

        if ($product_categories['total'] == '0') {
            tep_remove_product($product_id);
        }
    }

    return resp(array( 'ok' => TRUE ));
}


function import_product_stock( $params )
{
    if( !parse_args($params,$ret) )
    {
        return $ret;
    }
    $art = $params['product'];
    if( is_array($art) )
    {
        if( !isset($art['art_nr']) && count($art) )
        {
            $res = array( 'ok' => TRUE, 'success'=>array(), 'failed'=>array() );
            foreach( $art as $_i => $_a )
            {
                $res1 = _import_product_stock( $_a );
                $res['success'][$_i] = $res1['ok'];
                if( !$res1['ok'] )
                {
                    $res['failed'][$_i] = $res1;
                }
            }
        }
        else
        {
          $res = _import_product_stock( $art );
        }
    }
    else
    {
        $res = array( 'ok'=> FALSE, 'errno'=>EINVAL );
    }
    return resp($res);
}

function _import_product_stock( $art )
{
  $res = act_db_query( "SELECT `products_id` FROM ".TABLE_PRODUCTS." WHERE `products_model`='".esc($art['art_nr'])."'" );
  $prod = act_db_fetch_array($res);
  ((mysqli_free_result( $res ) || (is_object( $res ) && (get_class( $res ) == "mysqli_result"))) ? true : false);

  if( !is_array($prod) )
    return array( 'ok' => 0, 'errno' => ENOENT, 'error' => 'Keinen Artikel \''.$art['art_nr'].'\' gefunden.' );

  $q = '';
  if( isset($art['shipping_status']) && ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) )
    $q .= ', `products_shippingtime`='.(int)$art['shipping_status'];

  if( isset($art['products_status']) )
    $q .= ', `products_status`='.(int)$art['products_status'];

  $res = act_db_query( "UPDATE ".TABLE_PRODUCTS." SET `products_quantity`=".(float)$art['l_bestand']."{$q} WHERE `products_id`=".(int)$prod['products_id'] );
  if( !$res )
    return array( 'ok' => 0, 'errno' => EIO, 'error' => 'Fehler beim Update des Bestandes' );

  if( is_array($art['attributes']) && count($art['attributes']) && ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) ) {
      if (ACTINDO_ATTRIBUTES_MODE == 'properties')
      {
          require_once('attributeHandler.php');
          $attributes = new attributeHandler();
          $attributes->updateStock($art);
      }
      else
      {
          $res = _do_import_attributes_options( $art['attributes']['names'], $art['attributes']['values'], TRUE );
          if( !$res )
              return array( 'ok' => 0, 'errno' => EIO, 'error' => 'Fehler beim finden der Attribute' );

          $res = _do_set_article_attributes( $prod['products_id'], null, null, null,
              $art['attributes']['combination_simple'], $art['attributes']['names'], $art['attributes']['values'], TRUE );
          if( !$res )
              return array( 'ok' => 0, 'errno' => EIO, 'error' => 'Fehler beim setzen des Attributs-Bestandes' );
      }
  }

  return array( 'ok'=>TRUE );
}
