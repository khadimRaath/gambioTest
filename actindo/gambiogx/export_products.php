<?php

/**
 * export products
 *
 * actindo Faktura/WWS connector
 *
 * @package actindo
 * @author  Patrick Prasse <pprasse@actindo.de>
 * @author  Chris Westerfield <westerfield@actindo.de>
 * @version $Revision: 511 $
 * @copyright Copyright© Actindo GmbH 2015, <support@actindo.de>, Carl-Zeiss-Ring 15 - 85737 Ismaning
 * @license http://opensource.org/licenses/GPL-2.0 GNU Public License
*/


function export_products_count( $params )
{
    if( !parse_args($params,$ret) )
    {
        return $ret;
    }
    $categories_id = $params['categoryId'];
    $products_model = $params['orderNumber'];
    $categories = array();

    if( $categories_id > 0 )
    {
        $q = "pc.`categories_id`={$categories_id}";
    }
    elseif( !empty($products_model) )
    {
        $q = "m.`products_model`='".esc($products_model)."'";
    }
    else
    {
        $q = '1';
    }

    $res = act_db_query(
    "SELECT ".($categories_id<0 ? 'pc.`categories_id` AS cid,' : '\''.(int)$categories_id.'\' AS cid,')."COUNT(*) AS cnt FROM `products` AS m, `products_to_categories` AS pc, `categories` AS c WHERE ".
    "(c.`categories_id`=pc.`categories_id` OR (pc.`categories_id`=0 AND c.categories_id IS NULL)) AND m.`products_id`=pc.`products_id` AND {$q}".($categories_id<0 ? ' GROUP BY pc.`categories_id`' : '') );
    while( $c=mysqli_fetch_assoc($res) )
    {
        $categories[(int)$c['cid']] = (int)$c['cnt'];
    }
    ((mysqli_free_result($res) || (is_object($res) && (get_class($res) == "mysqli_result"))) ? true : false);

    $res = act_db_query( "SELECT COUNT(*) AS cnt, pc.categories_id, c.categories_id FROM (`products` AS m, `products_to_categories` AS pc) LEFT JOIN `categories` AS c USING(`categories_id`) WHERE m.`products_id`=pc.`products_id` GROUP BY m.`products_id` HAVING (pc.categories_id=c.categories_id OR pc.categories_id=0)" );
    while( $c=mysqli_fetch_assoc($res) )
    {
        $categories[-1] += (int)$c['cnt'];
    }
    ((mysqli_free_result($res) || (is_object($res) && (get_class($res) == "mysqli_result"))) ? true : false);

    return resp(array('ok'=>true,'count'=>$categories));
}


function export_products_list( $params )
{
    if( !parse_args($params,$ret) )
    {
        return $ret;
    }
    $categories_id      = $params['categoryId'];
    $lang               = $params['language'];
    $products_model     = $params['orderNumber'];
    $just_list          = $params['justList'];
    $from               = $params['offset'];
    $count              = $params['limit'];
    $filters            = $params['filters'];
    if(empty($count))
    {
        $count = 0x7FFFFFFF;
    }
    $categories_id      = (int)$categories_id;
    $categories_id = (int)$categories_id;
    $products = array();

    if( !$lang )
    {
        $lang = default_lang();
    }

    if( $categories_id )
    {
        $q = "pc.`categories_id`={$categories_id}";
    }
    elseif( !empty($products_model) )
    {
        $q = "m.`products_model`='".esc($products_model)."'";
    }
    else
    {
        $q = '1';
    }
    $mapping = array(
        'last_modified' => array( 'm', 'products_last_modified' ),
        'created' => array( 'm', 'products_date_added' ),
        'products_id' => array( 'm', 'products_id' ),
        'categories_id' => array( 'pc', 'categories_id' ),
        'art_nr' => array( 'm', 'products_model' ),
    );
    $res = create_query_from_filter( $filters, $mapping );
    if( !is_array($res) )
    {
        return resp(array( 'ok'=> FALSE, 'errno'=>EIO, 'error'=>'create_query_from_filter returned false' ));
    }
    $exported = array();
    $sql = "SELECT m.products_id AS `products_id`, m.`products_model` AS art_nr, m.`products_price` AS grundpreis, m.`products_date_added`, m.`products_last_modified`, m.`products_status`, pc.`categories_id` AS categories_id, pd.products_name AS art_name FROM (`products` AS m, `products_to_categories` AS pc) LEFT JOIN `categories` AS c ON (c.`categories_id`=pc.`categories_id` OR (pc.`categories_id`=0 AND c.categories_id IS NULL)) LEFT JOIN products_description AS pd ON (m.`products_id`=pd.`products_id` AND pd.`language_id`={$lang})  WHERE  m.`products_id`=pc.`products_id` AND (pc.categories_id=c.categories_id OR pc.categories_id=0) AND {$q} AND {$res['q_search']} GROUP BY m.products_id ORDER BY m.products_model, m.products_id LIMIT {$from}, {$count}";
    $res = act_db_query( $sql );
    while( $prod = mysqli_fetch_assoc($res) )
    {
        if( !$categories_id && isset($exported[(int)$prod['products_id']]) )   // already exported, skip
        {
            continue;
        }
        $exported[(int)$prod['products_id']] = 1;

        $prod['products_id'] = (int)$prod['products_id'];
        $prod['grundpreis'] = (float)$prod['grundpreis'];
        $prod['categories_id'] = (int)$prod['categories_id'];
        $prod['products_status'] = (int)$prod['products_status'];
        $prod['created'] = datetime_to_timestamp( $prod['products_date_added'] );
        $prod['last_modified'] = datetime_to_timestamp( $prod['products_last_modified'] );
        $prod['products_status'] = (int)$prod['products_status'];
        if( $prod['last_modified'] <= 0 )
        {
            $prod['last_modified'] = $prod['created'];
        }
        unset( $prod['products_date_added'], $prod['products_last_modified'] );

        $products[] = $prod;
    }
    ((mysqli_free_result($res) || (is_object($res) && (get_class($res) == "mysqli_result"))) ? true : false);

    return resp(array( 'ok' => TRUE, 'products' => $products ));
}

function export_products( $params )
{
    if( !parse_args($params,$ret) )
    {
        return $ret;
    }
    $categories_id      = $params['categoryId'];
    $lang               = $params['language'];
    $products_id        = $params['orderNumber'];
    $just_list          = $params['justList'];
    $from               = $params['offset'];
    $count              = $params['limit'];
    $filters            = $params['filters'];
    if(empty($count))
    {
        $count = 0x7FFFFFFF;
    }
    $categories_id      = (int)$categories_id;
    $products           = array();

    if( !$lang )
    {
        $lang = default_lang();
    }

    if( $categories_id )
    {
        $q = "pc.`categories_id`={$categories_id}";
    }
    elseif( !empty($products_id) )
    {
        $q = "m.`products_id`='".esc($products_id)."'";
    }
    else
    {
        $q = '1';
    }
    $mapping = array(
        'last_modified' => array( 'm', 'products_last_modified' ),
        'created' => array( 'm', 'products_date_added' ),
        'products_id' => array( 'm', 'products_id' ),
        'categories_id' => array( 'pc', 'categories_id' ),
        'art_nr' => array( 'm', 'products_model' ),
    );
    $res = create_query_from_filter( $filters, $mapping );
    if( !is_array($res) )
    {
        return resp(array( 'ok'=> FALSE, 'errno'=>EIO, 'error'=>'create_query_from_filter returned false' ));
    }
    $exported = array();

    $res = act_db_query( "SELECT m.*, pc.categories_id FROM (`products` AS m, `products_to_categories` AS pc) LEFT JOIN `categories` AS c ON (c.`categories_id`=pc.`categories_id` OR (pc.`categories_id`=0 AND c.categories_id IS NULL)) LEFT JOIN products_description AS pd ON (m.`products_id`=pd.`products_id` AND pd.`language_id`=2)  WHERE  m.`products_id`=pc.`products_id` AND (pc.categories_id=c.categories_id OR pc.categories_id=0) AND {$q} AND {$res['q_search']} GROUP BY m.products_id ORDER BY m.products_model, m.products_id LIMIT {$from}, {$count}" );
    while( $p = mysqli_fetch_assoc($res) )
    {
        if( !$categories_id && isset($exported[(int)$p['products_id']]) )   // already exported, skip
        {
            continue;
        }


        $exported[(int)$p['products_id']] = 1;

        $p['products_id'] = (int)$p['products_id'];
        $p["art_nr"] = $p["products_model"];
        $p["l_bestand"] = (float)$p["products_quantity"];
        $p["weight"] = (float)$p["products_weight"];
        $p["weight_unit"] = "kg";
        $p['info_template'] = $p['product_template'];
        $p['shipping_status'] = $p['products_shippingtime'];

        $p['created'] = datetime_to_timestamp( $p['products_date_added'] );
        $p['last_modified'] = datetime_to_timestamp( $p['products_last_modified'] );
        if( $p['last_modified'] <= 10000 )
        {
            $p['last_modified'] = $p['created'];
        }
        unset( $p['products_date_added'], $p['products_last_modified'] );

        $p['fsk18'] = (int)$p['products_fsk18'];
        unset( $p['products_fsk18'] );

        // primary category
        $p['categories_id'] = (int)$p['categories_id'];

        // other categories
        $p['all_categories'] = array();
        $catid_query = act_db_query( "SELECT `categories_id` FROM ".TABLE_PRODUCTS_TO_CATEGORIES." WHERE products_id=".(int)$p["products_id"] );
        while( $cat = act_db_fetch_array($catid_query) )
        {
              if( $p['categories_id'] == 0 )    // erste kategorie = startseite
              {
                    if( $cat['categories_id'] != 0 )    // set one that IS NOT zero
                    {
                          $p['categories_id'] = (int)$cat['categories_id'];
                          continue;
                    }
              }
              $p['all_categories'][] = (int)$cat['categories_id'];
        }
        ((mysqli_free_result($catid_query) || (is_object($catid_query) && (get_class($catid_query) == "mysqli_result"))) ? true : false);


        // base price, taxes
        $p['is_brutto'] = ( PRICE_IS_BRUTTO == 'true' );
        $p["grundpreis"] = export_convert_tax( (float)$p["products_price"], $p['is_brutto'], $p['products_tax_class_id'] );
        $p['mwst_stkey'] = -1;
        $p['mwst'] = act_get_tax_rate( $p['products_tax_class_id'] );
        switch( $p['products_tax_class_id'] )
        {
          case 0:
            $p['mwst_stkey'] = 0; // heh, 0, 1, 11 possible, we use 0, as actindo can handle this
            break;
          case 1:
            $p['mwst_stkey'] = 3;
            break;
          case 2:
            $p['mwst_stkey'] = 2;
            break;
        }


        // descriptions, names in all languages
        $desc_query = act_db_query( "SELECT * FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE products_id = ".(int)$p["products_id"]." ORDER BY `language_id` ASC" );
        while( $desc = act_db_fetch_array($desc_query) )
        {
              $desc['language_id'] = (int)$desc['language_id'];
              if( $desc['language_id'] == $lang )
              {
                  $p["art_name"] = $desc["products_name"];
              }
              foreach( $desc as $key => $val )
              {
                  $p["description"][(int)$desc["language_id"]][$key] = $val;
              }
        }
        ((mysqli_free_result($desc_query) || (is_object($desc_query) && (get_class($desc_query) == "mysqli_result"))) ? true : false);


        // price brackets
        if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) )
        {
              _do_export_preisgruppen( $p );
        }

        // attributes
        _do_export_attributes( $p );

        // cross-selling
        if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) )
        {
            _do_export_xselling( $p );
        }

        // content
        if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) )
        {
            _do_export_content( $p );
        }


        // group-permission
        if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) )
        {
          $groupperm = null ;
          foreach( $p as $_key => $_val )
          {
            if( stripos($_key, $_i='group_permission_')===0 )
            {
              is_array($groupperm) or $groupperm = array();
              if( $_val > 0 )
                $groupperm[] = (int)substr( $_key, strlen($_i) );
              unset( $p[$_key] );
            }
          }
          $p['group_permission'] = $groupperm;
        }


        // images
        if( strlen( $p["products_image"] ) )
        {
          $img["image_nr"] = 0;
          $img["image_name"] = $p["products_image"];
          $p["images"][0] = $img;
        }
        if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) )
        {
          $img_query = act_db_query( "SELECT * FROM ".TABLE_PRODUCTS_IMAGES." WHERE products_id = ".(int)$p["products_id"]." ORDER BY `image_nr` ASC" );
          while( $img = act_db_fetch_array($img_query) )
          {
            $p["images"][] = $img;
          }
          ((mysqli_free_result($img_query) || (is_object($img_query) && (get_class($img_query) == "mysqli_result"))) ? true : false);
        }
        if(count($p['images']) > 0)
        {
            foreach( $p["images"] as $idx => $img )
            {
                if( strlen($file_name = $img['image_name']) )
                {
                    if( ( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) ) )
                    {
                        $path = null;
                        if( defined( "DIR_FS_CATALOG_ORIGINAL_IMAGES" ) && is_readable($path=DIR_FS_CATALOG_ORIGINAL_IMAGES.$file_name) && filesize($path) )
                        {
                            $p['images'][$idx]['image_subfolder'] = basename( DIR_FS_CATALOG_ORIGINAL_IMAGES );
                        }
                        elseif( defined( "DIR_FS_CATALOG_POPUP_IMAGES" ) && is_readable($path=DIR_FS_CATALOG_POPUP_IMAGES.$file_name) && filesize($path) )
                        {
                            $p['images'][$idx]['image_subfolder'] = basename( DIR_FS_CATALOG_POPUP_IMAGES );
                        }
                        elseif( defined( "DIR_FS_CATALOG_INFO_IMAGES" ) && is_readable($path=DIR_FS_CATALOG_INFO_IMAGES.$file_name) && filesize($path) )
                        {
                            $p['images'][$idx]['image_subfolder'] = basename( DIR_FS_CATALOG_INFO_IMAGES );
                        }
                        if( !is_null($path) )
                        {
                            $p["images"][$idx]["image"] = new Zend_XmlRpc_Value_Base64(file_get_contents( $path ));
                            $size = getimagesize( $path );
                            $p["images"][$idx]["image_type"] = image_type_to_mime_type( $size[2] );
                        }
                        if( act_shop_is(SHOP_TYPE_GAMBIOGX) )
                        {
                            // TODO: image descriptions
                        }
                    }
                    else if( act_shop_is(SHOP_TYPE_OSCOMMERCE) )
                    {
                        if( defined( "DIR_FS_CATALOG_IMAGES" ) && is_readable($path=DIR_FS_CATALOG_IMAGES.$file_name) && filesize($path) )
                        {
                            $p['images'][$idx]['image_subfolder'] = '';
                        }
                        if( !is_null($path) )
                        {
                            $p["images"][$idx]["image"] = new Zend_XmlRpc_Value_Base64(file_get_contents( $path ));
                            $size = getimagesize( $path );
                            $p["images"][$idx]["image_type"] = image_type_to_mime_type( $size[2] );
                        }
                    }
                }
            }
        }
        if( act_shop_is(SHOP_TYPE_GAMBIOGX) )
        {
          if( is_array($p['images'][0]) )
          {
            $imgd_query = act_db_query( "SELECT `gm_alt_text`, `language_id` FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE `products_id`=".(int)$p["products_id"] );
            while( $d = act_db_fetch_array($imgd_query) )
            {
              $p['images'][0]['image_title'][get_language_code_by_id($d['language_id'])] = $d['gm_alt_text'];
            }
            act_db_free( $imgd_query );
          }
          for( $i=1; $i<count($p['images']); $i++ )
          {
            $imgd_query = act_db_query( "SELECT `gm_alt_text`, `language_id` FROM gm_prd_img_alt WHERE `products_id`=".(int)$p["products_id"]." AND `image_id`=".(int)$p['images'][$i]['image_id'] );
            while( $d = act_db_fetch_array($imgd_query) )
            {
              $p['images'][$i]['image_title'][get_language_code_by_id($d['language_id'])] = $d['gm_alt_text'];
            }
            act_db_free( $imgd_query );
          }
        }
        $products[] = $p;
    }
    ((mysqli_free_result($res) || (is_object($res) && (get_class($res) == "mysqli_result"))) ? true : false);

    return resp(array( 'ok' => TRUE, 'products' => $products ));
}

function _do_export_attributes( &$p )
{
  if(ACTINDO_ATTRIBUTES_MODE=='properties')
  {
      require_once('attributeHandler.php');
      $attributes = new attributeHandler();
      $attributes->setToActindo();
      $attributes->setArticleId($p['products_id']);
      $attributes->process();
      if($attributes->errorExists())
      {
          return $attributes->getErrorMessage();
      }
      else
      {
          if($attributes->attributesExist())
          {
              $p['attributes'] = $attributes->getAttributes();
              //$p['l_bestand'] = $attributes->getCummulativeStock();
          }
      }
  }
  else
  {
      $p['attributes'] = array();
      $attr_query = act_db_query( "SELECT * FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE `products_id`=".(int)$p["products_id"]." ORDER BY `options_id` ASC" );
      if( act_db_num_rows($attr_query) )
      {
        while( $row=act_db_fetch_array($attr_query) )
        {
          $row['options_id'] = (int)$row['options_id'];
          $row['options_values_id'] = (int)$row['options_values_id'];

          if( !isset($p['attributes']['names'][$row['options_id']]) )
          {
            $p['attributes']['names'][$row['options_id']] = $p['attributes']['values'][$row['options_id']] = array();
            $oid_query = act_db_query( "SELECT * FROM ".TABLE_PRODUCTS_OPTIONS." WHERE `products_options_id`={$row['options_id']}" );
            while( $opt=act_db_fetch_array($oid_query) )
              $p['attributes']['names'][$row['options_id']][get_language_code_by_id($opt['language_id'])] = $opt['products_options_name'];
            ((mysqli_free_result( $oid_query ) || (is_object( $oid_query ) && (get_class( $oid_query ) == "mysqli_result"))) ? true : false);

            $vid_query = act_db_query( "SELECT * FROM ".TABLE_PRODUCTS_OPTIONS_VALUES." AS v, ".TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." AS ov WHERE v.products_options_values_id=ov.products_options_values_id AND ov.products_options_id={$row['options_id']}" );
            while( $val=act_db_fetch_array($vid_query) )
              $p['attributes']['values'][$row['options_id']][(int)$val['products_options_values_id']][get_language_code_by_id($val['language_id'])] = $val['products_options_values_name'];
            ((mysqli_free_result( $vid_query ) || (is_object( $vid_query ) && (get_class( $vid_query ) == "mysqli_result"))) ? true : false);
          }

          $p['attributes']['combination_simple'][$row['options_id']][$row['options_values_id']] = array(
            'options_values_price' => export_convert_tax( abs($row['options_values_price']), $p['is_brutto'], $p['products_tax_class_id'] ) * ($row['price_prefix']=='-' ? -1 : 1),
            'attributes_model' => $row['attributes_model'],
            'l_bestand' => $row['attributes_stock'],
            'options_values_weight' => abs($row['options_values_weight']) * ($row['weight_prefix']=='-' ? -1 : 1),
            'sortorder' => $row['sortorder'],
          );
        }
      }
      ((mysqli_free_result($attr_query) || (is_object($attr_query) && (get_class($attr_query) == "mysqli_result"))) ? true : false);
  }
}

function _do_export_preisgruppen( &$p )
{
  $groups = array_keys(export_customers_status());
  $preisgruppen = array();
  foreach( $groups as $status_id )
  {
    $n = 0;
    $offer_res = act_db_query( "SELECT * FROM personal_offers_by_customers_status_".(int)$status_id." WHERE `products_id`=".(int)$p['products_id']." AND `personal_offer`<>0 ORDER BY `quantity` ASC" );
    while( $pg = mysqli_fetch_assoc($offer_res) )
    {
      $preisgruppen[(int)$status_id]['is_brutto'] = $p['is_brutto'];
      if( $pg['quantity'] == 1 )
        $preisgruppen[(int)$status_id]['grundpreis'] = export_convert_tax( (float)$pg['personal_offer'], $preisgruppen[(int)$status_id]['is_brutto'], $p['products_tax_class_id'] );
      else
      {
        $n++;
        $preisgruppen[(int)$status_id]['preis_gruppe'.$n] = export_convert_tax( (float)$pg['personal_offer'], $preisgruppen[(int)$status_id]['is_brutto'], $p['products_tax_class_id'] );
        $preisgruppen[(int)$status_id]['preis_range'.$n] = (int)$pg['quantity'];
      }
    }
    ((mysqli_free_result( $offer_res ) || (is_object( $offer_res ) && (get_class( $offer_res ) == "mysqli_result"))) ? true : false);
    if( !count($preisgruppen[(int)$status_id]) )
      continue;
    $preisgruppen[(int)$status_id]['is_brutto'] = ( PRICE_IS_BRUTTO == 'true' ? 1 : 0 );
  }
  $p['preisgruppen'] = $preisgruppen;
}

function _do_export_xselling( &$p )
{
  $p['xselling'] = array();
  $res = act_db_query( "SELECT px.products_xsell_grp_name_id, px.sort_order, p.products_model FROM ".TABLE_PRODUCTS_XSELL." AS px, ".TABLE_PRODUCTS." AS p WHERE px.`products_id`=".(int)$p['products_id']." AND p.products_id=px.xsell_id" );
  while( $row = act_db_fetch_array($res) )
  {
    $p['xselling'][] = array( 'art_nr'=>$row['products_model'], 'group'=>(int)$row['products_xsell_grp_name_id'], 'sort_order'=>(int)$row['sort_order'] );
  }
  ((mysqli_free_result( $res ) || (is_object( $res ) && (get_class( $res ) == "mysqli_result"))) ? true : false);
}

function _do_export_content( &$p )
{
  $p['content'] = array();
  $res = act_db_query( "SELECT * FROM ".TABLE_PRODUCTS_CONTENT." WHERE `products_id`=".(int)$p['products_id'] );
  while( $row = act_db_fetch_array($res) )
  {
    $content = array(
      'language_code' => get_language_code_by_id( $row['languages_id'] ),
      'content_name' => $row['content_name']
    );

    if( !empty($row['content_file']) )
    {
      $content['type'] = 'file';
      $content['content_file_name'] = $row['content_file'];
      $fn = DIR_FS_DOCUMENT_ROOT.'media/products/'.$row['content_file'];
      $content['content'] = file_get_contents( $fn );
      $content['content_file_size'] = strlen( $content['content'] );
      $content['content_file_md5'] = md5( $content['content'] );
    }
    else if( !empty($row['content_link']) )
    {
      $content['type'] = 'link';
      $content['content'] = $row['content_link'];
    }
    else if( !empty($row['file_comment']) )
    {
      $content['type'] = 'html';
      $content['content'] = $row['file_comment'];
    }
    $content['content'] = new Zend_XmlRpc_Value_Base64($content['content']);
    $p['content'][] = $content;
  }
  ((mysqli_free_result( $res ) || (is_object( $res ) && (get_class( $res ) == "mysqli_result"))) ? true : false);
}


function export_convert_tax( $price, $is_brutto=0, $products_tax_class_id )
{
  $is_brutto = $is_brutto > 0;
//  printf( "\$is_brutto=".var_dump_string($is_brutto).", \$shop_is_brutto=".var_dump_string($shop_is_brutto).", \$price=".var_dump_string($price).", " );
//  printf( "\$products_tax_class_id=".var_dump_string($products_tax_class_id)."\n" );

  if( $is_brutto )      // sooo...
    $price = round(($price * (act_get_tax_rate($products_tax_class_id) + 100) / 100), PRICE_PRECISION);

  return $price;
}



function export_categories( )
{
  $cats = array();
  $langs = export_shop_languages( );
  foreach( array_keys($langs) as $lang_id )
    $cats[$lang_id] = _do_export_categories( 0, $lang_id, 0 );
  return $cats;
}


function _do_export_categories( $children_of=0, $language_id=0, $depth=0 )
{
  $cats = array();
  $res = act_db_query( "SELECT c.categories_id, c.parent_id, d.language_id, d.categories_name FROM `categories` c,`categories_description` d WHERE c.`categories_id`=d.`categories_id` AND d.language_id=".(int)$language_id." AND c.parent_id=".(int)$children_of." ORDER BY c.`sort_order`, d.`categories_name`" );
  while( $c = mysqli_fetch_assoc($res) )
  {
    $cats[(int)$c['categories_id']] = $c;
    $ch = _do_export_categories( (int)$c['categories_id'], $language_id, $depth+1 );
    if( count($ch) )
      $cats[(int)$c['categories_id']]['children'] = $ch;
  }
  ((mysqli_free_result( $res ) || (is_object( $res ) && (get_class( $res ) == "mysqli_result"))) ? true : false);

  return $cats;
}

