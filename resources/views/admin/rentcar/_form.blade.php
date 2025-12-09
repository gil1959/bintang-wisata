@csrf

<div class="mb-3">
    <label class="form-label">Title</label>
    <input type="text" name="title" class="form-control"
           value="{{ old('title', $package->title ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Price Per Day</label>
    <input type="number" name="price_per_day" class="form-control"
           step="0.01"
           value="{{ old('price_per_day', $package->price_per_day ?? '') }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Thumbnail</label>
    <input type="file" name="thumbnail" class="form-control">

    @isset($package->thumbnail_path)
        <img src="{{ asset('storage/' . $package->thumbnail_path) }}"
             class="mt-2" style="width: 120px; border-radius: 5px;">
    @endisset
</div>

<div class="mb-3">
    <label class="form-label">Status</label>
    <select name="is_active" class="form-control">
        <option value="1" {{ old('is_active', $package->is_active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
        <option value="0" {{ old('is_active', $package->is_active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
    </select>
</div>

<hr>

<h5>Package Features</h5>

<div id="feature-list">

    @php
        $features = old('features', $package->features ?? []);
    @endphp

    @foreach ($features as $i => $feat)
        <div class="d-flex gap-2 mb-2 feature-row">

            <input type="text"
                   name="features[{{ $i }}][name]"
                   class="form-control"
                   placeholder="Feature name"
                   value="{{ $feat['name'] ?? '' }}"
                   required>

            <label class="mt-2">
                <input type="checkbox"
                       name="features[{{ $i }}][available]"
                       {{ isset($feat['available']) && $feat['available'] ? 'checked' : '' }}>
                Available
            </label>

            <button type="button" class="btn btn-danger btn-sm remove-feature">X</button>
        </div>
    @endforeach

</div>

<button type="button" class="btn btn-secondary mt-2" id="add-feature">
    + Add Feature
</button>

<div class="mt-4">
    <button type="submit" class="btn btn-primary">
        {{ $buttonText }}
    </button>
</div>


{{-- SCRIPT --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    let index = {{ count($features) }};

    document.getElementById('add-feature').addEventListener('click', function () {

        const wrapper = document.getElementById('feature-list');

        const row = document.createElement('div');
        row.classList.add('d-flex', 'gap-2', 'mb-2', 'feature-row');

        row.innerHTML = `
            <input type="text" name="features[${index}][name]" class="form-control"
                   placeholder="Feature name" required>

            <label class="mt-2">
                <input type="checkbox" name="features[${index}][available]">
                Available
            </label>

            <button type="button" class="btn btn-danger btn-sm remove-feature">X</button>
        `;

        wrapper.appendChild(row);
        index++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-feature')) {
            e.target.closest('.feature-row').remove();
        }
    });

});
</script>
