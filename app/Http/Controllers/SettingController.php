<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use App\Models\User;
use App\Models\AmazonSetting;
use App\Models\YahooSetting;
use App\Models\YahooStore;
use App\Models\AmazonRegisterHistory;
use App\Models\AmazonItem;
use App\Models\YahooStoreItem;
use App\Models\YahooOrderItem;
use App\Models\AutoToolInfo;
use Mockery\Undefined;

class SettingController extends Controller
{

    // ---------  Amazon Register  --------- //
    public function item_register(Request $request): View
    {
        $user_id = Auth::id();
        $store_id = $request->store_id;
        $yahoo_store = YahooStore::find($store_id);
        $amazon_setting = AmazonSetting::where('store_id', $yahoo_store->id)->get();
        $amazon_items = AmazonItem::where('store_id', $yahoo_store->id)->pluck('asin');

        return view('items.amazon_register', ['yahoo_store' => $yahoo_store, 'amazon_setting' => $amazon_setting]);
    }

    public function add_amSetting(Request $request)
    {
        $user_id = Auth::id();

        $amSetting = new AmazonSetting;
		$amSetting->user_id = $user_id;
		$amSetting->store_id = $request->store_id;
		$amSetting->partner_tag = $request->partner_tag;
		$amSetting->access_key = $request->access_key;
		$amSetting->secret_key = $request->secret_key;
		$amSetting->save();
        
		return $amSetting;
    }

    public function edit_amSetting(Request $request)
    {
        $user_id = Auth::id();

		$setting = AmazonSetting::find($request->id);
		$setting[$request->col] = $request->content;
		$setting->save();
		
		return;
    }

    public function delete_amSetting(Request $request)
    {
        AmazonSetting::find($request->id)->delete();
		return $request->id;
    }

    public function save_history(Request $request)
    {
        $user_id = Auth::id();

        $history = new AmazonRegisterHistory;
		$history->user_id = $user_id;
		$history->store_id = $request->store_id;
		$history->csv_filename = $request->file_name;
		$history->count = $request->len;
		$history->save();
        
		return $history;
    }



    // ---------  Amazon Exhibit  --------- //
    public function item_exhibit(Request $request): View
    {
        $user_id = Auth::id();
        $store_id = $request->store_id;
        $yahoo_store = YahooStore::find($store_id);
		$yahoo_setting = YahooSetting::where('store_id', $store_id)->first();
		$amazon_items = AmazonItem::where('store_id', $store_id)->where('exhibit', 0)->get();

        return view('items.yahoo_exhibit', ['yahoo_store' => $yahoo_store, 'yahoo_setting' => $yahoo_setting, 'amazon_items' => $amazon_items]);
    }

    public function edit_yaSetting(Request $request)
    {
        $user_id = Auth::id();

		$setting = YahooSetting::find($request->id);
		$setting[$request->col] = $request->content;
		$setting->save();
		
		return;
    }



    // ---------  Amazon Order  --------- //
    public function item_order(Request $request): View
    {
        $user_id = Auth::id();
        $store_id = $request->store_id;
        $yahoo_store = YahooStore::find($store_id);
		$yahoo_order_items = YahooOrderItem::where('store_id', $store_id)->get();

        return view('items.yahoo_order', ['yahoo_store' => $yahoo_store, 'yahoo_order_items' => $yahoo_order_items]);
    }

    public function csv_download(Request $request)
    {
        $string = $request->ids;
        $item_ids = explode(',', $string);

        $data = "OrderTime,ShipInvoiceNumber2,OrderId,ItemId,Title,QuantityDetail,ShipName,UnitPrice,LineSubTotal,TotalPrice,ShipNameKana,ShipZipCode,ShipPrefecture,ShipCity,ShipAddress1,ShipAddress2,ShipPhoneNumber";
        foreach($item_ids as $item_id){

            $downItem = YahooOrderItem::find($item_id);
            $store_name = YahooStore::where('id', $downItem->store_id)->pluck('store_name')->first();
            $order_id = str_replace($store_name.'-', '', $downItem->order_id);

            $data .= "\n" . $downItem->order_time
                    . "," . $downItem->ship_invoicenumber2
                    . "," . $order_id
                    . ",L1=" . $downItem->item_id
                    . ",L1=" . $downItem->title
                    . ",L1=" . $downItem->quantity
                    . "," . $downItem->ship_lastname . " " . $downItem->ship_firstname
                    . ",L1=" . $downItem->unit_price
                    . ",L1=" . $downItem->line_subtotal
                    . "," . $downItem->total_price
                    . "," . $downItem->ship_lastname_kana . " " . $downItem->ship_firstname_kana
                    . "," . $downItem->ship_zipcode
                    . "," . $downItem->ship_prefecture
                    . "," . $downItem->ship_city
                    . "," . str_replace(',', '、', $downItem->ship_address1)
                    . "," . str_replace(',', '、', $downItem->ship_address2)
                    . "," . str_replace('-', '', $downItem->ship_phonenumber);

        }
        $filename = $store_name;

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . "_" . date("Y-m-d") . '.csv"');
        echo $data;
        exit();

	}

    

    // ---------  Auto Tool API  --------- //
    public function api_key_validate($key)
    {
        $tool_info = AutoToolInfo::where('tool_key', $key)->first();
        
        if (isset($tool_info)) {

            $expired_date = $tool_info->expired_date;
            $today = date('Y-m-d H:i:s');
            $p9_today = date('Y-m-d H:i:s', strtotime($today . ' +9 hours'));

            $convert_today = strtotime($p9_today);
            $convert_expday = strtotime($expired_date);

            if ($convert_today <= $convert_expday) {
                $check = 1;
                $user_id = $tool_info->user_id;
                $license = 'true';
                $message = "The key is valid.";

            } elseif ($convert_today > $convert_expday) {
                $check = 2;
                $user_id = 0;
                $license = 'false';
                $message = "The key is expired.";

            }
        } else {
            $check = 3;
            $user_id = 0;
            $license = 'false';
            $message = 'The key is invalid.';

        }

        return [
            'check' => $check,
            'user_id' => $user_id,
            'license' => $license,
            'message' => $message,
        ];
        
    }


    public function tool_license_check(Request $request)
    {
        $validate = $this->api_key_validate($request->key);

        return response() -> json(
            [
                'license' => $validate['license'],
                'message' => $validate['message'],
            ], 200);
            
    }

    public function get_shop(Request $request)
    {
        $validate = $this->api_key_validate($request->key);

        if ($validate['check'] == 1) {
            $user_id = $validate['user_id'];
            $item_code = $request->item_code;
            $store_item = YahooStoreItem::where('user_id', $user_id)->where('item_code', $item_code)->first();

            if (isset($store_item)) {
                $item_asin = $store_item->asin;

                $store_id = $store_item->store_id;
                $partner_tags = AmazonSetting::where('store_id', $store_id)->pluck('partner_tag');

                $tool_info = AutoToolInfo::where('tool_key', $request->key)->first();
                $access_count = $tool_info->access_count;
                $index = $access_count % count($partner_tags);

                $shop_url = "https://www.amazon.co.jp/dp/{$item_asin}?tag={$partner_tags[$index]}&linkCode=ogi&th=1&psc=1";

                $tool_info->update([
                    'access_count' => ($access_count + 1),
                ]);


                return response() -> json(
                    [
                        'license' => $validate['license'],
                        'message' => $validate['message'],
                        'shopURL' => $shop_url,
                    ], 200);
            }
        }


        return response() -> json(
            [
                'license' => $validate['license'],
                'message' => $validate['message'],
                'shopURL' => 'Not',
            ], 200);

    }















    public function settings(Request $request): View
    {
        $user_id = Auth::id();
		$amazon_setting = AmazonSetting::where('user_id', $user_id)->first();
		$yahoo_setting = YahooSetting::where('user_id', $user_id)->first();

        return view('setting.setting', ['amazon_setting' => $amazon_setting, 'yahoo_setting' => $yahoo_setting]);
    }

    public function set_column(Request $request)
    {
		$user_id = Auth::id();
        if ($request->setting == 'as') {
            $setting = AmazonSetting::find($user_id);
        } else {
            $setting = YahooSetting::find($user_id);
        }
		$setting[$request->col] = $request->content;
		$setting->save();
		
		return;
	}
}
