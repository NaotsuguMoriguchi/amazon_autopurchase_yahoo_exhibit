@extends('layouts.main')

@section('content')

<div class="content-wrapper">
	<div class="container-xxl flex-grow-1 container-p-y">
		<div class="pagetitle">
			<nav>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="/">Yahoo</a></li>
					<li class="breadcrumb-item">設定</li>
					<li class="breadcrumb-item active">出品設定</li>
				</ol>
			</nav>
		</div>

		<div class="col-xl-12">
			<div class="nav-align-top mb-4">
				<ul class="nav nav-pills mb-3" role="tablist">
					<!-- <li class="nav-item">
						<button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#amazon_tab" aria-controls="amazon_tab" aria-selected="true">
							(Amazon JP/US)取得設定
						</button>
					</li> -->

					<li class="nav-item">
						<button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#yahoo_tab" aria-controls="yahoo_tab" aria-selected="false">
							出品設定
						</button>
					</li>
				</ul>

				<div class="tab-content">
					<div class="tab-pane fade" id="amazon_tab" role="tabpanel">
						<form id="amazon_settingForm" action={{ route('save_amazon_setting') }} method="post">
							@csrf
							<?php $a = json_decode($settings[0]->amazon_setting); ?>

							<div class="row mb-2">
								<h5> ・(Amazon JP/US)価格取得条件</h5>
							</div>

							<div class="row mb-2">
								<div class="col-sm-3">
									<label for="second-goods">中古品</label>
								</div>
								<div class="col-sm-2">
									<div>
										<input type="checkbox" id="second_goods" name="second_goods" @if ($a && isset($a->second_goods)) checked @endif>
									</div>
								</div>
								<div class="col-sm-1"></div>

								<div class="col-sm-3">
									<label for="select_ic">Item Condition</label>
								</div>
								<div class="col-sm-2">
									<select class="form-control" id="select_ic" name="select_ic">
										<option value="-1"></option>
										<option value="excellent" @if ($a && $a->select_ic == 'excellent') selected @endif>非常に良い</option>
										<option value="ok" @if ($a && $a->select_ic == 'ok') selected @endif>良い</option>
										<option value="good" @if ($a && $a->select_ic == 'good') selected @endif>可</option>
									</select>
								</div>
							</div>

							<div class="row mb-2">
								<div class="col-sm-3">
									<label for="select_fc">Fulfillment Channel</label>
								</div>
								<div class="col-sm-2">
									<select class="form-control" id="select_fc" name="select_fc">
										<option value="all" @if ($a && $a->select_fc == 'all') selected @endif>All</option>
										<option value="merhant" @if ($a && $a->select_fc == 'merhant') selected @endif>Merhant</option>
										<option value="fba" @if ($a && $a->select_fc == 'fba') selected @endif>FBA</option>
										<option value="fba-first" @if ($a && $a->select_fc == 'fba-first') selected @endif>FBA優先</option>
									</select>
								</div>
								<div class="col-sm-1"></div>

								<div class="col-sm-3">
									<label for="select_st">Shipping Time</label>
								</div>
								<div class="col-sm-2">
									<select class="form-control" id="select_st" name="select_st">
										<option value="all" @if ($a && $a->select_st == 'all') selected @endif>All</option>
										<option value="two" @if ($a && $a->select_st == 'two') selected @endif>2日以内</option>
										<option value="three-seven" @if ($a && $a->select_st == 'three-seven') selected @endif>3-7日</option>
										<option value="thirteen" @if ($a && $a->select_st == 'thirteen') selected @endif>13日以上</option>
									</select>
								</div>
							</div>

							<div class="row mb-2">
								<div class="col-sm-3">
									<label for="seller">Seller Feedback</label>
								</div>
								<div class="col-sm-2">
									<input class="form-control" type="number" min="1" max="5" id="seller" name="seller" @if ($a) value="{{ $a->seller }}" @endif />
								</div>
								<div class="col-sm-1"></div>

								<div class="col-sm-3">
									<label for="ration">高評価率</label>
								</div>
								<div class="col-sm-2">
									<input class="form-control" type="number" min="0" max="100" id="ration" name="ration" @if ($a) value="{{ $a->ration }}" @endif />
								</div>
							</div>

							<div class="row mb-2">
								<h5> ・アマゾン取得条件設定</h5>
							</div>

							<div class="row mb-2">
								<div class="col-sm-3">
									<label for="del_seller">セラー削除</label>
								</div>
								<div class="col-sm-2">
									<input class="form-control" type="number" id="del_seller" name="del_seller" @if ($a) value="{{ $a->del_seller }}" @endif />
								</div>
							</div>

							<div class="row mb-2">
								<div class="col-sm-3">
									<label for="low_p_bound">販売価格下限（円）</label>
								</div>
								<div class="col-sm-2">
									<input class="form-control" type="number" id="low_p_bound" name="low_p_bound" @if ($a) value="{{ $a->low_p_bound }}" @endif />
								</div>
								<div class="col-sm-1"></div>

								<div class="col-sm-3">
									<label for="up_p_bound">販売価格上限（円）</label>
								</div>
								<div class="col-sm-2">
									<input class="form-control" type="number" id="up_p_bound" name="up_p_bound" @if ($a) value="{{ $a->up_p_bound }}" @endif />
								</div>
							</div>

							<div class="row mb-2">
								<div class="col-sm-3">
									<label for="up_w_bound">出品商品重量上限（ｇ）</label>
								</div>
								<div class="col-sm-2">
									<input class="form-control" type="number" id="up_w_bound" name="up_w_bound" @if ($a) value="{{ $a->up_w_bound }}" @endif />
								</div>
								<div class="col-sm-1"></div>

								<div class="col-sm-3">
									<label for="size_info">サイズ情報取得不可の場合除外する</label>
								</div>
								<div class="col-sm-2">
									<div>
										<input type="checkbox" id="size_info" name="size_info" @if ($a && isset($a->size_info)) checked @endif />
									</div>
								</div>
							</div>

							<div class="row mb-2">
								<div class="col-sm-3">
									<label for="up_3l_bound">出品商品3辺の長さ上限（cm）</label>
								</div>
								<div class="col-sm-2">
									<input class="form-control" type="number" id="up_3l_bound" name="up_3l_bound" @if ($a) value="{{ $a->up_3l_bound }}" @endif />
								</div>
								<div class="col-sm-1"></div>

								<div class="col-sm-3">
									<label for="up_l_bound">出品商品長辺の長さ上限（cm）</label>
								</div>
								<div class="col-sm-2">
									<input class="form-control" type="number" id="up_l_bound" name="up_l_bound" @if ($a) value="{{ $a->up_l_bound }}" @endif />
								</div>
							</div>

							<div class="row mb-2">
								<div class="col-sm-3">
									<label for="ranking">ASINランキング（以内）</label>
								</div>
								<div class="col-sm-2">
									<input class="form-control" type="number" id="ranking" name="ranking" @if ($a) value="{{ $a->ranking }}" @endif />
								</div>
							</div>

							<div class="card-footer" style="text-align: center;">
								<button type="submit" class="btn btn-primary">保存</button>
								<!-- <button type="button" class="btn btn-info" onclick="save_amazonsetting()">保存</button> -->
							</div>
						</form>
					</div>

					<div class="tab-pane fade show active" id="yahoo_tab" role="tabpanel">
						<form id="yahoo_settingForm" action={{ route('save_yahoo_setting') }} method="post">
							@csrf
							<?php $y = json_decode($settings[0]->yahoo_setting); ?>

							<div class="row mb-2">
								<div class="col-sm-3">
									<label for="product_code">商品コード命名(半角英数字)</label>
								</div>
								<div class="col-sm-2">
									<input class="form-control" type="text" name="product_code" id="product_code" @if ($y && isset($y->product_code)) value="{{ $y->product_code }}" @endif />
								</div>
								<div class="col-sm-1"></div>

								<div class="col-sm-3">
									<label for="stock_number">在庫数</label>
								</div>

								<div class="col-sm-3">
									<input class="form-control" type="number" name="stock_number" id="stock_number" @if ($y && isset($y->stock_number)) value="{{ $y->stock_number }}" @endif />
								</div>
							</div>

							<div class="row mb-2">
								<div class="col-sm-3">
									<label for="date_info">発送日情報管理番号</label>
								</div>
								<div class="col-sm-2">
									<input class="form-control" type="text" name="date_info" id="date_info" @if ($y) value="{{ $y->date_info }}" @endif />
								</div>
								<div class="col-sm-1"></div>

								<div class="col-sm-3">
									<label for="delivery">デリバリー </label>
								</div>
								<div class="col-sm-3">
									<select class="form-control" id="delivery" name="delivery">
										<option value="0" @if ($y && $y->delivery == 0) selected @endif>なし（送料がかかる場合）</option>
										<option value="1" @if ($y && $y->delivery == 1) selected @endif>無料</option>
										<option value="3" @if ($y && $y->delivery == 3) selected @endif>条件付送料無料</option>
									</select>
								</div>
							</div>

							<div class="row mb-2">
								<div class="col-sm-3">
									<label for="date_info_out">発送日情報管理番号(在庫切れ時)</label>
								</div>
								<div class="col-sm-2">
									<input class="form-control" type="text" name="date_info_out" id="date_info_out" @if ($y) value="{{ $y->date_info_out }}" @endif />
								</div>
								<div class="col-sm-1"></div>

								<div class="col-sm-3">
									<label for="deli_group">配送グループ(半角数字のみ)</label>
								</div>
								<div class="col-sm-3">
									<input class="form-control" type="number" name="deli_group" id="deli_group" @if ($y) value="{{ $y->deli_group }}" @endif />
								</div>
							</div>

							{{-- <div class="row mb-2">
								<div class="col-sm-3">
									<label for="smartphone">SP-ADDITIONAL ※スマホ用説明欄</label>
								</div>
								<div class="col-sm-6">
									<textarea class="form-control" rows="5" id="smartphone" name="smartphone"> @if ($y && isset($y->smartphone)) {{ $y->smartphone }} @endif </textarea>
								</div>
							</div>

							<div class="row mb-2">
								<div class="col-sm-3">
									<label for="option">オプション付加文字</label>
								</div>
								<div class="col-sm-6">
									<textarea class="form-control" rows="5" id="option" name="option"> @if ($y && isset($y->option)) {{ $y->option }} @endif </textarea>
								</div>
							</div> --}}

							<div class="row mb-2">
								<div class="col-sm-3">
									<label for="category">ストアカテゴリ</label>
								</div>
								<div class="col-sm-6">
									<input class="form-control" type="number" id="category" name="category" @if ($y && isset($y->category)) value={{ $y->category }} @endif >
								</div>
							</div>

							<div class="card-footer" style="text-align: center;">
								<button type="submit" class="btn btn-primary" onclick="save_yahoosetting()">保存</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section("script")

<script>
	// const save_amazonsetting = () => {
	// 	let form_data = $('#amazon_settingForm').serializeJSON();
	// 	data = JSON.stringify(form_data);
	// 	console.log(data);
	// 	$.ajax({
	// 		url: '{{ route("save_amazon_setting") }}',
	// 		type: 'post',
	// 		headers: {
	// 			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	// 		},
	// 		data: {
	// 			amazon_setting: data
	// 		},
	// 		success: function(response) {
	// 			console.log(response);
	// 		}
	// 	});
	// }

	// const save_yahoosetting = () => {
	// 	let form_data = $('#yahoo_settingForm').serializeJSON();
	// 	data = JSON.stringify(form_data);
	// 	console.log(data);
	// 	$.ajax({
	// 		url: '{{ route("save_yahoo_setting") }}',
	// 		type: 'post',
	// 		headers: {
	// 			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	// 		},
	// 		data: {
	// 			yahoo_setting: data
	// 		},
	// 		success: function(response) {
	// 			console.log(response);
	// 		}
	// 	});
	// }
</script>

@endsection