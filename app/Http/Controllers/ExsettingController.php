<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Exsetting;
use App\Models\Item;

class ExsettingController extends Controller
{
    
	public function item_settings_exhibit() {
        $settings = Exsetting::where('user_id', Auth::id())->get();
		return view('items.item_exhibit', ['settings' => json_decode($settings)]);
	}

    public function save_amazon_setting(Request $request) {
        Exsetting::where("user_id", Auth::id())->update(['amazon_setting' => json_encode($request->all())]);
		return redirect()->route('item_settings_exhibit');
    }

    public function save_yahoo_setting(Request $request) {
        Exsetting::where("user_id", Auth::id())->update(['yahoo_setting' => json_encode($request->all())]);
		return redirect()->route('item_settings_exhibit');
    }

	public function item_settings_calculation() {
        $settings = Exsetting::where('user_id', Auth::id())->get();
		return view('items.item_calculation', ['settings' => $settings]);
	}

	public function save_price_settings(Request $request) {
        $price_settings = $request->price_settings;

        Exsetting::where("user_id", Auth::id())->update(['price_settings' => $price_settings]);
		return;
	}

	public function item_settings_exclusion() {
		$exsetting = Exsetting::where('user_id', Auth::id())->get();

		return view('items.item_exclusion', ['exsetting' => $exsetting]);
	}

	public function item_settings_commission(Request $request) {
		$exsetting = Exsetting::where('user_id', Auth::id())->update([
			'commission' => $request->commission
	  	]);
		return $exsetting;
	}

	public function item_settings_expenses(Request $request) {
		$exsetting = Exsetting::where('user_id', Auth::id())->update([
			'expenses' => $request->expenses
	  	]);
		return $exsetting;
	}

	public function set_column_user(Request $request) {
		$user_id = Auth::id();
		$user = User::find($user_id);
		$user[$request->col] = $request->content;
		$user->save();
		
		return;
	}

	public function set_column_exset(Request $request) {
		$user_id = Auth::id();
		$ex_setting = Exsetting::where('user_id', $user_id)->get();
		$ex_setting[0][$request->col] = $request->content;
		$ex_setting[0]->save();
		
		return;
	}

	public function save_userdata(Request $request)
	{
		$req = json_decode($request['exData']);
		$user_query = User::find($req->user_id);
		if ($user_query == null) {
			$user_query = new User;
		}

		$user_query->file_name = $req->file_name;
		$user_query->len = $req->len;
		$user_query->save();
	}
	
	public function save_limit(Request $request) {
		$user_id = $request->user_id;

		$user = User::find($user_id);
		$user->limit = $request['limit'];
		$user->save();
	}
}
