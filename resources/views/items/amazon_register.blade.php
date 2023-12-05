@extends("layouts.main")

@section('css')
<link rel='stylesheet' href='https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css'>
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css'>

<style>
	td {
		text-align: center !important;
		vertical-align: middle !important;
	}

	th {
		text-align: center !important;
		vertical-align: middle !important;
	}

	.input-margin {
		margin: 0.25rem auto;
	}
</style>
@endsection

@php
	$user = Auth::user();
@endphp

@section('content')
<div class="content-wrapper">
	<div class="container-xxl flex-grow-1 container-p-y">
		<div class="pagetitle">
			<nav>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="/">ストア</a></li>
					<li class="breadcrumb-item active">{{ $yahoo_store->store_name }}</li>
				</ol>
			</nav>
		</div>
		<div class="card">
			<div class="card-body">
				<div class="divider">
					<div class="divider-text">PA-API 管理</div>
				</div>
				<div class="amazon-setting-group mt-2">
					<div class="row mb-3">
						<div class="col-md-10"></div>
						<div class="col-md-2 text-end">
							<button
								type="button"
								class="btn rounded-pill btn-primary"
								data-bs-toggle="modal"
								data-bs-target="#addModal"
							>
								+ 追加
							</button>
						</div>
					</div>
					@if(count($amazon_setting) > 0)
						@foreach($amazon_setting as $setting)
						<div class="row mb-3">
							<label for="" class="col-form-label input-margin" style="width: 3%; margin-right: 0.25rem;">{{ $loop->iteration }}</label>
							<div class="row" style="width: 95%;">
								<div class="col-md-4 row" style="margin-left: 0.25rem;">
									<label for="" class="col-md-5 col-form-label input-margin">パートナータグ</label>
									<div class="col-md-7">
										<input class="form-control" name="partner_tag" data-keyid="{{ $setting->id }}" type="text" value="{{ $setting->partner_tag }}" onchange="edit_amSetting(event);">
									</div>
								</div>

								<div class="col-md-4 row" style="margin-left: 0.25rem;">
									<label for="" class="col-md-4 col-form-label input-margin">アクセスキー</label>
									<div class="col-md-8">
										<input class="form-control" name="access_key" data-keyid="{{ $setting->id }}" type="text" value="{{ $setting->access_key }}" onchange="edit_amSetting(event);">
									</div>
								</div>

								<div class="col-md-4 row" style="margin-left: 0.25rem;">
									<label for="" class="col-md-5 col-form-label input-margin">シークレットキー</label>
									<div class="col-md-7">
										<input class="form-control" name="secret_key" data-keyid="{{ $setting->id }}" type="text" value="{{ $setting->secret_key }}" onchange="edit_amSetting(event);">
									</div>
								</div>
							</div>
							<button
								type="button"
								class="btn btn-sm rounded-pill btn-icon btn-outline-primary input-margin"
								style="margin-left: -0.75rem;"
								data-id="{{ $setting->id }}"
								data-bs-toggle="modal"
								data-bs-target="#confirmModal"
							>
								<span class="tf-icons bx bx-trash bx-tada-hover bx-xs"></span>
							</button>
						</div>
						@endforeach
					@else
						<h5 class="text-center">登録されたPA-APIはありません。</h5>
					@endif
				</div>
				<div class="divider">
					<div class="divider-text">CSV 登録</div>
				</div>
				
				<div class="card-body" style="padding:0px">
					<div class="form-group row mb-3">
						<div class="col-md-1"></div>
						<label for="csv_load" class="col-md-1 col-form-label">CSV 選択</label>
						<div class="col-md-9">
							<input type="file" class="form-control" id="csv_load" name="csv_load">
						</div>
						<div class="col-md-1"></div>
					</div>

					<div id="register-status" class="col-lg-12 mt-4" style="display: block;">
						<div class="row">
							<div class="col text-center">
								<span id="progress-num">0</span> 件 / <span id="total-num">0</span> 件
							</div>
							<div class="col text-center">
								総数 : <span id="total-count">{{ App\Models\User::find(Auth::id())->limit_item }}</span> 件 / 登録商品数 : <span id="registered-count">{{ App\Models\AmazonItem::where('store_id', $yahoo_store->id)->count() }}</span> 件
							</div>
						</div>
						<div class="row mt-4">
							<div id="count" class="progress col-12 p-0">
								<div id="progress" style="width: 0%;" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="progress-bar progress-bar-animated bg-primary progress-bar-striped">
									<span id="percent-num">0%</span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="card-footer text-center">
					<button type="button" id="register" class="btn btn-raised btn-primary waves-effect" onclick="csv_register();">
						<i class="tf-icons bx bx-cloud-upload" style="padding-bottom: 2px"></i> 登 録 
					</button>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="addModal">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header bg-primary">
				<h4 class="modal-title text-white">PA-API 追加</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>

			<!-- Modal body -->
			<div class="modal-body m-4">
				<div class="row mt-2">
					<div class="col-4">
						<strong>パートナータグ</strong>
					</div>
					<div class="col-8">
						<input class="form-control" type="text" id="partner_tag" name="partner_tag" value="" required />
					</div>
				</div>	

				<div class="row mt-2">
					<div class="col-4">
						<strong>アクセスキー</strong>
					</div>
					<div class="col-8">
						<input class="form-control" type="text" id="access_key" name="access_key" value="" required />
					</div>
				</div>
				
				<div class="row mt-2">
					<div class="col-4">
						<strong>シークレット キー</strong>
					</div>
					<div class="col-8">
						<input class="form-control" type="text" id="secret_key" name="secret_key" value="" required />
					</div>
				</div>

				<div class="row mt-3">
					<h6 class="text-danger">*最初に登録するAPIは商品登録専用APIになります。</h6>
				</div>
			</div>
		
			<!-- Modal footer -->
			<div class="modal-footer" id="button-container">
				<button type="button" class="btn btn-primary" onclick="add_amSetting()">追加</button>
				<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">キャンセル</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-modal="true" role="dialog">
	<div class="modal-dialog modal-dialog-centered modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-12 mb-3 text-center">
						<h4>本当にデータを削除しますか？</h4>
					</div>
				</div>
			</div>
			<div class="modal-footer" id="btns">
				<!-- <button type="button" class="btn btn-primary">削除</button>
				<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">キャンセル</button> -->
			</div>
		</div>
	</div>
</div>

@endsection

@section("script")
<script>
	$('#confirmModal').on('shown.bs.modal', function(e) {
		var target = e.relatedTarget.dataset;
		$('#btns').html(
			`<button type="button" class="btn btn-primary" onclick="delete_amSetting(${target.id})">削除</button>
			<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">キャンセル</button>`
		);
	});


	var newCsvResult, csvFile;

	$('#csv_load').on('change', function(e) {

		result = e.target.id;
		clearInterval(scanInterval);

		csvFile = e.target.files[0];
		newCsvResult = [];

		$('#progress-num').html('0');
		$('#percent-num').html('0%');
		$('#progress').attr('aria-valuenow', 0);
		$('#progress').css('width', '0%');

		var ext = $('#csv_load').val().split(".").pop().toLowerCase();
		if ($.inArray(ext, ["csv", "xlsx"]) === -1) {
			toastr.error('CSV、XLSXファイルを選択してください。');
			return false;
		}
		
		if (csvFile !== undefined) {
			reader = new FileReader();
			reader.onload = function (e) {
				$('#count').css('visibility', 'visible');
				csvResult = e.target.result.split(/\n/);

				for (const i of csvResult) {
					let code = i.split('\r');
					code = i.split('"');

					if (code.length == 1) {
						code = i.split('\r');
						if (code[0] != '') {
							if (isValidASIN(code[0])) {
								newCsvResult.push(code[0]);
							}
						}
					} else {
						if (isValidASIN(code[1])) {
							newCsvResult.push(code[1]);
						}
					}
				}

				if (newCsvResult[0] == 'ASIN') { newCsvResult.shift(); }
				
				// to prevent csv list from being duplicated
				newCsvResult = [...new Set(newCsvResult)];

				$('#total-num').html(newCsvResult.length);
			}
			reader.readAsText(csvFile);
		}
	});

	function isValidASIN(asin) {
		const asinRegex = /^[A-Z0-9]{10}$/;
		return asinRegex.test(asin);
	}

	const csv_register = async () => {
		if (csvFile === undefined) {
			toastr.error('CSVファイルを選択してください。');
			return;
		}

		$.ajax({
			url:  "{{ route('save_register_history') }}",
			type: 'post',
			data: {
				store_id: '{{ $yahoo_store->id }}',
				file_name: csvFile.name,
				len: newCsvResult.length,
			},
			success: function () {
				toastr.info('商品登録を開始します。');

				setInterval(() => {
					scan();
				}, 5 * 1000);
				$('#register-status').css('display', 'block');

				$('#csv_load').attr('disabled', true);
				$('#register').attr('disabled', true);
			}
		});

		// then start registering products with ASIN code
		let postData = {
			user_id: '{{ $user->id }}',
			store_id: '{{ $yahoo_store->id }}',
			codes: newCsvResult
		};

		$.ajax({
			url: "/fmproxy/api/v1/amazon/product_register",
			type: "post",
			data: {
				registerData: JSON.stringify(postData)
			},
			success: function (res) {
				console.log(res);
				toastr.success('正常に登録されました。');
			}
		});

	};


	const add_amSetting = () => {
		$.ajax({
			url: "{{ route('add_amSetting') }}",
			type: "post",
			data: {
				store_id: '{{ $yahoo_store->id }}',
				partner_tag: $('#partner_tag').val(),
				access_key: $('#access_key').val(),
				secret_key: $('#secret_key').val(),
			},
			success: function (res) {
				console.log(res);
				location.reload();
				toastr.success('正常に追加されました。');
			}
		});
	}

	const edit_amSetting = (e) => {
		var setting_id = e.target.dataset.keyid;

		$.ajax({
			url: "{{ route('edit_amSetting') }}",
			type: "post",
			data:{
				id: setting_id,
				col: e.target.name,
				content: e.target.value,
			},
			success: function () {
				toastr.success(`正常に更新されました。`);
			}
		});
	}

	const delete_amSetting = (id) => {
		console.log(id);

		$.ajax({
			url: "{{ route('delete_amSetting') }}",
			type: "post",
			data: {
				id: id
			},
			success: function (res) {
				console.log(res);
				location.reload();
				toastr.success('正常に削除されました。');
			}
		});
	}

	var scanInterval = setInterval(() => {
		scan();
	}, 5 * 1000);

	$(document).ready(function () {
		$.ajax({
			url: '{{ route("progress") }}',
			type: 'get',
			success: function(response) {
				$('#total-num').html(response.registered_item);
				$('#progress-num').html(response.progress);
				var percent = Math.floor(response.progress / response.registered_item * 100);
				$('#percent-num').html(percent + '%');
				$('#progress').attr('aria-valuenow', percent);
				$('#progress').css('width', percent + '%');
			}
		});
	});

	function scan() {
		$.ajax({
			url: "{{ route('progress') }}",
			type: "get",
			success: function(response) {
				$('#total-num').html(response.registered_item);
				$('#progress-num').html(response.progress);
				var percent = Math.floor(response.progress / response.registered_item * 100);
				$('#percent-num').html(percent + '%');
				$('#progress').attr('aria-valuenow', percent);
				$('#progress').css('width', percent + '%');

				if (percent == 100) {
					toastr.success('正常に登録されました。');
				}
			}
		});
	}
		
</script>
@endsection