@extends("layouts.main")

@section('css')
<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css'>

<style>
	.text-nowrap {
		white-space: inherit !important;
	}

	.table> :not(caption)>*>* {
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

		<div class="card">
			<h5 class="card-header">Yahoo注文履歴</h5>
			<div class="table-responsive text-nowrap">
				<table class="table table-striped">
					<thead>
						<tr>
							<th>
								<input
									id="check_all"
									class="form-check-input mt-0"
									type="checkbox"
									data-check-pattern="[name^='check-key']"
									style="font-size: 1rem;"/>
							</th>
							<th> # </th>
							<th>注文番号</th>
							<th>アイテム ID</th>
							<th>Ship Name</th>
							<th>単価</th>
							<th>数量</th>
							<th>合計金額</th>
							<th>
								<span>
									<a
										href=#
										data-bs-toggle="modal"
										data-bs-target="#checked_download"
									><i class='bx bx-download text-primary' style="font-size: 1.5rem;"></i></a>
								</span>
							</th>
						</tr>
					</thead>
					<tbody class="table-border-bottom-0">
						@foreach( $yahoo_order_items as $item )
						<tr>
							<td style="border-right: 1px #efefef solid;"><input id="item-{{ $item->id }}" name="check-key{{ $item->id }}" data-id="{{ $item->id }}" class="form-check-input check-item mt-0" type="checkbox" value="" /></td>
							<td>{{ $loop->iteration }}</td>
							<td data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="right" data-bs-html="true" title="" data-bs-original-title="<i class='bx bx-alarm bx-xs mb-1' ></i> <span>{{ $item->order_time }}</span>">{{ $item->order_id }}</td>
							<td data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="right" data-bs-html="true" title="" data-bs-original-title="<i class='bx bx-trending-up bx-xs mb-1' ></i> <span>{{ $item->title }}</span>">{{ $item->item_id }}</td>
							<td>{{ $item->ship_lastname }} {{ $item->ship_firstname }}</td>
							<td>{{ $item->unit_price }}</td>
							<td>{{ $item->quantity }}</td>
							<td>{{ $item->total_price }}</td>
							<td>
								<div class="dropdown">
									<button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
										<i class="bx bx-dots-vertical-rounded"></i>
									</button>
									<div class="dropdown-menu">
										<a class="dropdown-item" href="{{ route('csv_download', $item->id) }}"><i class='bx bx-download text-primary' style="font-size: 1rem;"></i> ダウンロード</a>
										<a class="dropdown-item" href="javascript:void(0);"><i class="bx bx-trash me-1"></i> 消去</a>
									</div>
								</div>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
				@if(count($yahoo_order_items) > 0)
				@else
					<h5 class="text-center mt-4">注文データはありません。</h5>
				@endif
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="checked_download">
	<div class="modal-dialog">
		<div class="modal-content">

			<!-- Modal Header -->
			<div class="modal-header bg-primary">
				<h4 class="modal-title text-white">注文データダウンロード</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>

			<!-- Modal body -->
			<div class="modal-body">
				<div class="row mt-2">
					<h5 class="text-center">選択された注文データをダウンロードしますか？</h5>
				</div>
			</div>

			<!-- Modal footer -->
			<div class="modal-footer" id="button-container">
				<button type="button" class="btn btn-outline-primary" onclick="checked_download()"><span class="tf-icons bx bx-download"></span>&nbsp; ダウンロード</button>
				<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">キャンセル</button>
			</div>
			
		</div>
	</div>
</div>

@endsection

@section("script")


<script>
	jQuery(function () {
		jQuery('[data-check-pattern]').checkAll();
	});

	(function ($) {
		'use strict';

		$.fn.checkAll = function (options) {
			return this.each(function () {
				var mainCheckbox = $(this);
				var selector = mainCheckbox.attr('data-check-pattern');
				var onChangeHandler = function (e) {
					var $currentCheckbox = $(e.currentTarget);
					var $subCheckboxes;

					if ($currentCheckbox.is(mainCheckbox)) {
						$subCheckboxes = $(selector);
						$subCheckboxes.prop('checked', mainCheckbox.prop('checked'));
					} else if ($currentCheckbox.is(selector)) {
						$subCheckboxes = $(selector);
						mainCheckbox.prop('checked', $subCheckboxes.filter(':checked').length === $subCheckboxes.length);
					}
				};

				$(document).on('change', 'input[type="checkbox"]', onChangeHandler);
			});
		};
	})(jQuery);


	const checked_download = () => {

		const checkboxes = document.querySelectorAll('.check-item');
		const checkedCheckboxes = [...checkboxes].filter((checkbox) => checkbox.checked);
		const checkedCheckboxesLength = checkedCheckboxes.length;
		// console.log(checkedCheckboxes, checkedCheckboxesLength);

		if (checkedCheckboxesLength > 0) {

			const item_ids = [];
			for (let index = 0; index < checkedCheckboxes.length; index++) {
				var item_id = checkedCheckboxes[index].dataset.id;
				item_ids.push(item_id);
			}
			// console.log(item_ids);

			window.location = `/item/yahoo_order/csv_download/${item_ids}`;
			$('#checked_download').modal('hide');

		} else {
			$('#checked_download').modal('hide');
			toastr.warning('Not found checked item.');
		}
	}

</script>
@endsection