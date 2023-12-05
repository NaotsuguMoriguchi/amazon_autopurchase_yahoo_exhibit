@extends("layouts.main")

@section('css')
<link rel="stylesheet" href="{{ asset('assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" />
<link rel="stylesheet" href="{{asset('assets/css/datatables.css')}}">
<style>
	td {
		text-align: center !important;
		vertical-align: middle !important;
	}

	th {
		text-align: center !important;
		vertical-align: middle !important;
	}
</style>
@endsection

@section('content')
<!-- <h5>{{ $items }}</h5> -->
<div class="content-wrapper">
	<div class="container-xxl flex-grow-1 container-p-y">
		<div class="pagetitle">
			<nav>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="/">Yahoo</a></li>
					<li class="breadcrumb-item active">出品データリスト</li>
				</ol>
			</nav>
		</div><!-- End Page Title -->
		<div class="card">
			<div class="card-body" style="overflow: auto;">
				<table class="table table-bordered table-hover datatable">
					<thead>
						<tr>
							<th><input class="form-check-input" type="checkbox" id="check_all" onchange="check_all(event)"></th>
							<th>商品画像</th>
							<th style="width: 250px;">商品名</th>
							<th>ASIN</th>
							<th>JAN</th>
							<th>Amazon価格</th>
							<th>KeepaURL</th>
							<th>
								<span data-condition="all" data-id="{{ $user->id }}" data-bs-toggle="modal" data-bs-target="#confirmModal">
									<i class='bx bxs-trash text-danger'></i>
								</span>
								<span>
									<a href={{ route('csv', $user->id) }}><i class='bx bx-download text-primary'></i></a>
								</span>
							</th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
				<div class="row">
					<div style="text-align: center;">
						<button type="button" class="btn rounded-pill btn-outline-primary" data-bs-toggle="modal" data-bs-target="#exhibitModal">
							<span class="tf-icons bx bx-upload" style="padding-bottom: 3px;"></span>&nbsp; 出品
						</button>
					</div>
				</div>
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
						<h4>本当にデータを削除しますか?</h4>
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

<div class="modal fade" id="exhibitModal" tabindex="-1" aria-modal="true" role="dialog">
	<div class="modal-dialog modal-dialog-centered modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-12 mb-3 text-center">
						<!-- <h4><span id="selected_items"></span>つの商品が選択されました。</h4> -->
						<h4> 選択した商品を本当に出品しますか？</h4>
					</div>
				</div>
			</div>
			<div class="modal-footer" id="btns">			
				<button type="button" class="btn btn-primary" onclick="exhibit()" data-bs-dismiss="modal">確認</button>
				<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">キャンセル</button>
			</div>
		</div>
	</div>
</div>

@endsection

@section("script")
<script src="{{asset('assets/js/datatables.min.js')}}"></script>
<script>
	const checked = () => {
		var items = document.getElementsByClassName("check-item");
		console.log(items);

		var total = 0;
		// for (let i = 0; i < items.length; i++) {
		// 	let item = items[i];
		// 	console.log(item);
		// 	if (item.prop("checked")) {
		// 		total += 1;
		// 	}
		// }
		// console.log(total);
		// (total == 0) ? $('#check_all').prop( "checked", true ) : $('#check_all').prop( "checked", false );
	}

	const check_ext = (e) => {
		console.log(e.target.checked);
		var item_id = e.target.dataset.itemid;
		var exhibit = (e.target.checked) ? 1 : 0;

		$.ajax({
			url: "{{ route('check_item') }}",
			type: "post",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {
				item_id: item_id,
				exhibit: exhibit
			},
			success: function() {
				toastr.success(`正常に更新されました。`);
			}
		});
	}

	const check_all = (e) => {
		var exhibit_all = (e.target.checked) ? 1 : 0;

		if (exhibit_all == 1) {
			$('.check-item').prop("checked", true);
		} else {
			$('.check-item').prop("checked", false);
		}

		$.ajax({
			url: "{{ route('check_all_items') }}",
			type: "post",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {
				exhibit: exhibit_all
			},
			success: function() {
				toastr.success(`正常に更新されました。`);
			}
		});
	}

	var datatable = $('.datatable').DataTable({
		processing: true,
		serverSide: true,
		autoConfig: true,
		pageLength: 10,
		ajax: {
			'url': "{{ route('item_datatable') }}",
			'data': function(d) {
				d.userId = <?php echo $user->id; ?>;
			},
		},
		'fnInitComplete': function() {
			console.log('ok');
		},
		columns: [
			{
				data: 'exhibit',
				name: 'exhibit',
				sortable: false,
				orderable: false,
				render: function(data, type, row) {
					return (
						`<input class="form-check-input check-item" type="checkbox" onchange="check_ext(event)" data-itemid="${row.id}" ${row.exhibit == 1 ? 'checked' : ''}>`
					)
				}
			},
			{
				data: 'img_url',
				name: 'img_url',
				sortable: false,
				orderable: false,
				render: function(data, type, row) {
					return (
						`<img src=${row.img_url.split(',')[0]} style="width: 64px; height: 64px;" />`
					)
				}
			},
			{
				data: 'name',
				name: 'name'
			},
			{
				data: 'asin',
				name: 'asin',
			},
			{
				data: 'jan',
				name: 'jan',
			},
			{
				data: 'am_price',
				name: 'am_price',
				render: function(data, type, row) {
					return (
						`<a href="https://www.amazon.co.jp/dp/${row.asin}?tag=<?php echo $user->partner_tag; ?>&linkCode=ogi&th=1&psc=1" target="_blank"><span title="Amazon URL">${row.am_price == 0 ? '取得中' : '￥' + row.am_price}</span></a>`
					)
				}
			},
			{
				data: null,
				name: null,
				render: function(data, type, row) {
					return (
						`<a href='https://keepa.com/#!product/5-${row.asin}' target="_blank">
							<img style="width: 200px;" src='https://graph.keepa.com/pricehistory.png?asin=${row.asin}&domain=co.jp&salesrank=1' />
						</a>`
					)
				}
			},
			{
				data: null,
				name: 'id',
				sortable: false,
				orderable: false,
				render: function(data, type, row) {
					return (
						`<span class="text-danger" data-condition="one" data-id="${row.id}" data-bs-toggle="modal" data-bs-target="#confirmModal"><i class='bx bxs-trash'></i></span>`
					)
				}
			},
		]
	});

	function exhibit() {
		// $.ajax({
			// url: 'https://xs877048.xsrv.jp/fmproxy/api/v1/yahoo/stock_csv',
			// type: 'get',
			// data: {
			// 	user_id: "{{Auth::id()}}"
			// },
			// success: function(res) {
			// }
		// });
		var limit = <?php echo Auth::user()->limit; ?>
		
		let clientId = "{{Auth::user()->yahoo_id}}";
		location = 'https://auth.login.yahoo.co.jp/yconnect/v2/authorization?response_type=code&client_id=' + clientId + '&redirect_uri=https://xs877048.xsrv.jp/item/list&scope=openid';

	}

	$(document).ready(function() {
		// checked();
	});
</script>

@if(!empty($_REQUEST['code']))
<script>
	jQuery.ajax({
		url: "../fmproxy/api/v1/yahoo/authorization",
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		type: "post",
		data: {
			code: '{{$_REQUEST["code"]}}',
			index: '{{Auth::user()->id}}'
		},
		success: function(response) {

		},
		error: function(responseError) {

		},

	});
</script>
@endif

@endsection