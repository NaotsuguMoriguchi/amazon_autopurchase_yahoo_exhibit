@extends("layouts.main")

@section('content')

<div class="content-wrapper">
	<div class="container-xxl flex-grow-1 container-p-y">
		
		<div class="pagetitle">
			<nav>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="/">Yahoo</a></li>
					<li class="breadcrumb-item active">新規出品</li>
				</ol>
			</nav>
		</div>

		<div class="col-xl-12">
			<div class="nav-align-top mb-4">
				<ul class="nav nav-pills mb-3 nav-fill" role="tablist">
					<li class="nav-item">
						<button
							type="button"
							class="nav-link active"
							role="tab"
							data-bs-toggle="tab"
							data-bs-target="#navs-pills-justified-amazon"
							aria-controls="navs-pills-justified-amazon"
							aria-selected="true"
						>
							<i class="tf-icons bx bx-dollar" style="padding-bottom: 2px"></i> Amazon
						</button>
					</li>
					<li class="nav-item">
						<button
							type="button"
							class="nav-link"
							role="tab"
							data-bs-toggle="tab"
							data-bs-target="#navs-pills-justified-yahoo"
							aria-controls="navs-pills-justified-yahoo"
							aria-selected="false"
						>
							<i class="tf-icons bx bx-yen" style="padding-bottom: 2px"></i> Yahoo
						</button>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade show active" id="navs-pills-justified-amazon" role="tabpanel">

						<div class="form-group row mb-3">
							<label for="access_key" class="col-md-2 col-form-label">アクセスキー</label>
							<div class="col-md-10">
								<input type="text" class="form-control" id="access_key" name="access_key" value="{{ $amazon_setting->access_key }}" onchange="setColumn(event, 'as');" />
							</div>
						</div>

						<div class="form-group row mb-3">
							<label for="secret_key" class="col-md-2 col-form-label">シークレットキー</label>
							<div class="col-md-10">
								<input type="text" class="form-control" id="secret_key" name="secret_key" value="{{ $amazon_setting->secret_key }}" onchange="setColumn(event, 'as');" />
							</div>
						</div>

						<div class="form-group row mb-3">
							<label for="partner_tag" class="col-md-2 col-form-label">パートナータグ</label>
							<div class="col-md-10">
								<input type="text" class="form-control" id="partner_tag" name="partner_tag" value="{{ $amazon_setting->partner_tag }}" onchange="setColumn(event, 'as');" />
							</div>
						</div>

					</div>
					<div class="tab-pane fade" id="navs-pills-justified-yahoo" role="tabpanel">

						<div class="form-group row mb-3">
							<label for="yahoo_id" class="col-md-2 col-form-label">ヤフーアプリID</label>
							<div class="col-md-10">
								<input type="text" class="form-control" id="yahoo_id" name="yahoo_id" value="{{ $yahoo_setting->yahoo_id }}" onchange="setColumn(event, 'ys');" />
							</div>
						</div>

						<div class="form-group row mb-3">
							<label for="yahoo_secret" class="col-md-2 col-form-label">ヤフーシークレット</label>
							<div class="col-md-10">
								<input type="text" class="form-control" id="yahoo_secret" name="yahoo_secret" value="{{ $yahoo_setting->yahoo_secret }}" onchange="setColumn(event, 'ys');" />
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>

		<div class="card p-4">
			<form class="form-horizontal">
				<div class="card-body" style="padding:0px">

					<div class="form-group row mb-3">
						<label for="csv_load" class="col-md-2 col-form-label">CSV 選択</label>
						<div class="col-md-10">
							<input type="file" class="form-control" id="csv_load" name="csv_load">
						</div>
					</div>

					<div class="col-lg-12 mt-4" id="register-status" style="display: block;">
						<div class="row">
							<div class="col text-center">
								<span id="progress-num">0</span> 件 / <span id="total-num">0</span> 件
							</div>
							<div class="col text-center">
								<span id="round">0</span> 回目
							</div>
						</div>
						<div class="row mt-4">
							<div class="progress col-12 p-0" id="count">
								<div class="progress-bar progress-bar-animated bg-primary progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;" id="progress">
									<span id="percent-num">0%</span>
								</div>
							</div>
						</div>
					</div>

					<div class="col-lg-12 mt-4" id="track-status" style="display: none;">
						<div class="row">
							<div class="col text-center">
								<span id="progress-num1">0</span> 件 / <span id="total-num1">0</span> 件
							</div>
							<div class="col text-center">
								<span id="round1">0</span> 回目
							</div>
						</div>
						<div class="row mt-4">
							<div class="progress col-12 p-0" id="count1">
								<div class="progress-bar progress-bar-animated bg-info progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;" id="progress1">
									<span id="percent-num1">0%</span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="card-footer text-center">
					<button type="button" id="register" class="btn btn-raised btn-primary waves-effect" onclick="register1()">
                        <i class="tf-icons bx bx-upload" style="padding-bottom: 2px"></i> 登 録 
                    </button>
				</div>
			</form>
		</div>

	</div>
</div>
@endsection


@section('script')
<script>
	const setColumn = (e, set) => {
		console.log(set);
		$.ajax({
			url: "{{ route('set_column') }}",
			type: "post",
			data:{
				col: e.target.name,
				content: e.target.value,
				setting: set,
			},
			success: function () {
				toastr.success('正常に更新されました。');
			}
		});
	};

	
	var newCsvResult, csvFile;

	$('#csv_load').on('change', function(e) {
		result = e.target.id;
		// clearInterval(scanInterval);

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
							newCsvResult.push(code[0]);
						}
					} else {
						newCsvResult.push(code[1]);
					}
				}
				
				if (newCsvResult[0] == 'ASIN') { newCsvResult.shift(); }

				// $('#csv-name').html(csvFile.name);
				$('#total-num').html(newCsvResult.length);
			}
			reader.readAsText(csvFile);
		}
	});

	const register1 = async () => {
		if (csvFile === undefined) {
			toastr.error('CSVファイルを選択してください。');
			return;
		}

		let postData = {
			file_name: csvFile.name,
			len: newCsvResult.length,
		};

		console.log(postData);

		// first save user exhibition setting
		await $.ajax({
			url:  "{{ route('save_registered_item') }}",
			type: 'post',
			data: {
				exData: JSON.stringify(postData)
			},
			success: function () {
				console.log('OK');
				// scanInterval = setInterval(scan, 5000);
				toastr.info('商品登録を開始します。');

				$('#register-status').css('display', 'block');
				$('#track-status').css('display', 'none');

				$('#csv_load').attr('disabled', true);
				$('#register').attr('disabled', true);
			}
		});

		// then start registering products with ASIN code
		postData = {
			user_id: '{{ Auth::id() }}',
			codes: newCsvResult
		};

		$.ajax({
			url: "{{ env('NODE_URL') }}/api/v1/amazon/get_info",
			type: "post",
			data: {
				asin: JSON.stringify(postData)
			},
		});
	};
</script>
@endsection
