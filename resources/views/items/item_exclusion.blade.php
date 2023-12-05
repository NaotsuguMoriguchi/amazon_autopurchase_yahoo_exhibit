@extends("layouts.main")

@section('content')

<div class="content-wrapper">
	<div class="container-xxl flex-grow-1 container-p-y">
		
		<div class="pagetitle">
			<nav>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="/">Yahoo</a></li>
					<li class="breadcrumb-item">設定</li>
					<li class="breadcrumb-item active">除外設定</li>
				</ol>
			</nav>
		</div>

		<div class="card p-4">
			<form class="form-horizontal">
				<div class="card-body" style="padding:0px">
					<div class="row">

						<div class="col-lg-4">
							<div class="form-group mb-3">
								<label for="not_asin" class="form-label"><h5>・出品不可ASIN</h5></label>
								<div class="col-md-10">
									<textarea class="form-control" id="not_asin" name="not_asin" onchange="setColumn_exset(event);" style="height: 15rem;">{{ $exsetting[0]->not_asin }}</textarea>
								</div>
							</div>
						</div>

						<div class="col-lg-4">
							<div class="form-group mb-3">
								<label for="not_word" class="form-label"><h5>・出品不可ワード</h5></label>
								<div class="col-md-10">
									<textarea class="form-control" id="not_word" name="not_word" value="" onchange="setColumn_exset(event);" style="height: 15rem;">{{ $exsetting[0]->not_word }}</textarea>
								</div>
							</div>
						</div>

						{{-- <div class="col-lg-3">
							<div class="form-group mb-3">
								<label for="remove_word" class="form-label"><h5>・商品名削除ワード</h5></label>
								<div class="col-md-10">
									<textarea class="form-control" id="remove_word" name="remove_word" value="" onchange="setColumn_exset(event);" style="height: 15rem;">{{ $exsetting[0]->remove_word }}</textarea>
								</div>
							</div>
						</div>

						<div class="col-lg-3">
							<div class="form-group mb-3">
								<label for="invalid_word" class="form-label"><h5>・削除無効化ワード</h5></label>
								<div class="col-md-10">
									<textarea class="form-control" id="invalid_word" name="invalid_word" value="" onchange="setColumn_exset(event);" style="height: 15rem;">{{ $exsetting[0]->invalid_word }}</textarea>
								</div>
							</div>
						</div> --}}

					</div>
				</div>
			</form>
		</div>

	</div>
</div>
@endsection

@section('script')

<script>
	
    const setColumn_exset = (e) => {
		$.ajax({
			url: "{{ route('set_column_exset') }}",
			type: "post",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data:{
				col: e.target.name,
				content: e.target.value,
			},
			success: function () {
				toastr.success(`正常に更新されました。`);
			}
		});
	};
</script>

@endsection