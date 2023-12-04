<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use App\Models\User;
use App\Models\YahooSetting;
use App\Models\YahooStore;
use App\Models\YahooOrderItem;


class YahooOrderItemController extends Controller
{
    // public function item_store(Request $request): View
    // {
    //     $user_id = Auth::id();
    //     $store_id = $request->id;
	// 	$yahoo_store = YahooStore::where('id', $store_id)->first();
	// 	$yahoo_items = YahooOrderItem::where('store_id', $store_id)->get();

    //     return view('items.item_list', ['yahoo_store' => $yahoo_store, 'yahoo_items' => $yahoo_items]);
    // }

    // public function csv_download(Request $request)
    // {
	// 	$downItem = YahooOrderItem::find($request->id);
    //     $store_name = YahooStore::where('id', $downItem->store_id)->pluck('store_name')->first();
    //     $order_id = str_replace($store_name.'-', '', $downItem->order_id);

	// 	$data = "OrderTime,ShipInvoiceNumber2,OrderId,ItemId,Title,QuantityDetail,ShipName,UnitPrice,LineSubTotal,TotalPrice,ShipNameKana,ShipZipCode,ShipPrefecture,ShipCity,ShipAddress1,ShipAddress2,ShipPhoneNumber\n";
    //     $data .= $downItem->order_time
    //             . "," . $downItem->ship_invoicenumber2
    //             . "," . $order_id
    //             . ",L1=" . $downItem->item_id
    //             . ",L1=" . $downItem->title
    //             . ",L1=" . $downItem->quantity
    //             . "," . $downItem->ship_lastname . " " . $downItem->ship_firstname
    //             . ",L1=" . $downItem->unit_price
    //             . ",L1=" . $downItem->line_subtotal
    //             . "," . $downItem->total_price
    //             . "," . $downItem->ship_lastname_kana . " " . $downItem->ship_firstname_kana
    //             . "," . $downItem->ship_zipcode
    //             . "," . $downItem->ship_prefecture
    //             . "," . $downItem->ship_city
    //             . "," . $downItem->ship_address1
    //             . "," . $downItem->ship_address2
    //             . "," . $downItem->ship_phonenumber . "\n";
		
	// 	$filename = "Auto-Order";

	// 	header('Content-Type: application/csv');
	// 	header('Content-Disposition: attachment; filename="' . $filename . "_" . date("Y-m-d") . '.csv"');
	// 	echo $data;
	// 	exit();
	// }
}
