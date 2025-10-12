@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <div class="page-content">
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Banners</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $id == 0 ? 'Add' : 'Edit' }} Banner</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="card">
                    <div class="card-header px-4 py-3">
                        <h5 class="mb-0">{{ $id == 0 ? 'Add' : 'Edit' }} Banner</h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="bannerForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{ $id }}">
                            
                            <div class="row mb-3">
                                <label for="position" class="col-sm-3 col-form-label">Position</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" id="position" name="position" 
                                           value="{{ $banner ? $banner->position : '' }}" required min="1">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="image" class="col-sm-3 col-form-label">Image</label>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*" {{ $id == 0 ? 'required' : '' }}>
                                    @if($banner && $banner->image)
                                        <div class="mt-2">
                                            <img src="{{ $banner->image }}" alt="Current banner" style="max-width: 200px; height: auto;">
                                        </div>
                                    @endif
                                    <div id="imagePreview" class="mt-2" style="display: none;">
                                        <img id="previewImg" src="" alt="Preview" style="max-width: 200px; height: auto;">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="enable" class="col-sm-3 col-form-label">Enable</label>
                                <div class="col-sm-9">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="enable" name="enable" 
                                               {{ ($banner && $banner->enable) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="enable">
                                            Enable Banner
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-9 offset-sm-3">
                                    <button type="submit" class="btn btn-primary px-4">Save</button>
                                    <a href="{{ route('banners') }}" class="btn btn-secondary px-4 ms-2">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    console.log('Document ready');
    alert('Форма найдена: ' + ($('#bannerForm').length > 0));
    
    // Image preview
    $('#image').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImg').attr('src', e.target.result);
                $('#imagePreview').show();
            }
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').hide();
        }
    });

    // Form submission
    $('#bannerForm').submit(function(e) {
        alert('Форма отправляется!');
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Saving...');
        
        $.ajax({
            url: '{{ route("banners.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                alert('Успех: ' + JSON.stringify(response));
                if (response.success) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                }
            },
            error: function(xhr) {
                alert('Ошибка: ' + xhr.responseText);
            },
            complete: function() {
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });
});
</script>
@endsection
