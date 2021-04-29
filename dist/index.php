<?php

use Webso\Arr;
use Webso\Str;

require_once 'vendor/autoload.php';

$db = new MysqliDb('localhost', 'root', '', 'db_name');

$action = Arr::get($_GET, 'action');
$tinhthanh_url = 'https://partner.viettelpost.vn/v2/categories/listProvinceById?provinceId=';
$quanhuyen_url = 'https://partner.viettelpost.vn/v2/categories/listDistrict?provinceId=';
$phuongxa_url = 'https://partner.viettelpost.vn/v2/categories/listWards?districtId=';

switch ($action) {
	case 'createjson':

		$tinhthanh_json = (string) download_page($tinhthanh_url);
		$tinhthanh_array = json_decode($tinhthanh_json, true);
		$tinhthanh_data = Arr::get($tinhthanh_array, 'data');

		$table = Arr::get($_GET, 'table');

		if (! $table) {
			dd('vui long truyen table');
		}

		if ($table == 'tinhthanh') {

			$ids = $db->insertMulti('tbl_province', $tinhthanh_data);

			dump('Success' , $ids);
		}

		elseif ($table == 'quanhuyen') {
			$tinh = $db->get('tbl_province');

			foreach ($tinh as $t) {
				$quanhuyen_json = (string) download_page($quanhuyen_url.$t['PROVINCE_ID']);
				$quanhuyen_array = json_decode($quanhuyen_json, true);
				$quanhuyen_data = Arr::get($quanhuyen_array, "data");

				$ids = $db->insertMulti('tbl_district', $quanhuyen_data);

				dump('Success' , $ids);
			}

		}

		elseif ($table == 'phuongxa') {

			$quan = $db->get('tbl_district');

			foreach ($quan as $q) {
				$phuongxa_json = (string) download_page($phuongxa_url.$q['DISTRICT_ID']);
				$phuongxa_array = json_decode($phuongxa_json, true);
				$phuongxa_data = Arr::get($phuongxa_array, "data");

				$ids = $db->insertMulti('tbl_wards', $phuongxa_data);

				dump('Success' , $ids);
			}

		}
		break;

	default:
		# code...
		break;
}
