@extends("layouts.main")

@section('content')
<style>
	.pt-7 {
		padding-top: 7px;
	}
	.txt-a-e {
		text-align: end;
	}
</style>
<div class="content-wrapper">
	<div class="container-xxl flex-grow-1 container-p-y">
		
		<div class="pagetitle">
			<nav>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="/">Yahoo</a></li>
					<li class="breadcrumb-item">設定</li>
					<li class="breadcrumb-item active">利益計算設定</li>
				</ol>
			</nav>
		</div>

		<div class="card p-4">
			<form class="form-horizontal">
				<div class="card-body" style="padding:0px">
					<h4>・利益設定-経費手数料</h4>
					<div class="pb-3 row">

						<div class="col-md-4 row">
							<label for="html5-text-input" class="col-md-4 col-form-label txt-a-e">手数料(%)</label>
							<div class="col-md-8">
								<input class="form-control" type="number" name="commission" value="{{ $settings[0]->commission }}" onchange="save_com(event);" />
							</div>
						</div>
						<!-- <div class="col-md-4 row">
							<label for="html5-text-input" class="col-md-4 col-form-label txt-a-e">諸経費(円)</label>
							<div class="col-md-8">
								<input class="form-control" type="number" name="expenses" value="{{ $settings[0]->expenses }}" onchange="save_exp(event);" />
							</div>
						</div> -->
                        
					</div>
					
					<h4 style="padding-top: 2rem;">・利益設定-利益率または利益額</h4>
					<div class="table-responsive text-nowrap" style="text-align: center;">
						<table class="table table-sm table-bordered">
							<thead>
								<tr>
									<th>Amazon販売価格(from)</th>
									<th>Amazon販売価格(to)</th>
									<th>利益率</th>
									<th>プラス金額(円)</th>
									<th>マイナス金額(円)</th>
									<th>利益額(円)</th>
									<th><span class="text-primary" onclick="add_priceRange()"><i class='bx bxs-plus-circle'></i></span></th>
								</tr>
							</thead>
							<tbody id="tbody" class="table-border-bottom-0">
								@foreach(json_decode($settings[0]->price_settings) as $s)
								<tr data-row="{{$loop->index}}" class="table_tr">
									<td class="table_td" onclick="this.contentEditable=true;" onblur="save_td(event)">{{$s->start_price??0}}</td>
									<td class="table_td" onclick="this.contentEditable=true;" onblur="save_td(event)">{{$s->end_price??0}}</td>
									<td class="table_td" onclick="this.contentEditable=true;" onblur="save_td(event)">{{$s->profit_rate??0}}</td>
									<td class="table_td" onclick="this.contentEditable=true;" onblur="save_td(event)">{{$s->plus_amount??0}}</td>
									<td class="table_td" onclick="this.contentEditable=true;" onblur="save_td(event)">{{$s->minus_amount??0}}</td>
									<td class="table_td" onclick="this.contentEditable=true;" onblur="save_td(event)">{{$s->profit_amount??0}}</td>
									<td>
										<div>
											<span class="text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-no="{{$loop->index}}" ><i class='bx bxs-trash'></i></span>
										</div>
									</td>
								</tr>
								@endforeach
								<!-- <tr class="table_tr">
									<td class="table_td" onclick="this.contentEditable=true;" onblur="save_td(event)">A</td>
									<td class="table_td" onclick="this.contentEditable=true;" onblur="save_td(event)">B</td>
									<td class="table_td" onclick="this.contentEditable=true;" onblur="save_td(event)">C</td>
									<td class="table_td" onclick="this.contentEditable=true;" onblur="save_td(event)">D</td>
									<td class="table_td" onclick="this.contentEditable=true;" onblur="save_td(event)">E</td>
									<td>
										<div>
											<span class="text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class='bx bxs-trash'></i></span>
										</div>
									</td>
								</tr> -->
							</tbody>
						</table>
					</div>
				</div>
			</form>
		</div>

	</div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-modal="true" role="dialog">
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
			<div class="modal-footer" id="delete_btns">
				<!-- <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="delete_priceRange()">削除</button>
				<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">キャンセル</button> -->
			</div>
		</div>
	</div>
</div>
@endsection

@section('script')

<script>
	$('#deleteModal').on('shown.bs.modal', function(e) {
		var target = e.relatedTarget.dataset;
		$('#delete_btns').html(
			`<button type="button" class="btn btn-primary" onclick="delete_priceRange(${target.no})">削除</button>
			<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">キャンセル</button>`
		);
	}).on('hidden.bs.modal', function(e) {
	});

	const save_td = (e) => {
		var table_trs = $('.table_tr');
		var table_data = [];
		
		for (let i = 0; i < table_trs.length; i++) {
			var table_tds = table_trs[i].children;
			let td_key = ['start_price', 'end_price', 'profit_rate', 'plus_amount', 'minus_amount', 'profit_amount'];
			const out_data = {};

			for (let j = 0; j < table_tds.length-1; j++) {
				var td_data = table_tds[j].innerText;
				out_data[td_key[j]] = td_data;
			}

			table_data.push(out_data);
		}
		// console.log(table_data);
		price_settings = JSON.stringify(table_data);

		$.ajax({
			url: '{{ route("save_price_settings") }}',
			type: 'post',
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {
				price_settings: price_settings
			},
			success: function(response) {
				console.log('success');
				// toastr.success('設定保存しました。');
			}
		});
	}

	const add_priceRange = () => {
		let prepend_tr = `<tr class="table_tr">
							<td class="table_td" onclick="this.contentEditable=true;" onblur="save_td(event)"></td>
							<td class="table_td" onclick="this.contentEditable=true;" onblur="save_td(event)"></td>
							<td class="table_td" onclick="this.contentEditable=true;" onblur="save_td(event)">10</td>
							<td class="table_td" onclick="this.contentEditable=true;" onblur="save_td(event)">0</td>
							<td class="table_td" onclick="this.contentEditable=true;" onblur="save_td(event)">0</td>
							<td class="table_td" onclick="this.contentEditable=true;" onblur="save_td(event)">0</td>
							<td>
								<div>
									<span class="text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class='bx bxs-trash'></i></span>
								</div>
							</td>
						  </tr>`;
		$('#tbody').prepend(prepend_tr);
	}

	const delete_priceRange = (num) => {
		$(`tr[data-row=${num}]`).remove();
		save_td();
		$('#deleteModal').modal('hide');
	};

	const save_com = (e) => {
		$.ajax({
			url: '{{ route("item_settings_commission") }}',
			type: 'post',
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {
				commission: e.currentTarget.value
			},
			success: function(response) {
				toastr.success('設定保存しました。')
			}
		});
	};

	const save_exp = (e) => {
		$.ajax({
			url: '{{ route("item_settings_expenses") }}',
			type: 'post',
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {
				expenses: e.currentTarget.value
			},
			success: function(response) {
				toastr.success('設定保存しました。')
			}
		});
	};
</script>

@endsection