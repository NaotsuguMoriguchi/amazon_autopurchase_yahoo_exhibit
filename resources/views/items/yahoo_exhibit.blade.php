@extends("layouts.main")

@section('css')
<link rel='stylesheet' href='https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css'>
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css'>

<style>
	.text-nowrap {
		white-space: inherit !important;
	}

	.table > :not(caption) > * > * {
		padding: 0.5rem 0.75rem;
	}

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
		<div class="accordion" id="yahoo_exhibit_accordion">

			<div class="card accordion-item">
				<h2 class="accordion-header" id="headingOne">
					<button type="button" class="accordion-button btn-info collapsed" data-bs-toggle="collapse" data-bs-target="#yahoo_api" aria-expanded="false" aria-controls="yahoo_api">
						Yahoo API
					</button>
				</h2>
				<div id="yahoo_api" class="accordion-collapse collapse" data-bs-parent="#yahoo_exhibit_accordion">
					<div class="accordion-body">
						<div class="divider">
							<div class="divider-text">Yahoo-API 管理</div>
						</div>
						<div class="amazon-setting-group mt-4">

							<div class="row mb-3">
								<div class="form-group row mb-3">
									<label for="yahoo_id" class="col-md-2 col-form-label">Yahoo ID</label>
									<div class="col-md-10">
										<input type="text" class="form-control" data-keyid="{{ $yahoo_setting->id }}" id="yahoo_id" name="yahoo_id" value="{{ $yahoo_setting->yahoo_id }}" onchange="edit_yaSetting(event);" />
									</div>
								</div>

								<div class="form-group row mb-3">
									<label for="yahoo_secret" class="col-md-2 col-form-label">Yahoo シークレット</label>
									<div class="col-md-10">
										<input type="text" class="form-control" data-keyid="{{ $yahoo_setting->id }}" id="yahoo_secret" name="yahoo_secret" value="{{ $yahoo_setting->yahoo_secret }}" onchange="edit_yaSetting(event);" />
									</div>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>

			<div class="card accordion-item active">
				<h2 class="accordion-header" id="headingTwo">
					<button type="button" class="accordion-button btn-info" data-bs-toggle="collapse" data-bs-target="#amazon_product" aria-expanded="true" aria-controls="amazon_product">
						Amazon 商品
					</button>
				</h2>
				<div id="amazon_product" class="accordion-collapse collapse show" aria-labelledby="headingTwo" data-bs-parent="#yahoo_exhibit_accordion">
					<div class="accordion-body">
						<div class="amazon-item-group mt-4">

							<div class="table-responsive text-nowrap">
								<table id="example" class="table table-striped" style="width:100%">
									<thead>
										<tr>
											<th style="width: 80px;">商品画像</th>
											<th>商品名</th>
											<th style="width: 100px;">ASIN</th>
											<th style="width: 120px;">JAN</th>
											<th style="width: 100px;">Amazon価格</th>
										</tr>
									</thead>
									<tbody>
										@foreach($amazon_items as $item)
										<tr>
											<td>
												<a href="{{ $item->shop_url }}" target="_blank">
													<img style="width: 55px; height: 45px;" src="{{ $item->img_url }}" />
												</a>
											</td>
											<td> {{ $item->name }} </td>
											<td>{{ $item->asin }}</td>
											<td>{{ $item->jan }}</td>
											<td>{{ $item->am_price }}</td>
										</tr>
										@endforeach
									</tbody>
								</table>
							</div>

						</div>
					</div>
				</div>
			</div>

			<div class="card">
				<div class="card-footer text-center">
					<button type="button" id="register" class="btn btn-raised btn-primary waves-effect" onclick="exhibit();">
                        <i class="tf-icons bx bx-upload" style="padding-bottom: 2px"></i> 出 品 
                    </button>
				</div>
			</div>

		</div>
	</div>
</div>

@endsection

@section("script")
<script src='https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js'></script>
<script src='https://cdn.datatables.net/responsive/2.1.0/js/dataTables.responsive.min.js'></script>
<script src='https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js'></script>


<script>
	$(document).ready(function() {
		$('#example').DataTable({
			"columnDefs": [{
				"orderable": false,
				"targets": [1]
			}],
			language: {
				//customize pagination prev and next buttons: use arrows instead of words
				'paginate': {
					'previous': '<span class="fa fa-chevron-left"></span>',
					'next': '<span class="fa fa-chevron-right"></span>'
				},
				//customize number of elements to be displayed
				"lengthMenu": 'Display <select class="form-control input-sm">' +
					'<option value="10">10</option>' +
					'<option value="20">20</option>' +
					'<option value="50">50</option>' +
					'<option value="100">100</option>' +
					'<option value="500">500</option>' +
					'<option value="-1">All</option>' +
					'</select> results'
			}
		})
	});


	const edit_yaSetting = (e) => {
		var setting_id = e.target.dataset.keyid;

		$.ajax({
			url: "{{ route('edit_yaSetting') }}",
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

	const exhibit = () => {
		let postData = {
			user_id: '{{ $yahoo_store->user_id }}',
			store_id: '{{ $yahoo_store->id }}',
		}

		$.ajax({
			url: "/fmproxy/api/v1/yahoo/product_exhibit",
			type: "post",
			data: postData,
			success: function (res) {
				console.log(res);
				toastr.success('success');
			}
		});
		console.log('ajax sending.');
	}
</script>
@endsection