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
<div class="content-wrapper">
	<div class="container-xxl flex-grow-1 container-p-y">
		<div class="pagetitle">
			<nav>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="/">Yahoo</a></li>
					<li class="breadcrumb-item active">処理状況確認</li>
				</ol>
			</nav>
		</div><!-- End Page Title -->
		<div class="card">
			<div class="card-body" style="overflow: auto;">
				<table class="table table-bordered table-hover datatable">
					<thead>
						<tr>
							<th>アップロード日付</th>
							<th>ファイル名</th>
							<th>処理状況</th>
							<th>出品状況</th>
						</tr>
					</thead>
					<tbody>
						@foreach($logs as $l)
							<tr>
								<td>{{ $l->created_at }}</td>
								<td>{{ $l->csv }}</td>
								<td>
									@if ($l->status == 2)
									出品済み
									@elseif ($l->status == 1)
									出品中
									@endif
								</td>
								<td>{{ $l->exhibited }} / {{ $l->uploaded }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection

@section("script")
<script>

</script>
@endsection
