@extends("layouts.main")

@section('content')
<div class="content-wrapper">	
	<div class="container-xxl flex-grow-1 container-p-y">
		<main id="main" class="main">
			<div class="pagetitle">
				<nav>
					<ol class="breadcrumb">
						<li class="breadcrumb-item active">ダッシュボード</li>
					</ol>
				</nav>
			</div>
			<section class="section dashboard">
				<div class="row">
					<div class="col-lg-12">
						<div class="row">
							<div class="col-xxl-4 col-md-4">
								<div class="card info-card sales-card">
									<div class="card-body">
										<h4 class="card-title">登録されたストア </h4>
										<div class="d-flex align-items-center">
											<div class="d-flex align-items-center justify-content-center">
												<span><i class="bx bxs-home text-primary pe-2 pb-1"></i></span>
												{{ count($yahoo_stores) }}
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="row mt-5">
							<h6>Yahooストアの注文更新</h6>
							@foreach($yahoo_stores as $store)
								@php
									$created_refresh_token = App\Models\YahooSetting::where('store_id', $store->id)->pluck('created_refresh_token')->first();
									$today = new DateTime();
									$today->setTimezone(new DateTimeZone('Asia/Tokyo'));
									$createdDate = new DateTime($created_refresh_token);
									$createdDate->setTimezone(new DateTimeZone('Asia/Tokyo'));
									$createdDate->modify('-9 hours');
									$interval = $today->diff($createdDate);
									$diffInHours = $interval->h + ($interval->days * 24);

									$store_yahoo_id = App\Models\YahooSetting::where('store_id', $store->id)->pluck('yahoo_id')->first();
								@endphp
								<div class="col-xxl-4 col-md-4">
									<div class="card info-card revenue-card">
										<div class="card-body">
											<h4 class="card-title">{{ $store->store_name }}</h4>
											<div class="row align-items-center">
												<div class="col-md-6 align-items-center justify-content-center">
													<span><i class='bx bxs-cart-alt text-primary pe-2 pb-1'></i></span>
													{{ $store->order_count }}
												</div>
												<div class="col-md-6">
													<button type="button" class="btn rounded-pill btn-outline-info" onclick="update_orderCount('{{ $store->id }}', '{{ $diffInHours }}', '{{ $store_yahoo_id }}')">
														<span class="tf-icons bx bx-refresh"></span>&nbsp; 更新
													</button>
												</div>
											</div>
										</div>
									</div>
								</div>
							@endforeach
						</div>
					</div>
				</div>
			</section>

		</main>
	</div>
</div>
@endsection

@push('scripts')


@section('script')

<script>
	const get_code = (yahoo_id) => {
		let clientId = yahoo_id;
		var get_code_url = `https://auth.login.yahoo.co.jp/yconnect/v2/authorization?response_type=code&client_id=${clientId}&redirect_uri=https://xs767540.xsrv.jp/&scope=openid`;
		window.location = get_code_url;
	}

	const get_orderCount = (store_id, authorization_type, code) => {
		sessionStorage.clear();
		$.ajax({
			url: "/fmproxy/api/v1/yahoo/get_order",
			type: "post",
			data: {
				user_id: '{{ Auth::user()->id }}',
				store_id: store_id,
				authorization: authorization_type,
				code: code,
			},
			success: function(response) {
				console.log('success');
				toastr.success('success');
				setTimeout(() => {
					window.location = '/';
				}, 3000);
			},
			error: function(responseError) {
				console.log('error');
			},
		});
	}


	const update_orderCount = (store_id, exp_hours, yahoo_id) => {
		sessionStorage.setItem('store_id', store_id);
		let expiration_hours = parseInt(exp_hours);

		if (expiration_hours < 12) {
			toastr.success(`Access token expires in ${12 - expiration_hours} hours.`);
			let code = 'No';
			let authorization_type = 're';

			get_orderCount(store_id, authorization_type, code);

		} else {
			toastr.warning('Access token is expired.');
			setTimeout(() => {
				get_code(yahoo_id);
			}, 3000);

		}
	}
</script>


@if(!empty($_REQUEST['code']))
<script>
	let code = '{{ $_REQUEST["code"] }}';
	let authorization_type = 'new';
	let store_id = sessionStorage.getItem('store_id');
	get_orderCount(store_id, authorization_type, code);
</script>
@endif

@endsection
