<?php // transform script for product import
ini_set('display_errors',1);
error_reporting(E_ALL);
ini_set('max_execution_time', -1);
ini_set('memory_limit', -1);
include('includes/lookup_functions.inc');

if (($handle = fopen("/var/www/magento_api/resources/csv-de-productos-general-es.csv", "r")) !== FALSE) {

	// Get headers
	if (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {

		$user_CSV[0] = array('sku','store_view_code','attribute_set_code','product_type','categories','product_websites','name','description','short_description','weight','product_online','tax_class_name','visibility','price','special_price','special_price_from_date','special_price_to_date','url_key','meta_title','meta_keywords','meta_description','created_at','updated_at','qty','base_image','base_image_label', 'small_image', 'small_image_label', 'thumbnail_image', 'thumbnail_image_label', 'additional_images','related_skus','associated_skus','additional_attributes');

	$i = 0;
	$attribute_array = array();
	$skus_array = array();
	$category_array = array();

	// Get the rest
	while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
		$i++;

		// verifying the data feed
		if (strlen($data[0]) == 8 && preg_match('/(\.jpg|\.png|\.bmp)$/i', $data[0]) == FALSE) {

					$category_array[$data[1]] .= $data[0] . ',';

				if(return_attributes($data[3]) !== FALSE){
					$group_sku[toAscii($data[2])] = $data[0];
					$url = toAscii($data[2] . '-' . $data[5]);
					$user_CSV[$i] = write_to_csv($i, $data, 'simple', 'unpublished', '', $data[3],$data[5], $url);
					$skus_array[substr($data[0],0,5)] .= $data[0] . ',';
				}

			else{
				$url = toAscii($data[2]);
				$user_CSV[$i] = write_to_csv($i, $data, 'simple', 'published', '','','',$url);
			}

		}
		}
	}
	fclose($handle);
}

function write_to_csv($i, $data, $product_type, $published, $related_skus, $attribute_set_code, $attribute, $url){
		if($attribute_set_code !== ''){
			print $attribute_set_code . '****<br>';
		}
		$additional_attributes = '';
	switch ($attribute_set_code) {
		case '':
			$attribute_set_code = 'Default';
		break;
		case 'Diseño':
			$additional_attributes .= 'dise_o=' . $attribute ;
		break;
		case 'Color':
			$additional_attributes .= 'color=' . $attribute;
		break;
		case 'Talla':
			$additional_attributes .= 'talla=' . $attribute;
		break;
		case 'Animal':
			$additional_attributes .= 'animal=' . $attribute;
		break;
		case 'Modelo':
			$additional_attributes .= 'modelo=' . $attribute;
		break;
	}

	$image_url = $data[23];
	$img = '/var/www/magento/pub/media/import/' . toAscii($data[2]) . '.jpg';
	if (file_exists($img) !== TRUE) {
		copy($image_url, $img);
	}

	// file_put_contents($img, file_get_contents($image_url));
	$primary_image = toAscii($data[2]) . '.jpg';
	$a = 24;
	$b = 1;
	$additional_images = '';

	while(!empty($data[$a])){
		$image_url = $data[$a];
		$img = '/var/www/magento/pub/media/import/' . toAscii($data[2]) . '_' . $b . '.jpg';
		if (file_exists($img) !== TRUE) {
			copy($image_url, $img);
		}
		$additional_images .= toAscii($data[2]) . '_' . $b . '.jpg' . ',';
		$a++;
		$b++;
	}

  $additional_images = substr($additional_images, 0, -1);

	if($published === 'unpublished'){
		$published = 0;
	}else{
		$published = 1;
	}

	// $category_assigned = 'Juguetes Bebes/';
	$category_explode = explode(",", $data[1]);

	// $last_key = end(array_keys($category_explode));
	$numItems = count($category_explode);
	$a = 0;
	$category_assigned = '';
	foreach ($category_explode as $key => $value) {
		if(return_category($value) !== FALSE) {
			$category_assigned .= 'Mipeque/Categoria/' . return_category($value) . ',';
		}
	}

	if(isset($data[8]) && !empty($data[8])){
		if($additional_attributes == ''){
			$additional_attributes = 'marca=' . return_brand($data[8]);
		}else{
			$additional_attributes .= ',marca=' . return_brand($data[8]);
		}
		$category_assigned .= 'Mipeque/Marca/' . return_brand($data[8]);
	}else{
		$brand = '';
	}

	if(!empty($data[21])){
		$created = $data[21];
	}else{
		$created = '';
	}

	if(!empty($data[22])){
		$updated = $data[22];
	}else{
		$updated = '';
	}

	$user_CSV = array(
		$data[0], // 1. SKU
		'', // 2 STORE VIEW CODE
		'Default',  // 3 ATTRIBUTE SET CODE
		$product_type,  // 4 PRODUCT TYPE
		$category_assigned,  // 5 CATEGORIES
		'base', // 6 PRODUCT WEBSITES
		$data[2], // 7 NAME
		$data[7], // 8 DESCRIPTION
		'', // 9 SHORT DESC
		$data[19],  // 10 WEIGHT
		$published,  // 11 PRODUCT ONLINE
		'Taxable Goods', // 12 Tax class name
		'Catalog, Search', // 13 VISIBILITY
		$data[10], // 14PRICE
		$data[11], // 15 SPECIAL PRICE
		date('j/n/Y'), // 16 SPF
		date('j/n/Y', strtotime("+30 days")),
		$url, // 18 URL KEYlana del rey
		'', // 19 META TITLE
		'', // 20 META KEYWORDS
		'', // 21 META DESC
		$created, // 22 CREATED
		$updated, // 23 UPDATED
		$data[20], // 25 qty
		$primary_image, // Base image
		toAscii($data[2]) . ' imagen', // Base iamge label
		$primary_image, // Small image
		toAscii($data[2]) . ' imagen', // Small image label
		$primary_image, // Thumbnail
		toAscii($data[2]) . ' imagen', // Thumbnail label
		$additional_images, // Additional images
		$data[1], // related_skus',
		$data[1], //'associated_skus'
		$additional_attributes, // Small image label

	);
	// echo($i);
	// print_r($user_CSV);
	return($user_CSV);
}

function generate_file($user_CSV, $category_array){

	// print_r($user_CSV);
	$fp = fopen('/var/www/magento_api/resources/magento_upload.csv', 'w');
	// print_r($category_array);
	foreach ($user_CSV as $line) {
		if(isset($category_array[$line[31]])){
			$line[31] = substr($category_array[$line[31]], 0, -1);
			$line[32] = substr($category_array[$line[32]], 0, -1);
		}
		fputcsv($fp, $line, ',');
	}

	fclose($fp);
}
echo('writing file');
generate_file($user_CSV, $category_array);
?>

